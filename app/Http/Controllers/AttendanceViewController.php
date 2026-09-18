<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Models\Admission;
use App\Models\AttendanceMark;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Models\Role;
use App\Models\Master\Weekendcalendar;
use Session;
use Helper;

class AttendanceViewController extends Controller
{
    private function resolveAllowedClassIds(): array
    {
        if ((int) Session::get('role_id') === 1) {
            return [];
        }

        $user = User::find(Session::get('id'));
        if (!$user || empty($user->class_type_id)) {
            return [];
        }

        $classIds = is_array($user->class_type_id)
            ? $user->class_type_id
            : explode(',', (string) $user->class_type_id);

        return array_values(array_filter(array_map('intval', $classIds)));
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

    private function resolveStaffAliases($member)
    {
        $primary = $this->resolveStaffUniqueId($member);
        $legacy = 'USR-' . $member->id;

        return array_values(array_unique([$primary, $legacy]));
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


    private function statusLabel(?string $status): string
    {
        if (!$status) {
            return '';
        }

        return ucwords(str_replace('_', ' ', $status));
    }

    private function reportStatus(?string $status): ?string
    {
        return match (strtolower(trim((string) $status))) {
            'in', 'present', 'late' => 'in',
            'out', 'early_out' => 'out',
            'absent', 'leave' => 'absent',
            'halfday', 'half_day' => 'halfday',
            'holiday' => 'holiday',
            default => null,
        };
    }

    private function formatTime12(?string $time): string
    {
        if (!$time) {
            return '';
        }

        $ts = strtotime($time);
        return $ts ? date('h:i A', $ts) : '';
    }

    private function exportMonthlyCsv(string $activeTab, string $selectedUniqueId, string $selectedName, int $month, int $year, $marks)
    {
        $entityLabel = $activeTab === 'staff' ? 'Staff' : 'Student';
        $fileName = 'attendance-history-' . strtolower($entityLabel) . '-' . $selectedUniqueId . '-' . sprintf('%04d-%02d', $year, $month) . '.csv';

        $summary = [
            'In' => $marks->where('status', 'in')->count(),
            'Out' => $marks->where('status', 'out')->count(),
            'Absent' => $marks->where('status', 'absent')->count(),
            'Half Day' => $marks->where('status', 'halfday')->count(),
            'Holiday' => $marks->where('status', 'holiday')->count(),
            'Total Marked Days' => $marks->count(),
        ];

        return response()->streamDownload(function () use ($entityLabel, $selectedUniqueId, $selectedName, $month, $year, $marks, $summary) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Attendance History Export']);
            fputcsv($out, ['Entity Type', $entityLabel]);
            fputcsv($out, ['Unique ID', $selectedUniqueId]);
            fputcsv($out, ['Name', $selectedName]);
            fputcsv($out, ['Month', date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year]);
            fputcsv($out, []);
            fputcsv($out, ['Date', 'Day', 'Status', 'Check In', 'Check Out']);

            foreach ($marks as $mark) {
                fputcsv($out, [
                    $mark->date,
                    date('l', strtotime($mark->date)),
                    $this->statusLabel($mark->status),
                    $this->formatTime12($mark->in_time),
                    $this->formatTime12($mark->out_time),
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ['Summary', 'Count']);
            foreach ($summary as $label => $count) {
                fputcsv($out, [$label, $count]);
            }

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $canAccessStaffAttendance = (int) Session::get('role_id') === 1;
        $classes = $canAccessStaffAttendance ? Helper::classType() : Helper::classType()->whereIn('id', $this->resolveAllowedClassIds());
        $allowedClassIds = $canAccessStaffAttendance
            ? $classes->pluck('id')->map(function ($id) {
                return (int) $id;
            })
            : collect($this->resolveAllowedClassIds());
        $classFilter = $request->input('class_type_id', '');
        if ($classFilter !== '' && !$allowedClassIds->contains((int) $classFilter)) {
            abort(403, 'You are not assigned to the selected class.');
        }

        $activeTab = $canAccessStaffAttendance && $request->tab === 'staff' ? 'staff' : 'students';

        // Performance optimization: default classFilter to student's class or first assigned class
        if ($activeTab === 'students' && $classFilter === '') {
            if ($request->filled('student')) {
                $stuClassId = Admission::where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->where(function ($q) use ($request) {
                        $q->where('attendance_unique_id', $request->student)
                            ->orWhere('admissionNo', $request->student)
                            ->orWhere('id', (int) str_replace('STU-', '', $request->student));
                    })
                    ->value('class_type_id');
                if ($stuClassId && $allowedClassIds->contains((int) $stuClassId)) {
                    $classFilter = (string) $stuClassId;
                }
            }
            if ($classFilter === '' && $classes->isNotEmpty()) {
                $classFilter = (string) $classes->first()->id;
            }
        }

        if ($canAccessStaffAttendance) {
            $classes = $classes;
        }

        $students = Admission::select('id', 'attendance_unique_id', 'admissionNo', 'first_name', 'last_name', 'class_type_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->whereIn('class_type_id', $allowedClassIds)
            ->when($classFilter !== '', function ($query) use ($classFilter) {
                $query->where('class_type_id', (int) $classFilter);
            })
            ->orderBy('first_name')
            ->get()
            ->map(function ($student) {
                $student->attendance_unique_id = $this->resolveStudentUniqueId($student);
                return $student;
            });

        $staff = User::select('id', 'attendance_unique_id', 'first_name', 'last_name', 'role_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->where('role_id', '!=', 3)
            ->orderBy('first_name')
            ->when(!$canAccessStaffAttendance, function ($query) {
                $query->whereRaw('1 = 0');
            })->get()
            ->map(function ($member) {
                $member->attendance_unique_id = $this->resolveStaffUniqueId($member);
                $member->attendance_aliases = $this->resolveStaffAliases($member);
                return $member;
            });

        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));
        $startDate = date('Y-m-01', strtotime($year . '-' . $month . '-01'));
        $endDate = date('Y-m-t', strtotime($startDate));
        $yearStart = $year . '-01-01';
        $yearEnd = $year . '-12-31';

        $calendarMonthMap = $this->calendarStatusMap((int) $branchId, (int) $sessionId, $startDate, $endDate);
        $calendarYearMap = $this->calendarStatusMap((int) $branchId, (int) $sessionId, $yearStart, $yearEnd);

        if ($activeTab === 'staff') {
            $requestedStaffId = (string) ($request->staff ?? '');

            if ($requestedStaffId === '') {
                $preferredUid = AttendanceMark::where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->where('entity_type', 'staff')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderByDesc('date')
                    ->value('unique_id');

                if (!$preferredUid) {
                    $preferredUid = AttendanceMark::where('branch_id', $branchId)
                        ->where('entity_type', 'staff')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->orderByDesc('date')
                        ->value('unique_id');
                }

                $selectedUniqueId = $preferredUid ?: optional($staff->first())->attendance_unique_id;
            } else {
                $selectedUniqueId = $requestedStaffId;
            }
        } else {
            $requestedStudentId = (string) ($request->student ?? '');
            $selectedUniqueId = $students->contains('attendance_unique_id', $requestedStudentId)
                ? $requestedStudentId
                : optional($students->first())->attendance_unique_id;
        }

        $lookupIds = [(string) $selectedUniqueId];
        if ($activeTab === 'staff') {
            $selectedStaff = $staff->first(function ($member) use ($selectedUniqueId) {
                return in_array($selectedUniqueId, $member->attendance_aliases, true);
            });

            if ($selectedStaff) {
                $lookupIds = $selectedStaff->attendance_aliases;
                $selectedUniqueId = $selectedStaff->attendance_unique_id;
            }
        }

        $lookupIds = array_values(array_filter(array_unique($lookupIds)));

        $baseMonthQuery = AttendanceMark::whereIn('unique_id', $lookupIds)
            ->where('branch_id', $branchId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($activeTab === 'staff') {
            $baseMonthQuery->where('entity_type', 'staff');
        } else {
            $baseMonthQuery->where('entity_type', 'student');
        }

        $marks = (clone $baseMonthQuery)
            ->where('session_id', $sessionId)
            ->orderBy('date')
            ->get();

        if ($activeTab === 'staff' && $marks->isEmpty()) {
            $marks = (clone $baseMonthQuery)
                ->orderBy('date')
                ->get();
        }

        $marks = $marks->groupBy('date')
            ->map(function ($group) {
                return $group->sortByDesc('updated_at')->first();
            })
            ->map(function ($mark) {
                $mark->status = $this->reportStatus($mark->status ?? '');
                return $mark;
            })
            ->filter(fn ($mark) => $mark->status !== null)
            ->values();

        $marksByDate = $marks->keyBy('date');

        $totalDays = $marks->count();
        $normalizedMarks = $marks->map(fn ($mark) => $this->reportStatus($mark->status ?? ''));
        $inDays = $normalizedMarks->filter(fn ($status) => $status === 'in')->count();
        $outDays = $normalizedMarks->filter(fn ($status) => $status === 'out')->count();
        $absentDays = $normalizedMarks->filter(fn ($status) => $status === 'absent')->count();
        $halfDayDays = $normalizedMarks->filter(fn ($status) => $status === 'halfday')->count();
        $holidayDays = $normalizedMarks->filter(fn ($status) => $status === 'holiday')->count();
        $attendancePercent = $totalDays > 0 ? round((($inDays + $outDays + ($halfDayDays * 0.5)) / $totalDays) * 100, 1) : 0;

        $monthName = date('F', strtotime($startDate));

        $baseYearQuery = AttendanceMark::whereIn('unique_id', $lookupIds)
            ->where('branch_id', $branchId)
            ->whereBetween('date', [$yearStart, $yearEnd]);

        if ($activeTab === 'staff') {
            $baseYearQuery->where('entity_type', 'staff');
        } else {
            $baseYearQuery->where('entity_type', 'student');
        }

        $yearlyMarks = (clone $baseYearQuery)
            ->where('session_id', $sessionId)
            ->orderBy('date')
            ->get();

        if ($activeTab === 'staff' && $yearlyMarks->isEmpty()) {
            $yearlyMarks = (clone $baseYearQuery)
                ->orderBy('date')
                ->get();
        }

        $yearlyMarks = $yearlyMarks->groupBy('date')
            ->map(function ($group) {
                return $group->sortByDesc('updated_at')->first();
            })
            ->values();

        if ($activeTab === 'staff' && !empty($selectedUniqueId)) {
            $staffExists = $staff->contains(function ($member) use ($selectedUniqueId) {
                return $member->attendance_unique_id === $selectedUniqueId;
            });

            if (!$staffExists) {
                $legacyUser = User::select('first_name', 'last_name')
                    ->where('attendance_unique_id', $selectedUniqueId)
                    ->first();

                $legacy = new \stdClass();
                $legacy->id = 0;
                $legacy->attendance_unique_id = $selectedUniqueId;
                $legacy->attendance_aliases = [$selectedUniqueId];
                $legacy->role_id = null;
                $legacy->first_name = $legacyUser ? trim((string) $legacyUser->first_name) : 'Legacy Staff';
                $legacy->last_name = $legacyUser ? trim((string) $legacyUser->last_name) : ('(' . $selectedUniqueId . ')');

                $staff->prepend($legacy);
            }
        }

        $selectedName = '-';
        if ($activeTab === 'staff') {
            $selectedMember = $staff->first(function ($member) use ($selectedUniqueId) {
                $aliases = $member->attendance_aliases ?? [$member->attendance_unique_id];
                return in_array($selectedUniqueId, $aliases, true) || (string) $member->attendance_unique_id === (string) $selectedUniqueId;
            });
            if ($selectedMember) {
                $selectedName = trim((string) ($selectedMember->first_name ?? '') . ' ' . (string) ($selectedMember->last_name ?? ''));
            }
        } else {
            $selectedStudent = $students->firstWhere('attendance_unique_id', $selectedUniqueId);
            if ($selectedStudent) {
                $selectedName = trim((string) ($selectedStudent->first_name ?? '') . ' ' . (string) ($selectedStudent->last_name ?? ''));
            }
        }
        if ($selectedName === '') {
            $selectedName = '-';
        }

        if ((int) $request->input('export', 0) === 1) {
            return $this->exportMonthlyCsv($activeTab, (string) $selectedUniqueId, $selectedName, $month, $year, $marks);
        }

        $monthBuckets = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = $year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthBuckets[$key] = [
                'label' => date('M', mktime(0, 0, 0, $m, 1)),
                'year' => $year,
                'counts' => [
                    'in' => 0,
                    'out' => 0,
                    'halfday' => 0,
                    'absent' => 0,
                    'holiday' => 0,
                    'total' => 0,
                ],
                'grid' => [],
            ];
        }

        $statusByDate = [];
        foreach ($yearlyMarks as $mark) {
            $key = date('Y-m', strtotime($mark->date));
            if (!isset($monthBuckets[$key])) {
                continue;
            }

            $status = $this->reportStatus($mark->status ?? '');
            if ($status !== null) {
                $monthBuckets[$key]['counts'][$status]++;
                $monthBuckets[$key]['counts']['total']++;
                $statusByDate[$mark->date] = $status;
            }
        }

        foreach ($calendarYearMap as $calendarDate => $calendarStatus) {
            if (!isset($statusByDate[$calendarDate]) || $statusByDate[$calendarDate] === '') {
                $statusByDate[$calendarDate] = $calendarStatus;
            }
        }

        foreach ($monthBuckets as $key => &$bucket) {
            $firstDate = $key . '-01';
            $daysInMonth = (int) date('t', strtotime($firstDate));
            $firstDay = (int) date('w', strtotime($firstDate));

            $cells = [];
            for ($i = 0; $i < 42; $i++) {
                $day = $i - $firstDay + 1;
                if ($day < 1 || $day > $daysInMonth) {
                    $cells[] = null;
                } else {
                    $dateStr = $key . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $cells[] = $statusByDate[$dateStr] ?? '';
                }
            }
            $bucket['grid'] = $cells;
        }
        unset($bucket);

        $yearlyOverview = array_values($monthBuckets);

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

        $setting = AttendanceSetting::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->first();

        return Helper::view('attendance.view', compact(
            'students',
            'staff',
            'activeTab',
            'selectedUniqueId',
            'selectedName',
            'month',
            'year',
            'monthName',
            'marksByDate',
            'totalDays',
            'inDays',
            'outDays',
            'absentDays',
            'halfDayDays',
            'holidayDays',
            'attendancePercent',
            'calendar',
            'setting',
            'yearlyOverview',
            'calendarMonthMap',
            'canAccessStaffAttendance',
            'classes',
            'classFilter'
        ));
    }



public function monthlyReport(Request $request)
{
    $branchId = Session::get('branch_id');
    $sessionId = Session::get('session_id');
    $loginUserId = Session::get('id');
    $loginRoleId = Session::get('role_id');

    // 1. Month and Year
    $month = (int) $request->input('month', date('n'));
    $year  = (int) $request->input('year', date('Y'));

    // Total days
    $daysInMonth = cal_days_in_month(
        CAL_GREGORIAN,
        $month,
        $year
    );

    /*
    |--------------------------------------------------------------------------
    | 2. Staff Roles
    |--------------------------------------------------------------------------
    | Only Role ID 1 can see role filter
    |--------------------------------------------------------------------------
    */

    $staffRoles = collect();
    $selectedRoleId = null;

    if ($loginRoleId == 1) {

        $staffRoleIds = User::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->where('role_id', '!=', 3)
            ->distinct()
            ->pluck('role_id');

        $staffRoles = Role::whereIn('id', $staffRoleIds)
            ->orderBy('name')
            ->get();

        $selectedRoleId = $request->input('role_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Fetch Users
    |--------------------------------------------------------------------------
    |
    | ROLE 1:
    |   All staff
    |   Optional role filter
    |
    | OTHER ROLES:
    |   Only logged-in user
    |
    |--------------------------------------------------------------------------
    */

    $usersQuery = User::select('id', 'first_name', 'last_name', 'father_name', 'role_id', 'attendance_unique_id')
        ->where('branch_id', $branchId)
        ->where('session_id', $sessionId)
        ->where('status', 1);

    if ($loginRoleId == 1) {

        // Admin / Role 1 => All staff
        $usersQuery->where('role_id', '!=', 3);

        // Role filter only for Role 1
        if (!empty($selectedRoleId)) {
            $usersQuery->where('role_id', $selectedRoleId);
        }

    } else {

        // Other staff => Only own attendance
        $usersQuery->where('id', $loginUserId);
    }

    $users = $usersQuery
        ->orderBy('first_name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 4. Unique IDs
    |--------------------------------------------------------------------------
    */

    $userUniqueIds = $users
        ->pluck('attendance_unique_id')
        ->filter()
        ->values();

    $uniqueIdToUserId = $users
        ->pluck('id', 'attendance_unique_id')
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | 5. Selected Month Date Range
    |--------------------------------------------------------------------------
    */

    $formattedMonth = sprintf('%02d', $month);

    $startDate = "{$year}-{$formattedMonth}-01";

    $endDate = "{$year}-{$formattedMonth}-" .
        sprintf('%02d', $daysInMonth);

    /*
    |--------------------------------------------------------------------------
    | 6. Attendance Marks
    |--------------------------------------------------------------------------
    */

    $attendanceMarks = collect();

    if ($userUniqueIds->isNotEmpty()) {

        $attendanceMarks = AttendanceMark::select('id', 'branch_id', 'session_id', 'unique_id', 'date', 'status', 'in_time', 'out_time')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('unique_id', $userUniqueIds)
            ->orderBy('date')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Build Attendance Data
    |--------------------------------------------------------------------------
    */

    $attendanceData = [];
    $summaryData = [];

    foreach ($users as $user) {

        $user->name = trim(
            $user->first_name . ' ' . ($user->last_name ?? '')
        );

        $summaryData[$user->id] = [
            'present' => 0,
            'absent'  => 0,
            'holiday' => 0,
            'halfday' => 0,
            'in'      => 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Process Attendance
    |--------------------------------------------------------------------------
    */

    foreach ($attendanceMarks as $mark) {

        $userId = $uniqueIdToUserId[$mark->unique_id] ?? null;

        if (!$userId) {
            continue;
        }

        $day = (int) date('j', strtotime($mark->date));

        $attendanceData[$userId][$day] = $mark;

        /*
        |--------------------------------------------------------------------------
        | Present
        |--------------------------------------------------------------------------
        */

        if (
            $mark->status == 'present' ||
            $mark->status == 'in' ||
            $mark->status == 'out'
        ) {

            $summaryData[$userId]['present']++;

        }

        /*
        |--------------------------------------------------------------------------
        | Absent
        |--------------------------------------------------------------------------
        */

        elseif ($mark->status == 'absent') {

            $summaryData[$userId]['absent']++;

        }

        /*
        |--------------------------------------------------------------------------
        | Half Day
        |--------------------------------------------------------------------------
        */

        elseif ($mark->status == 'halfday') {

            $summaryData[$userId]['halfday']++;

        }

        /*
        |--------------------------------------------------------------------------
        | Holiday
        |--------------------------------------------------------------------------
        */

        elseif ($mark->status == 'holiday') {

            $summaryData[$userId]['holiday']++;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 8.5 Holidays & Calendar Map
    |--------------------------------------------------------------------------
    */

    $holidaysMap = $this->calendarStatusMap((int) $branchId, (int) $sessionId, $startDate, $endDate);

    $monthName = date('F', mktime(0, 0, 0, $month, 1));

    // Calculate Aggregate KPI Stats
    $totalPresentCount = 0;
    $totalAbsentCount = 0;
    $totalHalfDayCount = 0;
    $totalHolidayCount = 0;

    foreach ($summaryData as $uid => $sums) {
        $totalPresentCount += ($sums['present'] ?? 0);
        $totalAbsentCount += ($sums['absent'] ?? 0);
        $totalHalfDayCount += ($sums['halfday'] ?? 0);
        $totalHolidayCount += ($sums['holiday'] ?? 0);
    }

    $totalWorkingMarked = $totalPresentCount + $totalAbsentCount + $totalHalfDayCount;
    $avgAttendanceRate = $totalWorkingMarked > 0
        ? round((($totalPresentCount + ($totalHalfDayCount * 0.5)) / $totalWorkingMarked) * 100, 1)
        : 0;

    $overallStats = [
        'totalStaff' => $users->count(),
        'totalPresent' => $totalPresentCount,
        'totalAbsent' => $totalAbsentCount,
        'totalHalfDay' => $totalHalfDayCount,
        'totalHoliday' => $totalHolidayCount,
        'avgAttendanceRate' => $avgAttendanceRate,
    ];

    /*
    |--------------------------------------------------------------------------
    | 8.6 CSV Export Support
    |--------------------------------------------------------------------------
    */
    if ((int) $request->input('export', 0) === 1) {
        $fileName = 'Staff_Monthly_Attendance_Report_' . $monthName . '_' . $year . '.csv';
        $roleNames = $staffRoles->pluck('name', 'id');
        return response()->streamDownload(function () use ($users, $daysInMonth, $attendanceData, $summaryData, $monthName, $year, $roleNames) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Monthly Staff Attendance Report', $monthName . ' ' . $year]);
            fputcsv($out, []);

            // Header Row
            $header = ['#', 'Unique ID', 'Staff Name', 'Role'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $header[] = 'Day ' . $d;
            }
            $header[] = 'Present (P)';
            $header[] = 'Absent (A)';
            $header[] = 'Half Day (HD)';
            $header[] = 'Holiday (H)';
            fputcsv($out, $header);

            // Data Rows
            foreach ($users as $idx => $user) {
                $row = [
                    $idx + 1,
                    $user->attendance_unique_id ?? ('USR-' . $user->id),
                    $user->name,
                    $roleNames[$user->role_id] ?? 'Staff',
                ];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $m = $attendanceData[$user->id][$d] ?? null;
                    $st = $m ? strtoupper(substr((string) $m->status, 0, 1)) : '-';
                    if ($m && $m->status === 'halfday') $st = 'HD';
                    $row[] = $st;
                }
                $row[] = $summaryData[$user->id]['present'] ?? 0;
                $row[] = $summaryData[$user->id]['absent'] ?? 0;
                $row[] = $summaryData[$user->id]['halfday'] ?? 0;
                $row[] = $summaryData[$user->id]['holiday'] ?? 0;
                fputcsv($out, $row);
            }
            fclose($out);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /*
    |--------------------------------------------------------------------------
    | 9. Return View
    |--------------------------------------------------------------------------
    */

    return view('attendance.monthlyReport', compact(
        'users',
        'staffRoles',
        'selectedRoleId',
        'daysInMonth',
        'year',
        'month',
        'monthName',
        'attendanceData',
        'summaryData',
        'holidaysMap',
        'overallStats'
    ));
}


public function AttendanceViewstaff(Request $request)
{
    $branchId = Session::get('branch_id');
    $sessionId = Session::get('session_id');

    // Fetch logged-in role & ID with fallbacks
    $loggedInRoleId = Session::get('role_id') ?? (Auth::check() ? Auth::user()->role_id : null);
    $loggedInUserId = Session::get('user_id') ?? Session::get('id') ?? (Auth::check() ? Auth::id() : null);

    // 1. Get Month and Year from dropdowns
    $month = $request->input('month', date('n'));
    $year = $request->input('year', date('Y'));
    
    // Calculate total days in the selected month
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);

    // 2. Fetch Staff Roles for Filter Dropdown (Only needed for Admin)
    $staffRoles = collect();
    $selectedRoleId = $request->input('role_id');

    if ($loggedInRoleId == 1) {
        $staffRoleIds = User::where('branch_id', $branchId)
            ->where('status', 1)
            ->where('role_id', '!=', 3)
            ->distinct()
            ->pluck('role_id');
        
        $staffRoles = Role::whereIn('id', $staffRoleIds)->orderBy('name')->get();
    }

    // 3. Fetch Users/Staff based on Login Role Filter
    $users = User::select('id', 'first_name', 'last_name', 'father_name', 'role_id', 'attendance_unique_id')
        ->where('branch_id', $branchId)
        ->where('status', 1)
        ->when($loggedInRoleId != 1, function ($query) use ($loggedInUserId) {
            // Non-Admin: Fetch ONLY logged-in staff member
            $query->where('id', $loggedInUserId);
        })
        ->when($loggedInRoleId == 1 && $selectedRoleId, function ($q) use ($selectedRoleId) {
            // Admin: Apply selected role filter
            $q->where('role_id', $selectedRoleId);
        })
        ->orderBy('first_name')
        ->get();

    // Map unique_id to user id
    $userUniqueIds = $users->pluck('attendance_unique_id')->filter()->values();
    $uniqueIdToUserId = $users->pluck('id', 'attendance_unique_id')->toArray();

    // 4. Fetch Attendance Marks for the selected month range
    $formattedMonth = sprintf('%02d', $month);
    $startDate = "{$year}-{$formattedMonth}-01";
    $endDate = "{$year}-{$formattedMonth}-{$daysInMonth}";

    $attendanceMarks = collect();
    if ($userUniqueIds->isNotEmpty()) {
        $attendanceMarks = AttendanceMark::select('id', 'branch_id', 'session_id', 'unique_id', 'date', 'status', 'in_time', 'out_time')
            ->where('branch_id', $branchId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('unique_id', $userUniqueIds)
            ->get();
    }

    // 5. Build Attendance Data and Summary Data Arrays
    $attendanceData = [];
    $summaryData = [];

    foreach ($users as $user) {
        $user->name = trim($user->first_name . ' ' . ($user->last_name ?? ''));
        $summaryData[$user->id] = [
            'present' => 0, 
            'absent' => 0, 
            'holiday' => 0,
            'halfday' => 0,
            'in' => 0
        ];
    }

    foreach ($attendanceMarks as $mark) {
        $userId = $uniqueIdToUserId[$mark->unique_id] ?? null;
        if (!$userId) continue;

        $day = (int)date('j', strtotime($mark->date));
        $attendanceData[$userId][$day] = $mark;

        if ($mark->status == 'present' || $mark->status == 'in') {
            $summaryData[$userId]['present']++;
        } elseif ($mark->status == 'absent') {
            $summaryData[$userId]['absent']++;
        } elseif ($mark->status == 'halfday') {
            $summaryData[$userId]['halfday']++;
        } elseif ($mark->status == 'holiday') {
            $summaryData[$userId]['holiday']++;
        }
    }

    // 6. Pass variables to view
    return view('attendance.attendanceViewstaff', compact(
        'users', 
        'staffRoles', 
        'selectedRoleId', 
        'daysInMonth', 
        'year', 
        'month', 
        'attendanceData', 
        'summaryData',
        'loggedInRoleId'
    ));
}



}
