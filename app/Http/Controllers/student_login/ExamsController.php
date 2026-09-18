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
        $admission = Admission::where('id', Session::get('id'))
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
                // Results remain private until the publication feature is installed.
                $examIds = collect();
            }

            $exams = Exam::whereIn('id', $examIds)
                ->where('session_id', $admission->session_id)
                ->where('branch_id', $admission->branch_id)
                ->orderBy('id', 'desc')
                ->get();

            $subjects = Subject::where('class_type_id', $admission->class_type_id)
                ->where('session_id', $admission->session_id)
                ->where('branch_id', $admission->branch_id)
                ->orderBy('sort_by')
                ->get();

            $classStudents = Admission::where('class_type_id', $admission->class_type_id)
                ->where('session_id', $admission->session_id)
                ->where('branch_id', $admission->branch_id)
                ->where('status', 1)
                ->orderBy('first_name')
                ->get([
                    'id',
                    'admissionNo',
                    'first_name',
                    'last_name',
                    'father_name',
                    'roll_no',
                    'class_type_id',
                ]);

            $reportController = app(ReportController::class);
            $examReports = $exams->map(function ($exam) use (
                $reportController,
                $admission,
                $classStudents,
                $subjects
            ) {
                $assignedExamDate = AssignExam::where('exam_id', $exam->id)
                    ->where('class_type_id', $admission->class_type_id)
                    ->where('session_id', $admission->session_id)
                    ->where('branch_id', $admission->branch_id)
                    ->whereNull('deleted_at')
                    ->value('exam_date');

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

        return view('student_login.exams.view', [
            'examReports' => $examReports,
        ]);
    }
}
