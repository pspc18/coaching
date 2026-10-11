<?php

namespace App\Http\Controllers\student_login;

use App\Http\Controllers\Controller;
use App\Http\Controllers\offline_exam\ReportController;
use App\Models\Admission;
use App\Models\exam\AssignExam;
use App\Models\exam\Exam;
use App\Models\exam\FillMarks;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Session;

class ExamsController extends Controller
{
    public function resultCard(Request $request)
    {
        $admission = Admission::select([
                'id',
                'admissionNo',
                'first_name',
                'last_name',
                'father_name',
                'roll_no',
                'class_type_id',
                'session_id',
                'branch_id',
            ])
            ->where('id', Session::get('id'))
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->first();

        $examReports = collect();

        if ($admission) {
            $examIds = FillMarks::where('admission_id', $admission->id)
                ->where('class_type_id', $admission->class_type_id)
                ->where('session_id', $admission->session_id)
                ->where('branch_id', $admission->branch_id)
                ->whereNotNull('exam_id')
                ->where(function ($query) {
                    $query->where(function ($marksQuery) {
                        $marksQuery->whereNotNull('student_marks')
                            ->where('student_marks', '!=', '');
                    })->orWhere(function ($marksQuery) {
                        $marksQuery->whereNotNull('r_marks')
                            ->where('r_marks', '!=', '');
                    })->orWhere(function ($marksQuery) {
                        $marksQuery->whereNotNull('w_marks')
                            ->where('w_marks', '!=', '');
                    })->orWhere(function ($marksQuery) {
                        $marksQuery->whereNotNull('l_marks')
                            ->where('l_marks', '!=', '');
                    });
                })
                ->distinct()
                ->pluck('exam_id');

            if (Schema::hasTable('exam_result_publications')) {
                $publishedExamIds = DB::table('exam_result_publications')
                    ->where('class_type_id', $admission->class_type_id)
                    ->where('session_id', $admission->session_id)
                    ->where('branch_id', $admission->branch_id)
                    ->whereNotNull('published_at')
                    ->pluck('exam_id');

                $examIds = $examIds->intersect($publishedExamIds)->values();
            } else {
                $examIds = collect();
            }

            if ($examIds->isNotEmpty()) {
                $exams = Exam::select(['id', 'name', 'session_id', 'branch_id'])
                    ->whereIn('id', $examIds)
                    ->where('session_id', $admission->session_id)
                    ->where('branch_id', $admission->branch_id)
                    ->orderBy('id', 'desc')
                    ->get();

                $subjects = Subject::select(['id', 'name', 'sort_by', 'other_subject', 'class_type_id'])
                    ->where('class_type_id', $admission->class_type_id)
                    ->where('session_id', $admission->session_id)
                    ->where('branch_id', $admission->branch_id)
                    ->orderBy('sort_by')
                    ->get();

                $classStudents = Admission::select([
                        'id',
                        'admissionNo',
                        'first_name',
                        'last_name',
                        'father_name',
                        'roll_no',
                        'class_type_id',
                    ])
                    ->where('class_type_id', $admission->class_type_id)
                    ->where('session_id', $admission->session_id)
                    ->where('branch_id', $admission->branch_id)
                    ->where('status', 1)
                    ->orderBy('first_name')
                    ->get();

                // Batch fetch assigned exam dates to prevent N+1 queries
                $assignedExamDates = AssignExam::whereIn('exam_id', $examIds)
                    ->where('class_type_id', $admission->class_type_id)
                    ->where('session_id', $admission->session_id)
                    ->where('branch_id', $admission->branch_id)
                    ->whereNull('deleted_at')
                    ->pluck('exam_date', 'exam_id');

                $reportController = app(ReportController::class);
                $examReports = $exams->map(function ($exam) use (
                    $reportController,
                    $admission,
                    $classStudents,
                    $subjects,
                    $assignedExamDates
                ) {
                    $assignedExamDate = $assignedExamDates->get($exam->id);

                    $report = $reportController->prepareExamWiseReportData(
                        (int) $exam->id,
                        (int) $admission->class_type_id,
                        $classStudents,
                        $subjects
                    );

                    $studentRow = collect($report['rows'])->firstWhere('student_id', $admission->id);
                    if (!$studentRow) {
                        return null;
                    }

                    return (object) [
                        'exam' => $exam,
                        'assigned_exam_date' => $assignedExamDate,
                        'result' => $studentRow,
                        'summary' => $report['summary'],
                        'single_subject_mode' => $report['single_subject_mode'],
                    ];
                })->filter()->values();
            }
        }

        return view('student_login.exams.view', [
            'examReports' => $examReports,
            'student' => $admission,
        ]);
    }
}
