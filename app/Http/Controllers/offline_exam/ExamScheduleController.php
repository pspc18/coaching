<?php

namespace App\Http\Controllers\offline_exam;

use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\ExaminationSchedule;
use App\Models\exam\ExaminationScheduleDetail;
use App\Models\Master\TeacherSubject;
use App\Models\Subject;
use Session;
use Helper;
use Str;
use Redirect;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamScheduleController extends Controller
{
    /**
     * Helper to retrieve filtered subjects for class & optional teacher
     */
    private function getSubjectsForClass($classTypeId)
    {
        if (empty($classTypeId)) {
            return collect();
        }

        $query = Subject::where('class_type_id', $classTypeId)->orderBy('name', 'ASC');

        if ((int) Session::get('role_id') === 2) {
            $classes = TeacherSubject::where('teacher_id', Session::get('teacher_id'))
                ->where('class_type_id', $classTypeId)
                ->groupBy('subject_id')
                ->pluck('subject_id')
                ->toArray();

            if (!empty($classes)) {
                $query = $query->whereIn('id', $classes);
            }
        }

        return $query->get();
    }

    /**
     * Main view for examination schedule
     */
    public function addExaminationSchedule(Request $request)
    {
        $search['class_type_id'] = $request->class_type_id;
        $search['exam_id'] = $request->exam_id;
        $data = collect();
        $schedules = collect();

        $exam = collect();
        if (!empty($request->class_type_id)) {
            $exam = AssignExam::select('assign_exams.*', 'exam.id as exam_id', 'exam.name as exam_name', 'assign_exams.exam_date as assign_exam_date')
                ->leftJoin('exams as exam', 'assign_exams.exam_id', 'exam.id')
                ->where('exam.session_id', Session::get('session_id'))
                ->where('exam.branch_id', Session::get('branch_id'))
                ->where('assign_exams.class_type_id', $request->class_type_id)
                ->whereNull('assign_exams.deleted_at')
                ->whereNull('exam.deleted_at')
                ->get();
        }

        if (!empty($request->class_type_id)) {
            $data = $this->getSubjectsForClass($request->class_type_id);

            if (!empty($request->exam_id)) {
                $schedules = DB::table('examination_schedules')
                    ->where('exam_id', $request->exam_id)
                    ->where('class_type_id', $request->class_type_id)
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->whereNull('deleted_at')
                    ->get()
                    ->keyBy('subject_id');
            }
        }

        return view('examination.offline_exam.exam_schedule.add', [
            'search'    => $search,
            'data'      => $data,
            'exam'      => $exam,
            'schedules' => $schedules
        ]);
    }

    /**
     * Real-time AJAX endpoint to fetch subjects and mapped schedule
     */
    public function getScheduleData(Request $request)
    {
        $classTypeId = $request->class_type_id;
        $examId = $request->exam_id;

        if (empty($classTypeId) || empty($examId)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Please select both class and exam.',
                'data'    => [],
                'stats'   => [
                    'total'     => 0,
                    'scheduled' => 0,
                    'pending'   => 0
                ]
            ]);
        }

        $subjects = $this->getSubjectsForClass($classTypeId);
        $schedules = DB::table('examination_schedules')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', Session::get('branch_id'))
            ->where('session_id', Session::get('session_id'))
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('subject_id');

        $rows = [];
        $scheduledCount = 0;
        $pendingCount = 0;

        foreach ($subjects as $index => $sub) {
            $sched = $schedules->get($sub->id);
            $hasDate = !empty($sched->date);
            if ($hasDate) {
                $scheduledCount++;
            } else {
                $pendingCount++;
            }

            $rows[] = [
                's_no'                    => $index + 1,
                'subject_id'              => $sub->id,
                'subject_name'            => $sub->name,
                'schedule_id'             => $sched ? $sched->id : null,
                'date'                    => $sched ? ($sched->date ?? '') : '',
                'from_time'               => $sched ? ($sched->from_time ?? '') : '',
                'to_time'                 => $sched ? ($sched->to_time ?? '') : '',
                'is_scheduled'            => $hasDate,
            ];
        }

        return response()->json([
            'status'  => 'success',
            'data'    => $rows,
            'stats'   => [
                'total'     => count($rows),
                'scheduled' => $scheduledCount,
                'pending'   => $pendingCount
            ]
        ]);
    }

    /**
     * Submit / Save Examination Schedule (supports both AJAX and form submit)
     */
    public function SubmitSchedule(Request $request)
    {
        if ($request->isMethod('post')) {
            $classTypeId = $request->class_type_id;
            $examId = $request->exam_id;

            if (empty($classTypeId) || empty($examId)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Class and Examination are required.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Class and Examination are required.');
            }

            if (!empty($request->subject_id)) {
                $count = count($request->subject_id);
                for ($i = 0; $i < $count; $i++) {
                    $schedId = $request->examination_schedule_id[$i] ?? null;
                    $subId = $request->subject_id[$i];
                    $date = $request->date[$i] ?? null;
                    $fromTime = $request->from_time[$i] ?? null;
                    $toTime = $request->to_time[$i] ?? null;

                    if (!empty($schedId)) {
                        $schedule = ExaminationSchedule::find($schedId);
                    } else {
                        $schedule = ExaminationSchedule::where([
                            'class_type_id' => $classTypeId,
                            'exam_id'       => $examId,
                            'subject_id'    => $subId,
                            'branch_id'     => Session::get('branch_id'),
                            'session_id'    => Session::get('session_id')
                        ])->first();

                        if (!$schedule) {
                            $schedule = new ExaminationSchedule();
                        }
                    }

                    if (empty($date)) {
                        if ($schedule && $schedule->exists) {
                            $schedule->delete();
                        }
                    } else {
                        $schedule->user_id = Session::get('id');
                        $schedule->session_id = Session::get('session_id');
                        $schedule->branch_id = Session::get('branch_id');
                        $schedule->class_type_id = $classTypeId;
                        $schedule->exam_id = $examId;
                        $schedule->subject_id = $subId;
                        $schedule->date = $date;
                        $schedule->from_time = $fromTime;
                        $schedule->to_time = $toTime;
                        $schedule->save();
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Examination schedule updated successfully!'
                ]);
            }

            return redirect()->to('add/examination_schedule?class_type_id='.$classTypeId.'&exam_id='.$examId)
                ->with('message', 'Schedule Updated Successfully.');
        }

        return redirect()->to('add/examination_schedule');
    }
}