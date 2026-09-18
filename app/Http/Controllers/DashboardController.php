<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\SidebarSub;
use App\Models\User;
use App\Models\Dashboard;
use App\Models\FeesMaster;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\PermissionManagement;
use App\Models\Enquiry;
use App\Models\hostel\HostelAssign;
use App\Models\Admission;
use App\Models\FeesDiscount;
use App\Models\library\LibraryAssign;
use App\Models\AttendanceMark;
use App\Models\TeacherAttendance;
use App\Models\ClassType;
use App\Models\ManagedNotice;
use App\Helpers\helper;
use DB;
use Session;
use Hash;
use Str;
use Redirect;
use Response;
use Auth;
use App\Models\FeesDetail;
use App\Models\Master\Weekendcalendar;
use App\Models\SupportComplaint;
use App\Services\StudentNotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Detection\MobileDetect;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller

{
    
    public function dashboard(){

        
        $current_date = date('Y-m-d');
        $result = array();
        $barnch =Session::all();
        //dd($barnch);
        // $class_type = Admission::where('id',Session::get('id'))->first();
      
        if($barnch['role_id'] == 1){
            return Helper::view('dashboard.admin_dashboard', $this->adminDashboardData());
        }
        elseif($barnch['role_id'] == 2){
            return Helper::view('dashboard.teacher_dashboard', $this->teacherDashboardData());
        } 
        elseif($barnch['role_id'] == 3){
                $notificationService = app(StudentNotificationService::class);
                return Helper::view('student_login.dashboard', [
                    'notificationCounts' => $notificationService->counts((int) Session::get('id')),
                ]);
            
            }
        elseif($barnch['role_id'] == 4){
            return Helper::view('dashboard.libraryAdmin_dashboard',['result'=>$result]);
        }     
        elseif($barnch['role_id'] == 5){
            return Helper::view('dashboard.hostelAdmin_dashboard',['result'=>$result]);
        } 
        elseif($barnch['role_id'] == 6){
            return Helper::view('dashboard.admin_dashboard', array_merge(['result' => $result], $this->adminDashboardData()));
        } 
        elseif($barnch['role_id'] == 7){
            return Helper::view('dashboard.transportAdmin_dashboard',['result'=>$result]);
        } 
        /*elseif($barnch['role_id'] == 8){
            return Helper::view('dashboard.libraryAdmin_dashboard',['result'=>$result]);
        }*/  
        elseif($barnch['role_id'] == 9){
            return Helper::view('dashboard.accountant_dashboard',['result'=>$result]);
        } 
        elseif($barnch['role_id'] == 10){
            return Helper::view('dashboard.otherSchoolStaff_dashboard',['result'=>$result]);
        }         
        else{
            return Helper::view('dashboard.else_dashboard',['result'=>$result]);
        }
     
      
    }

    public function todayAttendanceAbsentNotMarkedPdf()
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $today = now()->toDateString();

        $isHoliday = Weekendcalendar::leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereDate('weekendcalendar.date', $today)
            ->whereRaw("LOWER(COALESCE(attendance_status.name, '')) IN (?, ?)", ['holiday', 'event'])
            ->exists();

        $students = Admission::query()
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->where('admissions.session_id', $sessionId)
            ->where('admissions.branch_id', $branchId)
            ->where('admissions.status', 1)
            ->where('admissions.school', 1)
            ->orderBy('class_types.orderBy')
            ->orderBy('class_types.name')
            ->orderBy('admissions.first_name')
            ->get([
                'admissions.id',
                'admissions.admissionNo',
                'admissions.attendance_unique_id',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.father_name',
                'admissions.mobile',
                'admissions.class_type_id',
                'class_types.name as class_name',
                'class_types.orderBy as class_order',
            ]);

        $todayMarks = AttendanceMark::query()
            ->whereDate('date', $today)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('entity_type', 'student')
            ->select('unique_id', 'status', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->unique('unique_id')
            ->values()
            ->mapWithKeys(function ($mark) {
                return [trim((string) $mark->unique_id) => $mark];
            });

        $rows = collect();
        if (!$isHoliday) {
            foreach ($students as $student) {
                $attendanceUniqueId = trim((string) ($student->attendance_unique_id ?? ''));
                if ($attendanceUniqueId === '') {
                    $attendanceUniqueId = trim((string) ($student->admissionNo ?? ''));
                }
                if ($attendanceUniqueId === '') {
                    $attendanceUniqueId = 'STU-' . $student->id;
                }

                $mark = $todayMarks->get($attendanceUniqueId);
                $status = null;
                if ($mark) {
                    $normalized = strtolower(trim((string) $mark->status));
                    if (in_array($normalized, ['absent', 'leave'], true)) {
                        $status = 'Absent';
                    } else {
                        continue;
                    }
                } else {
                    $status = 'Not Marked';
                }

                $rows->push([
                    'class_name' => (string) ($student->class_name ?? ''),
                    'class_order' => (int) ($student->class_order ?? 99999),
                    'admission_no' => (string) ($student->admissionNo ?? ''),
                    'attendance_unique_id' => $attendanceUniqueId,
                    'name' => trim((string) ($student->first_name ?? '') . ' ' . (string) ($student->last_name ?? '')),
                    'father_name' => (string) ($student->father_name ?? ''),
                    'mobile' => (string) ($student->mobile ?? ''),
                    'status' => $status,
                ]);
            }
        }

        $rows = $rows->sortBy(function ($row) {
            return sprintf(
                '%05d|%s|%d|%s',
                (int) ($row['class_order'] ?? 99999),
                strtolower((string) ($row['class_name'] ?? '')),
                ($row['status'] === 'Absent' ? 0 : 1),
                strtolower((string) ($row['name'] ?? ''))
            );
        })->values();

        $data = [
            'getSetting' => Helper::getSetting(),
            'reportDate' => Carbon::parse($today),
            'isHoliday' => $isHoliday,
            'rows' => $rows,
            'absentCount' => $rows->where('status', 'Absent')->count(),
            'notMarkedCount' => $rows->where('status', 'Not Marked')->count(),
        ];

        $pdf = PDF::loadView('print_file.attendance.today_absent_not_marked_pdf', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->stream('today-attendance-absent-not-marked.pdf');
    }
 private function getAbsentStudentsData(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $dateInput = $request->input('date') ?: $request->input('report_date');
        $today = !empty($dateInput) ? Carbon::parse($dateInput)->toDateString() : now()->toDateString();
        $selectedClassIds = $request->input('class_type_id', []);

        // String to Array conversion agar single class_type_id aayi ho
        if (!is_array($selectedClassIds) && !empty($selectedClassIds)) {
            $selectedClassIds = explode(',', $selectedClassIds);
        }

        $isHoliday = Weekendcalendar::leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
            ->where('weekendcalendar.branch_id', $branchId)
            ->where('weekendcalendar.session_id', $sessionId)
            ->whereDate('weekendcalendar.date', $today)
            ->whereRaw("LOWER(COALESCE(attendance_status.name, '')) IN (?, ?)", ['holiday', 'event'])
            ->exists();

        $studentsQuery = Admission::query()
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->where('admissions.session_id', $sessionId)
            ->where('admissions.branch_id', $branchId)
            ->where('admissions.status', 1)
            ->where('admissions.school', 1);

        // Class Filter: Agar user ne class select ki ho tabhi query me apply karein
        if (!empty($selectedClassIds)) {
            $studentsQuery->whereIn('admissions.class_type_id', $selectedClassIds);
        }

        $students = $studentsQuery
            ->orderBy('class_types.orderBy')
            ->orderBy('class_types.name')
            ->orderBy('admissions.first_name')
            ->get([
                'admissions.id',
                'admissions.admissionNo',
                'admissions.attendance_unique_id',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.father_name',
                'admissions.mobile',
                'admissions.class_type_id',
                'class_types.name as class_name',
                'class_types.orderBy as class_order',
            ]);

        $todayMarks = AttendanceMark::query()
            ->whereDate('date', $today)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('entity_type', 'student')
            ->select('unique_id', 'status', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->unique('unique_id')
            ->values()
            ->mapWithKeys(function ($mark) {
                return [trim((string) $mark->unique_id) => $mark];
            });

        $rows = collect();
        if (!$isHoliday) {
            foreach ($students as $student) {
                $attendanceUniqueId = trim((string) ($student->attendance_unique_id ?? ''));
                if ($attendanceUniqueId === '') {
                    $attendanceUniqueId = trim((string) ($student->admissionNo ?? ''));
                }
                if ($attendanceUniqueId === '') {
                    $attendanceUniqueId = 'STU-' . $student->id;
                }

                $mark = $todayMarks->get($attendanceUniqueId);
                
                // Sirf Absent/Leave wale bachhon ko hi filter karein (Not Marked skip kar diya hai)
                if ($mark) {
                    $normalized = strtolower(trim((string) $mark->status));
                    if (in_array($normalized, ['absent', 'leave'], true)) {
                        $status = 'Absent';
                    } else {
                        continue;
                    }
                } else {
                    continue; // Skip "Not Marked"
                }

                $rows->push([
                    'class_name' => (string) ($student->class_name ?? ''),
                    'class_order' => (int) ($student->class_order ?? 99999),
                    'admission_no' => (string) ($student->admissionNo ?? ''),
                    'attendance_unique_id' => $attendanceUniqueId,
                    'name' => trim((string) ($student->first_name ?? '') . ' ' . (string) ($student->last_name ?? '')),
                    'father_name' => (string) ($student->father_name ?? ''),
                    'mobile' => (string) ($student->mobile ?? ''),
                    'status' => $status,
                ]);
            }
        }

        $rows = $rows->sortBy(function ($row) {
            return sprintf(
                '%05d|%s|%d|%s',
                (int) ($row['class_order'] ?? 99999),
                strtolower((string) ($row['class_name'] ?? '')),
                0,
                strtolower((string) ($row['name'] ?? ''))
            );
        })->values();

        return [
            'today' => $today,
            'isHoliday' => $isHoliday,
            'rows' => $rows,
            'selectedClassIds' => $selectedClassIds,
        ];
    }

    // 1. Filter Page View Function
    public function todayAttendanceAbsentClassWise(Request $request)
    {
         $sessionId = Session::get('session_id');
        $data = $this->getAbsentStudentsData($request);
        
        // Form ke dropdown ke liye sabhi classes get karein
        $classes = ClassType::where('session_id', $sessionId)->orderBy('orderBy')->get();

        return Helper::view('print_file.attendance.today_absent_class_wise', [
            'getSetting' => Helper::getSetting(),
            'reportDate' => Carbon::parse($data['today']),
            'isHoliday' => $data['isHoliday'],
            'rows' => $data['rows'],
            'classes' => $classes,
            'selectedClassIds' => (array) $data['selectedClassIds'],
            'absentCount' => $data['rows']->count(),
            'selectedDate' => $data['today'],
        ]);
    }

    // 2. PDF Download Function
    public function todayAttendanceAbsentClassWisePdf(Request $request)
    {
        $data = $this->getAbsentStudentsData($request);

        $pdfData = [
            'getSetting' => Helper::getSetting(),
            'reportDate' => Carbon::parse($data['today']),
            'isHoliday' => $data['isHoliday'],
            'rows' => $data['rows'],
            'absentCount' => $data['rows']->count(),
        ];

        $pdf = PDF::loadView('print_file.attendance.today_absent_class_wise_pdf', $pdfData)
            ->setPaper('A4', 'landscape');

        return $pdf->stream('today-attendance-absent-class-wise.pdf');
    }

    /**
     * Build the administrator dashboard once, outside the view.  Keeping the
     * aggregation here prevents Blade from issuing the same database query
     * multiple times while rendering a single page.
     */
    private function adminDashboardData(): array
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $roleId = Session::get('role_id');
        $studentCountCap = (int) Session::get('student_count');
        $today = now()->toDateString();
        $cacheKey = 'dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today;

        $cachedData = Cache::remember($cacheKey, 300, function () use ($branchId, $sessionId, $today) {
            $students = Admission::where('session_id', $sessionId)
                ->where('status', 1)
                ->where('school', 1)
                ->where('branch_id', $branchId);

            $studentCounts = (clone $students)->selectRaw('COUNT(*) as total, SUM(gender_id = 1) as male, SUM(gender_id = 2) as female')->first();
            $activeStudents = (int) ($studentCounts->total ?? 0);

            $attendanceMarks = AttendanceMark::query()
                ->whereDate('date', $today)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->where('entity_type', 'student');

            $todayStudentMarks = (clone $attendanceMarks)
                ->select('unique_id', 'status', 'updated_at')
                ->orderByDesc('updated_at')
                ->get()
                ->unique('unique_id')
                ->map(function ($mark) {
                    $mark->status = strtolower(trim((string) $mark->status));
                    return $mark;
                })
                ->values();

            $studentPresentStatuses = ['present', 'in', 'out', 'early_out', 'earlyout'];
            $studentPresent = $todayStudentMarks->whereIn('status', $studentPresentStatuses)->count();
            $studentAbsent = $todayStudentMarks->where('status', 'absent')->count();
            $studentMarked = $todayStudentMarks->count();
            $studentAttendanceBreakdown = [
                'Present' => $studentPresent,
                'Absent' => $studentAbsent,
                'Half Day' => $todayStudentMarks->where('status', 'halfday')->count(),
                'Holiday' => $todayStudentMarks->where('status', 'holiday')->count(),
                'Leave' => $todayStudentMarks->where('status', 'leave')->count(),
                'Event' => $todayStudentMarks->where('status', 'event')->count(),
                'Exam' => $todayStudentMarks->where('status', 'exam')->count(),
                'Late' => $todayStudentMarks->where('status', 'late')->count(),
                'Not Marked' => max(0, $activeStudents - $studentMarked),
            ];

            $classStudents = Admission::query()
                ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                ->where('admissions.session_id', $sessionId)
                ->where('admissions.branch_id', $branchId)
                ->where('admissions.status', 1)
                ->where('admissions.school', 1)
                ->orderBy('class_types.orderBy')
                ->orderBy('class_types.name')
                ->get([
                    'admissions.id',
                    'admissions.admissionNo',
                    'admissions.attendance_unique_id',
                    'admissions.class_type_id',
                    'class_types.name as class_name',
                ]);

            $classAttendanceRows = [];
            $studentClassIndex = [];
            foreach ($classStudents as $student) {
                $classId = (int) $student->class_type_id;
                $classLabel = trim((string) $student->class_name) ?: 'Unassigned';
                if (!isset($classAttendanceRows[$classId])) {
                    $classAttendanceRows[$classId] = [
                        'label' => $classLabel,
                        'total' => 0,
                        'present' => 0,
                        'absent' => 0,
                        'halfday' => 0,
                        'late' => 0,
                        'leave' => 0,
                        'holiday' => 0,
                        'not_marked' => 0,
                    ];
                }

                $attendanceId = trim((string) $student->attendance_unique_id);
                if ($attendanceId === '') {
                    $attendanceId = trim((string) $student->admissionNo);
                }
                if ($attendanceId === '') {
                    $attendanceId = 'STU-' . $student->id;
                }

                $studentClassIndex[$attendanceId] = $classId;
                $classAttendanceRows[$classId]['total']++;
            }

            foreach ($todayStudentMarks as $mark) {
                $classId = $studentClassIndex[(string) $mark->unique_id] ?? null;
                if ($classId === null || !isset($classAttendanceRows[$classId])) {
                    continue;
                }

                $status = (string) $mark->status;
                if (in_array($status, ['present', 'in', 'out', 'early_out', 'earlyout'], true)) {
                    $classAttendanceRows[$classId]['present']++;
                } elseif ($status === 'late') {
                    $classAttendanceRows[$classId]['late']++;
                } elseif (isset($classAttendanceRows[$classId][$status])) {
                    $classAttendanceRows[$classId][$status]++;
                }
            }

            foreach ($classAttendanceRows as &$classAttendanceRow) {
                $marked = $classAttendanceRow['present']
                    + $classAttendanceRow['absent']
                    + $classAttendanceRow['halfday']
                    + $classAttendanceRow['late']
                    + $classAttendanceRow['leave']
                    + $classAttendanceRow['holiday'];
                $classAttendanceRow['not_marked'] = max(0, $classAttendanceRow['total'] - $marked);
            }
            unset($classAttendanceRow);

            $classAttendanceChart = [
                'labels' => array_column($classAttendanceRows, 'label'),
                'present' => array_column($classAttendanceRows, 'present'),
                'absent' => array_column($classAttendanceRows, 'absent'),
                'halfday' => array_column($classAttendanceRows, 'halfday'),
                'late' => array_column($classAttendanceRows, 'late'),
                'leave' => array_column($classAttendanceRows, 'leave'),
                'holiday' => array_column($classAttendanceRows, 'holiday'),
                'notMarked' => array_column($classAttendanceRows, 'not_marked'),
            ];

            $staffAttendance = TeacherAttendance::query()
                ->whereDate('date', $today)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId);
            $staffPresent = (int) (clone $staffAttendance)->where('attendance_status_id', 1)->distinct('staff_id')->count('staff_id');
            $staffAbsent = (int) (clone $staffAttendance)->where('attendance_status_id', 2)->distinct('staff_id')->count('staff_id');
            $staffWfh = (int) (clone $staffAttendance)->where('attendance_status_id', 3)->distinct('staff_id')->count('staff_id');
            $staffHalfDay = (int) (clone $staffAttendance)->where('attendance_status_id', 4)->distinct('staff_id')->count('staff_id');
            $staffHoliday = (int) (clone $staffAttendance)->where('attendance_status_id', 5)->distinct('staff_id')->count('staff_id');
            $staffTotalMarked = (int) (clone $staffAttendance)->distinct('staff_id')->count('staff_id');
            $staffTotal = User::where('branch_id', $branchId)->where('status', 1)->count();
            $staffAttendanceBreakdown = [
                'Present' => $staffPresent,
                'Absent' => $staffAbsent,
                'Work From Home' => $staffWfh,
                'Half Day' => $staffHalfDay,
                'Holiday' => $staffHoliday,
                'Not Marked' => max(0, $staffTotal - $staffTotalMarked),
            ];

            $assignedFees = (float) (\App\Models\fees\FeesAssignDetail::Collection() ?? 0);
            $collectedFees = (float) (FeesDetail::totalCollection() ?? 0);
            $todayFees = (float) (FeesDetail::todayCollection() ?? 0);
            $expenses = \App\Models\Expense::query()
                ->where('branch_id', $branchId)->where('session_id', $sessionId)
                ->selectRaw('COALESCE(SUM(amount), 0) as total, COALESCE(SUM(date = ?), 0) as today, COALESCE(SUM(MONTH(date) = ?), 0) as month', [$today, now()->month])
                ->first();

            $complaintsQuery = SupportComplaint::query()
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId);
            $complaintTotal = (int) (clone $complaintsQuery)->count();
            $complaintOpen = (int) (clone $complaintsQuery)
                ->where('status', 'open')
                ->count();
            $complaintResolved = (int) (clone $complaintsQuery)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();
            $complaintActive = (int) (clone $complaintsQuery)
                ->whereIn('status', ['open', 'acknowledged', 'in_progress', 'reopened'])
                ->count();
            $noticesQuery = ManagedNotice::query()
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId);
            $noticeTotal = (int) (clone $noticesQuery)->count();
            $noticePending = (int) (clone $noticesQuery)
                ->where('status', 'pending')
                ->count();

            $feeByMonth = FeesDetail::selectRaw('MONTH(date) as period, COALESCE(SUM(total_amount), 0) as total')
                ->where('session_id', $sessionId)->where('branch_id', $branchId)->whereIn('status', [0, 1])
                ->whereYear('date', now()->year)->groupBy('period')->pluck('total', 'period');
            $expenseByMonth = \App\Models\Expense::selectRaw('MONTH(date) as period, COALESCE(SUM(amount), 0) as total')
                ->where('session_id', $sessionId)->where('branch_id', $branchId)->whereYear('date', now()->year)
                ->groupBy('period')->pluck('total', 'period');
            $months = collect(range(1, 12))->map(fn ($month) => now()->month($month)->format('M'))->all();

            return [
                'studentStats' => ['total' => $activeStudents, 'male' => (int) ($studentCounts->male ?? 0), 'female' => (int) ($studentCounts->female ?? 0)],
                'attendanceStats' => ['present' => $studentPresent, 'absent' => $studentAbsent, 'unmarked' => max(0, $activeStudents - $studentMarked)],
                'studentAttendanceBreakdown' => $studentAttendanceBreakdown,
                'staffAttendanceBreakdown' => $staffAttendanceBreakdown,
                'classAttendanceChart' => $classAttendanceChart,
                'feeStats' => ['assigned' => $assignedFees, 'collected' => $collectedFees, 'today' => $todayFees],
                'expenseStats' => ['total' => (float) ($expenses->total ?? 0), 'today' => (float) ($expenses->today ?? 0), 'month' => (float) ($expenses->month ?? 0)],
                'staffStats' => ['total' => $staffTotal, 'present' => $staffPresent, 'away' => max(0, $staffTotal - $staffPresent)],
                'complaintStats' => [
                    'total' => $complaintTotal,
                    'resolved' => $complaintResolved,
                    'pending' => $complaintOpen,
                    'active' => $complaintActive,
                ],
                'noticeStats' => [
                    'total' => $noticeTotal,
                    'pending' => $noticePending,
                ],
                'feeChart' => ['labels' => $months, 'fees' => collect(range(1, 12))->map(fn ($month) => (float) ($feeByMonth[$month] ?? 0))->all(), 'expenses' => collect(range(1, 12))->map(fn ($month) => (float) ($expenseByMonth[$month] ?? 0))->all()],
            ];
        });

        $studentStats = $cachedData['studentStats'];
        $studentStats['capacity'] = $studentCountCap;

        return array_merge($cachedData, [
            'roleName' => \DB::table('role')->whereNull('deleted_at')->find($roleId),
            'studentStats' => $studentStats,
            'birthdays' => Helper::getstudentbirthday(),
            'remarks' => Helper::getremark(),
            'notices' => Helper::noticeBoard(),
        ]);
    }

    private function teacherDashboardData(): array
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $userId = (int) Session::get('id');
        $today = now()->toDateString();

        $teacher = User::select('id', 'first_name', 'last_name', 'class_type_id')
            ->where('id', $userId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        $teacherClassIds = collect();
        if (!empty($teacher?->class_type_id)) {
            $rawClassIds = $teacher->class_type_id;
            if (is_array($rawClassIds)) {
                $teacherClassIds = collect($rawClassIds);
            } else {
                $decodedClassIds = null;
                if (is_string($rawClassIds)) {
                    $decodedClassIds = @unserialize($rawClassIds);
                }

                if (is_array($decodedClassIds)) {
                    $teacherClassIds = collect($decodedClassIds);
                } else {
                    $teacherClassIds = collect(explode(',', (string) $rawClassIds));
                }
            }
            $teacherClassIds = $teacherClassIds->map(function ($id) {
                return (int) trim((string) $id);
            })->filter()->values();
        }

        $classIds = $teacherClassIds->filter()->unique()->values();

        $classDetails = $classIds->isEmpty()
            ? collect()
            : ClassType::whereIn('id', $classIds)
                ->orderBy('orderBy', 'ASC')
                ->orderBy('name', 'ASC')
                ->get(['id', 'name', 'orderBy']);

        $students = $classIds->isEmpty()
            ? collect()
            : Admission::query()
                ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                ->where('admissions.session_id', $sessionId)
                ->where('admissions.branch_id', $branchId)
                ->where('admissions.status', 1)
                ->where('admissions.school', 1)
                ->whereIn('admissions.class_type_id', $classIds)
                ->orderBy('class_types.orderBy')
                ->orderBy('class_types.name')
                ->orderBy('admissions.first_name')
                ->get([
                    'admissions.id',
                    'admissions.admissionNo',
                    'admissions.attendance_unique_id',
                    'admissions.first_name',
                    'admissions.last_name',
                    'admissions.class_type_id',
                    'class_types.name as class_name',
                    'class_types.orderBy as class_order',
                ]);

        $studentUniqueMap = [];
        $classAttendanceRows = [];
        foreach ($classDetails as $class) {
            $classAttendanceRows[$class->id] = [
                'class_id' => (int) $class->id,
                'label' => (string) ($class->name ?? 'Class'),
                'order' => (int) ($class->orderBy ?? 99999),
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'not_marked' => 0,
            ];
        }

        foreach ($students as $student) {
            $classId = (int) $student->class_type_id;
            if (!isset($classAttendanceRows[$classId])) {
                $classAttendanceRows[$classId] = [
                    'class_id' => $classId,
                    'label' => (string) ($student->class_name ?? 'Class'),
                    'order' => (int) ($student->class_order ?? 99999),
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'not_marked' => 0,
                ];
            }

            $attendanceId = trim((string) ($student->attendance_unique_id ?? ''));
            if ($attendanceId === '') {
                $attendanceId = trim((string) ($student->admissionNo ?? ''));
            }
            if ($attendanceId === '') {
                $attendanceId = 'STU-' . $student->id;
            }

            $studentUniqueMap[$attendanceId] = $classId;
            $classAttendanceRows[$classId]['total']++;
        }

        $todayMarks = AttendanceMark::query()
            ->whereDate('date', $today)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('entity_type', 'student')
            ->whereIn('unique_id', array_keys($studentUniqueMap))
            ->select('unique_id', 'status', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->unique('unique_id')
            ->values();

        $presentStatuses = ['present', 'in', 'out', 'early_out', 'earlyout'];
        foreach ($todayMarks as $mark) {
            $classId = $studentUniqueMap[(string) $mark->unique_id] ?? null;
            if ($classId === null || !isset($classAttendanceRows[$classId])) {
                continue;
            }

            $status = strtolower(trim((string) $mark->status));
            if (in_array($status, $presentStatuses, true)) {
                $classAttendanceRows[$classId]['present']++;
            } elseif ($status !== '') {
                $classAttendanceRows[$classId]['absent']++;
            }
        }

        foreach ($classAttendanceRows as &$classAttendanceRow) {
            $classAttendanceRow['not_marked'] = max(
                0,
                (int) $classAttendanceRow['total']
                    - (int) $classAttendanceRow['present']
                    - (int) $classAttendanceRow['absent']
            );
        }
        unset($classAttendanceRow);

        uasort($classAttendanceRows, function ($left, $right) {
            $leftOrder = (int) ($left['order'] ?? 99999);
            $rightOrder = (int) ($right['order'] ?? 99999);
            if ($leftOrder === $rightOrder) {
                return strcasecmp((string) ($left['label'] ?? ''), (string) ($right['label'] ?? ''));
            }

            return $leftOrder <=> $rightOrder;
        });

        $classAttendanceRows = array_values($classAttendanceRows);
        $chartData = [
            'labels' => array_values(array_map(function ($row) {
                return $row['label'];
            }, $classAttendanceRows)),
            'present' => array_values(array_map(function ($row) {
                return (int) $row['present'];
            }, $classAttendanceRows)),
            'absent' => array_values(array_map(function ($row) {
                return (int) $row['absent'];
            }, $classAttendanceRows)),
            'notMarked' => array_values(array_map(function ($row) {
                return (int) $row['not_marked'];
            }, $classAttendanceRows)),
        ];

        $teacherNoticeQuery = ManagedNotice::query()
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('created_by', $userId)
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today);

        $teacherNotices = (clone $teacherNoticeQuery)
            ->with(['creator'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $assignedStudentCount = (int) $students->count();
        $presentCount = (int) collect($classAttendanceRows)->sum('present');
        $absentCount = (int) collect($classAttendanceRows)->sum('absent');
        $notMarkedCount = (int) collect($classAttendanceRows)->sum('not_marked');

        return [
            'teacherName' => trim((string) (($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? ''))),
            'assignedClasses' => $classDetails,
            'assignedClassCount' => (int) $classDetails->count(),
            'assignedStudentCount' => $assignedStudentCount,
            'todayPresentCount' => $presentCount,
            'todayAbsentCount' => $absentCount,
            'todayNotMarkedCount' => $notMarkedCount,
            'attendanceChartData' => $chartData,
            'classAttendanceRows' => $classAttendanceRows,
            'teacherNotices' => $teacherNotices,
            'teacherNoticeCount' => (int) $teacherNoticeQuery->count(),
        ];
    }

    public function sendAttendanceStatus(){
        
        //$dateTime = date('l jS \of F Y');
        $dateTime = date('l');
        $date = date('Y-m-d');
       /* $adminMail = User::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('role_id','1')->get()->first();*/    
        $staff = TeacherAttendance::with('Teacher')->where('session_id',5)->where('branch_id',1)->where('date',$date)->get();    
            
         

           if($dateTime !== 'Sunday'){
               
      
            $emailData = ['email' => 'skwork91@gmail.com','staff' => $staff,'dateTime' => $dateTime,'subject' => 'Today Staff Attendance.'];
            Helper::sendMail('email_print.admin.send_attendance_status', $emailData);

            $emailData = ['email' => 'veonkumawat@gmail.com','staff' => $staff,'dateTime' => $dateTime,'subject' => 'Today Staff Attendance.'];
            Helper::sendMail('email_print.admin.send_attendance_status', $emailData);
           } 
        return view('test',['staff'=>$staff,'dateTime'=>$dateTime]);
    }  

     public function discountdata(Request $request,$id){
         
        if(!empty($id)){
         
            $data = FeesDiscount::where('id',$id)->get()->first();
            
            $feesDiscount =$data['amount'];
    
           echo $feesDiscount;
            
           } 
    }
    
     public function duedate(Request $request,$id){
         
        if(!empty($id)){
         
            $data = FeesMaster::where('id',$id)->get()->first();
            
            $dueDate =$data['due_date'];
    
           echo $dueDate;
            
           } 
    }
    
    public function allStudentsSearch(Request $request){
        $value = $request->name;
        $data['Student'] = Admission::with('ClassTypes')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                ->where(function($query) use ($value){
    		        $query->where('first_name', 'like', '%' .$value. '%');
                    $query->orWhere('last_name', 'like', '%' .$value. '%');
                    $query->orWhere('mobile', 'like', '%' .$value. '%');
                    $query->orWhere('aadhaar', 'like', '%' .$value. '%');
                    $query->orWhere('email', 'like', '%' .$value. '%');
                    $query->orWhere('father_name', 'like', '%' .$value. '%');
                    $query->orWhere('mother_name', 'like', '%' .$value. '%');
                    $query->orWhere('address', 'like', '%' .$value. '%');
        		})->get();

        $data['Teacher'] = Teacher::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                ->where(function($query) use ($value){
    		        $query->where('first_name', 'like', '%' .$value. '%');
                    $query->orWhere('last_name', 'like', '%' .$value. '%');
                    $query->orWhere('mobile', 'like', '%' .$value. '%');
                    $query->orWhere('aadhaar', 'like', '%' .$value. '%');
                    $query->orWhere('email', 'like', '%' .$value. '%');
                    $query->orWhere('father_name', 'like', '%' .$value. '%');
                    $query->orWhere('address', 'like', '%' .$value. '%');
        		})->get();

        $data['User'] = User::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                ->where(function($query) use ($value){
    		        $query->where('first_name', 'like', '%' .$value. '%');
                    $query->orWhere('last_name', 'like', '%' .$value. '%');
                    $query->orWhere('mobile', 'like', '%' .$value. '%');
                    $query->orWhere('email', 'like', '%' .$value. '%');
                    $query->orWhere('father_name', 'like', '%' .$value. '%');
                    $query->orWhere('address', 'like', '%' .$value. '%');
        		})->get();
        		
        $data['SidebarSub'] = SidebarSub::where(function($query) use ($value){
    		        $query->where('sidebar_name', 'like', '%' .$value. '%');
                    $query->orWhere('name', 'like', '%' .$value. '%');
                    $query->orWhere('url', 'like', '%' .$value. '%');
        		})->get();
      return  view('dashboard.admin.all_students',['data'=>$data]);
    }   

    public function taskList(){
        return view('task_list');
    }

    
    public function studentDetail($id){
         $studentDetail = Admission::with('ClassTypes')->with('Section')->find($id);
         $feesDetail =  FeesDetail::with('PaymentMode')->with('FeesCollect')->with('FeesType')->where('admission_id', $id)->where('branch_id',Session::get('branch_id'))->get();
        return view('dashboard.admin.student_detail',['data'=>$studentDetail,'feesDetail'=>$feesDetail]);
    }

    public function stuStatus(Request $request){
        
       if($request->id >0){
        $data = Admission::where('id',$request->id)->update(['status'=>$request->status]);
      
       if(!empty($data)){
       echo json_encode(1);
            }else{
                 echo json_encode(0);
            }
        }else{
        echo json_encode(2);
        }
        
    }       


    
    public function minidashboard(){
        return view('dashboard.minidashboard');
    }

    public function getModules(Request $request){
        if($request->isMethod('post')){
            $value = $request->name;
            
            $data = SidebarSub::where(function($query) use ($value){
		        $query->where('sidebar_name', 'like', '%' .$value. '%');
                $query->orWhere('name', 'like', '%' .$value. '%');
                $query->orWhere('url', 'like', '%' .$value. '%');
    		})->get();
    		
    		$sub_id = []; 
    		
    		foreach($data as $item){
    		    $sub_id[] = $item->id;
    		}
    		
    		$getModules = [];
    		
    		$find_permission = PermissionManagement::where('reg_user_id',Session::get('id'))->first();
    		foreach(explode(',', $find_permission->sidebar_sub_id) as $id){
    		    foreach($sub_id as $subId){
    		        if($id == $subId){
    		            $getModules[] = $subId;
    		        }
    		    }
    		}
    		
    		$modules = SidebarSub::whereIn('id',$getModules)->get();
    	//dd($modules);
    		   $html ='';
    		   
            foreach($modules as $item)
            {
                
               $html .= '<div class="col-md-2 info-box mb-3 bg-warning">';
                $html .= '<a href="'.htmlspecialchars(url($item->url), ENT_QUOTES, 'UTF-8') . '" class="d-flex">';
                $html .= '<span class="info-box-icon"><i class="fa '.htmlspecialchars($item->ican, ENT_QUOTES, 'UTF-8') . '"></i></span>';
                $html .= '<div class="info-box-content">';
                $html .= '<span class="info-box-text" style="font-size: 12px; white-space: break-spaces;">' . htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8') . '</span>';
                $html .= '<span class="info-box-number" style="font-size: 12px;">5,200</span>';
                $html .= '</div>';
                $html .= '</a>';
                $html .= '</div>';

               
                 
            }
           echo $html;
    		
    	
        }
    }

    public function test(Request $request){
        
        // $data = Admission::get();
        
        // foreach($data as $item){
        //     $admission = Admission::find($item->id);
        //     $admission->password = Hash::make($item->userName);
        //     $admission->save();
        // }
        dd('ok');
    }

	
}
