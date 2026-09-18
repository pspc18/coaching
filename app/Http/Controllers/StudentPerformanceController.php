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
use App\Models\TcCertificate;
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
    
     public function studentParticularPerformance(Request $request,$id){
      
      $data = Admission::find($id);

      $attendance = $data ? Helper::getAttendancePerformance($data->id,$data->class_type_id) : 'N/A';
      $examIds = $data ? array_values(array_filter(array_map('trim', Helper::getExamsForPerformance($data->class_type_id)))) : [];
      $subjectIds = $data ? array_values(array_filter(array_map('trim', Helper::getPerformaceSubjects($data->id,$data->class_type_id)))) : [];
      $otherIds = $data ? array_values(array_filter(array_map('trim', Helper::getPerformaceOtherSubjets($data->id,$data->class_type_id)))) : [];

      $subjectList = !empty($subjectIds)
          ? Subject::whereIn('id', $subjectIds)->pluck('name', 'id')->all()
          : [];
      $otherList = !empty($otherIds)
          ? Subject::whereIn('id', $otherIds)->pluck('name', 'id')->all()
          : [];

      $examList = !empty($examIds)
          ? Exam::whereNull('deleted_at')->whereIn('id', $examIds)->pluck('name', 'id')
          : collect();

      $performanceMatrix = [];
      $otherPerformanceMatrix = [];

      if ($data && !empty($examIds)) {
          $subjectMarks = FillMarks::where('session_id', Session::get('session_id'))
              ->where('admission_id', $data->id)
              ->where('class_type_id', $data->class_type_id)
              ->whereIn('exam_id', $examIds)
              ->whereIn('subject_id', $subjectIds ?: [0])
              ->get(['exam_id', 'subject_id', 'student_marks']);

          $subjectMaxMarks = FillMinMaxMarks::where('session_id', Session::get('session_id'))
              ->where('class_type_id', $data->class_type_id)
              ->whereIn('exam_id', $examIds)
              ->whereIn('subject_id', $subjectIds ?: [0])
              ->get(['exam_id', 'subject_id', 'exam_maximum_marks']);

          $subjectMarksMap = [];
          foreach ($subjectMarks as $mark) {
              $subjectMarksMap[$mark->exam_id][$mark->subject_id] = $mark->student_marks;
          }
          $subjectMaxMap = [];
          foreach ($subjectMaxMarks as $mark) {
              $subjectMaxMap[$mark->exam_id][$mark->subject_id] = $mark->exam_maximum_marks;
          }

          foreach ($examIds as $examId) {
              foreach ($subjectIds as $subjectId) {
                  $studentMarks = $subjectMarksMap[$examId][$subjectId] ?? 0;
                  $maxMarks = $subjectMaxMap[$examId][$subjectId] ?? 0;
                  $percentage = $maxMarks > 0 ? round(($studentMarks / $maxMarks) * 100, 2) : null;
                  $performanceMatrix[$examId][$subjectId] = [
                      'student_marks' => $studentMarks,
                      'max_marks' => $maxMarks,
                      'percentage' => $percentage,
                  ];
              }
          }

          if (!empty($otherIds)) {
              $otherMarks = DB::table('performance_marks')
                  ->where('session_id', Session::get('session_id'))
                  ->where('admission_id', $data->id)
                  ->whereIn('term_id', $examIds)
                  ->whereIn('subject_id', $otherIds)
                  ->get(['term_id', 'subject_id', 'student_marks']);

              $otherMarksMap = [];
              foreach ($otherMarks as $mark) {
                  $otherMarksMap[$mark->term_id][$mark->subject_id] = $mark->student_marks;
              }

              foreach ($examIds as $examId) {
                  foreach ($otherIds as $otherId) {
                      $otherPerformanceMatrix[$examId][$otherId] = [
                          'student_marks' => $otherMarksMap[$examId][$otherId] ?? 0,
                      ];
                  }
              }
          }
      }

      return view('students.academic_performance.student_particular_performance',[
          'data' => $data,
          'attendance' => $attendance,
          'examIds' => $examIds,
          'examList' => $examList,
          'subjectIds' => $subjectIds,
          'otherIds' => $otherIds,
          'subjectList' => $subjectList,
          'otherList' => $otherList,
          'performanceMatrix' => $performanceMatrix,
          'otherPerformanceMatrix' => $otherPerformanceMatrix,
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
