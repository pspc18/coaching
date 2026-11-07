<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\FillMarksByExcel;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\FillMarks;
use App\Models\Admission;
use App\Models\exam\ExaminationSchedule;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\exam\ExaminationScheduleDetail;

use App\Models\Subject;
use App\Models\ClassType;
use App\Models\Master\TeacherSubject;
use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
class FillMarkController extends Controller
{


    /**
     * Legacy excel import route redirector.
     * All excel marks import functionality is now handled by MarksImportController at /fill-marks-by-excel.
     */
    public function fillMarksByExcel(Request $request)
    {
        $queryString = $request->getQueryString();
        return redirect()->to(url('fill-marks-by-excel') . ($queryString ? '?' . $queryString : ''), 301);
    }
    public function fillMarks(Request $request)
    {
        $requestedClassId = $request->class_name ?: $request->class_type_id;

        // Auto-detect class_type_id from exam_id if class was not provided in URL
        if (empty($requestedClassId) && !empty($request->exam_id)) {
            $examLookup = Exam::find($request->exam_id);
            if ($examLookup && !empty($examLookup->class_type_id)) {
                $requestedClassId = $examLookup->class_type_id;
            } else {
                $assignLookup = AssignExam::where('exam_id', $request->exam_id)->first();
                if ($assignLookup && !empty($assignLookup->class_type_id)) {
                    $requestedClassId = $assignLookup->class_type_id;
                }
            }
        }

        $search["class_type_id"] = $requestedClassId;
        $search["subject_name_id"] = array_values(array_filter((array) $request->input('subject_name_id', [])));
        $search["exam_id"] = $request->exam_id ?? "";
        $search["subject_id"] = $request->subject_id ?? "";
        $showAllSubjects = (int) $request->input('show_all_subjects', 0) === 1;
        $selectedSubjectIds = $search["subject_name_id"];
        if (empty($selectedSubjectIds) && !empty($requestedClassId) && !empty($search["exam_id"])) {
            $showAllSubjects = true;
        }
        $showMarksSection = $showAllSubjects || !empty($selectedSubjectIds);

        $subjects = collect();
        $data2 = collect();
        $marks = "";

        if ($request->isMethod("post") || (!empty($requestedClassId) && !empty($request->exam_id))) {
            $request->validate([]);
            $classOrderBy = ClassType::where('id',$requestedClassId)->where("branch_id", Session::get("branch_id"))->first('orderBy');
            $subjects = Subject::where("class_type_id", $requestedClassId)->where("branch_id", Session::get("branch_id"))->orderBy("sort_by", "ASC");
            $Allsubjects = Subject::where("class_type_id", $requestedClassId)->where("branch_id", Session::get("branch_id"))->orderBy("sort_by", "ASC");

            if (!empty($search["exam_id"])) {
                $examModel = Exam::where('id', $search["exam_id"])->first();
                if ($examModel && !empty($examModel->selected_subject_ids)) {
                    $subjects->whereIn('id', $examModel->selected_subject_ids);
                    $Allsubjects->whereIn('id', $examModel->selected_subject_ids);
                }
            }
                $selectedSubjectIds = array_values(array_filter((array) $request->input('subject_name_id', [])));
                if (!empty($selectedSubjectIds)) {
                $subjects->whereIn('id', $selectedSubjectIds);
                }
                      if(Session::get('role_id') == 2)
            {
                  $checkClassTeacher= null;
                  
                   if(empty($checkClassTeacher))
              {
                 $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->where("branch_id", Session::get("branch_id"))->where('class_type_id',$requestedClassId)->groupBy('subject_id')->get();
                
              if(!empty($classes))
              {
                   $att = array();
                  foreach($classes as $item)
                  {
                      $att[] = $item->subject_id;
                  }
                  
                  $subjects =$subjects->whereIn('id',$att);
                  $Allsubjects =$Allsubjects->whereIn('id',$att);
              }
                  }
            }
            if ($showMarksSection) {
                $subjects = $subjects->get();
            } else {
                $subjects = collect();
            }
            $Allsubjects = $Allsubjects->get();
            if ($Allsubjects->isEmpty()) {
                $csQuery = \App\Models\CourseSubject::where('course_id', $requestedClassId);
                if (!empty($search["exam_id"])) {
                    $examModel = Exam::where('id', $search["exam_id"])->first();
                    if ($examModel && !empty($examModel->selected_subject_ids)) {
                        $csQuery->whereIn('id', $examModel->selected_subject_ids);
                    }
                }
                $Allsubjects = $csQuery->get();
                if ($showMarksSection) {
                    $subjects = $Allsubjects;
                }
            }
            if ($showAllSubjects && empty($selectedSubjectIds)) {
                $selectedSubjectIds = $Allsubjects->pluck('id')->map(function ($id) {
                    return (int) $id;
                })->values()->all();
                $search['subject_name_id'] = $selectedSubjectIds;
                $showMarksSection = true;
            }
            $students = Admission::where("class_type_id", $requestedClassId)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))->where('status',1)
                ->orderBy("first_name", "ASC")
                ->get();

            $studentSubjectAssignments = [];
            if (($classOrderBy->orderBy ?? 0) > 10) {
                $storedSubjectIds = $students->flatMap(function ($student) {
                    return collect(explode(',', (string) $student->stream_subject))
                        ->map(function ($id) {
                            return (int) trim($id);
                        })
                        ->filter();
                })->unique()->values();

                $storedSubjectNames = Subject::withTrashed()
                    ->whereIn('id', $storedSubjectIds)
                    ->get(['id', 'name'])
                    ->keyBy('id');

                $currentSubjectsByName = $Allsubjects->groupBy(function ($subject) {
                    return strtolower(trim(preg_replace('/\s+/', ' ', (string) $subject->name)));
                });

                foreach ($students as $student) {
                    $resolvedIds = collect(explode(',', (string) $student->stream_subject))
                        ->map(function ($id) {
                            return (int) trim($id);
                        })
                        ->filter()
                        ->flatMap(function ($storedId) use ($storedSubjectNames, $currentSubjectsByName, $Allsubjects) {
                            if ($Allsubjects->contains('id', $storedId)) {
                                return [$storedId];
                            }

                            $storedSubject = $storedSubjectNames->get($storedId);
                            if (!$storedSubject) {
                                return [];
                            }

                            $normalizedName = strtolower(trim(preg_replace('/\s+/', ' ', (string) $storedSubject->name)));
                            return $currentSubjectsByName->get($normalizedName, collect())->pluck('id')->all();
                        })
                        ->map(function ($id) {
                            return (int) $id;
                        })
                        ->unique()
                        ->values()
                        ->all();

                    $studentSubjectAssignments[$student->id] = array_fill_keys($resolvedIds, true);
                }
            }

            $examlist = AssignExam::select(
                "assign_exams.*",
                "exam.id as exam_id",
                "exam.name as exam_name"
            )
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $requestedClassId)
                ->where('assign_exams.session_id',Session::get("session_id"))
                 ->where('exam.deleted_at',null)->where("assign_exams.branch_id", Session::get("branch_id"))->orderBy('exam.id','ASC')
                ->get();

            $currentExam = !empty($search['exam_id']) ? Exam::find($search['exam_id']) : null;

            $fillMinMaxMarksMap = DB::table('fill_min_max_marks')
                ->where('exam_id', $search['exam_id'] ?? '')
                ->where('class_type_id', $requestedClassId)
                ->where('branch_id', Session::get('branch_id'))
                ->where('session_id', Session::get('session_id'))
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('subject_id');

            $existingMarksMap = collect();
            if ($students->isNotEmpty()) {
                $existingMarksMap = DB::table('fill_marks')
                    ->where('exam_id', $search['exam_id'] ?? '')
                    ->whereIn('admission_id', $students->pluck('id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->whereNull('deleted_at')
                    ->get()
                    ->keyBy(function($item) {
                        return $item->admission_id . '_' . $item->subject_id;
                    });
            }

            $isPublished = DB::table('exam_result_publications')
                ->where('exam_id', $search['exam_id'] ?? '')
                ->where('class_type_id', $requestedClassId)
                ->whereNotNull('published_at')
                ->whereDate('published_at', '<=', now()->toDateString())
                ->exists();

            return Helper::view("examination.offline_exam.fill_mark.fill_marks", [
                "subjects" => $subjects,
                "Allsubjects" => $Allsubjects,
                "data2" => $students,
                "search" => $search,
                "examlist" => $examlist,
                "classOrderBy" => $classOrderBy->orderBy ?? '',
                "selectedSubjectIds" => $selectedSubjectIds,
                "currentExam" => $currentExam,
                "studentSubjectAssignments" => $studentSubjectAssignments,
                "showMarksSection" => $showMarksSection,
                "fillMinMaxMarksMap" => $fillMinMaxMarksMap,
                "existingMarksMap" => $existingMarksMap,
                "isPublished" => $isPublished,
            ]);
        }
        return Helper::view("examination.offline_exam.fill_mark.fill_marks", [
            "search" => $search,
            "showMarksSection" => $showMarksSection,
        ]);
    }

    public function fillMarksSubmit(Request $request)
    {
        
        if ($request->isMethod("post")) {
            $request->validate([
                'class_type_id' => 'required|integer',
                'exam_id' => 'required|integer',
                'subject_id' => 'required|array|min:1',
                'subject_id.*' => 'required|integer|distinct',
                'exam_maximum_marks' => 'required|array',
                'exam_maximum_marks.*' => 'required|numeric|min:0',
                'exam_minimum_marks' => 'required|array',
                'exam_minimum_marks.*' => 'required|numeric|min:0',
                'admission_id' => 'required|array|min:1',
                'admission_id.*' => 'required|integer|distinct',
                'subject_id_fill' => 'required|array|min:1',
                'subject_id_fill.*' => 'required|integer',
                'student_marks' => 'required|array',
                'fill_marks_id' => 'required|array',
                'r_marks' => 'nullable|array',
                'r_marks.*' => 'nullable|string|max:20',
                'w_marks' => 'nullable|array',
                'w_marks.*' => 'nullable|string|max:20',
                'l_marks' => 'nullable|array',
                'l_marks.*' => 'nullable|string|max:20',
            ]);

            if (!empty($request->subject_id)) {
                for ($i = 0; $i < count($request->subject_id); $i++) {
                    $add = FillMinMaxMarks::withTrashed()
                        ->where('exam_id', $request->exam_id)
                        ->where('class_type_id', $request->class_type_id)
                        ->where('subject_id', $request->subject_id[$i])
                        ->where('session_id', Session::get("session_id"))
                        ->where('branch_id', Session::get("branch_id"))
                        ->first();
                    $add = $add ?: new FillMinMaxMarks();
                    $add->class_type_id = $request->class_type_id;
                    $add->exam_id = $request->exam_id;
                    $add->user_id = Session::get("id");
                    $add->session_id = Session::get("session_id");
                    $add->branch_id = Session::get("branch_id");
                    $add->subject_id = $request->subject_id[$i];
                    $add->exam_minimum_marks = $request->exam_minimum_marks[$i];
                    $add->exam_maximum_marks = $request->exam_maximum_marks[$i];
                    $add->deleted_at = null;
                    $add->save();
                }
            }
            $count = 0;
            if (!empty($request->admission_id)) {
                for ($i = 0; $i < count($request->admission_id); $i++) {
                    for ($j = 0; $j < count($request->subject_id); $j++) {
                        $fillMarksId = $request->fill_marks_id[$count] ?? null;
                        $subjectId = $request->subject_id_fill[$count] ?? null;

                        $add1 = !empty($fillMarksId)
                            ? FillMarks::withTrashed()->find($fillMarksId)
                            : null;

                        // A repeated/stale form submission can contain blank hidden IDs.
                        // Fall back to the record's natural key instead of inserting again.
                        if (!$add1) {
                            $add1 = FillMarks::withTrashed()
                                ->where('exam_id', $request->exam_id)
                                ->where('class_type_id', $request->class_type_id)
                                ->where('admission_id', $request->admission_id[$i])
                                ->where('subject_id', $subjectId)
                                ->where('session_id', Session::get('session_id'))
                                ->where('branch_id', Session::get('branch_id'))
                                ->first();
                        }

                        $add1 = $add1 ?: new FillMarks();

                        $exam_maximum_marks = FillMinMaxMarks::where('exam_id', $request->exam_id)
                            ->where('class_type_id', $request->class_type_id)
                            ->where('subject_id', $subjectId)
                            ->where('session_id', Session::get('session_id'))
                            ->where('branch_id', Session::get('branch_id'))
                            ->whereNull('deleted_at')
                            ->first();
                      
                        $add1->exam_id = $request->exam_id;
                        $add1->class_type_id = $request->class_type_id;
                        $add1->admission_id = $request->admission_id[$i];
                        $add1->user_id = Session::get("id");
                        $add1->session_id = Session::get("session_id");
                        $add1->branch_id = Session::get("branch_id");
                        $add1->subject_id = $subjectId;
                        $add1->student_marks = $request->student_marks[$count];
                        $add1->r_marks = $request->r_marks[$count] ?? null;
                        $add1->w_marks = $request->w_marks[$count] ?? null;
                        $add1->l_marks = $request->l_marks[$count] ?? null;
                        $add1->fill_min_max_marks_id = $exam_maximum_marks->id ?? null;
                        $add1->exam_maximum_marks = $exam_maximum_marks->exam_maximum_marks ?? '';
                        $add1->deleted_at = null;
                        $add1->save();
                        $count++;
                    }
                }
            }
            return redirect::to("fill_marks")->with(
                "message",
                "Marks Updated Successfully."
            );
        }
    }

    public function download_marksheet(Request $request)
    {
        $search["name"] = "";
        $search["class_type_id"] = $request->class_name;
        $search["exam_id"] = $request->exam_id ?? "";
        if ($request->isMethod("post")) {
            $students = Admission::where("class_type_id", $request->class_name)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->orderBy("first_name", "ASC")
                ->get();
            $examlist = AssignExam::select(
                "assign_exams.*",
                "exam.id as exam_id",
                "exam.name as exam_name"
            )
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $request->class_name)
                ->get();

            return view(
                "examination.offline_exam.download_marksheet.download_marksheet",
                [
                    "data" => $students,
                    "search" => $search,
                    "examlist" => $examlist,
                ]
            );
        }
        return view(
            "examination.offline_exam.download_marksheet.download_marksheet",
            ["search" => $search]
        );
    }
      public function printReportCard(Request $request){
        if($request->isMethod('post')){
             $student = Admission::where("id", $request->admission_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->first();
         $subjects = Subject::where("class_type_id", $request->class_type_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->orderBy("name", "ASC")
                ->get();
           return view('examination.offline_exam.download_marksheet.download_pdf',['data'=>$student,'subjects'=>$subjects,'exam_id'=>$request->exam_id]);
        }
    }
}
