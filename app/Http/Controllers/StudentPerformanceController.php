<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Admission;
use App\Models\Classs;
use App\Models\ClassType;
use App\Models\Subject;
use App\Models\exam\AssignExam;
use App\Models\exam\Exam;
use App\Models\exam\FillMarks;
use App\Models\exam\FillMinMaxMarks;
use App\Models\Sessions;
use App\Models\Master\Branch;
use App\Models\Master\Weekendcalendar;
use App\Models\StudentAttendance;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\BloodGroup;
use App\Models\DatatableFields;
use App\Models\FeesMaster;
use App\Models\FeesCollect;
use App\Models\WhatsappSetting;
use App\Models\FeesStructure;
use App\Models\FeesDetail;
use App\Models\Setting;
use App\Models\State;
use App\Models\Gender;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\City;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesAssignDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Session;
use Hash;
use PDF;
use Helper;
use Str;
use Mail;
use File;
use DB;
use Redirect;
use Auth;
use App\Imports\YourImportClassName;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;



class StudentPerformanceController extends Controller
{
    
     public function studentParticularPerformance(Request $request, $id)
     {
         $data = Admission::find($id);
         if (!$data) {
             return redirect()->back()->with('error', 'Student not found.');
         }

         $sessionId = Session::get('session_id') ?? $data->session_id ?? 5;
         $classTypeId = $data->class_type_id;
         $class = ClassType::find($classTypeId);

         // 1. Ultra-Fast Indexed Attendance Calculation
         $attSummary = DB::table('student_attendance')
             ->where('admission_id', $data->id)
             ->select('attendance_status_id', DB::raw('count(*) as count'))
             ->groupBy('attendance_status_id')
             ->pluck('count', 'attendance_status_id')
             ->all();

         $presentDays = (int)($attSummary[1] ?? 0);
         $absentDays  = (int)($attSummary[2] ?? 0);
         $leaveDays   = (int)($attSummary[3] ?? 0);
         $totalAttDays = $presentDays + $absentDays + $leaveDays;
         $attendancePercent = $totalAttDays > 0 ? round(($presentDays / $totalAttDays) * 100, 1) : null;
         $attendance = $attendancePercent !== null ? $attendancePercent . '%' : 'N/A';

         // 2. Comprehensive Exam Discovery
         $assignedExamIds = DB::table('assign_exams')
             ->where('class_type_id', $classTypeId)
             ->whereNull('deleted_at')
             ->pluck('exam_id')
             ->all();

         $filledExamIds = DB::table('fill_marks')
             ->where('admission_id', $data->id)
             ->pluck('exam_id')
             ->all();

         $examIds = array_values(array_unique(array_filter(array_merge($assignedExamIds, $filledExamIds))));

         // Fetch Exam models
         $examList = !empty($examIds)
             ? Exam::whereIn('id', $examIds)
                 ->whereNull('deleted_at')
                 ->orderBy('exam_date', 'asc')
                 ->orderBy('id', 'asc')
                 ->get()
                 ->keyBy('id')
             : collect();

         // 3. Comprehensive Subject Discovery
         $streamSubjectIds = array_filter(array_map('trim', explode(',', $data->stream_subject ?? '')));
         $filledSubjectIds = DB::table('fill_marks')
             ->where('admission_id', $data->id)
             ->pluck('subject_id')
             ->all();

         $subjectIds = array_values(array_unique(array_filter(array_merge($streamSubjectIds, $filledSubjectIds))));

         if (empty($subjectIds) && $class && !empty($class->subject_id)) {
             $subjectIds = array_values(array_filter(array_map('trim', explode(',', $class->subject_id))));
         }

         $subjectList = !empty($subjectIds)
             ? Subject::whereIn('id', $subjectIds)
                 ->whereNull('deleted_at')
                 ->get()
                 ->keyBy('id')
             : collect();

         // 4. Single-Query Batch Marks Retrieval
         $fillMarks = DB::table('fill_marks')
             ->where('admission_id', $data->id)
             ->whereIn('exam_id', $examIds ?: [0])
             ->get();

         $minMaxMarks = DB::table('fill_min_max_marks')
             ->where('class_type_id', $classTypeId)
             ->whereIn('exam_id', $examIds ?: [0])
             ->get();

         $minMaxMap = [];
         foreach ($minMaxMarks as $mm) {
             $minMaxMap[$mm->exam_id][$mm->subject_id] = [
                 'max' => (float)$mm->exam_maximum_marks,
                 'min' => (float)$mm->exam_minimum_marks,
             ];
         }

         // 5. Build Performance Matrix & Analytics
         $performanceMatrix = [];
         $examSummaries = [];
         $subjectTotals = [];
         $chartLabels = [];
         $chartPercentages = [];
         $totalObtainedAll = 0;
         $totalMaxAll = 0;
         $hasCompetitiveMarks = false;

         // Map fill marks into lookup map
         $fillMarksMap = [];
         foreach ($fillMarks as $fm) {
             $fillMarksMap[$fm->exam_id][$fm->subject_id] = $fm;
             if ($fm->r_marks !== null || $fm->w_marks !== null || $fm->l_marks !== null) {
                 $hasCompetitiveMarks = true;
             }
         }

         foreach ($examList as $examId => $examObj) {
             $examObtained = 0;
             $examMax = 0;
             $examSubjectAttempts = 0;

             foreach ($subjectList as $subId => $subObj) {
                 $markRecord = $fillMarksMap[$examId][$subId] ?? null;
                 $studentMarks = $markRecord && $markRecord->student_marks !== null ? (float)$markRecord->student_marks : null;

                 $maxFromRecord = $markRecord ? (float)($markRecord->exam_maximum_marks ?? 0) : 0;
                 $maxFromConfig = (float)($minMaxMap[$examId][$subId]['max'] ?? 0);
                 $maxMarks = $maxFromRecord > 0 ? $maxFromRecord : $maxFromConfig;

                 $pct = ($studentMarks !== null && $maxMarks > 0) ? round(($studentMarks / $maxMarks) * 100, 1) : null;

                 $performanceMatrix[$examId][$subId] = [
                     'student_marks' => $studentMarks,
                     'max_marks'     => $maxMarks,
                     'percentage'    => $pct,
                     'r_marks'       => $markRecord ? $markRecord->r_marks : null,
                     'w_marks'       => $markRecord ? $markRecord->w_marks : null,
                     'l_marks'       => $markRecord ? $markRecord->l_marks : null,
                 ];

                 if ($studentMarks !== null) {
                     $examObtained += $studentMarks;
                     $examMax += $maxMarks;
                     $examSubjectAttempts++;

                     if (!isset($subjectTotals[$subId])) {
                         $subjectTotals[$subId] = [
                             'name'     => $subObj->name,
                             'obtained' => 0,
                             'max'      => 0,
                             'attempts' => 0,
                             'highest'  => $studentMarks,
                             'lowest'   => $studentMarks,
                         ];
                     }
                     $subjectTotals[$subId]['obtained'] += $studentMarks;
                     $subjectTotals[$subId]['max'] += $maxMarks;
                     $subjectTotals[$subId]['attempts']++;
                     if ($studentMarks > $subjectTotals[$subId]['highest']) {
                         $subjectTotals[$subId]['highest'] = $studentMarks;
                     }
                     if ($studentMarks < $subjectTotals[$subId]['lowest']) {
                         $subjectTotals[$subId]['lowest'] = $studentMarks;
                     }
                 }
             }

             $examPct = $examMax > 0 ? round(($examObtained / $examMax) * 100, 1) : null;
             $examSummaries[$examId] = [
                 'name'         => $examObj->name,
                 'date'         => $examObj->exam_date ? date('d-m-Y', strtotime($examObj->exam_date)) : 'N/A',
                 'obtained'     => $examObtained,
                 'max'          => $examMax,
                 'percentage'   => $examPct,
                 'sub_attempts' => $examSubjectAttempts,
             ];

             if ($examPct !== null) {
                 $chartLabels[] = Str::limit($examObj->name, 15);
                 $chartPercentages[] = $examPct;
                 $totalObtainedAll += $examObtained;
                 $totalMaxAll += $examMax;
             }
         }

         // Calculate subject averages & identify strongest/weakest subject
         $bestSubject = null;
         $weakSubject = null;
         $maxSubPct = -1;
         $minSubPct = 9999;

         foreach ($subjectTotals as $sId => &$sData) {
             $sData['percentage'] = $sData['max'] > 0 ? round(($sData['obtained'] / $sData['max']) * 100, 1) : null;
             if ($sData['percentage'] !== null) {
                 if ($sData['percentage'] > $maxSubPct) {
                     $maxSubPct = $sData['percentage'];
                     $bestSubject = $sData['name'] . ' (' . $sData['percentage'] . '%)';
                 }
                 if ($sData['percentage'] < $minSubPct) {
                     $minSubPct = $sData['percentage'];
                     $weakSubject = $sData['name'] . ' (' . $sData['percentage'] . '%)';
                 }
             }
         }
         unset($sData);

         $overallPercentage = $totalMaxAll > 0 ? round(($totalObtainedAll / $totalMaxAll) * 100, 1) : null;

         // Performance Tier / Rating
         $performanceGrade = 'N/A';
         $performanceBadgeClass = 'badge-secondary';
         if ($overallPercentage !== null) {
             if ($overallPercentage >= 85) {
                 $performanceGrade = 'Outstanding (A+)';
                 $performanceBadgeClass = 'badge-success';
             } elseif ($overallPercentage >= 75) {
                 $performanceGrade = 'Distinction (A)';
                 $performanceBadgeClass = 'badge-success';
             } elseif ($overallPercentage >= 60) {
                 $performanceGrade = 'First Class (B)';
                 $performanceBadgeClass = 'badge-primary';
             } elseif ($overallPercentage >= 50) {
                 $performanceGrade = 'Second Class (C)';
                 $performanceBadgeClass = 'badge-warning';
             } elseif ($overallPercentage >= 35) {
                 $performanceGrade = 'Pass Class (D)';
                 $performanceBadgeClass = 'badge-info';
             } else {
                 $performanceGrade = 'Needs Attention (E)';
                 $performanceBadgeClass = 'badge-danger';
             }
         }

         // Backward compatibility for existing legacy variables
         $otherIds = [];
         $otherList = [];
         $otherPerformanceMatrix = [];

         return view('students.academic_performance.student_particular_performance', [
             'data'                  => $data,
             'class'                 => $class,
             'attendance'            => $attendance,
             'presentDays'           => $presentDays,
             'absentDays'            => $absentDays,
             'totalAttDays'          => $totalAttDays,
             'examIds'               => $examIds,
             'examList'              => $examList,
             'subjectIds'            => $subjectIds,
             'otherIds'              => $otherIds,
             'subjectList'           => $subjectList,
             'otherList'             => $otherList,
             'performanceMatrix'     => $performanceMatrix,
             'otherPerformanceMatrix'=> $otherPerformanceMatrix,
             'examSummaries'         => $examSummaries,
             'subjectTotals'         => $subjectTotals,
             'totalObtainedAll'      => $totalObtainedAll,
             'totalMaxAll'           => $totalMaxAll,
             'overallPercentage'     => $overallPercentage,
             'performanceGrade'      => $performanceGrade,
             'performanceBadgeClass' => $performanceBadgeClass,
             'bestSubject'           => $bestSubject,
             'weakSubject'           => $weakSubject,
             'chartLabels'           => $chartLabels,
             'chartPercentages'      => $chartPercentages,
             'hasCompetitiveMarks'   => $hasCompetitiveMarks,
         ]);
     }
     public function studentPerformance(Request $request){
         
           $search['admissionNo'] = $request->admissionNo;
        $search['class_type_id'] = $request->class_type_id;
        $search['name'] = $request->name;
        
        
   $data = Admission::select('admissions.*','class.name as class_name')
                            ->leftJoin('class_types as class','class.id','admissions.class_type_id')
                            ->orderBy('admissions.class_type_id', 'ASC')
                            ->where('admissions.session_id', Session::get('session_id'));
      
        if(Session::get('role_id') > 1) {
            $data = $data->where('admissions.branch_id', Session::get('branch_id'));
        }
        if (Session::get('role_id') == 2) {
            $data = $data->where('admissions.class_type_id', $request->class_type_id);
        }
        if (!empty(Session::get('admin_branch_id'))) {
            $data = $data->where('admissions.branch_id', Session::get('admin_branch_id'));
        }
        
            if ($request->name != '') {
                $value = $request->name;
                $data = $data->where(function ($query) use ($value) {
                    $query->where('first_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('last_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('father_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('mother_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('mobile', 'LIKE', '%' . $value . '%');
                    $query->orWhere('email', 'LIKE', '%' . $value . '%');
                    $query->orWhere('aadhaar', 'LIKE', '%' . $value . '%');
                    $query->orWhere('address', 'LIKE', '%' . $value . '%');
                    
                    $query->orWhere('ledger_no', 'LIKE', '%' . $value . '%');
                    $query->orWhere('srn', 'LIKE', '%' . $value . '%');
                    $query->orWhere('dob', 'LIKE', '%' . $value . '%');
                    $query->orWhere('village_city', 'LIKE', '%' . $value . '%');
                    $query->orWhere('address', 'LIKE', '%' . $value . '%');
                    $query->orWhere('pincode', 'LIKE', '%' . $value . '%');
                    $query->orWhere('caste_category', 'LIKE', '%' . $value . '%');
                    $query->orWhere('house', 'LIKE', '%' . $value . '%');
                    
                    $query->orWhere('height', 'LIKE', '%' . $value . '%');
                    $query->orWhere('weight', 'LIKE', '%' . $value . '%');
                    $query->orWhere('family_annual_income', 'LIKE', '%' . $value . '%');
                    $query->orWhere('family_id', 'LIKE', '%' . $value . '%');
                    $query->orWhere('religion', 'LIKE', '%' . $value . '%');
                    $query->orWhere('category', 'LIKE', '%' . $value . '%');
                    $query->orWhere('father_mobile', 'LIKE', '%' . $value . '%');
                    $query->orWhere('father_aadhaar', 'LIKE', '%' . $value . '%');
                    
                    $query->orWhere('mother_mob', 'LIKE', '%' . $value . '%');
                    $query->orWhere('mother_aadhaar', 'LIKE', '%' . $value . '%');
                    $query->orWhere('guardian_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('guardian_mobile', 'LIKE', '%' . $value . '%');
                    $query->orWhere('bus_number', 'LIKE', '%' . $value . '%');
                    $query->orWhere('bus_route', 'LIKE', '%' . $value . '%');
                    $query->orWhere('stoppage', 'LIKE', '%' . $value . '%');
                    $query->orWhere('transpor_charges', 'LIKE', '%' . $value . '%');
                    
                    $query->orWhere('bank_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('bank_account', 'LIKE', '%' . $value . '%');
                    $query->orWhere('branch_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('ifsc', 'LIKE', '%' . $value . '%');
                    $query->orWhere('micr_code', 'LIKE', '%' . $value . '%');
                    $query->orWhere('remark_1', 'LIKE', '%' . $value . '%');
                    $query->orWhere('admission_date', 'LIKE', '%' . $value . '%');
                });
            }

            if ($request->admissionNo != '') {
                $data = $data->where("admissionNo", $request->admissionNo);
            }
            if ($request->class_type_id != '') {
                $data = $data->where("class_type_id", $request->class_type_id);
            }
            
           
    
                 $data = $data->where("admissions.status", 1)->where('admissions.school',1)->get();

        $studentMetrics = [];
        $gradeArray = [
            'grade_1' => 0,
            'grade_2' => 0,
            'grade_3' => 0,
            'grade_4' => 0,
            'grade_5' => 0,
        ];
        $totalAttendance = 0;

        if ($data->isNotEmpty()) {
            $classTypeId = (int) ($request->class_type_id ?: $data->first()->class_type_id);
            $examIds = \App\Models\exam\AssignExam::where('session_id', Session::get('session_id'))
                ->where('class_type_id', $classTypeId)
                ->pluck('exam_id')
                ->all();

            $totalMaximumMarks = 0;
            $marksByStudent = collect();
            if (!empty($examIds)) {
                $totalMaximumMarks = (float) \App\Models\exam\FillMinMaxMarks::where('session_id', Session::get('session_id'))
                    ->where('class_type_id', $classTypeId)
                    ->whereIn('exam_id', $examIds)
                    ->sum('exam_maximum_marks');

                $marksByStudent = \App\Models\exam\FillMarks::where('session_id', Session::get('session_id'))
                    ->where('class_type_id', $classTypeId)
                    ->whereIn('exam_id', $examIds)
                    ->select('admission_id', DB::raw('SUM(student_marks) as total_marks'))
                    ->groupBy('admission_id')
                    ->pluck('total_marks', 'admission_id');
            }

            $attendanceFirstDate = \App\Models\StudentAttendance::where('class_type_id', $classTypeId)
                ->where('session_id', Session::get('session_id'))
                ->orderBy('date', 'ASC')
                ->value('date');
            $attendanceLastDate = \App\Models\StudentAttendance::where('class_type_id', $classTypeId)
                ->where('session_id', Session::get('session_id'))
                ->orderBy('date', 'DESC')
                ->value('date');

            $holidayDates = \App\Models\Master\Weekendcalendar::where('session_id', Session::get('session_id'))
                ->where('attendance_status', 5)
                ->pluck('date')
                ->map(function ($date) {
                    return date('Y-m-d', strtotime($date));
                })
                ->all();

            $totalDays = 0;
            if (!empty($attendanceFirstDate) && !empty($attendanceLastDate)) {
                $startDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($attendanceFirstDate)));
                $endDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($attendanceLastDate)));

                while ($startDate->lte($endDate)) {
                    $currentDate = $startDate->format('Y-m-d');
                    if ($startDate->dayOfWeek !== Carbon::SUNDAY && !in_array($currentDate, $holidayDates)) {
                        $totalDays++;
                    }
                    $startDate->addDay();
                }
            }

            $attendanceByStudent = \App\Models\StudentAttendance::where('class_type_id', $classTypeId)
                ->where('session_id', Session::get('session_id'))
                ->select('admission_id',
                    DB::raw('SUM(CASE WHEN attendance_status_id = 1 THEN 1 ELSE 0 END) as present_count')
                )
                ->groupBy('admission_id')
                ->pluck('present_count', 'admission_id');

            foreach ($data as $student) {
                $obtainedMarks = (float) ($marksByStudent[$student->id] ?? 0);
                $grade = $totalMaximumMarks > 0 ? ($obtainedMarks / $totalMaximumMarks) * 100 : 0;

                if ($grade >= 91 && $grade <= 100) {
                    $gradeLabel = 'Grade 1';
                    $gradeArray['grade_1']++;
                } elseif ($grade >= 81 && $grade <= 90.99) {
                    $gradeLabel = 'Grade 2';
                    $gradeArray['grade_2']++;
                } elseif ($grade >= 71 && $grade <= 80.99) {
                    $gradeLabel = 'Grade 3';
                    $gradeArray['grade_3']++;
                } elseif ($grade >= 61 && $grade <= 70.99) {
                    $gradeLabel = 'Grade 4';
                    $gradeArray['grade_4']++;
                } else {
                    $gradeLabel = 'Grade 5';
                    $gradeArray['grade_5']++;
                }

                $presentCount = (int) ($attendanceByStudent[$student->id] ?? 0);
                $attendancePercentage = $totalDays > 0 ? round(($presentCount / $totalDays) * 100, 2) : null;
                $totalAttendance += $attendancePercentage ?? 0;

                $studentMetrics[$student->id] = [
                    'grade_percentage' => round($grade, 2),
                    'grade_label' => $gradeLabel,
                    'attendance' => $attendancePercentage !== null ? number_format($attendancePercentage, 2) . '%' : 'N/A',
                ];
            }
        }

        $averageAttendance = $data->count() > 0 ? number_format($totalAttendance / $data->count(), 2) : null;

         return view('students.academic_performance.student_performance',[
             'students' => $data,
             'search' => $search,
             'studentMetrics' => $studentMetrics,
             'gradeArray' => $gradeArray,
             'averageAttendance' => $averageAttendance,
         ]);
     }
    
    
    
    
    
}
