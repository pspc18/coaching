<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSetting;
use App\Models\Admission;
use App\Models\User;
use App\Models\Role;
use App\Models\AttendanceStatus;
use App\Models\AttendanceMark;
use App\Models\AttendanceMarkingWindow;
use App\Models\Master\Weekendcalendar;
use Carbon\Carbon;
use Session;
use Helper;
use Illuminate\Support\Facades\Log;

class AttendanceMarkController extends Controller
{
    private function normalizedMessagingServices($raw): array
    {
        $default = ['whatsapp', 'firebase', 'sms'];

        if (is_string($raw) && trim($raw) !== '') {
            $raw = explode(',', $raw);
        }

        if (!is_array($raw) || empty($raw)) {
            return $default;
        }

        $allowed = ['whatsapp', 'firebase', 'sms'];
        $services = [];
        foreach ($raw as $item) {
            $service = strtolower(trim((string) $item));
            if (in_array($service, $allowed, true)) {
                $services[] = $service;
            }
        }

        $services = array_values(array_unique($services));
        return !empty($services) ? $services : $default;
    }

    private function resolveAttendanceMessageEntity(string $uniqueId, string $entityType, int $branchId, int $sessionId): array
    {
        $entityType = strtolower(trim($entityType));

        if ($entityType === 'student') {
            $student = Admission::select('id', 'first_name', 'last_name', 'mobile')
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->where(function ($q) use ($uniqueId) {
                    $q->where('attendance_unique_id', $uniqueId)
                        ->orWhere('admissionNo', $uniqueId);
                })
                ->first();

            return [
                'entity_type' => 'student',
                'source_table' => 'admission',
                'mobile' => (string) ($student->mobile ?? ''),
                'display_name' => $student
                    ? trim((string) (($student->first_name ?? '') . ' ' . ($student->last_name ?? '')))
                    : $uniqueId,
            ];
        }

        $staff = User::select('id', 'first_name', 'last_name', 'mobile')
            ->where('branch_id', $branchId)
            ->where(function ($q) use ($uniqueId) {
                $q->where('attendance_unique_id', $uniqueId)
                    ->orWhere('id', (stripos($uniqueId, 'USR-') === 0) ? (int) str_replace('USR-', '', $uniqueId) : 0);
            })
            ->first();

        return [
            'entity_type' => 'staff',
            'source_table' => 'users',
            'mobile' => (string) ($staff->mobile ?? ''),
            'display_name' => $staff
                ? trim((string) (($staff->first_name ?? '') . ' ' . ($staff->last_name ?? '')))
                : $uniqueId,
        ];
    }

    private function buildBulkAttendanceMessage(AttendanceMark $mark, array $entity): string
    {
        $name = trim((string) ($entity['display_name'] ?? '')) ?: (string) $mark->unique_id;
        $dateStr = Carbon::parse((string) $mark->date)->format('d/m/Y');
        $hasIn = !empty($mark->in_time);
        $hasOut = !empty($mark->out_time);
        $inStr = $hasIn ? Carbon::parse((string) $mark->in_time)->format('h:i A') : '';
        $rawStatus = (string) ($mark->status ?? '');
        $status = ucwords(str_replace('_', ' ', $rawStatus));

        if (!$hasIn && !$hasOut) {
            return "Attendance update for {$name}: Date {$dateStr}, Status {$status}.";
        }

        if ($hasIn && !$hasOut) {
            return "Attendance update for {$name}: Check-in {$inStr} on {$dateStr}, Status {$status}.";
        }

        $outStr = Carbon::parse((string) $mark->out_time)->format('h:i A');
        if (!$hasIn && $hasOut) {
            return "Attendance update for {$name}: Check-out {$outStr} on {$dateStr}, Status {$status}.";
        }
        return "Attendance update for {$name}: Check-in {$inStr} on {$dateStr}, Check-out {$outStr}, Status {$status}.";
    }

    private function resolveStudentUniqueId($student)
    {
        $attendanceId = trim((string) ($student->attendance_unique_id ?? ''));
        if ($attendanceId !== '') {
            return $attendanceId;
        }

        $admissionNo = trim((string) ($student->admissionNo ?? ''));
        if ($admissionNo !== '') {
            return $admissionNo;
        }

        return 'STU-' . $student->id;
    }

    private function resolveStaffUniqueId($member)
    {
        $attendanceId = trim((string) ($member->attendance_unique_id ?? ''));

        return $attendanceId !== '' ? $attendanceId : ('USR-' . $member->id);
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

    private function normalizeTimeValue($value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $formats = ['H:i:s', 'H:i', 'h:i A', 'h:iA', 'g:i A', 'g:iA'];
        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, strtoupper($raw));
                if ($dt !== false) {
                    return $dt->format('H:i:s');
                }
            } catch (\Throwable $e) {
                // Try next format
            }
        }

        try {
            return Carbon::parse($raw)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function isWithinAttendanceWindow(?AttendanceMarkingWindow $window): bool
    {
        if (empty($window) || (int) ($window->is_active ?? 0) !== 1) {
            return false;
        }

        $from = $this->normalizeTimeValue($window->from_time ?? null);
        $to = $this->normalizeTimeValue($window->to_time ?? null);
        if (empty($from) || empty($to)) {
            return false;
        }

        $now = Carbon::now(Session::get('timezone') ?: config('app.timezone'))->format('H:i:s');

        if ($from <= $to) {
            return $now >= $from && $now <= $to;
        }

        return $now >= $from || $now <= $to;
    }

    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $roleId = (int) Session::get('role_id');
        $canAccessStaffAttendance = $roleId === 1;
        $activeTab = in_array((string) $request->input('tab', 'students'), ['students', 'staff'], true)
            ? (string) $request->input('tab', 'students')
            : 'students';
        if (!$canAccessStaffAttendance) {
            $activeTab = 'students';
        }

        $classes = Helper::classType();
        $allowedClassIds = $classes->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();
        $classTypeId = $request->input('class_type_id');
        if ($classTypeId !== null && $classTypeId !== '' && !in_array((int) $classTypeId, $allowedClassIds, true)) {
            abort(403, 'You are not assigned to the selected class.');
        }

        $setting = AttendanceSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->first();

        $selectedDate = $request->date ?? date('Y-m-d');
        $allowBackDate = (int) ($setting->allow_back_date_attendance ?? 0) === 1;
        $allowBackDateForUser = $allowBackDate || $roleId === 1;

        if (!$request->isMethod('post') && !$allowBackDateForUser && $selectedDate < date('Y-m-d')) {
            $selectedDate = date('Y-m-d');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'date' => 'required|date',
            ]);

            $selectedDate = $request->input('date', $selectedDate);

            if (!$allowBackDateForUser && $selectedDate < date('Y-m-d')) {
                return redirect()->to($request->fullUrl())->with('error', 'Back date attendance is not allowed.');
            }

            if ($this->isHolidayDate((int) $branchId, (int) $sessionId, (string) $selectedDate)) {
                return redirect()->to($request->fullUrl())->with('error', 'Attendance marking is not allowed on holiday dates.');
            }

            $rows = $request->input('rows', []);
            $jsonRows = $request->input('attendance_rows_json');
            if (is_string($jsonRows) && trim($jsonRows) !== '') {
                $decodedRows = json_decode($jsonRows, true);
                if (is_array($decodedRows)) {
                    $rows = $decodedRows;
                }
            }

            if (!$canAccessStaffAttendance) {
                abort_if(empty($classTypeId), 403, 'Select one of your assigned classes before marking attendance.');

                $window = AttendanceMarkingWindow::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('class_type_id', (int) $classTypeId)
                    ->first();

                if (!$this->isWithinAttendanceWindow($window)) {
                    return redirect()
                        ->to($request->fullUrl())
                        ->with('error', 'Attendance marking is not allowed at this time for the selected class.');
                }

                $allowedStudentIds = [];
                $allowedStudents = Admission::select('id', 'attendance_unique_id', 'admissionNo')
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->where('status', 1)
                    ->where('class_type_id', (int) $classTypeId)
                    ->get();
                foreach ($allowedStudents as $allowedStudent) {
                    $allowedStudentIds[] = $this->resolveStudentUniqueId($allowedStudent);
                }

                foreach ($rows as $row) {
                    if (!is_array($row) || (string) ($row['selected'] ?? '') !== '1') {
                        continue;
                    }
                    abort_unless(
                        (string) ($row['entity_type'] ?? '') === 'student'
                            && in_array((string) ($row['unique_id'] ?? ''), $allowedStudentIds, true),
                        403,
                        'You can mark attendance only for students in your assigned classes.'
                    );
                }
            }
            $submittedRowCount = count(array_filter($rows, function ($row) {
                return is_array($row) && (string) ($row['selected'] ?? '') === '1';
            }));
            $resetKeys = [];
            $savedMarks = [];
            $savedCount = 0;
            foreach ($rows as $row) {
                if ((string) ($row['selected'] ?? '') !== '1') {
                    continue;
                }

                $uniqueId = $row['unique_id'] ?? null;
                $entityType = $row['entity_type'] ?? null;
                $status = $row['status'] ?? null;
                $inTime = $this->normalizeTimeValue($row['in_time'] ?? null);
                $outTime = $this->normalizeTimeValue($row['out_time'] ?? null);

                if (!$uniqueId || !$entityType) {
                    continue;
                }

                if ($status !== null && $status !== '' && !in_array($status, ['in', 'out', 'absent', 'halfday', 'holiday'], true)) {
                    continue;
                }

                if (!$status && !$inTime && !$outTime) {
                    $resetKeys[] = $uniqueId;
                    continue;
                }

                $mark = AttendanceMark::where('unique_id', $uniqueId)
                    ->where('date', $selectedDate)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                $newValues = [
                    'entity_type' => (string) $entityType,
                    'status' => $status ?: null,
                    'in_time' => $inTime ?: null,
                    'out_time' => $outTime ?: null,
                ];

                $isChanged = !$mark
                    || (string) ($mark->entity_type ?? '') !== $newValues['entity_type']
                    || (string) ($mark->status ?? '') !== (string) ($newValues['status'] ?? '')
                    || $this->normalizeTimeValue($mark->in_time ?? null) !== $newValues['in_time']
                    || $this->normalizeTimeValue($mark->out_time ?? null) !== $newValues['out_time'];

                if (!$mark) {
                    $mark = new AttendanceMark();
                    $mark->unique_id = $uniqueId;
                    $mark->date = $selectedDate;
                    $mark->branch_id = $branchId;
                    $mark->session_id = $sessionId;
                }

                if ($isChanged) {
                    $mark->entity_type = $newValues['entity_type'];
                    $mark->status = $newValues['status'];
                    $mark->in_time = $newValues['in_time'];
                    $mark->out_time = $newValues['out_time'];
                    $mark->created_by = Session::get('id');
                    $mark->save();
                }

                if ($isChanged) {
                    $savedMarks[] = [
                        'mark' => $mark,
                        'entity_type' => (string) $entityType,
                    ];
                    $savedCount++;
                }
            }

            if (!empty($resetKeys)) {
                AttendanceMark::where('date', $selectedDate)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->whereIn('unique_id', $resetKeys)
                    ->delete();
            }

            $notificationSummary = [
                'processed' => 0,
                'sent' => 0,
                'failed' => 0,
                'skipped' => 0,
                'errors' => [],
            ];

            $manualMessagingEnabled = (int) ($setting->manual_attendance_messaging_enabled ?? 0) === 1;
            if ($manualMessagingEnabled && !empty($savedMarks)) {
                $services = $this->normalizedMessagingServices($setting->messaging_services ?? []);
                $dispatcher = app(MultipleCronController::class);

                foreach ($savedMarks as $savedMark) {
                    /** @var AttendanceMark $attendanceMark */
                    $attendanceMark = $savedMark['mark'];
                    $entity = $this->resolveAttendanceMessageEntity(
                        (string) $attendanceMark->unique_id,
                        (string) $savedMark['entity_type'],
                        (int) $branchId,
                        (int) $sessionId
                    );

                    try {
                        $dispatch = $dispatcher->sendDirectAttendanceNotifications(
                            $services,
                            $attendanceMark,
                            $entity
                        );
                        $notificationSummary['processed'] += (int) ($dispatch['processed'] ?? 0);
                        $notificationSummary['sent'] += (int) ($dispatch['sent'] ?? 0);
                        $notificationSummary['failed'] += (int) ($dispatch['failed'] ?? 0);
                        $notificationSummary['skipped'] += (int) ($dispatch['duplicate_skipped'] ?? 0);
                        foreach (($dispatch['results'] ?? []) as $result) {
                            if (empty($result['ok']) && !empty($result['error'])) {
                                $notificationSummary['errors'][] = (string) $result['error'];
                            }
                        }
                    } catch (\Throwable $e) {
                        $notificationSummary['failed']++;
                        Log::error('Manual attendance notification dispatch failed.', [
                            'attendance_mark_id' => $attendanceMark->id,
                            'attendance_unique_id' => $attendanceMark->unique_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            $deletedCount = count($resetKeys);
            $message = $savedCount > 0
                ? "Attendance saved successfully. Updated: {$savedCount}."
                : ($deletedCount > 0
                    ? "Attendance reset successfully. Reset: {$deletedCount}."
                : ($submittedRowCount > 0
                    ? 'Attendance is already up to date. Select a different status or change check-in/check-out time to update it.'
                    : 'No attendance changes were selected.'));
            if ($manualMessagingEnabled) {
                $message .= ' Push notifications sent: ' . $notificationSummary['sent'] . '.';
                if ($notificationSummary['failed'] > 0) {
                    $message .= ' Failed: ' . $notificationSummary['failed'] . '.';
                    if (!empty($notificationSummary['errors'])) {
                        $message .= ' ' . implode(', ', array_unique($notificationSummary['errors'])) . '.';
                    }
                }
            }

            return redirect()->to($request->fullUrl())->with('message', $message);
        }

        $roleFilterId = $request->input('role_id');

        $studentsQuery = Admission::select('id', 'admissionNo', 'attendance_unique_id', 'first_name', 'last_name', 'class_type_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->when($classTypeId !== null && $classTypeId !== '', function ($query) use ($classTypeId) {
                $query->where('class_type_id', $classTypeId);
            })
            ->orderBy('first_name');

        $students = ($activeTab === 'students' && $classTypeId !== null && $classTypeId !== ''
            ? $studentsQuery->get()
            : collect())
            ->map(function ($student) {
                $student->attendance_unique_id = $this->resolveStudentUniqueId($student);
                return $student;
            });

        $staffQuery = User::select('id', 'attendance_unique_id', 'first_name', 'last_name', 'role_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->where('role_id', '!=', 3)
            ->when($roleFilterId !== null && $roleFilterId !== '', function ($query) use ($roleFilterId) {
                $query->where('role_id', $roleFilterId);
            })
            ->orderBy('first_name');

        $staff = ($canAccessStaffAttendance && $activeTab === 'staff'
            ? $staffQuery->get()
            : collect())
            ->map(function ($member) {
                $member->attendance_unique_id = $this->resolveStaffUniqueId($member);
                return $member;
            });

        $staffRoleIds = $canAccessStaffAttendance ? User::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->where('role_id', '!=', 3)
            ->distinct()
            ->pluck('role_id') : collect();
        $staffRoles = Role::whereIn('id', $staffRoleIds)->orderBy('name')->get();

        $attendanceStatuses = AttendanceStatus::markable()->orderBy('id')->get();

        $visibleUniqueIds = $activeTab === 'staff'
            ? $staff->pluck('attendance_unique_id')->filter()->values()
            : $students->pluck('attendance_unique_id')->filter()->values();
        $attendanceMarks = $visibleUniqueIds->isEmpty()
            ? collect()
            : AttendanceMark::where('date', $selectedDate)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->whereIn('unique_id', $visibleUniqueIds)
                ->get()
                ->keyBy('unique_id');

        $attendanceType = $setting->attendance_type ?? 2;
        $isHolidayDate = $this->isHolidayDate((int) $branchId, (int) $sessionId, (string) $selectedDate);

        if ($attendanceType == 1) {
            return Helper::view('attendance.biometric', compact('setting', 'classes', 'students', 'staff', 'staffRoles', 'attendanceStatuses', 'attendanceMarks', 'selectedDate', 'activeTab', 'allowBackDateForUser', 'isHolidayDate', 'canAccessStaffAttendance'));
        }

        if ($attendanceType == 3) {
            return Helper::view('attendance.qr', compact('setting', 'selectedDate', 'activeTab', 'allowBackDateForUser', 'isHolidayDate', 'canAccessStaffAttendance'));
        }

        return Helper::view('attendance.normal', compact('setting', 'classes', 'students', 'staff', 'staffRoles', 'attendanceStatuses', 'attendanceMarks', 'selectedDate', 'activeTab', 'allowBackDateForUser', 'isHolidayDate', 'canAccessStaffAttendance'));
    }
}
