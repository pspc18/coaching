<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\AssignQuestion;
use App\Models\Subject;
use App\Models\Admission;
use App\Models\Notification;
use App\Services\FcmDirectService;
use DB;
use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function viewExam(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');

        $searchName = trim($request->input('name', ''));
        $termId = $request->input('term_id');
        $classTypeId = $request->input('class_type_id');
        $status = $request->input('status');
        $page = max(1, (int) $request->input('page', 1));
        $perPageInput = $request->input('per_page', 25);
        $perPage = ($perPageInput === 'all' || (int)$perPageInput === 0) ? 999999 : max(1, (int) $perPageInput);

        // Base Query with Filters
        $query = Exam::query()
            ->where('exams.session_id', $sessionId)
            ->where('exams.branch_id', $branchId)
            ->whereNull('exams.deleted_at');

        if (!empty($searchName)) {
            $query->where('exams.name', 'like', '%' . $searchName . '%');
        }

        if (!empty($classTypeId)) {
            $query->whereExists(function($q) use ($classTypeId, $sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.class_type_id', $classTypeId)
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
        }

        if ($status === 'assigned') {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
        } elseif ($status === 'unassigned') {
            $query->whereNotExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
        } elseif ($status === 'published' && Schema::hasTable('exam_result_publications')) {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('exam_result_publications')
                  ->whereColumn('exam_result_publications.exam_id', 'exams.id')
                  ->where('exam_result_publications.session_id', $sessionId)
                  ->where('exam_result_publications.branch_id', $branchId);
            });
        } elseif ($status === 'pending') {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('assign_exams')
                  ->whereColumn('assign_exams.exam_id', 'exams.id')
                  ->where('assign_exams.session_id', $sessionId)
                  ->where('assign_exams.branch_id', $branchId)
                  ->whereNull('assign_exams.deleted_at');
            });
            if (Schema::hasTable('exam_result_publications')) {
                $query->whereNotExists(function($q) use ($sessionId, $branchId) {
                    $q->select(DB::raw(1))
                      ->from('exam_result_publications')
                      ->whereColumn('exam_result_publications.exam_id', 'exams.id')
                      ->where('exam_result_publications.session_id', $sessionId)
                      ->where('exam_result_publications.branch_id', $branchId);
                });
            }
        }

        // Total Count for Pagination
        $totalCount = $query->count('exams.id');
        $lastPage = $perPage > 0 ? (int) ceil($totalCount / $perPage) : 1;
        if ($page > $lastPage && $lastPage > 0) {
            $page = $lastPage;
        }
        $offset = ($page - 1) * $perPage;

        // Fetch ONLY Requested Page Slice
        $data = $query->select('exams.id', 'exams.name', 'exams.exam_date', 'exams.exam_term_id', DB::raw("NULL as exam_term_name"))
            ->orderBy('exams.id', 'DESC')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $examIds = $data->pluck('id')->toArray();
        $examNames = $data->pluck('name');

        // Batch load assigned classes only for current page slice
        $allAssignedClasses = collect();
        if (!empty($examIds)) {
            $allAssignedClasses = AssignExam::query()
                ->join('class_types', 'class_types.id', '=', 'assign_exams.class_type_id')
                ->whereIn('assign_exams.exam_id', $examIds)
                ->where('assign_exams.session_id', $sessionId)
                ->where('assign_exams.branch_id', $branchId)
                ->whereNull('assign_exams.deleted_at')
                ->select('assign_exams.exam_id', 'assign_exams.class_type_id', 'assign_exams.exam_date', 'class_types.name as class_name')
                ->groupBy('assign_exams.exam_id', 'assign_exams.class_type_id', 'assign_exams.exam_date', 'class_types.name')
                ->get()
                ->groupBy('exam_id');
        }

        // Batch load result publication statuses only for current page slice
        $publishedSet = [];
        if (!empty($examIds) && Schema::hasTable('exam_result_publications')) {
            $publishedRecords = DB::table('exam_result_publications')
                ->whereIn('exam_id', $examIds)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->select('exam_id', 'class_type_id')
                ->get();

            foreach ($publishedRecords as $pr) {
                $publishedSet[$pr->exam_id . '_' . $pr->class_type_id] = true;
            }
        }

        foreach ($data as $exam) {
            $baseName = preg_replace('/-\d+$/', '', $exam->name);
            $nextNumber = 1;
            $pattern = '/^' . preg_quote($baseName, '/') . '-(\d+)$/';

            foreach ($examNames as $existingName) {
                if (preg_match($pattern, $existingName, $matches)) {
                    $nextNumber = max($nextNumber, ((int) $matches[1]) + 1);
                }
            }

            $exam->copy_name = $baseName . '-' . $nextNumber;
            
            $examClasses = $allAssignedClasses->get($exam->id, collect());
            foreach ($examClasses as $assignedClass) {
                $assignedClass->is_published = !empty($publishedSet[$exam->id . '_' . $assignedClass->class_type_id]);
            }
            $exam->assigned_classes = $examClasses;
        }

        // Overall Global KPI Stats
        $statsTotalExams = Exam::where('session_id', $sessionId)->where('branch_id', $branchId)->whereNull('deleted_at')->count();
        $statsAssignedClasses = AssignExam::where('session_id', $sessionId)->where('branch_id', $branchId)->whereNull('deleted_at')->count();
        $statsPublished = 0;
        if (Schema::hasTable('exam_result_publications')) {
            $statsPublished = DB::table('exam_result_publications')->where('session_id', $sessionId)->where('branch_id', $branchId)->count();
        }
        $statsPending = max(0, $statsAssignedClasses - $statsPublished);

        $stats = [
            'total' => $statsTotalExams,
            'assigned' => $statsAssignedClasses,
            'published' => $statsPublished,
            'pending' => $statsPending,
        ];

        // Real-time AJAX response
        if ($request->ajax()) {
            $permission = Helper::permissioncheck(8);
            $rowsHtml = view('examination.offline_exam.exam.table_rows', [
                'data' => $data,
                'startIndex' => $offset,
                'permission' => $permission
            ])->render();

            $modalsHtml = view('examination.offline_exam.exam.modals', [
                'data' => $data
            ])->render();

            return response()->json([
                'status' => 'success',
                'html' => $rowsHtml,
                'modals_html' => $modalsHtml,
                'total' => $totalCount,
                'current_page' => $page,
                'per_page' => $perPageInput === 'all' ? 'all' : $perPage,
                'last_page' => $lastPage,
                'from' => $totalCount === 0 ? 0 : ($offset + 1),
                'to' => min($offset + count($data), $totalCount),
                'stats' => $stats,
            ]);
        }

        return Helper::view('examination.offline_exam.exam.view', [
            'data' => $data,
            'search' => ['name' => $searchName, 'term_id' => $termId, 'class_type_id' => $classTypeId, 'status' => $status],
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'perPage' => $perPageInput,
            'lastPage' => $lastPage,
            'stats' => $stats,
            'classType' => Helper::classType(),
            'examTerms' => collect([])
        ]);
    }

    public function publishResult(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'class_type_id' => 'required|integer',
        ]);

        if (!Schema::hasTable('exam_result_publications')) {
            return Redirect::to('view/exam')->with('error', 'Result publication setup is pending. Please run database migrations.');
        }

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $examId = (int) $request->exam_id;
        $classTypeId = (int) $request->class_type_id;

        $exam = Exam::query()
            ->where('id', $examId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        $assignmentExists = AssignExam::query()
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->exists();

        if (!$exam || !$assignmentExists) {
            return Redirect::to('view/exam')->with('error', 'Exam or assigned class not found.');
        }

        $hasResultData = DB::table('fill_marks')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where(function ($query) {
                $query->where(function ($marks) {
                    $marks->whereNotNull('student_marks')->where('student_marks', '!=', '');
                })->orWhere(function ($marks) {
                    $marks->whereNotNull('r_marks')->where('r_marks', '!=', '');
                })->orWhere(function ($marks) {
                    $marks->whereNotNull('w_marks')->where('w_marks', '!=', '');
                })->orWhere(function ($marks) {
                    $marks->whereNotNull('l_marks')->where('l_marks', '!=', '');
                });
            })
            ->exists();

        if (!$hasResultData) {
            return Redirect::to('view/exam')->with('error', 'Result cannot be published because marks have not been entered for this class.');
        }

        $alreadyPublished = DB::table('exam_result_publications')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->exists();

        if ($alreadyPublished) {
            return Redirect::to('view/exam')->with('message', 'Result is already published for this class.');
        }

        $students = Admission::query()
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->get();

        $title = 'Exam Result Published';
        $body = "Your result for {$exam->name} has been published. Open Exam Result to view your result.";
        $now = now();

        DB::beginTransaction();
        try {
            DB::table('exam_result_publications')->insert([
                'exam_id' => $examId,
                'class_type_id' => $classTypeId,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'published_by' => (int) Session::get('id'),
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($students as $student) {
                Notification::create([
                    'title' => $title,
                    'content' => $body,
                    'type' => 'exam_result',
                    'admission_id' => (int) $student->id,
                    'user_id' => null,
                    'device_token' => null,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'message_seen' => 0,
                    'show_status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Exam result publication failed.', [
                'exam_id' => $examId,
                'class_type_id' => $classTypeId,
                'error' => $e->getMessage(),
            ]);

            return Redirect::to('view/exam')->with('error', 'Result could not be published. ' . $e->getMessage());
        }

        $pushSent = 0;
        $pushFailed = 0;
        $fcmService = new FcmDirectService();

        foreach ($students as $student) {
            $tokens = $this->studentFirebaseTokens($student);
            foreach ($tokens as $token) {
                $result = $fcmService->send(
                    $token,
                    [
                        'type' => 'exam_result',
                        'notification_type' => 'default',
                        'channel_id' => 'default',
                        'channelId' => 'default',
                        'exam_id' => (string) $examId,
                        'class_type_id' => (string) $classTypeId,
                    ],
                    'high',
                    $title,
                    $body
                );

                if (!empty($result['success'])) {
                    $pushSent++;
                } else {
                    $pushFailed++;
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Result published successfully. Students notified: {$students->count()}.",
                'is_published' => true,
                'exam_id' => $examId,
                'class_type_id' => $classTypeId
            ]);
        }

        return Redirect::to('view/exam')->with(
            'message',
            "Result published successfully. Students notified: {$students->count()}. Push sent: {$pushSent}. Failed: {$pushFailed}."
        );
    }

    public function resetResultPublication(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'class_type_id' => 'required|integer',
        ]);

        if (!Schema::hasTable('exam_result_publications')) {
            return Redirect::to('view/exam')->with('error', 'Result publication setup is not available.');
        }

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $examId = (int) $request->exam_id;
        $classTypeId = (int) $request->class_type_id;

        $exam = Exam::query()
            ->where('id', $examId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->first();

        $assignmentExists = AssignExam::query()
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exam || !$assignmentExists) {
            return Redirect::to('view/exam')->with('error', 'Exam or assigned class not found.');
        }

        $deleted = DB::table('exam_result_publications')
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->delete();

        if (!$deleted) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'This result is not currently published.'], 422);
            }
            return Redirect::to('view/exam')->with('error', 'This result is not currently published.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "{$exam->name} result publication has been reset. Students can no longer view this result.",
                'is_published' => false,
                'exam_id' => $examId,
                'class_type_id' => $classTypeId
            ]);
        }

        return Redirect::to('view/exam')->with(
            'message',
            "{$exam->name} result publication has been reset. Students can no longer view this result."
        );
    }

    private function studentFirebaseTokens(Admission $student): array
    {
        if (!Schema::hasTable('notification_tokens')) {
            return [];
        }

        $attendanceUniqueId = trim((string) ($student->attendance_unique_id ?? ''));
        $hasAdmissionId = Schema::hasColumn('notification_tokens', 'admission_id');
        $hasAttendanceUniqueId = Schema::hasColumn('notification_tokens', 'attendance_unique_id');

        if (!$hasAdmissionId && (!$hasAttendanceUniqueId || $attendanceUniqueId === '')) {
            return [];
        }

        $query = DB::table('notification_tokens')
            ->where(function ($query) use ($student, $attendanceUniqueId, $hasAdmissionId, $hasAttendanceUniqueId) {
                if ($hasAdmissionId) {
                    $query->where('admission_id', (int) $student->id);
                }
                if ($hasAttendanceUniqueId && $attendanceUniqueId !== '') {
                    $query->orWhere('attendance_unique_id', $attendanceUniqueId);
                }
            })
            ->where('platform', 'android')
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '');

        if (Schema::hasColumn('notification_tokens', 'branch_id')) {
            $query->where('branch_id', (int) $student->branch_id);
        }
        if (Schema::hasColumn('notification_tokens', 'session_id')) {
            $query->where('session_id', (int) $student->session_id);
        }
        if (Schema::hasColumn('notification_tokens', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query
            ->orderByDesc('id')
            ->pluck('device_token')
            ->filter(fn ($token) => trim((string) $token) !== '')
            ->map(fn ($token) => trim((string) $token))
            ->unique()
            ->values()
            ->all();
    }

    public function copyExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'exam_date' => 'required|date',
        ]);

        $sourceExam = Exam::where('id', $request->exam_id)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->first();

        if (!$sourceExam) {
            return Redirect::to('view/exam')->with('error', 'Exam not found.');
        }

        DB::beginTransaction();

        try {
            $copiedExam = $sourceExam->replicate();
            $copiedExam->name = trim($request->name);
            $copiedExam->exam_date = $request->exam_date;
            $copiedExam->user_id = Session::get('id');
            $copiedExam->session_id = Session::get('session_id');
            $copiedExam->branch_id = Session::get('branch_id');
            $copiedExam->deleted_at = null;
            $copiedExam->save();

            $assignments = AssignExam::where('exam_id', $sourceExam->id)
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->get()
                ->unique('class_type_id');

            foreach ($assignments as $assignment) {
                $copiedAssignment = $assignment->replicate();
                $copiedAssignment->exam_id = $copiedExam->id;
                $copiedAssignment->user_id = Session::get('id');
                $copiedAssignment->session_id = Session::get('session_id');
                $copiedAssignment->branch_id = Session::get('branch_id');
                $copiedAssignment->deleted_at = null;
                $copiedAssignment->save();
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Exam copied successfully with the same assigned classes.',
                    'exam_id' => $copiedExam->id
                ]);
            }

            return Redirect::to('view/exam')->with('message', 'Exam copied successfully with the same assigned classes.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Exam could not be copied. ' . $e->getMessage()], 422);
            }

            return Redirect::to('view/exam')->with('error', 'Exam could not be copied. ' . $e->getMessage());
        }
    }
    
    public function getBankQuestions(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $classTypeId = $request->input('class_type_id');
        $subjectId = $request->input('subject_id');
        $typeId = $request->input('question_type_id');
        $search = trim($request->input('search', ''));

        $query = Question::whereNull('deleted_at');

        if (Session::get('role_id') > 1) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($classTypeId)) {
            $query->where('class_type_id', $classTypeId);
        }

        if (!empty($subjectId)) {
            $query->where('subject_id', $subjectId);
        }

        if (!empty($typeId)) {
            $query->where('question_type_id', $typeId);
        }

        if (!empty($search)) {
            $kw = '%' . $search . '%';
            $query->where(function($q) use ($kw) {
                $q->where('name', 'like', $kw)
                  ->orWhere('explanation', 'like', $kw)
                  ->orWhere('direction', 'like', $kw);
            });
        }

        $questions = $query->orderBy('id', 'DESC')
            ->take(200)
            ->get([
                'id', 'class_type_id', 'subject_id', 'question_type_id',
                'name', 'ans1', 'ans2', 'ans3', 'ans4', 'ans5',
                'correct_ans', 'image', 'explanation', 'difficulty_level',
                'group_id', 'direction'
            ]);

        $formatted = $questions->map(function ($q) {
            $correctLetter = '';
            if ($q->question_type_id == 1) {
                $val = trim((string)$q->correct_ans);
                $mapping = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                $correctLetter = $mapping[$val] ?? (in_array(strtoupper($val), ['A', 'B', 'C', 'D', 'E']) ? strtoupper($val) : 'A');
            } else {
                $correctLetter = (string)$q->correct_ans;
            }
            return [
                'id' => $q->id,
                'class_type_id' => $q->class_type_id,
                'subject_id' => $q->subject_id,
                'question_type_id' => $q->question_type_id,
                'name' => $q->name,
                'ans1' => $q->ans1,
                'ans2' => $q->ans2,
                'ans3' => $q->ans3,
                'ans4' => $q->ans4,
                'ans5' => $q->ans5,
                'correct_ans' => $q->correct_ans,
                'correct_letter' => $correctLetter,
                'image' => $q->image ? asset($q->image) : null,
                'explanation' => $q->explanation,
                'difficulty_level' => $q->difficulty_level,
                'group_id' => $q->group_id,
                'direction' => $q->direction,
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $formatted->count(),
            'questions' => $formatted
        ]);
    }

    public function addExam(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $setting = Helper::getSetting();
        $instituteName = $setting->name ?? 'Arise ERP Institute';
        $examId = $request->input('exam_id') ?: $request->route('id');

        if ($request->isMethod('post')) {
            $creationMode = $request->input('exam_creation_mode', 'normal');
            $isOnline = ($creationMode === 'online') || (bool)$request->input('is_online', false);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'class_type_id' => 'required|integer',
                'subject_id' => 'nullable|integer',
                'code' => 'nullable|string|max:100',
                'exam_date' => 'nullable|date',
                'number_of_questions' => 'nullable|integer|min:0|max:100',
                'exam_maximum_marks' => 'nullable|numeric|min:0',
                'duration' => 'nullable|integer|min:1',
                'is_online' => 'nullable|boolean',
                'marks_per_correct' => 'nullable|numeric',
                'marks_per_incorrect' => 'nullable|numeric',
                'answer_key' => 'nullable|array',
                'online_questions' => 'nullable|array',
                'online_questions_json' => 'nullable|string',
                'selected_questions_json' => 'nullable|string',
                'selected_question_ids' => 'nullable',
                'question_paper' => 'nullable|file|mimes:pdf|max:10240',
                'description' => 'nullable|string|max:255',
                'exam_term_id' => 'nullable|integer',
                'exam_creation_mode' => 'nullable|string',
                'exam_id' => 'nullable|integer',
            ]);

            $isEdit = false;
            $add = null;
            if (!empty($examId)) {
                $add = Exam::where('id', $examId)->first();
                if ($add) {
                    $isEdit = true;
                }
            }

            if (!$add) {
                $add = new Exam;
                $add->user_id = Session::get('id') ?: 1;
                $add->session_id = $sessionId;
                $add->branch_id = $branchId;
            }

            $code = !empty($validated['code']) ? trim($validated['code']) : ($request->input('code') ?: ($add->code ?: ($add->exam_code ?: null)));
            if (!$code) {
                do {
                    $code = 'EXAM-' . strtoupper(Str::random(6));
                } while (Exam::when($isEdit, function($q) use ($add) {
                    $q->where('id', '!=', $add->id);
                })->where(function($q) use ($code) {
                    $q->where('code', $code)->orWhere('exam_code', $code);
                })->exists());
            }

            $paperPath = $isEdit ? $add->question_paper_path : null;
            if ($request->hasFile('question_paper')) {
                $paperUploadDir = public_path('uploads/question_papers');
                if (!file_exists($paperUploadDir)) {
                    @mkdir($paperUploadDir, 0777, true);
                }
                $paperFile = $request->file('question_paper');
                $paperFileName = 'qp_' . time() . '_' . Str::slug(pathinfo($paperFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $paperFile->getClientOriginalExtension();
                $paperFile->move($paperUploadDir, $paperFileName);
                $paperPath = 'uploads/question_papers/' . $paperFileName;
            }

            $onlineQuestions = [];
            $derivedAnswerKey = [];
            $questionIds = [];

            if (in_array($creationMode, ['question_bank', 'online'])) {
                $rawJson = $request->input('selected_questions_json');
                if (!empty($rawJson)) {
                    $decoded = is_string($rawJson) ? json_decode($rawJson, true) : (is_array($rawJson) ? $rawJson : null);
                    if ($decoded === null && is_string($rawJson)) {
                        $decoded = json_decode(stripslashes($rawJson), true);
                    }
                    if (is_array($decoded)) {
                        $onlineQuestions = $decoded;
                        $questionIds = array_values(array_filter(array_map('intval', array_column($onlineQuestions, 'id'))));
                    }
                } elseif ($request->filled('selected_question_ids')) {
                    $rawIds = $request->input('selected_question_ids');
                    $questionIds = is_array($rawIds) ? $rawIds : explode(',', (string)$rawIds);
                    $questionIds = array_values(array_filter(array_map('intval', $questionIds)));
                }

                if (!empty($questionIds) && empty($onlineQuestions)) {
                    $fetchedQuestions = Question::whereIn('id', $questionIds)->get()->keyBy('id');
                    $index = 1;
                    foreach ($questionIds as $qid) {
                        if (!isset($fetchedQuestions[$qid])) continue;
                        $q = $fetchedQuestions[$qid];
                        $c = trim((string)$q->correct_ans);
                        $mapping = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                        $ans = $q->question_type_id == 1 ? ($mapping[$c] ?? (in_array(strtoupper($c), ['A','B','C','D','E']) ? strtoupper($c) : 'A')) : (string)$q->correct_ans;

                        $onlineQuestions[] = [
                            'id' => $q->id,
                            'question_number' => $index,
                            'name' => $q->name,
                            'question_type_id' => $q->question_type_id,
                            'ans1' => $q->ans1,
                            'ans2' => $q->ans2,
                            'ans3' => $q->ans3,
                            'ans4' => $q->ans4,
                            'ans5' => $q->ans5,
                            'correct_ans' => $ans,
                            'correct_letter' => $ans,
                            'image' => $q->image,
                            'explanation' => $q->explanation,
                            'difficulty_level' => $q->difficulty_level,
                            'group_id' => $q->group_id,
                            'direction' => $q->direction,
                        ];
                        $index++;
                    }
                }

                // Fallback for edit: if questions payload was omitted, preserve existing
                if ($isEdit && empty($onlineQuestions) && !$request->has('selected_questions_json') && !$request->has('selected_question_ids')) {
                    $onlineQuestions = is_array($add->online_questions) ? $add->online_questions : (json_decode($add->online_questions, true) ?: []);
                    $questionIds = array_values(array_filter(array_map('intval', array_column($onlineQuestions, 'id'))));
                }

                $qNum = 1;
                foreach ($onlineQuestions as &$oq) {
                    $oq['question_number'] = $qNum;
                    $isMcq = (int)($oq['question_type_id'] ?? 1) === 1;
                    $ans = !empty($oq['correct_letter']) ? $oq['correct_letter'] : ($oq['correct_ans'] ?? 'A');
                    $mapping = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                    if ($isMcq && isset($mapping[(string)$ans])) {
                        $ans = $mapping[(string)$ans];
                    }
                    $oq['correct_letter'] = $ans;
                    $derivedAnswerKey[(string)$qNum] = $ans;
                    $qNum++;
                }
                unset($oq);

                $numQuestions = count($onlineQuestions);
                if ($numQuestions === 0) {
                    $numQuestions = (int) ($validated['number_of_questions'] ?? ($isEdit ? ($add->number_of_questions ?? 0) : 0));
                }
                $marksPerCorrect = isset($validated['marks_per_correct']) ? (float)$validated['marks_per_correct'] : ($isEdit && isset($add->marks_per_correct) ? (float)$add->marks_per_correct : 1.0);
                $marksPerIncorrect = isset($validated['marks_per_incorrect']) ? (float)$validated['marks_per_incorrect'] : ($isEdit && isset($add->marks_per_incorrect) ? (float)$add->marks_per_incorrect : 0.0);
                $maxMarks = $numQuestions * $marksPerCorrect;
                $finalAnswerKey = $derivedAnswerKey;
            } elseif ($creationMode === 'pdf_upload') {
                $numQuestions = (int) ($validated['number_of_questions'] ?? ($isEdit ? ($add->number_of_questions ?? 50) : 50));
                $numQuestions = max(1, $numQuestions);
                $marksPerCorrect = isset($validated['marks_per_correct']) ? (float)$validated['marks_per_correct'] : ($isEdit && isset($add->marks_per_correct) ? (float)$add->marks_per_correct : 1.0);
                $marksPerIncorrect = isset($validated['marks_per_incorrect']) ? (float)$validated['marks_per_incorrect'] : ($isEdit && isset($add->marks_per_incorrect) ? (float)$add->marks_per_incorrect : 0.0);
                $maxMarks = $numQuestions * $marksPerCorrect;
                $submittedKey = $request->answer_key ?? [];
                $finalAnswerKey = !empty($submittedKey) ? $submittedKey : ($isEdit && !empty($add->answer_key) ? $add->answer_key : []);
            } else {
                // Mode 1: Normal Offline Exam
                $creationMode = 'normal';
                $numQuestions = max(0, (int) ($request->input('number_of_questions', $isEdit ? ($add->number_of_questions ?? 0) : 0)));
                $maxMarks = (float) ($request->input('exam_maximum_marks', $validated['exam_maximum_marks'] ?? ($isEdit ? ($add->exam_maximum_marks ?? 100) : 100)));
                $marksPerCorrect = 1.0;
                $marksPerIncorrect = 0.0;
                $finalAnswerKey = [];
            }

            $examDate = !empty($validated['exam_date']) ? $validated['exam_date'] : ($isEdit && !empty($add->exam_date) ? (is_object($add->exam_date) ? $add->exam_date->format('Y-m-d') : $add->exam_date) : date('Y-m-d'));

            DB::beginTransaction();
            try {
                $add->name = trim($request->name);
                $add->code = $code;
                $add->exam_code = $code;
                $add->class_type_id = $request->class_type_id;
                $add->subject_id = $request->subject_id;
                $add->exam_term_id = $request->exam_term_id;
                $add->exam_date = $examDate;
                $add->number_of_questions = $numQuestions;
                $add->is_online = $isOnline ? 1 : 0;
                $add->exam_creation_mode = $creationMode;
                $add->marks_per_correct = $marksPerCorrect;
                $add->marks_per_incorrect = $marksPerIncorrect;
                $add->exam_maximum_marks = $maxMarks;
                $add->answer_key = $finalAnswerKey;
                if (in_array($creationMode, ['question_bank', 'online'])) {
                    $add->online_questions = $onlineQuestions;
                } else {
                    $add->online_questions = null;
                }
                if ($paperPath) {
                    $add->question_paper_path = $paperPath;
                }
                if ($request->filled('duration')) {
                    $add->duration = (int)$request->input('duration', 60);
                }
                if (!$isEdit) {
                    $add->result_status = 'not_scheduled';
                }
                $add->description = $request->description;
                $add->save();

                $assignment = AssignExam::withTrashed()
                    ->where('exam_id', $add->id)
                    ->where('class_type_id', $request->class_type_id)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$assignment) {
                    $existingAssignment = AssignExam::where('exam_id', $add->id)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->first();
                    $assignment = $existingAssignment ?: new AssignExam;
                }

                $assignment->exam_id = $add->id;
                $assignment->class_type_id = $request->class_type_id;
                $assignment->exam_date = $examDate;
                $assignment->session_id = $sessionId;
                $assignment->branch_id = $branchId;
                $assignment->user_id = Session::get('id') ?: 1;
                $assignment->deleted_at = null;
                $assignment->save();

                if (in_array($creationMode, ['question_bank', 'online'])) {
                    AssignQuestion::where('exam_id', $add->id)->delete();
                    foreach ($questionIds as $qId) {
                        $aq = new AssignQuestion;
                        $aq->user_id = Session::get('id') ?: 1;
                        $aq->branch_id = $branchId;
                        $aq->session_id = $sessionId;
                        $aq->exam_id = $add->id;
                        $aq->question_id = $qId;
                        $aq->class_type_id = $request->class_type_id;
                        $aq->subject_id = $request->subject_id;
                        $aq->save();
                    }
                } elseif (in_array($creationMode, ['normal', 'pdf_upload'])) {
                    AssignQuestion::where('exam_id', $add->id)->delete();
                }

                DB::commit();

                $actionWord = $isEdit ? 'updated' : 'created';

                if ($isEdit) {
                    return Redirect::to('edit/exam/' . $add->id)
                        ->with('message', 'Exam "' . $add->name . '" updated successfully.');
                }

                return Redirect::to('view/exam')
                    ->with('message', 'Exam "' . $add->name . '" (' . $add->code . ') created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::back()->withInput()->with('error', 'Exam could not be ' . ($isEdit ? 'updated' : 'created') . ': ' . $e->getMessage());
            }
        }

        $savedExam = null;
        $subjects = collect([]);
        $preloadedQuestions = [];
        $preloadedClassId = null;
        $preloadedSubjectId = null;

        if (!empty($examId)) {
            $savedExam = Exam::where('id', $examId)->first();
            if ($savedExam) {
                $preloadedClassId = $savedExam->class_type_id;
                $preloadedSubjectId = $savedExam->subject_id ?? null;

                if (!empty($savedExam->class_type_id)) {
                    $subjects = Subject::where('class_type_id', $savedExam->class_type_id)->whereNull('deleted_at')->get();
                }

                // Preload questions from savedExam->online_questions
                if (!empty($savedExam->online_questions)) {
                    $rawOq = $savedExam->online_questions;
                    if (is_string($rawOq)) {
                        $rawOq = json_decode($rawOq, true) ?: [];
                    }
                    if (is_array($rawOq)) {
                        $preloadedQuestions = $rawOq;
                        if (empty($preloadedSubjectId) && !empty($preloadedQuestions[0]['subject_id'])) {
                            $preloadedSubjectId = $preloadedQuestions[0]['subject_id'];
                        }
                    }
                }

                // If online_questions is empty, check assign_questions
                if (empty($preloadedQuestions)) {
                    $assignedQIds = AssignQuestion::where('exam_id', $savedExam->id)
                        ->whereNull('deleted_at')
                        ->pluck('question_id')
                        ->toArray();

                    if (!empty($assignedQIds)) {
                        $fetched = Question::whereIn('id', $assignedQIds)->get()->keyBy('id');
                        $idx = 1;
                        foreach ($assignedQIds as $qid) {
                            if (!isset($fetched[$qid])) continue;
                            $q = $fetched[$qid];
                            if (!$preloadedSubjectId && !empty($q->subject_id)) {
                                $preloadedSubjectId = $q->subject_id;
                            }
                            $c = trim((string)$q->correct_ans);
                            $mapping = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                            $ans = ($q->question_type_id == 1) ? ($mapping[$c] ?? (in_array(strtoupper($c), ['A','B','C','D','E']) ? strtoupper($c) : 'A')) : (string)$q->correct_ans;

                            $preloadedQuestions[] = [
                                'id' => $q->id,
                                'question_number' => $idx,
                                'name' => $q->name,
                                'question_type_id' => $q->question_type_id,
                                'ans1' => $q->ans1,
                                'ans2' => $q->ans2,
                                'ans3' => $q->ans3,
                                'ans4' => $q->ans4,
                                'ans5' => $q->ans5,
                                'correct_ans' => $ans,
                                'correct_letter' => $ans,
                                'image' => $q->image ? asset($q->image) : null,
                                'explanation' => $q->explanation,
                                'difficulty_level' => $q->difficulty_level,
                                'group_id' => $q->group_id,
                                'direction' => $q->direction,
                            ];
                            $idx++;
                        }
                    }
                }
            }
        } elseif ($request->filled('question_ids')) {
            $rawIds = $request->input('question_ids');
            $qIds = is_array($rawIds) ? $rawIds : explode(',', (string)$rawIds);
            $qIds = array_map('intval', array_filter($qIds));
            if (!empty($qIds)) {
                $fetched = Question::whereIn('id', $qIds)->get()->keyBy('id');
                $idx = 1;
                foreach ($qIds as $qid) {
                    if (!isset($fetched[$qid])) continue;
                    $q = $fetched[$qid];
                    if (!$preloadedClassId && !empty($q->class_type_id)) {
                        $preloadedClassId = $q->class_type_id;
                    }
                    if (!$preloadedSubjectId && !empty($q->subject_id)) {
                        $preloadedSubjectId = $q->subject_id;
                    }
                    $c = trim((string)$q->correct_ans);
                    $mapping = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                    $ans = ($q->question_type_id == 1) ? ($mapping[$c] ?? (in_array(strtoupper($c), ['A','B','C','D','E']) ? strtoupper($c) : 'A')) : (string)$q->correct_ans;

                    $preloadedQuestions[] = [
                        'id' => $q->id,
                        'question_number' => $idx,
                        'name' => $q->name,
                        'question_type_id' => $q->question_type_id,
                        'ans1' => $q->ans1,
                        'ans2' => $q->ans2,
                        'ans3' => $q->ans3,
                        'ans4' => $q->ans4,
                        'ans5' => $q->ans5,
                        'correct_ans' => $ans,
                        'correct_letter' => $ans,
                        'image' => $q->image ? asset($q->image) : null,
                        'explanation' => $q->explanation,
                        'difficulty_level' => $q->difficulty_level,
                        'group_id' => $q->group_id,
                        'direction' => $q->direction,
                    ];
                    $idx++;
                }

                if ($request->filled('class_type_id')) {
                    $preloadedClassId = (int)$request->input('class_type_id');
                }
                if ($request->filled('subject_id')) {
                    $preloadedSubjectId = (int)$request->input('subject_id');
                }

                if ($preloadedClassId) {
                    $subjects = Subject::where('class_type_id', $preloadedClassId)->whereNull('deleted_at')->get();
                }
            }
        }

        $examCode = $savedExam ? ($savedExam->code ?: ($savedExam->exam_code ?: ('EXAM-' . strtoupper(Str::random(6))))) : ('EXAM-' . strtoupper(Str::random(6)));
        $classType = Helper::ClassType();

        return view('examination.offline_exam.exam.add', compact('examCode', 'classType', 'instituteName', 'savedExam', 'subjects', 'preloadedQuestions', 'preloadedClassId', 'preloadedSubjectId'));
    } 
    
    public function editExam(Request $request, $id){
        $request->merge(['exam_id' => $id]);
        return $this->addExam($request);
    } 
    
    public function assignExam(Request $request, $id){
        $examId = $id;
        $data2 = Exam::where('id', $id)->first();

        if (!$data2) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Exam not found.'], 404);
            }
            return redirect::to('view/exam')->with('error', 'Exam not found.');
        }

        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');

        $AssignExam = AssignExam::select('assign_exams.*', 'class_types.name as class_name')
            ->leftJoin('class_types', 'class_types.id', 'assign_exams.class_type_id')
            ->where('assign_exams.exam_id', $id)
            ->where('assign_exams.session_id', $sessionId)
            ->where('assign_exams.branch_id', $branchId)
            ->whereNull('assign_exams.deleted_at')
            ->orderBy('class_types.id', 'ASC')
            ->get();
        
        if ($request->isMethod('post')) {
            if ($request->input('submit_action') === 'global') {
                $request->validate([
                    'exam_date' => 'required|date',
                ]);
            } elseif ($request->filled('assign_id')) {
                $request->validate([
                    'exam_date' => 'required|date',
                ]);
            } else {
                $request->validate([
                    'class_type_id' => 'required',
                    'exam_date' => 'required|date',
                ]);
            }

            // 1. Bulk Apply Date to All
            if ($request->input('submit_action') === 'global') {
                $updated = AssignExam::where('exam_id', $examId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->whereNull('deleted_at')
                    ->update(['exam_date' => $request->exam_date]);

                $msg = $updated > 0
                    ? 'Exam date applied to all (' . $updated . ') assigned classes successfully.'
                    : 'No assigned classes found to update.';

                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => $msg,
                        'updated_count' => $updated,
                        'exam_date' => $request->exam_date,
                        'formatted_date' => date('d-m-Y', strtotime($request->exam_date))
                    ]);
                }

                return redirect::to('assign/exam/' . $examId)->with('message', $msg);
            }

            // 2. Inline update for an existing assignment
            if ($request->filled('assign_id')) {
                $assignment = AssignExam::where('id', $request->assign_id)
                    ->where('exam_id', $examId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$assignment) {
                    if ($request->ajax()) {
                        return response()->json(['status' => 'error', 'message' => 'Assignment not found.'], 404);
                    }
                    return redirect::to('assign/exam/' . $examId)->with('error', 'Assignment not found !');
                }

                $assignment->exam_date = $request->exam_date;
                $assignment->save();

                if ($request->ajax()) {
                    $className = DB::table('class_types')->where('id', $assignment->class_type_id)->value('name') ?? 'Class';
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Exam date updated successfully for ' . $className . '.',
                        'data' => [
                            'id' => $assignment->id,
                            'class_type_id' => $assignment->class_type_id,
                            'class_name' => $className,
                            'exam_date' => $assignment->exam_date,
                            'formatted_date' => date('d-m-Y', strtotime($assignment->exam_date))
                        ]
                    ]);
                }

                return redirect::to('assign/exam/' . $examId)->with('message', 'Exam date updated successfully.');
            }

            // 3. Add or restore assignment for a class
            $old = AssignExam::withTrashed()
                ->where('class_type_id', $request->class_type_id)
                ->where('exam_id', $examId)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->first();

            $className = DB::table('class_types')->where('id', $request->class_type_id)->value('name') ?? 'Class';

            if (!empty($old)) {
                if ($old->trashed()) {
                    $old->restore();
                }
                $old->exam_date = $request->exam_date;
                $old->save();

                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Exam date updated successfully for ' . $className . '.',
                        'is_new' => false,
                        'data' => [
                            'id' => $old->id,
                            'class_type_id' => $old->class_type_id,
                            'class_name' => $className,
                            'exam_date' => $old->exam_date,
                            'formatted_date' => date('d-m-Y', strtotime($old->exam_date))
                        ]
                    ]);
                }

                return redirect::to('assign/exam/' . $examId)->with('message', 'Exam date updated successfully.');
            }

            $add = new AssignExam;
            $add->user_id = Session::get('id');
            $add->session_id = $sessionId;
            $add->branch_id = $branchId;
            $add->class_type_id = $request->class_type_id;
            $add->exam_id = $examId;
            $add->exam_date = $request->exam_date;
            $add->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Class ' . $className . ' assigned to exam successfully.',
                    'is_new' => true,
                    'data' => [
                        'id' => $add->id,
                        'class_type_id' => $add->class_type_id,
                        'class_name' => $className,
                        'exam_date' => $add->exam_date,
                        'formatted_date' => date('d-m-Y', strtotime($add->exam_date))
                    ]
                ]);
            }

            return redirect::to('assign/exam/' . $examId)->with('message', 'Exam Assigned Successfully.');
        }

        return view('examination.offline_exam.exam.assign', ['AssignExam' => $AssignExam, 'data' => $data2]);
    }

    public function deleteAssignExam(Request $request){
        $assignId = $request->input('assign_id');
        $examId = $request->input('exam_id');

        $assignment = AssignExam::where('id', $assignId)->first();
        if ($assignment) {
            $assignment->delete();
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Class unassigned successfully.'
            ]);
        }

        return redirect::to('assign/exam/' . $examId)->with('message', 'Class Unassigned Successfully.');
    }

    public function deleteExam(Request $request)
    {
        $examId = (int) $request->delete_id;

        $exam = Exam::withTrashed()
            ->where('id', $examId)
            ->where('branch_id', Session::get('branch_id'))
            ->where('session_id', Session::get('session_id'))
            ->first();

        if (!$exam) {
            return Redirect::to('view/exam')->with('error', 'Exam not found.');
        }

        DB::beginTransaction();

        try {
            DB::table('examination_admit_cards')->where('exam_id', $examId)->delete();
            DB::table('examination_schedules')->where('exam_id', $examId)->delete();
            DB::table('assign_questions')->where('exam_id', $examId)->delete();
            DB::table('fill_marks')->where('exam_id', $examId)->delete();
            DB::table('fill_min_max_marks')->where('exam_id', $examId)->delete();
            DB::table('fill_marks_by_excel')->where('exam_id', $examId)->delete();
            if (Schema::hasTable('exam_result_publications')) {
                DB::table('exam_result_publications')->where('exam_id', $examId)->delete();
            }
            DB::table('assign_exams')->where('exam_id', $examId)->delete();
            DB::table('exams')->where('id', $examId)->delete();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Exam and all related data were permanently deleted.',
                    'exam_id' => $examId
                ]);
            }

            return Redirect::to('view/exam')->with('message', 'Exam and all related data were permanently deleted.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Exam could not be deleted. ' . $e->getMessage()], 422);
            }

            return Redirect::to('view/exam')->with('error', 'Exam could not be deleted. ' . $e->getMessage());
        }
    }
}
