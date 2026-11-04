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

         return Helper::view('students.academic_performance.student_particular_performance', [
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
    public function studentPerformance(Request $request)
    {
        $search['admissionNo'] = $request->admissionNo;
        $search['class_type_id'] = $request->class_type_id;
        $search['name'] = $request->name;

        $sessionId = Session::get('session_id') ?? 5;

        $query = Admission::select([
                'admissions.id',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.father_name',
                'admissions.admissionNo',
                'admissions.mobile',
                'admissions.father_mobile',
                'admissions.email',
                'admissions.aadhaar',
                'admissions.image',
                'admissions.class_type_id',
                'admissions.branch_id',
                'admissions.session_id',
                'class.name as class_name',
            ])
            ->leftJoin('class_types as class', 'class.id', '=', 'admissions.class_type_id')
            ->where('admissions.session_id', $sessionId)
            ->where('admissions.status', 1)
            ->where('admissions.school', 1);

        if (Session::get('role_id') > 1 && Session::has('branch_id')) {
            $query->where('admissions.branch_id', Session::get('branch_id'));
        }
        if (Session::get('role_id') == 2 && !empty($request->class_type_id)) {
            $query->where('admissions.class_type_id', $request->class_type_id);
        }
        if (Session::has('admin_branch_id') && !empty(Session::get('admin_branch_id'))) {
            $query->where('admissions.branch_id', Session::get('admin_branch_id'));
        }

        if (!empty($request->admissionNo)) {
            $query->where('admissions.admissionNo', $request->admissionNo);
        }
        if (!empty($request->class_type_id)) {
            $query->where('admissions.class_type_id', $request->class_type_id);
        }
        if (!empty($request->name)) {
            $val = trim($request->name);
            $query->where(function ($q) use ($val) {
                $q->where('admissions.first_name', 'LIKE', '%' . $val . '%')
                  ->orWhere('admissions.last_name', 'LIKE', '%' . $val . '%')
                  ->orWhere('admissions.father_name', 'LIKE', '%' . $val . '%')
                  ->orWhere('admissions.mobile', 'LIKE', '%' . $val . '%')
                  ->orWhere('admissions.father_mobile', 'LIKE', '%' . $val . '%')
                  ->orWhere('admissions.admissionNo', 'LIKE', '%' . $val . '%');
            });
        }

        $data = $query->orderBy('admissions.class_type_id', 'ASC')
                      ->orderBy('admissions.first_name', 'ASC')
                      ->get();

        $studentMetrics = [];
        $gradeArray = [
            'grade_1' => 0,
            'grade_2' => 0,
            'grade_3' => 0,
            'grade_4' => 0,
            'grade_5' => 0,
        ];
        $totalAttendance = 0;
        $attendanceCount = 0;

        if ($data->isNotEmpty()) {
            $studentIds = $data->pluck('id')->all();

            // 1. Ultra-Fast Aggregated Marks Lookup (Single Batch Query)
            $marksSummary = DB::table('fill_marks')
                ->whereIn('admission_id', $studentIds)
                ->whereNotNull('student_marks')
                ->whereNull('deleted_at')
                ->select(
                    'admission_id',
                    DB::raw('SUM(student_marks) as total_obtained'),
                    DB::raw('SUM(CASE WHEN exam_maximum_marks > 0 THEN exam_maximum_marks ELSE 100 END) as total_max')
                )
                ->groupBy('admission_id')
                ->get()
                ->keyBy('admission_id');

            // 2. Ultra-Fast Direct Attendance Lookup (Single Batch Query by admission_id)
            $attSummary = DB::table('student_attendance')
                ->whereIn('admission_id', $studentIds)
                ->whereIn('attendance_status_id', [1, 2, 3])
                ->whereNull('deleted_at')
                ->select(
                    'admission_id',
                    DB::raw('SUM(CASE WHEN attendance_status_id = 1 THEN 1 ELSE 0 END) as present_days'),
                    DB::raw('COUNT(*) as total_days')
                )
                ->groupBy('admission_id')
                ->get()
                ->keyBy('admission_id');

            foreach ($data as $student) {
                // Marks & Grade calculation
                $mRec = $marksSummary->get($student->id);
                $hasMarks = ($mRec && (float)$mRec->total_max > 0);
                $obtainedMarks = $hasMarks ? (float)$mRec->total_obtained : 0;
                $maxMarks = $hasMarks ? (float)$mRec->total_max : 0;
                $gradePct = $hasMarks ? round(($obtainedMarks / $maxMarks) * 100, 2) : null;

                if ($gradePct !== null) {
                    if ($gradePct >= 91) {
                        $gradeLabel = 'Grade 1';
                        $gradeArray['grade_1']++;
                    } elseif ($gradePct >= 81) {
                        $gradeLabel = 'Grade 2';
                        $gradeArray['grade_2']++;
                    } elseif ($gradePct >= 71) {
                        $gradeLabel = 'Grade 3';
                        $gradeArray['grade_3']++;
                    } elseif ($gradePct >= 61) {
                        $gradeLabel = 'Grade 4';
                        $gradeArray['grade_4']++;
                    } else {
                        $gradeLabel = 'Grade 5';
                        $gradeArray['grade_5']++;
                    }
                } else {
                    $gradeLabel = 'Grade 5';
                    $gradeArray['grade_5']++;
                }

                // Attendance calculation
                $aRec = $attSummary->get($student->id);
                $attDays = $aRec ? (int)$aRec->total_days : 0;
                $presentDays = $aRec ? (int)$aRec->present_days : 0;
                $attPct = $attDays > 0 ? round(($presentDays / $attDays) * 100, 2) : null;

                if ($attPct !== null) {
                    $totalAttendance += $attPct;
                    $attendanceCount++;
                }

                $studentMetrics[$student->id] = [
                    'grade_percentage' => $gradePct,
                    'grade_label'      => $gradeLabel,
                    'attendance'       => $attPct !== null ? number_format($attPct, 1) . '%' : 'N/A',
                ];
            }
        }

        $averageAttendance = $attendanceCount > 0 ? number_format($totalAttendance / $attendanceCount, 1) : null;

        return Helper::view('students.academic_performance.student_performance', [
            'students'          => $data,
            'search'            => $search,
            'studentMetrics'    => $studentMetrics,
            'gradeArray'        => $gradeArray,
            'averageAttendance' => $averageAttendance,
        ]);
    }

    
    
    
    
    
}
