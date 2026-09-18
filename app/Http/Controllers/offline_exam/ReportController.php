<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\FillMarks;
use App\Models\Admission;
use App\Models\Sessions;
use App\Models\exam\ExaminationSchedule;
use App\Models\exam\ExaminationScheduleDetail;
use App\Models\examoffline\PerformanceMarks;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassType;
use App\Models\Master\Branch;
use App\Models\Master\TeacherSubject;
use DB;
use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    public function studentExamComparisonStudents(Request $request)
    {
        $branchId = (int) ($request->branch_id ?? Session::get('branch_id'));
        $sessionId = (int) ($request->session_id ?? Session::get('session_id'));
        $classTypeId = (int) ($request->class_type_id ?? 0);
        $selectedStudentId = (int) ($request->admission_id ?? 0);

        $accessibleBranchIds = $this->getAccessibleBranchIds();
        if (!in_array($branchId, $accessibleBranchIds, true)) {
            $branchId = (int) ($accessibleBranchIds[0] ?? Session::get('branch_id'));
        }

        $studentOptions = '<option value="">Select Student</option>';

        if ($classTypeId > 0) {
            $classIds = $this->getAccessibleClassIds($branchId, $sessionId);

            if (in_array($classTypeId, $classIds, true)) {
                $students = Admission::where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->where('class_type_id', $classTypeId)
                    ->where('status', 1)
                    ->orderBy('first_name', 'ASC')
                    ->orderBy('last_name', 'ASC')
                    ->get(['id', 'admissionNo', 'roll_no', 'first_name', 'last_name']);

                foreach ($students as $student) {
                    $studentLabel = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                    $studentLabel .= !empty($student->admissionNo) ? ' [' . $student->admissionNo . ']' : '';
                    $selected = $selectedStudentId === (int) $student->id ? ' selected' : '';
                    $studentOptions .= '<option value="' . $student->id . '"' . $selected . '>' . e($studentLabel) . '</option>';
                }
            }
        }

        return response($studentOptions);
    }

    public function studentExamComparisonReport(Request $request)
    {
        $currentBranchId = (int) (Session::get('admin_branch_id') ?: Session::get('branch_id'));
        $accessibleBranchIds = $this->getAccessibleBranchIds();

        $branches = Branch::whereNull('deleted_at')
            ->whereIn('id', $accessibleBranchIds)
            ->orderBy('branch_name', 'ASC')
            ->get(['id', 'branch_name']);

        $selectedBranchId = (int) ($request->branch_id ?? $currentBranchId);
        if (!in_array($selectedBranchId, $accessibleBranchIds, true)) {
            $selectedBranchId = $currentBranchId;
        }

        $sessions = Sessions::where('branch_id', $selectedBranchId)
            ->whereNull('deleted_at')
            ->orderBy('from_year', 'DESC')
            ->orderBy('to_year', 'DESC')
            ->get(['id', 'branch_id', 'from_year', 'to_year']);

        $sessionIds = $sessions->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();

        $defaultSessionId = in_array((int) Session::get('session_id'), $sessionIds, true)
            ? (int) Session::get('session_id')
            : (int) optional($sessions->first())->id;

        $selectedSessionId = (int) ($request->session_id ?? $defaultSessionId);
        if (!in_array($selectedSessionId, $sessionIds, true)) {
            $selectedSessionId = $defaultSessionId;
        }

        $classIds = $this->getAccessibleClassIds($selectedBranchId, $selectedSessionId);

        $classTypes = ClassType::where('branch_id', $selectedBranchId)
            ->where('session_id', $selectedSessionId)
            ->whereNull('deleted_at')
            ->when(!empty($classIds), function ($query) use ($classIds) {
                $query->whereIn('id', $classIds);
            }, function ($query) {
                if (Session::get('role_id') == 2) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->orderBy('name', 'ASC')
            ->get(['id', 'name']);

        $search = [
            'branch_id' => $selectedBranchId,
            'session_id' => $selectedSessionId,
            'class_type_id' => (int) ($request->class_type_id ?? 0),
            'admission_id' => (int) ($request->admission_id ?? 0),
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
        ];

        if (!$classTypes->pluck('id')->contains($search['class_type_id'])) {
            $search['class_type_id'] = 0;
            $search['admission_id'] = 0;
        }

        $students = collect();
        if (!empty($search['class_type_id'])) {
            $students = Admission::where('branch_id', $selectedBranchId)
                ->where('session_id', $selectedSessionId)
                ->where('class_type_id', $search['class_type_id'])
                ->where('status', 1)
                ->orderBy('first_name', 'ASC')
                ->orderBy('last_name', 'ASC')
                ->get(['id', 'admissionNo', 'roll_no', 'first_name', 'last_name']);
        }

        if (!$students->pluck('id')->contains($search['admission_id'])) {
            $search['admission_id'] = 0;
        }

        $report = null;

        if (($request->isMethod('post') || $request->filled('admission_id')) && !empty($search['class_type_id']) && !empty($search['admission_id'])) {
            $report = $this->prepareStudentExamComparisonData(
                $selectedBranchId,
                $selectedSessionId,
                (int) $search['class_type_id'],
                (int) $search['admission_id'],
                !empty($search['date_from']) ? $search['date_from'] : null,
                !empty($search['date_to']) ? $search['date_to'] : null
            );
        }

        return view('examination.offline_exam.report.student_exam_comparison_report', [
            'search' => $search,
            'branches' => $branches,
            'sessions' => $sessions,
            'classTypes' => $classTypes,
            'students' => $students,
            'report' => $report,
        ]);
    }


    public function exam_wise_report(Request $request)
    {
        $search["exam_id"] = $request->exam_id ?? "";
        $search["class_type_id"] = $request->class_type_id;
        $search["subject_id"] = array_values(array_filter((array) $request->input('subject_id', [])));
        $search["admission_id"] = $request->input('admission_id');

        $students = collect();
        $examlist = collect();
        $list_subject = collect();
        $reportData = null;
        $exam = null;
        $className = null;
        $reportRows = [];
        $summary = [];
        $singleSubjectMode = false;
        
        if (!empty($request->class_type_id)) {
            $examlist = AssignExam::select("assign_exams.*","exam.id as exam_id",
                "exam.name as exam_name","assign_exams.exam_date as assign_exam_date")
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $request->class_type_id)
                ->where('assign_exams.session_id', Session::get("session_id"))
                ->where('exam.deleted_at', null)
                ->where("assign_exams.branch_id", Session::get("branch_id"))
                ->orderBy('exam.id', 'ASC')
                ->get();
        }
                
        if ($request->isMethod("post") || (!empty($request->class_type_id) && !empty($request->exam_id))) {
            $requestClassTypeId = (int) $request->class_type_id;
            $requestExamId = (int) $request->exam_id;

            $list_subject = Subject::where("class_type_id", $requestClassTypeId)
                ->where("branch_id", Session::get("branch_id"))
                ->where('session_id', Session::get("session_id"));

            if (!empty($search["subject_id"])) {
                $list_subject = $list_subject->whereIn('id', $search["subject_id"]);
            }

            $list_subject = $list_subject->orderBy("sort_by", "ASC")->get();

            $students = Admission::where("class_type_id", $requestClassTypeId)
                    ->where("session_id", Session::get("session_id"))
                    ->where("branch_id", Session::get("branch_id"))
                    ->where('status', 1)
                    ->orderBy("first_name", "ASC")
                    ->get(['id', 'admissionNo', 'first_name', 'last_name', 'father_name', 'roll_no', 'class_type_id']);

            $exam = Exam::where('id', $requestExamId)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->whereNull('deleted_at')->first();

            $className = ClassType::where('id', $requestClassTypeId)
                        ->where('branch_id', Session::get('branch_id'))
                        ->whereNull('deleted_at')
                        ->first();

            if ($exam && $className && $list_subject->isNotEmpty()) {
                $reportData = $this->prepareExamWiseReportData(
                    $requestExamId,
                    $requestClassTypeId,
                    $students,
                    $list_subject,
                    !empty($search['admission_id']) ? (int) $search['admission_id'] : null
                );

                $reportRows = $reportData['rows'] ?? [];
                $summary = $reportData['summary'] ?? [];
                $singleSubjectMode = (bool) ($reportData['single_subject_mode'] ?? false);
            }
        }

        return Helper::view("examination.offline_exam.report.exam_wise_report", [
            "search" => $search,
            'examlist' => $examlist,
            'students' => $students,
            'list_subject' => $list_subject,
            'exam' => $exam,
            'className' => $className,
            'reportRows' => $reportRows,
            'summary' => $summary,
            'singleSubjectMode' => $singleSubjectMode,
        ]);
    }

    public function downloadExamWiseReportPdf(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|integer',
            'class_type_id' => 'required|integer',
            'subject_id' => 'required|array|min:1',
            'subject_id.*' => 'required|integer|distinct',
            'admission_id' => 'nullable|integer',
        ]);

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $classTypeId = (int) $validated['class_type_id'];
        $examId = (int) $validated['exam_id'];

        $exam = Exam::where('id', $examId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $className = ClassType::where('id', $classTypeId)
            ->where('branch_id', $branchId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $listSubject = Subject::where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereIn('id', $validated['subject_id'])
            ->orderBy('sort_by', 'ASC')
            ->get();

        abort_if($listSubject->count() !== count($validated['subject_id']), 404);

        $students = Admission::where('class_type_id', $classTypeId)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->orderBy('first_name', 'ASC')
            ->get(['id', 'admissionNo', 'first_name', 'last_name', 'father_name', 'roll_no', 'class_type_id']);

        $reportData = $this->prepareExamWiseReportData(
            $examId,
            $classTypeId,
            $students,
            $listSubject,
            !empty($validated['admission_id']) ? (int) $validated['admission_id'] : null
        );
        $fileName = 'exam-wise-report-' . Str::slug($exam->name . '-' . $className->name) . '.pdf';

        return PDF::loadView('examination.offline_exam.report.exam_wise_report_pdf', [
            'exam' => $exam,
            'className' => $className,
            'list_subject' => $listSubject,
            'reportRows' => $reportData['rows'] ?? [],
            'summary' => $reportData['summary'] ?? [],
            'singleSubjectMode' => (bool) ($reportData['single_subject_mode'] ?? false),
        ])->setPaper('a4', 'landscape')->download($fileName);
    }

    public function prepareExamWiseReportData(
        int $examId,
        int $classTypeId,
        $students,
        $listSubject,
        ?int $displayStudentId = null
    ): array
    {
        $studentIds = $students->pluck('id')->all();
        $subjectIds = $listSubject->pluck('id')->all();

        $maxMarksRows = FillMinMaxMarks::where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->whereNull('deleted_at')
            ->whereIn('subject_id', $subjectIds)
            ->get()
            ->keyBy('subject_id');

        $fillMarksRows = FillMarks::where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->whereNull('deleted_at')
            ->whereIn('admission_id', $studentIds)
            ->whereIn('subject_id', $subjectIds)
            ->get()
            ->groupBy('admission_id');

        $resultUpdateRows = DB::table('exam_result_updates')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->whereNull('deleted_at')
            ->whereIn('admission_id', $studentIds)
            ->get()
            ->keyBy('admission_id');

        $singleSubjectMode = $listSubject->count() === 1;
        $rows = [];
        $rankSeed = [];
        $overallRankSeed = [];
        $totalPercentage = 0;
        $selectedSubject = $singleSubjectMode ? $listSubject->first() : null;
        $selectedSubjectMax = $selectedSubject ? (float) optional($maxMarksRows->get($selectedSubject->id))->exam_maximum_marks : 0;

        foreach ($students as $student) {
            $studentMarks = collect($fillMarksRows->get($student->id, []))->keyBy('subject_id');
            $subjectRows = [];
            $totalObtained = 0;
            $totalMaximum = 0;
            $subjectNumericMarks = 0;

            foreach ($listSubject as $subject) {
                $markRow = $studentMarks->get($subject->id);
                $maxMarks = (float) optional($maxMarksRows->get($subject->id))->exam_maximum_marks;
                $displayMark = $markRow->student_marks ?? '-';
                $normalizedDisplayMark = is_string($displayMark) ? strtoupper(trim($displayMark)) : $displayMark;
                $numericMarks = is_numeric($displayMark) ? (float) $displayMark : 0;

                if ((int) ($subject->other_subject ?? 0) === 0) {
                    $totalMaximum += $maxMarks;
                    if (is_numeric($displayMark)) {
                        $totalObtained += $numericMarks;
                    }
                }

                if ($singleSubjectMode) {
                    $subjectNumericMarks = $numericMarks;
                }

                $subjectRows[] = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'r_marks' => $markRow->r_marks ?? '-',
                    'w_marks' => $markRow->w_marks ?? '-',
                    'l_marks' => $markRow->l_marks ?? '-',
                    'display_marks' => $normalizedDisplayMark,
                    'numeric_marks' => $numericMarks,
                    'maximum_marks' => $maxMarks,
                ];
            }

            $percentage = $totalMaximum > 0 ? round(($totalObtained / $totalMaximum) * 100, 2) : 0;
            $totalPercentage += $percentage;

            $rows[] = [
                'student_id' => $student->id,
                'admission_no' => $student->admissionNo,
                'roll_no' => optional($resultUpdateRows->get($student->id))->roll_no ?? $student->roll_no ?? '-',
                'student_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'father_name' => $student->father_name ?? '',
                'subject_rows' => $subjectRows,
                'total_obtained' => $totalObtained,
                'total_maximum' => $totalMaximum,
                'percentage' => $percentage,
                'subject_rank' => null,
                'overall_rank' => optional($resultUpdateRows->get($student->id))->rank,
                'subject_numeric_marks' => $subjectNumericMarks,
            ];

            if ($singleSubjectMode) {
                $rankSeed[] = [
                    'student_id' => $student->id,
                    'student_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'marks' => $subjectNumericMarks,
                ];
            }

            $overallRankSeed[] = [
                'student_id' => $student->id,
                'student_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'marks' => $totalObtained,
            ];
        }

        $summary = [
            'total_students' => count($rows),
            'selected_subject_count' => $listSubject->count(),
            'report_maximum' => $listSubject->sum(function ($subject) use ($maxMarksRows) {
                if ((int) ($subject->other_subject ?? 0) !== 0) {
                    return 0;
                }

                return (float) optional($maxMarksRows->get($subject->id))->exam_maximum_marks;
            }),
            'average_percentage' => count($rows) > 0 ? round($totalPercentage / count($rows), 2) : 0,
            'topper_name' => '-',
            'topper_score' => 0,
            'topper_label' => 'Overall Topper',
            'single_subject_name' => $selectedSubject->name ?? null,
            'single_subject_maximum' => $selectedSubjectMax,
            'subject_maximums' => $listSubject->mapWithKeys(function ($subject) use ($maxMarksRows) {
                return [$subject->id => (float) optional($maxMarksRows->get($subject->id))->exam_maximum_marks];
            })->all(),
        ];

        if ($singleSubjectMode && !empty($rankSeed)) {
            usort($rankSeed, function ($a, $b) {
                if ($a['marks'] == $b['marks']) {
                    return strcmp($a['student_name'], $b['student_name']);
                }

                return $b['marks'] <=> $a['marks'];
            });

            $rankByStudent = [];
            $currentRank = 0;
            $lastMarks = null;

            foreach ($rankSeed as $index => $entry) {
                if ($lastMarks === null || $entry['marks'] !== $lastMarks) {
                    $currentRank = $index + 1;
                    $lastMarks = $entry['marks'];
                }
                $rankByStudent[$entry['student_id']] = $currentRank;
            }

            foreach ($rows as &$row) {
                $row['subject_rank'] = $rankByStudent[$row['student_id']] ?? null;
            }
            unset($row);

            $topMarks = $rankSeed[0]['marks'] ?? 0;
            $topperNames = collect($rankSeed)
                ->where('marks', $topMarks)
                ->pluck('student_name')
                ->implode(', ');

            $summary['topper_name'] = $topperNames ?: '-';
            $summary['topper_score'] = $topMarks;
            $summary['topper_label'] = ($selectedSubject->name ?? 'Subject') . ' Topper';
            $summary['average_marks'] = count($rankSeed) > 0 ? round(collect($rankSeed)->avg('marks'), 2) : 0;
        } elseif (!empty($rows)) {
            $rows = $this->applyOverallRanks($rows, $overallRankSeed);
            $topper = collect($rows)->sortByDesc('total_obtained')->first();
            $summary['topper_name'] = $topper['student_name'] ?? '-';
            $summary['topper_score'] = $topper['total_obtained'] ?? 0;
            $summary['average_marks'] = count($rows) > 0 ? round(collect($rows)->avg('total_obtained'), 2) : 0;
        } else {
            $summary['average_marks'] = 0;
        }

        if ($singleSubjectMode) {
            usort($rows, function ($a, $b) {
                $rankA = $a['subject_rank'] ?? PHP_INT_MAX;
                $rankB = $b['subject_rank'] ?? PHP_INT_MAX;

                if ($rankA === $rankB) {
                    return strcmp($a['student_name'], $b['student_name']);
                }

                return $rankA <=> $rankB;
            });
        } else {
            usort($rows, function ($a, $b) {
                $rankA = is_numeric($a['overall_rank']) ? (int) $a['overall_rank'] : PHP_INT_MAX;
                $rankB = is_numeric($b['overall_rank']) ? (int) $b['overall_rank'] : PHP_INT_MAX;

                if ($rankA === $rankB) {
                    if ((float) $a['total_obtained'] === (float) $b['total_obtained']) {
                        return strcmp($a['student_name'], $b['student_name']);
                    }

                    return (float) $b['total_obtained'] <=> (float) $a['total_obtained'];
                }

                return $rankA <=> $rankB;
            });
        }

        // Rank against the complete active class cohort, then limit the visible
        // report to the student whose profile opened it.
        if ($displayStudentId !== null) {
            $rows = array_values(array_filter($rows, function ($row) use ($displayStudentId) {
                return (int) $row['student_id'] === $displayStudentId;
            }));
            $summary['total_students'] = count($rows);
        }

        return [
            'rows' => $rows,
            'summary' => $summary,
            'single_subject_mode' => $singleSubjectMode,
        ];
    }

    private function applyOverallRanks(array $rows, array $overallRankSeed): array
    {
        $needsComputedRank = collect($rows)->contains(function ($row) {
            return !is_numeric($row['overall_rank']);
        });

        if (!$needsComputedRank) {
            return $rows;
        }

        usort($overallRankSeed, function ($a, $b) {
            if ((float) $a['marks'] === (float) $b['marks']) {
                return strcmp($a['student_name'], $b['student_name']);
            }

            return (float) $b['marks'] <=> (float) $a['marks'];
        });

        $rankByStudent = [];
        $currentRank = 0;
        $lastMarks = null;

        foreach ($overallRankSeed as $index => $entry) {
            if ($lastMarks === null || (float) $entry['marks'] !== (float) $lastMarks) {
                $currentRank = $index + 1;
                $lastMarks = (float) $entry['marks'];
            }

            $rankByStudent[$entry['student_id']] = $currentRank;
        }

        foreach ($rows as &$row) {
            if (!is_numeric($row['overall_rank'])) {
                $row['overall_rank'] = $rankByStudent[$row['student_id']] ?? null;
            }
        }
        unset($row);

        return $rows;
    }
    
    
    public function subjectWiseReport(Request $request)
    {
        

        $search["class_type_id"] = $request->class_type_id;
               $search["exam_id"] = $request->exam_id  ?? "1";

        $students = "";
        $examlist = "";
        $list_subject = "";
        
           $examlist = AssignExam::select(
                "assign_exams.*",
                "exam.id as exam_id",
                "exam.name as exam_name"
            )
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $request->class_type_id)
                ->where('assign_exams.session_id',Session::get("session_id"))
                 ->where('exam.deleted_at',null)->where("assign_exams.branch_id", Session::get("branch_id"))->orderBy('exam.id','ASC')
                ->get();
               
        if ($request->isMethod("post")) {
         
            $request->validate([]);
          
            $list_subject = Subject::where("class_type_id", $request->class_type_id)
                ->where("branch_id", Session::get("branch_id"));
                if(!empty($request->subject_id)){
                   $list_subject = $list_subject ->whereIn('id',$request->subject_id);
                }
                $list_subject = $list_subject->orderBy("sort_by", "ASC")
                ->get();
            $students = Admission::where("class_type_id", $request->class_type_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))->where('status',1)
                ->orderBy("first_name", "ASC")
                ->get();
        return view("examination.offline_exam.report.subject_wise_report_print", ["search" => $search,'examlist'=>$examlist,'students'=>$students,'list_subject'=>$list_subject]);

        }
      
        return view("examination.offline_exam.report.subject_wise_report", ["search" => $search]);
    }
    public function greenSheetReport(Request $request)
    {
        

        $search["class_type_id"] = $request->class_type_id;
               $search["exam_id"] = $request->exam_id  ?? "1";

        $students = "";
        $examlist = "";
        $list_subject = "";
        
           $examlist = AssignExam::select(
                "assign_exams.*",
                "exam.id as exam_id",
                "exam.name as exam_name"
            )
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $request->class_type_id)
                ->where('assign_exams.session_id',Session::get("session_id"))
                 ->where('exam.deleted_at',null)->where("assign_exams.branch_id", Session::get("branch_id"))->orderBy('exam.id','ASC')
                ->get();
              
        if ($request->isMethod("post")) {
         
            $request->validate([]);
          
            $list_subject = Subject::where("class_type_id", $request->class_type_id)
                ->where("branch_id", Session::get("branch_id"));
                if(!empty($request->subject_id)){
                   $list_subject = $list_subject ->whereIn('id',$request->subject_id);
                }
                $list_subject = $list_subject->orderBy("sort_by", "ASC")
                ->get();
            $students = Admission::where("class_type_id", $request->class_type_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))->where('status',1)
                ->orderBy("first_name", "ASC")
                ->get();
        return view("examination.offline_exam.report.green_sheet_report_print", ["search" => $search,'examlist'=>$examlist,'students'=>$students,'list_subject'=>$list_subject]);

        }
      
        return view("examination.offline_exam.report.green_sheet_report", ["search" => $search]);
    }

    private function prepareStudentExamComparisonData(
        int $branchId,
        int $sessionId,
        int $classTypeId,
        int $studentId,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        $student = Admission::select(
                'admissions.id',
                'admissions.admissionNo',
                'admissions.roll_no',
                'admissions.first_name',
                'admissions.last_name',
                'admissions.father_name',
                'admissions.mobile',
                'admissions.image',
                'admissions.class_type_id',
                'class_types.name as class_name'
            )
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->where('admissions.id', $studentId)
            ->where('admissions.branch_id', $branchId)
            ->where('admissions.session_id', $sessionId)
            ->where('admissions.class_type_id', $classTypeId)
            ->where('admissions.status', 1)
            ->first();

        if (empty($student)) {
            return $this->emptyStudentExamComparisonData();
        }

        $examRows = Exam::select(
                'exams.id',
                'exams.name',
                'exams.exam_term_id',
                'exams.exam_maximum_marks',
                'exams.created_at',
                'exam_terms.name as exam_term_name',
                'assign_exams.result_declaration_date'
            )
            ->join('fill_marks as student_fill_marks', function ($join) use ($studentId, $classTypeId, $branchId, $sessionId) {
                $join->on('student_fill_marks.exam_id', '=', 'exams.id')
                    ->where('student_fill_marks.admission_id', $studentId)
                    ->where('student_fill_marks.class_type_id', $classTypeId)
                    ->where('student_fill_marks.branch_id', $branchId)
                    ->where('student_fill_marks.session_id', $sessionId)
                    ->whereNull('student_fill_marks.deleted_at');
            })
            ->leftJoin('exam_terms', 'exam_terms.id', '=', 'exams.exam_term_id')
            ->leftJoin('assign_exams', function ($join) use ($classTypeId, $branchId, $sessionId) {
                $join->on('assign_exams.exam_id', '=', 'exams.id')
                    ->where('assign_exams.class_type_id', $classTypeId)
                    ->where('assign_exams.branch_id', $branchId)
                    ->where('assign_exams.session_id', $sessionId)
                    ->whereNull('assign_exams.deleted_at');
            })
            ->where('exams.branch_id', $branchId)
            ->where('exams.session_id', $sessionId)
            ->whereNull('exams.deleted_at')
            ->when(!empty($dateFrom), function ($query) use ($dateFrom) {
                $query->whereRaw('DATE(COALESCE(assign_exams.result_declaration_date, exams.created_at)) >= ?', [$dateFrom]);
            })
            ->when(!empty($dateTo), function ($query) use ($dateTo) {
                $query->whereRaw('DATE(COALESCE(assign_exams.result_declaration_date, exams.created_at)) <= ?', [$dateTo]);
            })
            ->groupBy(
                'exams.id',
                'exams.name',
                'exams.exam_term_id',
                'exams.exam_maximum_marks',
                'exams.created_at',
                'exam_terms.name',
                'assign_exams.result_declaration_date'
            )
            ->orderByRaw('DATE(COALESCE(assign_exams.result_declaration_date, exams.created_at)) ASC')
            ->orderBy('exams.id', 'ASC')
            ->get();

        if ($examRows->isEmpty()) {
            return $this->emptyStudentExamComparisonData($student);
        }

        $examIds = $examRows->pluck('id')->all();

        $classStudents = Admission::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('class_type_id', $classTypeId)
            ->where('status', 1)
            ->orderBy('first_name', 'ASC')
            ->orderBy('last_name', 'ASC')
            ->get(['id', 'first_name', 'last_name']);

        $studentNames = $classStudents->mapWithKeys(function ($classStudent) {
            return [
                $classStudent->id => trim(($classStudent->first_name ?? '') . ' ' . ($classStudent->last_name ?? '')),
            ];
        })->all();

        $fillMarksRows = FillMarks::whereIn('exam_id', $examIds)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('class_type_id', $classTypeId)
            ->whereNull('deleted_at')
            ->get([
                'exam_id',
                'admission_id',
                'subject_id',
                'student_marks',
                'exam_maximum_marks',
            ]);

        $fillMinMaxRows = FillMinMaxMarks::whereIn('exam_id', $examIds)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('class_type_id', $classTypeId)
            ->whereNull('deleted_at')
            ->get([
                'exam_id',
                'subject_id',
                'exam_maximum_marks',
            ]);

        $storedRanks = DB::table('exam_result_updates')
            ->whereIn('exam_id', $examIds)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('class_type_id', $classTypeId)
            ->whereNull('deleted_at')
            ->get(['exam_id', 'admission_id', 'rank'])
            ->groupBy('exam_id');

        $marksMap = [];
        foreach ($fillMarksRows as $markRow) {
            $marksMap[$markRow->exam_id][$markRow->admission_id][$markRow->subject_id] = $markRow;
        }

        $maxMarksMap = [];
        foreach ($fillMinMaxRows as $maxRow) {
            $maxMarksMap[$maxRow->exam_id][$maxRow->subject_id] = (float) ($maxRow->exam_maximum_marks ?? 0);
        }

        $rankMap = [];
        foreach ($examRows as $examRow) {
            $computedRankMap = $this->buildExamRankMap($marksMap[$examRow->id] ?? [], $studentNames);
            $storedRankRows = collect($storedRanks->get($examRow->id, []))
                ->filter(function ($rankRow) {
                    return is_numeric($rankRow->rank);
                })
                ->pluck('rank', 'admission_id')
                ->map(function ($rank) {
                    return (int) $rank;
                })
                ->all();

            $rankMap[$examRow->id] = !empty($storedRankRows) ? array_replace($computedRankMap, $storedRankRows) : $computedRankMap;
        }

        $studentSubjectIds = $fillMarksRows
            ->where('admission_id', $studentId)
            ->pluck('subject_id')
            ->unique()
            ->values()
            ->all();

        $subjects = Subject::whereIn('id', $studentSubjectIds)
            ->orderBy('sort_by', 'ASC')
            ->orderBy('name', 'ASC')
            ->get(['id', 'name']);

        $subjectNameMap = $subjects->mapWithKeys(function ($subject) {
            return [$subject->id => $subject->name];
        })->all();

        foreach ($studentSubjectIds as $subjectId) {
            if (!isset($subjectNameMap[$subjectId])) {
                $subjectNameMap[$subjectId] = 'Subject #' . $subjectId;
            }
        }

        $examSummaries = [];
        $trendLabels = [];
        $trendPercentages = [];
        $trendRanks = [];
        $previousPercentage = null;

        foreach ($examRows as $examRow) {
            $studentExamMarks = $marksMap[$examRow->id][$studentId] ?? [];
            if (empty($studentExamMarks)) {
                continue;
            }

            $subjectBreakdown = [];
            $totalObtained = 0;
            $derivedMaximum = 0;

            foreach ($studentSubjectIds as $subjectId) {
                $markRow = $studentExamMarks[$subjectId] ?? null;
                $displayMarks = $markRow->student_marks ?? '-';
                $numericMarks = $this->normalizeNumericMarks($displayMarks);
                $maximumMarks = (float) ($maxMarksMap[$examRow->id][$subjectId] ?? optional($markRow)->exam_maximum_marks ?? 0);
                $subjectPercentage = $maximumMarks > 0 ? round(($numericMarks / $maximumMarks) * 100, 2) : 0;

                if ($markRow) {
                    $derivedMaximum += $maximumMarks;
                }

                if (is_numeric($displayMarks)) {
                    $totalObtained += $numericMarks;
                }

                $subjectBreakdown[$subjectId] = [
                    'subject_id' => $subjectId,
                    'subject_name' => $subjectNameMap[$subjectId] ?? ('Subject #' . $subjectId),
                    'display_marks' => is_string($displayMarks) ? strtoupper(trim($displayMarks)) : $displayMarks,
                    'numeric_marks' => $numericMarks,
                    'maximum_marks' => $maximumMarks,
                    'percentage' => $subjectPercentage,
                ];
            }

            $totalMaximum = (float) ($examRow->exam_maximum_marks ?? 0);
            if ($totalMaximum <= 0) {
                $totalMaximum = $derivedMaximum;
            }

            $percentage = $totalMaximum > 0 ? round(($totalObtained / $totalMaximum) * 100, 2) : 0;
            $changeFromPrevious = $previousPercentage === null ? null : round($percentage - $previousPercentage, 2);
            $status = $previousPercentage === null ? 'Baseline' : $this->performanceTrendStatus($changeFromPrevious);
            $reportDate = $examRow->result_declaration_date;
            if (empty($reportDate) && !empty($examRow->created_at)) {
                $reportDate = Carbon::parse($examRow->created_at)->format('Y-m-d');
            }

            $examSummaries[] = [
                'exam_id' => (int) $examRow->id,
                'exam_name' => $examRow->name ?? '',
                'exam_term_name' => $examRow->exam_term_name ?? '-',
                'report_date' => $reportDate,
                'report_date_label' => !empty($reportDate) ? Carbon::parse($reportDate)->format('d M Y') : '-',
                'total_maximum' => round($totalMaximum, 2),
                'total_obtained' => round($totalObtained, 2),
                'percentage' => $percentage,
                'rank' => $rankMap[$examRow->id][$studentId] ?? null,
                'change_from_previous' => $changeFromPrevious,
                'status' => $status,
                'subjects' => $subjectBreakdown,
            ];

            $trendLabels[] = $examRow->name ?? '';
            $trendPercentages[] = $percentage;
            $trendRanks[] = $rankMap[$examRow->id][$studentId] ?? null;
            $previousPercentage = $percentage;
        }

        if (empty($examSummaries)) {
            return $this->emptyStudentExamComparisonData($student);
        }

        $subjectComparisonRows = [];
        $subjectAverageLabels = [];
        $subjectAverageValues = [];

        foreach ($studentSubjectIds as $subjectId) {
            $examScores = [];
            $averageSeed = [];

            foreach ($examSummaries as $summary) {
                $subjectData = $summary['subjects'][$subjectId] ?? null;
                $examScores[] = [
                    'exam_id' => $summary['exam_id'],
                    'exam_name' => $summary['exam_name'],
                    'display_marks' => $subjectData['display_marks'] ?? '-',
                    'numeric_marks' => $subjectData['numeric_marks'] ?? 0,
                    'maximum_marks' => $subjectData['maximum_marks'] ?? 0,
                    'percentage' => $subjectData['percentage'] ?? 0,
                ];

                if (!empty($subjectData) && ($subjectData['maximum_marks'] ?? 0) > 0) {
                    $averageSeed[] = $subjectData['percentage'];
                }
            }

            $averagePercentage = !empty($averageSeed) ? round(array_sum($averageSeed) / count($averageSeed), 2) : 0;
            $latestSubjectPercentage = count($examScores) > 0 ? (float) ($examScores[count($examScores) - 1]['percentage'] ?? 0) : 0;
            $previousSubjectPercentage = count($examScores) > 1 ? (float) ($examScores[count($examScores) - 2]['percentage'] ?? 0) : null;

            $subjectComparisonRows[] = [
                'subject_id' => $subjectId,
                'subject_name' => $subjectNameMap[$subjectId] ?? ('Subject #' . $subjectId),
                'exam_scores' => $examScores,
                'average_percentage' => $averagePercentage,
                'status' => $this->classifyPerformanceBand($averagePercentage),
                'trend' => $previousSubjectPercentage === null
                    ? 'Baseline'
                    : $this->performanceTrendStatus(round($latestSubjectPercentage - $previousSubjectPercentage, 2)),
                'latest_percentage' => $latestSubjectPercentage,
                'previous_percentage' => $previousSubjectPercentage,
            ];

            $subjectAverageLabels[] = $subjectNameMap[$subjectId] ?? ('Subject #' . $subjectId);
            $subjectAverageValues[] = $averagePercentage;
        }

        $percentages = array_column($examSummaries, 'percentage');
        $numericRanks = collect(array_column($examSummaries, 'rank'))
            ->filter(function ($rank) {
                return is_numeric($rank);
            })
            ->map(function ($rank) {
                return (int) $rank;
            })
            ->values()
            ->all();

        $latestExam = $examSummaries[count($examSummaries) - 1];
        $firstExam = $examSummaries[0];
        $previousExam = count($examSummaries) > 1 ? $examSummaries[count($examSummaries) - 2] : null;

        $latestVsPrevious = null;
        $latestPreviousChart = [
            'labels' => [],
            'latest' => [],
            'previous' => [],
        ];

        if (!empty($previousExam)) {
            $improvedSubjects = [];
            $declinedSubjects = [];
            $weakSubjects = [];
            $stableSubjects = [];

            foreach ($subjectComparisonRows as $subjectRow) {
                $previousSubjectPercentage = (float) ($previousExam['subjects'][$subjectRow['subject_id']]['percentage'] ?? 0);
                $latestSubjectPercentage = (float) ($latestExam['subjects'][$subjectRow['subject_id']]['percentage'] ?? 0);
                $difference = round($latestSubjectPercentage - $previousSubjectPercentage, 2);

                $latestPreviousChart['labels'][] = $subjectRow['subject_name'];
                $latestPreviousChart['previous'][] = $previousSubjectPercentage;
                $latestPreviousChart['latest'][] = $latestSubjectPercentage;

                $subjectSnapshot = [
                    'subject_name' => $subjectRow['subject_name'],
                    'difference' => $difference,
                    'latest_percentage' => $latestSubjectPercentage,
                    'previous_percentage' => $previousSubjectPercentage,
                ];

                if ($difference > 0.01) {
                    $improvedSubjects[] = $subjectSnapshot;
                } elseif ($difference < -0.01) {
                    $declinedSubjects[] = $subjectSnapshot;
                } else {
                    $stableSubjects[] = $subjectSnapshot;
                }

                if ($latestSubjectPercentage < 60) {
                    $weakSubjects[] = $subjectSnapshot;
                }
            }

            $latestVsPrevious = [
                'previous_exam_name' => $previousExam['exam_name'],
                'latest_exam_name' => $latestExam['exam_name'],
                'previous_percentage' => $previousExam['percentage'],
                'latest_percentage' => $latestExam['percentage'],
                'difference' => round($latestExam['percentage'] - $previousExam['percentage'], 2),
                'improved_subjects' => $improvedSubjects,
                'declined_subjects' => $declinedSubjects,
                'stable_subjects' => $stableSubjects,
                'weak_subjects' => $weakSubjects,
            ];
        }

        $summary = [
            'total_exams_attempted' => count($examSummaries),
            'average_percentage' => count($percentages) > 0 ? round(array_sum($percentages) / count($percentages), 2) : 0,
            'highest_percentage' => count($percentages) > 0 ? max($percentages) : 0,
            'lowest_percentage' => count($percentages) > 0 ? min($percentages) : 0,
            'latest_percentage' => $latestExam['percentage'] ?? 0,
            'overall_change' => count($examSummaries) > 1 ? round(($latestExam['percentage'] ?? 0) - ($firstExam['percentage'] ?? 0), 2) : null,
            'current_rank' => $latestExam['rank'] ?? null,
            'best_rank' => !empty($numericRanks) ? min($numericRanks) : null,
        ];

        $remarks = $this->generateStudentExamRemarks($summary, $subjectComparisonRows, $latestVsPrevious);

        return [
            'student' => $student,
            'exam_summaries' => $examSummaries,
            'subject_rows' => $subjectComparisonRows,
            'summary' => $summary,
            'latest_vs_previous' => $latestVsPrevious,
            'remarks' => $remarks,
            'chart_data' => [
                'trend' => [
                    'labels' => $trendLabels,
                    'percentages' => $trendPercentages,
                    'ranks' => $trendRanks,
                ],
                'subject_average' => [
                    'labels' => $subjectAverageLabels,
                    'percentages' => $subjectAverageValues,
                ],
                'latest_previous' => $latestPreviousChart,
            ],
        ];
    }

    private function getAccessibleBranchIds(): array
    {
        if (!empty(Session::get('admin_branch_id'))) {
            return [(int) Session::get('admin_branch_id')];
        }

        if (Session::get('role_id') > 1) {
            return [(int) Session::get('branch_id')];
        }

        return Branch::whereNull('deleted_at')->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();
    }

    private function getAccessibleClassIds(int $branchId, int $sessionId): array
    {
        $query = ClassType::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at');

        if (Session::get('role_id') == 2) {
            $teachers = Teacher::where('id', Session::get('teacher_id'))->get(['class_type_id']);
            $classTypeIds = [];

            foreach ($teachers as $teacher) {
                $teacherClassTypes = @unserialize($teacher->class_type_id ?? '');
                if (is_array($teacherClassTypes)) {
                    $classTypeIds = array_merge($classTypeIds, $teacherClassTypes);
                }
            }

            $classTypeIds = array_values(array_unique(array_map('intval', array_filter($classTypeIds))));
            if (empty($classTypeIds)) {
                return [];
            }

            $query->whereIn('id', $classTypeIds);
        }

        return $query->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();
    }

    private function buildExamRankMap(array $examMarks, array $studentNames): array
    {
        $rankingSeed = [];

        foreach ($examMarks as $admissionId => $subjectRows) {
            $totalMarks = 0;

            foreach ($subjectRows as $subjectRow) {
                if (is_numeric($subjectRow->student_marks)) {
                    $totalMarks += (float) $subjectRow->student_marks;
                }
            }

            $rankingSeed[] = [
                'admission_id' => (int) $admissionId,
                'student_name' => $studentNames[$admissionId] ?? ('Student #' . $admissionId),
                'marks' => $totalMarks,
            ];
        }

        usort($rankingSeed, function ($left, $right) {
            if ((float) $left['marks'] === (float) $right['marks']) {
                return strcmp($left['student_name'], $right['student_name']);
            }

            return (float) $right['marks'] <=> (float) $left['marks'];
        });

        $rankMap = [];
        $currentRank = 0;
        $lastMarks = null;

        foreach ($rankingSeed as $index => $row) {
            if ($lastMarks === null || (float) $row['marks'] !== (float) $lastMarks) {
                $currentRank = $index + 1;
                $lastMarks = (float) $row['marks'];
            }

            $rankMap[$row['admission_id']] = $currentRank;
        }

        return $rankMap;
    }

    private function normalizeNumericMarks($marks): float
    {
        return is_numeric($marks) ? (float) $marks : 0.0;
    }

    private function performanceTrendStatus(?float $difference): string
    {
        if ($difference === null) {
            return 'Baseline';
        }

        if ($difference > 0.01) {
            return 'Improved';
        }

        if ($difference < -0.01) {
            return 'Declined';
        }

        return 'Stable';
    }

    private function classifyPerformanceBand(float $percentage): string
    {
        if ($percentage >= 80) {
            return 'Strong';
        }

        if ($percentage >= 60) {
            return 'Average';
        }

        return 'Weak';
    }

    private function generateStudentExamRemarks(array $summary, array $subjectRows, ?array $latestVsPrevious): array
    {
        $remarks = [];

        if ($latestVsPrevious) {
            $difference = (float) ($latestVsPrevious['difference'] ?? 0);

            if ($difference > 0.01) {
                $remarks[] = 'Student improved by ' . abs($difference) . '% compared to the previous exam.';
            } elseif ($difference < -0.01) {
                $remarks[] = 'Student declined by ' . abs($difference) . '% compared to the previous exam.';
            } else {
                $remarks[] = 'Performance is stable compared to the previous exam.';
            }
        }

        $strongSubject = collect($subjectRows)
            ->sortByDesc('average_percentage')
            ->first(function ($subjectRow) {
                return ($subjectRow['average_percentage'] ?? 0) >= 80;
            });

        if (!empty($strongSubject)) {
            $remarks[] = $strongSubject['subject_name'] . ' is a strong subject with an average of ' . $strongSubject['average_percentage'] . '%.';
        }

        $weakSubjects = collect($subjectRows)
            ->filter(function ($subjectRow) {
                return ($subjectRow['average_percentage'] ?? 0) < 60;
            })
            ->pluck('subject_name')
            ->values()
            ->all();

        if (!empty($weakSubjects)) {
            $remarks[] = implode(', ', array_slice($weakSubjects, 0, 3)) . ' need improvement.';
        }

        if (($summary['overall_change'] ?? null) !== null) {
            if ((float) $summary['overall_change'] > 0.01) {
                $remarks[] = 'Overall trend is positive from the first recorded exam to the latest one.';
            } elseif ((float) $summary['overall_change'] < -0.01) {
                $remarks[] = 'Overall trend is downward from the first recorded exam to the latest one.';
            }
        }

        return array_values(array_unique($remarks));
    }

    private function emptyStudentExamComparisonData($student = null): array
    {
        return [
            'student' => $student,
            'exam_summaries' => [],
            'subject_rows' => [],
            'summary' => [
                'total_exams_attempted' => 0,
                'average_percentage' => 0,
                'highest_percentage' => 0,
                'lowest_percentage' => 0,
                'latest_percentage' => 0,
                'overall_change' => null,
                'current_rank' => null,
                'best_rank' => null,
            ],
            'latest_vs_previous' => null,
            'remarks' => [],
            'chart_data' => [
                'trend' => [
                    'labels' => [],
                    'percentages' => [],
                    'ranks' => [],
                ],
                'subject_average' => [
                    'labels' => [],
                    'percentages' => [],
                ],
                'latest_previous' => [
                    'labels' => [],
                    'latest' => [],
                    'previous' => [],
                ],
            ],
        ];
    }
}
