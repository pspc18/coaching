<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AttendanceMark;
use App\Models\AttendanceSetting;
use App\Models\Master\LeaveManagement;
use App\Models\Master\Weekendcalendar;
use App\Models\User;
use App\Models\UserAttendanceMarkingWindow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;

class SelfAttendanceController extends Controller
{
    private function isWithinUserMarkingWindow(UserAttendanceMarkingWindow $window): bool
    {
        if ((int) $window->is_active !== 1) return false;
        $from = substr((string) $window->from_time, 0, 8);
        $to = substr((string) $window->to_time, 0, 8);
        $now = Carbon::now(Session::get('timezone') ?: config('app.timezone'))->format('H:i:s');
        return $from <= $to ? ($now >= $from && $now <= $to) : ($now >= $from || $now <= $to);
    }

    private function statusLabel(?string $status): string
    {
        if (!$status) {
            return '';
        }

        return ucwords(str_replace('_', ' ', $status));
    }

    private function leaveStatusMeta($status)
    {
        $raw = (string) $status;
        if ($raw === '1') {
            return ['label' => 'Approved', 'class' => 'badge-success'];
        }

        if ($raw === '0') {
            return ['label' => 'Rejected', 'class' => 'badge-danger'];
        }

        if ($raw === '3') {
            return ['label' => 'Cancelled', 'class' => 'badge-secondary'];
        }

        return ['label' => 'Pending', 'class' => 'badge-warning'];
    }

    private function clearLeaveMarks(string $uniqueId, int $branchId, int $sessionId, string $fromDate, string $toDate): void
    {
        AttendanceMark::where('unique_id', $uniqueId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->where('status', 'absent')
            ->delete();
    }


    private function normalizeCalendarStatus(?string $rawName): string
    {
        $name = strtolower(trim((string) $rawName));
        if ($name === 'holiday') {
            return 'holiday';
        }
        if ($name === 'event') {
            return 'event';
        }
        return '';
    }

    private function calendarStatusMap(int $branchId, int $sessionId, string $fromDate, string $toDate): array
    {
        $rows = Weekendcalendar::select('weekendcalendar.date', 'attendance_status.name as status_name')
            ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereBetween('weekendcalendar.date', [$fromDate, $toDate])
            ->orderBy('weekendcalendar.date')
            ->orderBy('weekendcalendar.id')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $dateKey = (string) $row->date;
            $status = $this->normalizeCalendarStatus($row->status_name ?? '');
            if ($status === '') {
                continue;
            }

            if (!isset($map[$dateKey])) {
                $map[$dateKey] = $status;
                continue;
            }

            if ($map[$dateKey] !== 'holiday' && $status === 'holiday') {
                $map[$dateKey] = 'holiday';
            }
        }

        return $map;
    }

    private function isHolidayDate(int $branchId, int $sessionId, string $date): bool
    {
        return Weekendcalendar::leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereDate('weekendcalendar.date', $date)
            ->whereRaw("LOWER(COALESCE(attendance_status.name, '')) = ?", ['holiday'])
            ->exists();
    }

    public function index(Request $request)
    {
        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $roleId = (int) Session::get('role_id');
        $userId = (int) Session::get('id');

        if ($roleId === 1) {
            return redirect()->to('master_dashboard')
                ->with('error', 'Self Attendance is not available for admin users.');
        }

        $setting = AttendanceSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->first();

        $attendanceType = (int) ($setting->attendance_type ?? 2);
        $allowBackDate = (int) ($setting->allow_back_date_attendance ?? 0) === 1;
        $canBackDateMarking = $allowBackDate || $roleId === 1;

        $entityType = $roleId == 3 ? 'student' : 'staff';
        $leaveUserType = $entityType === 'student' ? 'student' : 'user';
        $displayName = '';
        $uniqueId = '';
        $entityModelId = 0;

        if ($entityType === 'student') {
            $student = Admission::select('id', 'attendance_unique_id', 'admissionNo', 'first_name', 'last_name')
                ->where('id', $userId)
                ->first();
            if ($student) {
                $uniqueId = $student->attendance_unique_id ?? $student->admissionNo ?? ('STU-' . $student->id);
                $displayName = trim($student->first_name . ' ' . $student->last_name);
                $entityModelId = (int) $student->id;
            }
        } else {
            $user = User::select('id', 'attendance_unique_id', 'first_name', 'last_name')
                ->where('id', $userId)
                ->first();
            if ($user) {
                $uniqueId = $user->attendance_unique_id ?? ('USR-' . $user->id);
                $displayName = trim($user->first_name . ' ' . $user->last_name);
                $entityModelId = (int) $user->id;
            }
        }

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $selectedDate = $request->date ?? date('Y-m-d');

        if ($request->isMethod('post')) {
            if ((int) $request->input('leave_mode', 0) === 1) {
                $request->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                    'reason' => 'nullable|string|max:1000',
                ]);

                if (!$uniqueId || !$entityModelId) {
                    return response()->json(['ok' => false, 'message' => 'User not found.'], 422);
                }

                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate = Carbon::parse($request->to_date)->startOfDay();
                $reason = trim((string) $request->reason);

                if (!$canBackDateMarking && $fromDate->toDateString() < date('Y-m-d')) {
                    return response()->json(['ok' => false, 'message' => 'Back date leave request is not allowed.'], 422);
                }

                $leave = new LeaveManagement();
                $leave->session_id = $sessionId;
                $leave->branch_id = $branchId;
                $leave->user_id = $entityModelId;
                $leave->user_type = $leaveUserType;
                $leave->attendance_unique_id = (string) $uniqueId;
                $leave->admission_id = null;
                $leave->class_type_id = null;
                $leave->subject = 'Self Attendance Leave';
                $leave->from_date = $fromDate->toDateString();
                $leave->to_date = $toDate->toDateString();
                $leave->reason = $reason;
                $leave->status = '2';
                $leave->save();

                if ($request->ajax()) {
                    return response()->json([
                        'ok' => true,
                        'message' => 'Leave request submitted for approval.',
                    ]);
                }

                return redirect()->to(url('attendance/self?month=' . $month . '&year=' . $year))->with('message', 'Leave request submitted for approval.');
            }

            if ((int) $request->input('leave_action_mode', 0) === 1) {
                $request->validate([
                    'leave_id' => 'required|integer',
                    'action' => 'required|in:cancel,delete',
                ]);

                $leave = LeaveManagement::where('id', (int) $request->leave_id)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('user_type', $leaveUserType)
                    ->where('attendance_unique_id', (string) $uniqueId)
                    ->first();

                if (!$leave) {
                    return response()->json(['ok' => false, 'message' => 'Leave request not found.'], 404);
                }

                if ((string) $leave->status === '1') {
                    return response()->json(['ok' => false, 'message' => 'Approved leave cannot be cancelled or deleted by user.'], 422);
                }

                if ($request->action === 'cancel') {
                    $leave->status = '3';
                    $leave->save();
                    $this->clearLeaveMarks((string) $uniqueId, $branchId, $sessionId, (string) $leave->from_date, (string) $leave->to_date);

                    return response()->json(['ok' => true, 'message' => 'Leave cancelled successfully.']);
                }

                $this->clearLeaveMarks((string) $uniqueId, $branchId, $sessionId, (string) $leave->from_date, (string) $leave->to_date);
                $leave->delete();

                return response()->json(['ok' => true, 'message' => 'Leave deleted successfully.']);
            }

            if ($entityType === 'staff') {
                $userWindow = UserAttendanceMarkingWindow::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)->where('user_id', $entityModelId)->first();
                if ($userWindow && !$this->isWithinUserMarkingWindow($userWindow)) {
                    $message = 'Self attendance marking is not allowed at this time.';
                    return $request->ajax()
                        ? response()->json(['ok'=>false, 'message'=>$message], 422)
                        : redirect()->back()->with('error', $message);
                }
            }

            $request->validate([
                'date' => 'required|date',
                'status' => 'nullable|in:in,out,absent,halfday,holiday',
                'in_time' => 'nullable|date_format:H:i',
                'out_time' => 'nullable|date_format:H:i',
            ]);

            $date = $request->date;
            if (!$canBackDateMarking && $date < date('Y-m-d')) {
                if ($request->ajax()) {
                    return response()->json(['ok' => false, 'message' => 'Back date attendance is not allowed.'], 422);
                }
                return redirect()->back()->with('error', 'Back date attendance is not allowed.');
            }

            if ($this->isHolidayDate($branchId, $sessionId, (string) $date)) {
                if ($request->ajax()) {
                    return response()->json(['ok' => false, 'message' => 'Attendance marking is not allowed on holiday dates.'], 422);
                }
                return redirect()->back()->with('error', 'Attendance marking is not allowed on holiday dates.');
            }

            $status = $request->status ?: null;
            $inTime = $request->in_time ?: null;
            $outTime = $request->out_time ?: null;
            $existingMark = AttendanceMark::where('unique_id', $uniqueId)
                ->where('date', $date)
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->first();

            if ($attendanceType !== 1) {
                // For non-biometric mode, keep existing punch times if already present.
                $inTime = $existingMark->in_time ?? null;
                $outTime = $existingMark->out_time ?? null;
            }

            if (!$status && !$inTime && !$outTime) {
                AttendanceMark::where('unique_id', $uniqueId)
                    ->where('date', $date)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->delete();

                if ($request->ajax()) {
                    return response()->json([
                        'ok' => true,
                        'message' => 'Attendance cleared.',
                        'date' => $date,
                        'mark' => null,
                    ]);
                }
            } else {
                $mark = AttendanceMark::updateOrCreate(
                    [
                        'unique_id' => $uniqueId,
                        'date' => $date,
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                    ],
                    [
                        'entity_type' => $entityType,
                        'status' => $status,
                        'in_time' => $inTime,
                        'out_time' => $outTime,
                        'created_by' => $userId,
                    ]
                );

                if ($request->ajax()) {
                    return response()->json([
                        'ok' => true,
                        'message' => 'Attendance saved successfully.',
                        'date' => $date,
                        'mark' => [
                            'status' => $mark->status,
                            'status_label' => $this->statusLabel($mark->status),
                            'in_time' => $mark->in_time,
                            'out_time' => $mark->out_time,
                        ],
                    ]);
                }
            }

            return redirect()->to(url('attendance/self?month=' . $month . '&year=' . $year))->with('message', 'Attendance saved successfully.');
        }

        $marks = AttendanceMark::where('unique_id', $uniqueId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy('date');

        $calendarStatusByDate = $this->calendarStatusMap($branchId, $sessionId, $startDate, $endDate);

        $leaveRequests = LeaveManagement::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('user_type', $leaveUserType)
            ->where('attendance_unique_id', (string) $uniqueId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                $meta = $this->leaveStatusMeta($row->status);
                $row->status_label = $meta['label'];
                $row->status_class = $meta['class'];
                return $row;
            });

        $leaveCalendarByDate = [];
        foreach ($leaveRequests as $leaveRow) {
            $rawStatus = (string) ($leaveRow->status ?? '');
            if (!in_array($rawStatus, ['1', '2'], true)) {
                continue;
            }

            $from = Carbon::parse((string) $leaveRow->from_date)->startOfDay();
            $to = Carbon::parse((string) $leaveRow->to_date)->startOfDay();

            if ($to->lt(Carbon::parse($startDate)) || $from->gt(Carbon::parse($endDate))) {
                continue;
            }

            if ($from->lt(Carbon::parse($startDate))) {
                $from = Carbon::parse($startDate);
            }
            if ($to->gt(Carbon::parse($endDate))) {
                $to = Carbon::parse($endDate);
            }

            $cursor = $from->copy();
            while ($cursor->lte($to)) {
                $dateKey = $cursor->toDateString();
                $isApproved = $rawStatus === '1';

                if (!isset($leaveCalendarByDate[$dateKey])) {
                    $leaveCalendarByDate[$dateKey] = [
                        'status' => $isApproved ? 'leave' : 'leave_pending',
                        'lock' => $isApproved,
                    ];
                } elseif ($isApproved) {
                    // Approved leave has higher priority over pending leave.
                    $leaveCalendarByDate[$dateKey] = [
                        'status' => 'leave',
                        'lock' => true,
                    ];
                }

                $cursor->addDay();
            }
        }

        $calendar = [];
        $firstDayOfWeek = (int) date('w', strtotime($startDate));
        $daysInMonth = (int) date('t', strtotime($startDate));
        $dayCounter = 1;

        for ($week = 0; $week < 6; $week++) {
            $row = [];
            for ($d = 0; $d < 7; $d++) {
                if ($week === 0 && $d < $firstDayOfWeek) {
                    $row[] = null;
                } elseif ($dayCounter > $daysInMonth) {
                    $row[] = null;
                } else {
                    $row[] = sprintf('%04d-%02d-%02d', $year, $month, $dayCounter);
                    $dayCounter++;
                }
            }
            $calendar[] = $row;
        }

        $prevMonthTs = strtotime('-1 month', strtotime($startDate));
        $nextMonthTs = strtotime('+1 month', strtotime($startDate));

        $prevMonth = (int) date('n', $prevMonthTs);
        $prevYear = (int) date('Y', $prevMonthTs);
        $nextMonth = (int) date('n', $nextMonthTs);
        $nextYear = (int) date('Y', $nextMonthTs);

        $monthName = date('F', strtotime($startDate));

        return view('attendance.self_calendar', compact(
            'setting',
            'attendanceType',
            'displayName',
            'uniqueId',
            'month',
            'year',
            'monthName',
            'calendar',
            'marks',
            'selectedDate',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'leaveRequests',
            'canBackDateMarking',
            'calendarStatusByDate',
            'leaveCalendarByDate'
        ));
    }
}
