<?php

namespace App\Http\Controllers\student_login;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AttendanceMark;
use App\Models\Master\Weekendcalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;

class AttendanceController extends Controller
{
    public function view()
    {
        return view('student_login.attendence');
    }

    private function studentAttendanceIds(Admission $student): array
    {
        return array_values(array_filter(array_unique([
            trim((string) $student->attendance_unique_id),
            trim((string) $student->admissionNo),
            'STU-' . $student->id,
        ])));
    }

    private function normalizeStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'present', 'in', 'out' => 'present',
            'late' => 'late',
            'early out', 'early-out', 'early_out' => 'early_out',
            'half day', 'half-day', 'halfday', 'half_day' => 'halfday',
            'absent' => 'absent',
            'leave', 'on leave' => 'leave',
            'holiday' => 'holiday',
            'event' => 'event',
            'exam' => 'exam',
            default => '',
        };
    }

    public function getAttendanceDates(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2100',
        ]);

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $student = Admission::where('id', Session::get('id'))
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->firstOrFail();

        $startDate = Carbon::create((int) $validated['year'], (int) $validated['month'], 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();
        $today = Carbon::today();

        $records = AttendanceMark::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('entity_type', 'student')
            ->whereIn('unique_id', $this->studentAttendanceIds($student))
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('id')
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $calendarRows = Weekendcalendar::select('weekendcalendar.date', 'attendance_status.name as status_name')
            ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereBetween('weekendcalendar.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('weekendcalendar.id')
            ->get();

        $calendar = [];
        foreach ($calendarRows as $row) {
            $date = Carbon::parse($row->date)->format('Y-m-d');
            $status = $this->normalizeStatus($row->status_name);
            if ($status && (!isset($calendar[$date]) || $status === 'holiday')) {
                $calendar[$date] = $status;
            }
        }

        $attendance = [];
        $details = [];
        $total = array_fill_keys(['Present', 'Late', 'Early Out', 'Half Day', 'Absent', 'Leave', 'Holiday', 'Event', 'Exam'], 0);
        $scheduledDays = 0;
        $attendanceCredit = 0.0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->toDateString();
            $mark = $records->get($dateKey);
            $status = $mark ? $this->normalizeStatus($mark->status) : ($calendar[$dateKey] ?? '');

            if (!$status && $date->isSunday()) {
                $status = 'holiday';
            }
            if (!$status) {
                continue;
            }

            $attendance[$dateKey] = $status;
            $label = match ($status) {
                'early_out' => 'Early Out',
                'halfday' => 'Half Day',
                default => ucwords($status),
            };
            if (isset($total[$label])) {
                $total[$label]++;
            }

            if (in_array($status, ['present', 'late', 'early_out', 'halfday', 'absent', 'leave'], true)) {
                $scheduledDays++;
                $attendanceCredit += $status === 'halfday' ? 0.5 : (in_array($status, ['present', 'late', 'early_out'], true) ? 1 : 0);
            }

            $details[] = [
                'date' => $dateKey,
                'day' => $date->format('D'),
                'status' => $status,
                'label' => $label,
                'in_time' => $mark && $mark->in_time ? Carbon::parse($mark->in_time)->format('h:i A') : null,
                'out_time' => $mark && $mark->out_time ? Carbon::parse($mark->out_time)->format('h:i A') : null,
            ];
        }

        return response()->json([
            'data' => $attendance,
            'details' => array_values(array_reverse($details)),
            'total' => $total,
            'metrics' => [
                'scheduled_days' => $scheduledDays,
                'attendance_credit' => $attendanceCredit,
                'percentage' => $scheduledDays > 0 ? round(($attendanceCredit / $scheduledDays) * 100, 1) : 0,
            ],
            'month' => $startDate->format('F Y'),
            'is_current_month' => $startDate->isSameMonth($today),
        ]);
    }
}
