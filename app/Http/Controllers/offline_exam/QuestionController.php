<?php

namespace App\Http\Controllers\offline_exam;

use App\Models\exam\Question;
use App\Services\AiQuestionExtractorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Helper;
use Redirect;
use DB;
use Str;

class QuestionController extends Controller
{
    protected $aiService;

    public function __construct(AiQuestionExtractorService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * View all questions in Question Bank
     */
    public function viewQuestion(Request $request)
    {
        $search = [
            'name' => $request->name,
            'subject_id' => $request->subject_id,
            'class_type_id' => $request->class_type_id,
            'question_type_id' => $request->question_type_id,
            'difficulty_level' => $request->difficulty_level,
            'has_diagram' => $request->has_diagram,
            'group_filter' => $request->group_filter,
            'ai_extracted' => $request->ai_extracted,
            'format_filter' => $request->format_filter,
            'chapter_name' => $request->chapter_name,
            'topic_name' => $request->topic_name,
        ];

        // Base query for branch scoping and KPI stats
        $baseQuery = Question::whereNull('deleted_at');
        if (Session::get('role_id') > 1) {
            $baseQuery = $baseQuery->where('questions.branch_id', Session::get('branch_id'));
        }

        // Compute KPI Summary Stats in a single aggregated query for maximum performance
        $statsRaw = (clone $baseQuery)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN question_type_id = 1 THEN 1 ELSE 0 END) as mcq,
                SUM(CASE WHEN question_type_id = 2 THEN 1 ELSE 0 END) as descriptive,
                SUM(CASE WHEN group_id IS NOT NULL AND group_id != "" THEN 1 ELSE 0 END) as grouped,
                SUM(CASE WHEN image IS NOT NULL AND image != "" THEN 1 ELSE 0 END) as diagram,
                SUM(CASE WHEN ai_extracted = 1 THEN 1 ELSE 0 END) as ai_extracted
            ')->first();

        $stats = [
            'total' => (int)($statsRaw->total ?? 0),
            'mcq' => (int)($statsRaw->mcq ?? 0),
            'descriptive' => (int)($statsRaw->descriptive ?? 0),
            'grouped' => (int)($statsRaw->grouped ?? 0),
            'diagram' => (int)($statsRaw->diagram ?? 0),
            'ai_extracted' => (int)($statsRaw->ai_extracted ?? 0),
        ];

        // Optimized selective columns query
        $data = Question::select(
            'questions.id',
            'questions.class_type_id',
            'questions.subject_id',
            'questions.question_type_id',
            'questions.name',
            'questions.difficulty_level',
            'questions.ans1',
            'questions.ans2',
            'questions.ans3',
            'questions.ans4',
            'questions.ans5',
            'questions.correct_ans',
            'questions.group_id',
            'questions.sub_question_no',
            'questions.direction',
            'questions.direction_table',
            'questions.image',
            'questions.explanation',
            'questions.ai_extracted',
            'questions.created_at',
            'questions.chapter_id',
            'questions.topic_id',
            'questions.chapter_name',
            'questions.topic_name',
            'questions.language',
            'questions.name_hi',
            'questions.ans1_hi',
            'questions.ans2_hi',
            'questions.ans3_hi',
            'questions.ans4_hi',
            'questions.ans5_hi',
            'questions.direction_hi',
            'questions.explanation_hi',
            'questions.is_pyq',
            'questions.pyq_exam',
            'questions.pyq_year',
            'subjects.name as subject_name',
            'class_types.name as class_name'
        )
        ->leftJoin('subject as subjects', 'subjects.id', '=', 'questions.subject_id')
        ->leftJoin('class_types', 'class_types.id', '=', 'questions.class_type_id');

        if (Session::get('role_id') > 1) {
            $data = $data->where('questions.branch_id', Session::get('branch_id'));
        }

        // 1. Keyword search (statement, Hindi statement, explanation, chapter, pyq, direction, group_id)
        if (!empty($request->name)) {
            $kw = '%' . trim($request->name) . '%';
            $data = $data->where(function($q) use ($kw) {
                $q->where('questions.name', 'like', $kw)
                  ->orWhere('questions.name_hi', 'like', $kw)
                  ->orWhere('questions.chapter_name', 'like', $kw)
                  ->orWhere('questions.topic_name', 'like', $kw)
                  ->orWhere('questions.pyq_exam', 'like', $kw)
                  ->orWhere('questions.explanation', 'like', $kw)
                  ->orWhere('questions.explanation_hi', 'like', $kw)
                  ->orWhere('questions.direction', 'like', $kw)
                  ->orWhere('questions.group_id', 'like', $kw);
            });
        }

        // 2. Class Type filter
        if (!empty($request->class_type_id)) {
            $data = $data->where('questions.class_type_id', $request->class_type_id);
        }

        // Chapter filter
        if ($request->filled('chapter_name')) {
            $data = $data->where('questions.chapter_name', $request->chapter_name);
        }

        // Topic filter
        if ($request->filled('topic_name')) {
            $data = $data->where('questions.topic_name', $request->topic_name);
        }

        // 3. Subject filter
        $selectedSubjectName = null;
        if (!empty($request->subject_id)) {
            if (!empty($request->class_type_id)) {
                // If specific class selected, match exact subject_id
                $data = $data->where('questions.subject_id', $request->subject_id);
                $selectedSubjectName = DB::table('subject')->where('id', $request->subject_id)->value('name');
            } else {
                // If "All Classes", match all questions of this subject name across all classes
                $selectedSubjectName = DB::table('subject')->where('id', $request->subject_id)->value('name');
                if ($selectedSubjectName) {
                    $matchingIdsQuery = DB::table('subject')
                        ->where('name', $selectedSubjectName)
                        ->whereNull('deleted_at');
                    if (Session::get('role_id') > 1) {
                        $matchingIdsQuery = $matchingIdsQuery->where('branch_id', Session::get('branch_id'));
                    }
                    if (Session::get('session_id')) {
                        $matchingIdsQuery = $matchingIdsQuery->where('session_id', Session::get('session_id'));
                    }
                    $matchingIds = $matchingIdsQuery->pluck('id')->toArray();
                    $data = $data->whereIn('questions.subject_id', !empty($matchingIds) ? $matchingIds : [$request->subject_id]);
                } else {
                    $data = $data->where('questions.subject_id', $request->subject_id);
                }
            }
        }

        // 4. Question Type (1 = MCQ/Objective, 2 = Descriptive)
        if (!empty($request->question_type_id)) {
            $data = $data->where('questions.question_type_id', $request->question_type_id);
        }

        // 5. Difficulty Level (Easy, Medium, Hard)
        if (!empty($request->difficulty_level)) {
            $data = $data->where('questions.difficulty_level', $request->difficulty_level);
        }

        // 6. Format Quick Filter (from KPI pills or dropdown: grouped, standalone, diagram, ai_extracted)
        if (!empty($request->format_filter)) {
            if ($request->format_filter === 'grouped') {
                $data = $data->whereNotNull('questions.group_id')->where('questions.group_id', '!=', '');
            } elseif ($request->format_filter === 'standalone') {
                $data = $data->where(function($q) {
                    $q->whereNull('questions.group_id')->orWhere('questions.group_id', '');
                });
            } elseif ($request->format_filter === 'diagram') {
                $data = $data->whereNotNull('questions.image')->where('questions.image', '!=', '');
            } elseif ($request->format_filter === 'ai_extracted') {
                $data = $data->where('questions.ai_extracted', 1);
            }
        }

        // 7. Attached Diagram Filter
        if ($request->filled('has_diagram')) {
            if ($request->has_diagram == '1') {
                $data = $data->whereNotNull('questions.image')->where('questions.image', '!=', '');
            } elseif ($request->has_diagram == '0') {
                $data = $data->where(function($q) {
                    $q->whereNull('questions.image')->orWhere('questions.image', '');
                });
            }
        }

        // 8. Grouped / Direction Filter
        if (!empty($request->group_filter)) {
            if ($request->group_filter === 'grouped') {
                $data = $data->whereNotNull('questions.group_id')->where('questions.group_id', '!=', '');
            } elseif ($request->group_filter === 'standalone') {
                $data = $data->where(function($q) {
                    $q->whereNull('questions.group_id')->orWhere('questions.group_id', '');
                });
            } elseif ($request->group_filter !== 'all') {
                $data = $data->where('questions.group_id', $request->group_filter);
            }
        }

        // 9. AI Extracted Filter
        if ($request->filled('ai_extracted')) {
            $data = $data->where('questions.ai_extracted', (int)$request->ai_extracted);
        }

        $perPage = (int) $request->input('per_page', 25);
        if ($perPage < 5) $perPage = 25;
        if ($perPage > 200) $perPage = 200;

        $data = $data->orderBy('questions.id', 'DESC')->paginate($perPage)->appends($request->all());
        $classType = Helper::classType();

        // Retrieve real active subjects (scoped to branch, session and non-deleted)
        $subjQuery = DB::table('subject')->whereNull('deleted_at');
        if (Session::get('role_id') > 1) {
            $subjQuery = $subjQuery->where('branch_id', Session::get('branch_id'));
        }
        if (Session::get('session_id')) {
            $subjQuery = $subjQuery->where('session_id', Session::get('session_id'));
        }

        if (!empty($request->class_type_id)) {
            // Scoped to the selected class
            $allSubjects = $subjQuery
                ->where('class_type_id', $request->class_type_id)
                ->select('id', 'name')
                ->orderBy('sort_by', 'ASC')
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            // Unique distinct active subjects (no duplicates)
            $allSubjects = $subjQuery
                ->select('name', DB::raw('MIN(id) as id'))
                ->groupBy('name')
                ->orderBy('name', 'ASC')
                ->get();
        }

        return Helper::view('examination.offline_exam.question_bank.view', [
            'data' => $data,
            'search' => $search,
            'stats' => $stats,
            'classType' => $classType,
            'allSubjects' => $allSubjects,
            'selectedSubjectName' => $selectedSubjectName,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Create / Add Question (Multi-Mode: Manual Standalone, Manual Directional Set, AI Single Image, AI Bulk PDF)
     */
    public function addQuestion(Request $request)
    {
        if ($request->isMethod('post')) {
            $manualMode = $request->input('manual_mode', 'standalone');

            // 1. DIRECTIONAL TYPE QUESTION SET INGESTION
            if ($manualMode === 'directional' || $request->has('directional_questions')) {
                $request->validate([
                    'class_type_id' => 'required',
                    'subject_id' => 'required',
                ]);

                if (empty(trim($request->direction ?? '')) && empty(trim($request->direction_hi ?? ''))) {
                    return redirect()->back()->with('error', 'Please enter common direction / passage in English or Hindi.')->withInput();
                }

                $userId = Session::get('id') ?: 1;
                $sessionId = Session::get('session_id') ?: 1;
                $branchId = Session::get('branch_id') ?: 1;
                $classTypeId = (int)$request->class_type_id;
                $subjectId = (int)$request->subject_id;
                $now = Carbon::now();

                $groupId = trim($request->input('group_id') ?: ('DIR-' . date('Ymd-His') . '-' . strtoupper(Str::random(4))));
                $direction = $request->input('direction') ?: ($request->input('direction_hi') ?? '');
                $directionHi = $request->input('direction_hi');
                $directionTable = $request->input('direction_table');
                $chapterId = $request->input('chapter_id');
                $topicId = $request->input('topic_id');
                $chapterName = $request->filled('chapter_name') ? trim($request->chapter_name) : null;
                $topicName = $request->filled('topic_name') ? trim($request->topic_name) : null;

                $syncRes = $this->syncChapterAndTopic($classTypeId, $subjectId, $chapterName, $topicName, $userId, $branchId, $sessionId, $chapterId, $topicId);
                if ($syncRes) {
                    $chapterId = $syncRes['chapter_id'];
                    $chapterName = $syncRes['chapter'];
                    $topicId = $syncRes['topic_id'];
                    $topicName = $syncRes['topic'];
                }
                $language = $request->input('language', 'en');
                $isPyq = (bool)$request->input('is_pyq', false);
                $pyqExam = $request->filled('pyq_exam') ? trim($request->pyq_exam) : null;
                $pyqYear = $request->filled('pyq_year') ? trim($request->pyq_year) : null;

                // Handle common direction diagram/graph
                $dirImagePath = null;
                if ($request->hasFile('direction_image')) {
                    $file = $request->file('direction_image');
                    $filename = 'dir_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $destPath = public_path('uploads/questions');
                    if (!file_exists($destPath)) {
                        mkdir($destPath, 0755, true);
                    }
                    $file->move($destPath, $filename);
                    $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                    $dirImagePath = 'uploads/questions/' . $filename;
                } elseif ($request->filled('direction_image_path')) {
                    $dirImagePath = $request->input('direction_image_path');
                }

                $subQuestions = $request->input('directional_questions', []);
                if (empty($subQuestions) || !is_array($subQuestions)) {
                    return redirect()->back()->with('error', 'Please add at least one sub-question to the directional set.')->withInput();
                }

                DB::beginTransaction();
                try {
                    $savedCount = 0;
                    foreach ($subQuestions as $idx => $subQ) {
                        $qName = trim($subQ['name'] ?? '');
                        $qNameHi = trim($subQ['name_hi'] ?? '');
                        if (empty($qName) && empty($qNameHi)) {
                            continue;
                        }
                        $finalQName = !empty($qName) ? $qName : $qNameHi;

                        // Sub-question specific image
                        $subImgPath = null;
                        if ($request->hasFile("directional_questions.{$idx}.image")) {
                            $file = $request->file("directional_questions.{$idx}.image");
                            $filename = 'q_sub_' . time() . "_{$idx}_" . Str::random(6) . '.' . $file->getClientOriginalExtension();
                            $destPath = public_path('uploads/questions');
                            if (!file_exists($destPath)) {
                                mkdir($destPath, 0755, true);
                            }
                            $file->move($destPath, $filename);
                            $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                            $subImgPath = 'uploads/questions/' . $filename;
                        } elseif (!empty($subQ['image_path'])) {
                            $subImgPath = $subQ['image_path'];
                        }

                        $finalImg = $subImgPath ?: $dirImagePath;

                        $add = new Question;
                        $add->user_id = $userId;
                        $add->session_id = $sessionId;
                        $add->branch_id = $branchId;
                        $add->class_type_id = $classTypeId;
                        $add->subject_id = $subjectId;
                        $add->chapter_id = $chapterId;
                        $add->topic_id = $topicId;
                        $add->chapter_name = $chapterName;
                        $add->topic_name = $topicName;
                        $add->language = $language;
                        $add->name = $finalQName;
                        $add->name_hi = !empty($qNameHi) ? $qNameHi : null;
                        $subQTypeId = isset($subQ['question_type_id']) ? (int)$subQ['question_type_id'] : 1;
                        $add->question_type_id = $subQTypeId;
                        if ($subQTypeId === 2) {
                            $add->ans1 = null;
                            $add->ans2 = null;
                            $add->ans3 = null;
                            $add->ans4 = null;
                            $add->ans5 = null;
                            $add->ans1_hi = null;
                            $add->ans2_hi = null;
                            $add->ans3_hi = null;
                            $add->ans4_hi = null;
                            $add->ans5_hi = null;
                        } else {
                            $add->ans1 = $subQ['ans1'] ?? null;
                            $add->ans2 = $subQ['ans2'] ?? null;
                            $add->ans3 = $subQ['ans3'] ?? null;
                            $add->ans4 = $subQ['ans4'] ?? null;
                            $add->ans5 = $subQ['ans5'] ?? null;
                            $add->ans1_hi = $subQ['ans1_hi'] ?? null;
                            $add->ans2_hi = $subQ['ans2_hi'] ?? null;
                            $add->ans3_hi = $subQ['ans3_hi'] ?? null;
                            $add->ans4_hi = $subQ['ans4_hi'] ?? null;
                            $add->ans5_hi = $subQ['ans5_hi'] ?? null;
                        }
                        $add->correct_ans = isset($subQ['correct_ans']) ? (string)$subQ['correct_ans'] : '0';
                        $add->group_id = $groupId;
                        $add->direction = $direction;
                        $add->direction_hi = $directionHi;
                        $add->direction_table = $directionTable;
                        $add->sub_question_no = !empty($subQ['sub_question_no']) ? $subQ['sub_question_no'] : ($idx + 1);
                        $add->explanation = $subQ['explanation'] ?? null;
                        $add->explanation_hi = $subQ['explanation_hi'] ?? null;
                        $add->difficulty_level = $subQ['difficulty_level'] ?? 'Medium';
                        $add->is_pyq = $isPyq;
                        $add->pyq_exam = $pyqExam;
                        $add->pyq_year = $pyqYear;
                        $add->image = $finalImg;
                        $add->ai_extracted = false;
                        $add->created_at = $now;
                        $add->updated_at = $now;
                        $add->save();

                        $savedCount++;
                    }

                    if ($savedCount === 0) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Please enter at least one valid question statement in the directional set.')->withInput();
                    }

                    DB::commit();

                    $this->syncChapterAndTopic($request->class_type_id, $request->subject_id, $chapterName, $topicName, $userId, $branchId, $sessionId);

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => true,
                            'message' => "Successfully created Directional Question Set [{$groupId}] with {$savedCount} sub-questions!",
                            'group_id' => $groupId,
                            'count' => $savedCount,
                            'redirect' => url('view/question?group_filter=' . urlencode($groupId))
                        ]);
                    }

                    if ($request->input('submit_action') === 'save_and_add') {
                        return redirect()->back()
                            ->withInput($request->only('class_type_id', 'subject_id', 'chapter_name', 'language'))
                            ->with('message', "Directional Set [{$groupId}] added successfully with {$savedCount} sub-questions! You can now add another set.");
                    }

                    return redirect('view/question?group_filter=' . urlencode($groupId))
                        ->with('message', "Successfully created Directional Question Set [{$groupId}] with {$savedCount} sub-questions!");
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Failed to save directional set: ' . $e->getMessage())->withInput();
                }
            }

            // 2. STANDALONE SINGLE QUESTION INGESTION
            $request->validate([
                'class_type_id' => 'required',
                'subject_id' => 'required',
                'question_type_id' => 'required',
            ]);

            if (empty(trim($request->name ?? '')) && empty(trim($request->name_hi ?? ''))) {
                return redirect()->back()->with('error', 'Please enter question statement in English or Hindi.')->withInput();
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'q_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/questions');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $filename);
                $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                $imagePath = 'uploads/questions/' . $filename;
            } elseif ($request->filled('image_path')) {
                $imagePath = $request->image_path;
            }

            $finalName = $request->filled('name') ? trim($request->name) : trim($request->name_hi ?? '');

            $chapterId = $request->input('chapter_id');
            $topicId = $request->input('topic_id');
            $chapterName = $request->filled('chapter_name') ? trim($request->chapter_name) : null;
            $topicName = $request->filled('topic_name') ? trim($request->topic_name) : null;

            $syncRes = $this->syncChapterAndTopic($request->class_type_id, $request->subject_id, $chapterName, $topicName, Session::get('id'), Session::get('branch_id'), Session::get('session_id'), $chapterId, $topicId);
            if ($syncRes) {
                $chapterId = $syncRes['chapter_id'];
                $chapterName = $syncRes['chapter'];
                $topicId = $syncRes['topic_id'];
                $topicName = $syncRes['topic'];
            }

            $add = new Question;
            $add->user_id = Session::get('id') ?: 1;
            $add->session_id = Session::get('session_id') ?: 1;
            $add->branch_id = Session::get('branch_id') ?: 1;
            $add->class_type_id = $request->class_type_id;
            $add->subject_id = $request->subject_id;
            $add->chapter_id = $chapterId;
            $add->topic_id = $topicId;
            $add->chapter_name = $chapterName;
            $add->topic_name = $topicName;
            $add->language = $request->input('language', 'en');
            $add->name = $finalName;
            $add->name_hi = $request->filled('name_hi') ? trim($request->name_hi) : null;
            $add->question_type_id = (int)$request->question_type_id;
            if ($add->question_type_id === 2) {
                $add->ans1 = null;
                $add->ans2 = null;
                $add->ans3 = null;
                $add->ans4 = null;
                $add->ans5 = null;
                $add->ans1_hi = null;
                $add->ans2_hi = null;
                $add->ans3_hi = null;
                $add->ans4_hi = null;
                $add->ans5_hi = null;
            } else {
                $add->ans1 = $request->ans1;
                $add->ans2 = $request->ans2;
                $add->ans3 = $request->ans3;
                $add->ans4 = $request->ans4;
                $add->ans5 = $request->ans5;
                $add->ans1_hi = $request->ans1_hi;
                $add->ans2_hi = $request->ans2_hi;
                $add->ans3_hi = $request->ans3_hi;
                $add->ans4_hi = $request->ans4_hi;
                $add->ans5_hi = $request->ans5_hi;
            }
            $add->group_id = $request->group_id;
            $add->direction = $request->direction;
            $add->direction_hi = $request->direction_hi;
            $add->direction_table = $request->direction_table;
            $add->sub_question_no = $request->sub_question_no;
            $add->correct_ans = $request->correct_ans;
            $add->explanation = $request->explanation;
            $add->explanation_hi = $request->explanation_hi;
            $add->difficulty_level = $request->difficulty_level ?: 'Medium';
            $add->is_pyq = (bool)$request->input('is_pyq', false);
            $add->pyq_exam = $request->filled('pyq_exam') ? trim($request->pyq_exam) : null;
            $add->pyq_year = $request->filled('pyq_year') ? trim($request->pyq_year) : null;
            $add->image = $imagePath;
            $add->ai_extracted = (bool)$request->input('ai_extracted', false);
            $add->save();

            $this->syncChapterAndTopic($request->class_type_id, $request->subject_id, $request->chapter_name, $request->topic_name, $userId, $branchId, $sessionId);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Question added successfully to Question Bank!',
                    'question_id' => $add->id,
                    'redirect' => url('view/question')
                ]);
            }

            if ($request->input('submit_action') === 'save_and_add') {
                return redirect()->back()
                    ->withInput($request->only('class_type_id', 'subject_id', 'question_type_id', 'chapter_name', 'language'))
                    ->with('message', 'Question added successfully! You can now add the next question.');
            }

            return redirect('view/question')->with('message', 'Question added successfully.');
        }

        $classType = Helper::classType();
        $allSubjects = Helper::getSubject();
        $geminiApiKey = $this->aiService->getApiKey();
        $existingChapters = DB::table('exam_chapters')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'class_type_id', 'subject_id']);
        $existingTopics = DB::table('exam_topics')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'chapter_id', 'name', 'class_type_id', 'subject_id']);

        return view('examination.offline_exam.question_bank.add', compact('classType', 'allSubjects', 'geminiApiKey', 'existingChapters', 'existingTopics'));
    }

    /**
     * AI Agent Single Image Question Extractor (AJAX)
     */
    public function aiExtractSingle(Request $request)
    {
        @set_time_limit(120);
        @ini_set('max_execution_time', 120);
        try {
            $input = null;
            $customKey = $request->input('api_key');
            $extraPrompt = $request->input('extra_instructions', '');

            if ($request->hasFile('image')) {
                $input = $request->file('image');
            } elseif ($request->filled('image_base64')) {
                $input = $request->image_base64;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No image file or image data provided.'
                ], 422);
            }

            $result = $this->aiService->extractFromImage($input, $customKey, $extraPrompt);

            // If question has an image that user wants to save on server, handle it
            if ($result['success'] && $request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'ai_q_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/questions');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $filename);
                $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                $fullPath = $destPath . '/' . $filename;
                $result['data']['image_path'] = 'uploads/questions/' . $filename;
                $result['data']['original_image_path'] = 'uploads/questions/' . $filename;

                // Automatic watermark review & clean
                try {
                    $cleanRes = $this->aiService->cleanWatermarkImage($fullPath);
                    if (!empty($cleanRes['success']) && !empty($cleanRes['has_watermark'])) {
                        $result['data']['image_path'] = $cleanRes['image_path'];
                        $result['data']['has_watermark'] = true;
                        $result['data']['watermark_cleaned'] = true;
                    }
                } catch (\Exception $ex) {
                    // non-fatal
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI Extraction Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Agent Bulk PDF Question Extractor (AJAX)
     */
    public function aiExtractPdf(Request $request)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', 180);
        try {
            $request->validate([
                'pdf_file' => 'required|file|mimes:pdf,png,jpg,jpeg,webp|max:20480', // up to 20MB (PDF or exam image paper)
            ]);

            $customKey = $request->input('api_key');
            $extraPrompt = $request->input('extra_instructions', '');
            $file = $request->file('pdf_file');

            $origExt = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $tempName = 'temp_exam_' . time() . '_' . Str::random(8) . '.' . $origExt;
            $destPath = storage_path('app/temp_ai');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $file->move($destPath, $tempName);
            $fullPath = $destPath . '/' . $tempName;

            $result = $this->aiService->extractFromPdf($fullPath, $customKey, $extraPrompt);

            // Clean up temp file
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF AI Extraction Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Interactive Question Generator (Prompt + Optional PDF/Image Reference)
     */
    public function aiGenerateQuestions(Request $request)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', 180);
        @ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        $filePath = null;
        try {
            $request->validate([
                'prompt' => 'required|string|min:3',
                'file' => 'nullable|file|mimes:pdf,png,jpg,jpeg,webp|max:20480',
            ]);

            $customKey = $request->input('api_key');
            $userPrompt = trim($request->input('prompt'));

            // Handle optional reference file (PDF or image)
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
                $tempName = 'ref_' . time() . '_' . Str::random(8) . '.' . $ext;
                $destPath = storage_path('app/temp_ai');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $tempName);
                $filePath = $destPath . '/' . $tempName;
            }

            // Retrieve class and subject names if IDs were passed
            $classTypeId = $request->input('class_type_id');
            $subjectId = $request->input('subject_id');
            $chapterName = trim($request->input('chapter_name') ?? '');
            $topicName = trim($request->input('topic_name') ?? '');
            $mode = trim($request->input('mode') ?? 'standalone');
            $language = trim($request->input('language') ?? 'en');
            $className = null;
            $subjectName = null;

            if (!empty($classTypeId)) {
                $className = DB::table('class_types')->where('id', $classTypeId)->value('name');
            }
            if (!empty($subjectId)) {
                $subjectName = DB::table('subject')->where('id', $subjectId)->value('name');
            }

            // Sync chapter and topic into master tables
            if (!empty($classTypeId) && !empty($subjectId) && !empty($chapterName)) {
                $this->syncChapterAndTopic($classTypeId, $subjectId, $chapterName, $topicName);
            }

            $context = [
                'class_name' => $className,
                'subject_name' => $subjectName,
                'chapter_name' => $chapterName,
                'topic_name' => $topicName,
                'mode' => $mode,
                'language' => $language,
                'difficulty' => $request->input('difficulty', 'Mixed'),
                'options_count' => (int)$request->input('options_count', 4),
                'question_type_id' => (int)$request->input('question_type_id', 1),
            ];

            $result = $this->aiService->generateQuestionsWithAi($userPrompt, $filePath, $customKey, $context);

            // Clean up temporary file
            if ($filePath && file_exists($filePath)) {
                @unlink($filePath);
            }

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            if ($filePath && file_exists($filePath)) {
                @unlink($filePath);
            }
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            return response()->json([
                'success' => false,
                'message' => 'AI Generation Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Store Questions (From AI batch or manual multi-import)
     */
    public function bulkStore(Request $request)
    {
        @ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        $request->validate([
            'class_type_id' => 'required',
            'subject_id' => 'required',
            'questions' => 'required|array|min:1',
        ]);

        $userId = Session::get('id') ?: 1;
        $sessionId = Session::get('session_id') ?: 1;
        $branchId = Session::get('branch_id') ?: 1;
        $classTypeId = (int)$request->class_type_id;
        $subjectId = (int)$request->subject_id;
        $now = Carbon::now();

        $bulkChapId = $request->input('chapter_id');
        $bulkTopId = $request->input('topic_id');
        $bulkChap = $request->input('chapter_name');
        $bulkTop = $request->input('topic_name');

        $syncRes = $this->syncChapterAndTopic($classTypeId, $subjectId, $bulkChap, $bulkTop, $userId, $branchId, $sessionId, $bulkChapId, $bulkTopId);
        if ($syncRes) {
            $bulkChapId = $syncRes['chapter_id'];
            $bulkChap = $syncRes['chapter'];
            $bulkTopId = $syncRes['topic_id'];
            $bulkTop = $syncRes['topic'];
        }

        $questionsData = $request->input('questions', []);
        $savedCount = 0;

        DB::beginTransaction();
        try {
            $normalizeField = function($val) {
                if ($val === null || $val === '') return null;
                $str = (string)$val;
                // Replace literal '\n' (backslash followed by n) with a real newline if not a LaTeX keyword
                $str = preg_replace('/(?<!\\\\)\\\\n(?![a-zA-Z])/', "\n", $str);
                if (strpos($str, '<table') === false && strpos($str, '<br') === false && strpos($str, "\n") !== false) {
                    $str = nl2br($str);
                }
                return trim($str);
            };

            foreach ($questionsData as $q) {
                if (empty($q['name'])) {
                    continue;
                }

                $qName = $normalizeField($q['name']);
                $direction = isset($q['direction']) ? $normalizeField($q['direction']) : null;
                $explanation = isset($q['explanation']) ? $normalizeField($q['explanation']) : null;
                $ans1 = isset($q['ans1']) ? $normalizeField($q['ans1']) : null;
                $ans2 = isset($q['ans2']) ? $normalizeField($q['ans2']) : null;
                $ans3 = isset($q['ans3']) ? $normalizeField($q['ans3']) : null;
                $ans4 = isset($q['ans4']) ? $normalizeField($q['ans4']) : null;
                $ans5 = isset($q['ans5']) ? $normalizeField($q['ans5']) : null;

                $add = new Question;
                $add->user_id = $userId;
                $add->session_id = $sessionId;
                $add->branch_id = $branchId;
                $add->class_type_id = $classTypeId;
                $add->subject_id = $subjectId;
                $add->name = $qName;
                $bulkQTypeId = isset($q['question_type_id']) ? (int)$q['question_type_id'] : 1;
                $add->question_type_id = $bulkQTypeId;
                if ($bulkQTypeId === 2) {
                    $add->ans1 = null;
                    $add->ans2 = null;
                    $add->ans3 = null;
                    $add->ans4 = null;
                    $add->ans5 = null;
                } else {
                    $add->ans1 = $ans1;
                    $add->ans2 = $ans2;
                    $add->ans3 = $ans3;
                    $add->ans4 = $ans4;
                    $add->ans5 = $ans5;
                }
                $add->group_id = $q['group_id'] ?? null;
                $add->direction = $direction;
                $add->direction_table = $q['direction_table'] ?? null;
                $add->sub_question_no = $q['sub_question_no'] ?? null;
                $add->correct_ans = isset($q['correct_ans']) ? (string)$q['correct_ans'] : null;
                $add->explanation = $explanation;
                $add->difficulty_level = $q['difficulty_level'] ?? 'Medium';
                $add->chapter_id = $q['chapter_id'] ?? $bulkChapId;
                $add->topic_id = $q['topic_id'] ?? $bulkTopId;
                $add->chapter_name = $q['chapter_name'] ?? $bulkChap;
                $add->topic_name = $q['topic_name'] ?? $bulkTop;
                $add->language = $q['language'] ?? ($request->input('language') ?: 'en');
                $add->name_hi = isset($q['name_hi']) ? $normalizeField($q['name_hi']) : null;
                $add->direction_hi = isset($q['direction_hi']) ? $normalizeField($q['direction_hi']) : null;
                $add->ans1_hi = isset($q['ans1_hi']) ? $normalizeField($q['ans1_hi']) : null;
                $add->ans2_hi = isset($q['ans2_hi']) ? $normalizeField($q['ans2_hi']) : null;
                $add->ans3_hi = isset($q['ans3_hi']) ? $normalizeField($q['ans3_hi']) : null;
                $add->ans4_hi = isset($q['ans4_hi']) ? $normalizeField($q['ans4_hi']) : null;
                $add->ans5_hi = isset($q['ans5_hi']) ? $normalizeField($q['ans5_hi']) : null;
                $add->explanation_hi = isset($q['explanation_hi']) ? $normalizeField($q['explanation_hi']) : null;
                $add->is_pyq = !empty($q['is_pyq']) || (bool)$request->input('is_pyq', false);
                $add->pyq_exam = $q['pyq_exam'] ?? $request->input('pyq_exam');
                $add->pyq_year = $q['pyq_year'] ?? $request->input('pyq_year');
                $add->image = $q['image'] ?? null;
                $add->source_file = $q['source_file'] ?? null;
                $add->ai_extracted = true;
                $add->created_at = $now;
                $add->updated_at = $now;
                $add->save();

                $savedCount++;
            }

            DB::commit();

            $bulkChap = $request->input('chapter_name');
            $bulkTop = $request->input('topic_name');
            if (!empty($bulkChap)) {
                $this->syncChapterAndTopic($classTypeId, $subjectId, $bulkChap, $bulkTop, $userId, $branchId, $sessionId);
            }

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            return response()->json([
                'success' => true,
                'count' => $savedCount,
                'message' => "Successfully imported {$savedCount} questions into Question Bank!",
                'redirect' => url('view/question')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to save bulk questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save / Test AI API Key Settings (AJAX)
     */
    public function saveAiKey(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string|min:15',
        ]);

        $apiKey = trim($request->api_key);
        $testResult = $this->aiService->testConnection($apiKey);

        if (!$testResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'API Key validation failed: ' . $testResult['message']
            ], 422);
        }

        try {
            DB::table('settings')->updateOrInsert(
                ['id' => 1],
                ['gemini_api_key' => $apiKey, 'updated_at' => Carbon::now()]
            );

            $modelName = $testResult['model_used'] ?? 'Gemini AI';
            return response()->json([
                'success' => true,
                'message' => "Google Gemini API key validated and saved successfully! (Connected to {$modelName})"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to persist API key: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Diagram / Graph Image via AJAX
     */
    public function uploadDiagram(Request $request)
    {
        try {
            $destPath = public_path('uploads/questions');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }

            // Case 1: Standard file upload
            if ($request->hasFile('diagram_file')) {
                $file = $request->file('diagram_file');
                $filename = 'diagram_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($destPath, $filename);
                $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                return response()->json([
                    'success' => true,
                    'file_path' => 'uploads/questions/' . $filename,
                    'url' => asset('uploads/questions/' . $filename)
                ]);
            }

            // Case 2: Base64 clipboard paste (data:image/...)
            if ($request->has('diagram_base64')) {
                $base64 = $request->input('diagram_base64');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                    $type = strtolower($type[1]);
                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                        $type = 'png';
                    }
                    $base64 = base64_decode($base64);
                    if ($base64 !== false) {
                        $filename = 'diagram_' . time() . '_' . Str::random(8) . '.' . $type;
                        file_put_contents($destPath . '/' . $filename, $base64);
                        $this->mirrorSaveQuestionFile($filename, $base64, true);
                        return response()->json([
                            'success' => true,
                            'file_path' => 'uploads/questions/' . $filename,
                            'url' => asset('uploads/questions/' . $filename)
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'No valid image provided.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean Watermark from an Image (AJAX)
     */
    public function cleanWatermark(Request $request)
    {
        try {
            $imagePath = $request->input('image_path');
            if (empty($imagePath)) {
                return response()->json(['success' => false, 'message' => 'No image path provided.'], 422);
            }

            $fullPath = public_path($imagePath);
            if (!file_exists($fullPath)) {
                return response()->json(['success' => false, 'message' => 'Image file not found on server.'], 404);
            }

            $res = $this->aiService->cleanWatermarkImage($fullPath);
            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Recreate Chart Image with AI vector graphics (AJAX)
     */
    public function recreateChart(Request $request)
    {
        @set_time_limit(120);
        try {
            $imagePath = $request->input('image_path');
            if (empty($imagePath)) {
                return response()->json(['success' => false, 'message' => 'No image path provided.'], 422);
            }

            $fullPath = public_path($imagePath);
            if (!file_exists($fullPath)) {
                return response()->json(['success' => false, 'message' => 'Image file not found on server.'], 404);
            }

            $customKey = $request->input('api_key');
            $res = $this->aiService->recreateChartFromImage($fullPath, $customKey);
            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Edit Question (Unified with Add Page)
     */
    public function editQuestion(Request $request, $id)
    {
        $data = Question::find($id);
        if (!$data) {
            return redirect('view/question')->with('error', 'Question not found.');
        }

        if ($request->isMethod('post')) {
            $manualMode = $request->input('manual_mode', 'standalone');

            $normalizeField = function($val) {
                if ($val === null || $val === '') return null;
                $str = (string)$val;
                $str = preg_replace('/(?<!\\\\)\\\\n(?![a-zA-Z])/', "\n", $str);
                return trim($str);
            };

            // 1. DIRECTIONAL TYPE QUESTION SET UPDATE
            if ($manualMode === 'directional' || $request->has('directional_questions')) {
                $request->validate([
                    'class_type_id' => 'required',
                    'subject_id' => 'required',
                ]);

                if (empty(trim($request->direction ?? '')) && empty(trim($request->direction_hi ?? ''))) {
                    return redirect()->back()->with('error', 'Please enter common direction / passage in English or Hindi.')->withInput();
                }

                $userId = Session::get('id') ?: 1;
                $sessionId = Session::get('session_id') ?: 1;
                $branchId = Session::get('branch_id') ?: 1;
                $classTypeId = (int)$request->class_type_id;
                $subjectId = (int)$request->subject_id;
                $now = Carbon::now();

                $groupId = trim($request->input('group_id') ?: ($data->group_id ?: ('DIR-' . date('Ymd-His') . '-' . strtoupper(Str::random(4)))));
                $direction = $normalizeField($request->input('direction') ?: ($request->input('direction_hi') ?? ''));
                $directionHi = $normalizeField($request->input('direction_hi'));
                $directionTable = $request->input('direction_table');
                $chapterId = $request->input('chapter_id');
                $topicId = $request->input('topic_id');
                $chapterName = $request->filled('chapter_name') ? trim($request->chapter_name) : null;
                $topicName = $request->filled('topic_name') ? trim($request->topic_name) : null;

                $syncRes = $this->syncChapterAndTopic($classTypeId, $subjectId, $chapterName, $topicName, $userId, $branchId, $sessionId, $chapterId, $topicId);
                if ($syncRes) {
                    $chapterId = $syncRes['chapter_id'];
                    $chapterName = $syncRes['chapter'];
                    $topicId = $syncRes['topic_id'];
                    $topicName = $syncRes['topic'];
                }
                $language = $request->input('language', 'en');
                $isPyq = (bool)$request->input('is_pyq', false);
                $pyqExam = $request->filled('pyq_exam') ? trim($request->pyq_exam) : null;
                $pyqYear = $request->filled('pyq_year') ? trim($request->pyq_year) : null;

                // Handle common direction diagram/graph
                $dirImagePath = $data->image;
                if ($request->hasFile('direction_image')) {
                    $file = $request->file('direction_image');
                    $filename = 'dir_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $destPath = public_path('uploads/questions');
                    if (!file_exists($destPath)) {
                        mkdir($destPath, 0755, true);
                    }
                    $file->move($destPath, $filename);
                    $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                    $dirImagePath = 'uploads/questions/' . $filename;
                } elseif ($request->filled('direction_image_path')) {
                    $dirImagePath = $request->input('direction_image_path');
                } elseif ($request->has('remove_direction_image')) {
                    $dirImagePath = null;
                }

                $subQuestions = $request->input('directional_questions', []);
                if (empty($subQuestions) || !is_array($subQuestions)) {
                    return redirect()->back()->with('error', 'Please enter at least one valid sub-question.')->withInput();
                }

                DB::beginTransaction();
                try {
                    $savedCount = 0;
                    $retainedIds = [];

                    foreach ($subQuestions as $idx => $subQ) {
                        $qName = $normalizeField($subQ['name'] ?? '');
                        $qNameHi = $normalizeField($subQ['name_hi'] ?? '');
                        if (empty($qName) && empty($qNameHi)) {
                            continue;
                        }
                        $finalQName = !empty($qName) ? $qName : $qNameHi;

                        $subId = !empty($subQ['id']) ? (int)$subQ['id'] : null;
                        $qItem = null;
                        if ($subId) {
                            $qItem = Question::find($subId);
                        }
                        if (!$qItem && $idx === 0 && !empty($data->id) && empty($data->group_id)) {
                            $qItem = $data;
                        }
                        if (!$qItem) {
                            $qItem = new Question;
                            $qItem->user_id = $userId;
                            $qItem->session_id = $sessionId;
                            $qItem->branch_id = $branchId;
                            $qItem->created_at = $now;
                        }

                        // Sub-question specific image
                        $subImgPath = null;
                        if ($request->hasFile("directional_questions.{$idx}.image")) {
                            $file = $request->file("directional_questions.{$idx}.image");
                            $filename = 'q_sub_' . time() . "_{$idx}_" . Str::random(6) . '.' . $file->getClientOriginalExtension();
                            $destPath = public_path('uploads/questions');
                            if (!file_exists($destPath)) {
                                mkdir($destPath, 0755, true);
                            }
                            $file->move($destPath, $filename);
                            $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                            $subImgPath = 'uploads/questions/' . $filename;
                        } elseif (!empty($subQ['image_path'])) {
                            $subImgPath = $subQ['image_path'];
                        } elseif (!empty($qItem->image) && !$request->has("directional_questions.{$idx}.remove_image")) {
                            $subImgPath = $qItem->image;
                        } else {
                            $subImgPath = $dirImagePath;
                        }

                        $qItem->class_type_id = $classTypeId;
                        $qItem->subject_id = $subjectId;
                        $qItem->chapter_name = $chapterName;
                        $qItem->topic_name = $topicName;
                        $qItem->language = $language;
                        $qItem->name = $finalQName;
                        $qItem->name_hi = !empty($qNameHi) ? $qNameHi : null;
                        $subQTypeId = isset($subQ['question_type_id']) ? (int)$subQ['question_type_id'] : 1;
                        $qItem->question_type_id = $subQTypeId;
                        if ($subQTypeId === 2) {
                            $qItem->ans1 = null;
                            $qItem->ans2 = null;
                            $qItem->ans3 = null;
                            $qItem->ans4 = null;
                            $qItem->ans5 = null;
                            $qItem->ans1_hi = null;
                            $qItem->ans2_hi = null;
                            $qItem->ans3_hi = null;
                            $qItem->ans4_hi = null;
                            $qItem->ans5_hi = null;
                        } else {
                            $qItem->ans1 = $normalizeField($subQ['ans1'] ?? null);
                            $qItem->ans2 = $normalizeField($subQ['ans2'] ?? null);
                            $qItem->ans3 = $normalizeField($subQ['ans3'] ?? null);
                            $qItem->ans4 = $normalizeField($subQ['ans4'] ?? null);
                            $qItem->ans5 = $normalizeField($subQ['ans5'] ?? null);
                            $qItem->ans1_hi = $normalizeField($subQ['ans1_hi'] ?? null);
                            $qItem->ans2_hi = $normalizeField($subQ['ans2_hi'] ?? null);
                            $qItem->ans3_hi = $normalizeField($subQ['ans3_hi'] ?? null);
                            $qItem->ans4_hi = $normalizeField($subQ['ans4_hi'] ?? null);
                            $qItem->ans5_hi = $normalizeField($subQ['ans5_hi'] ?? null);
                        }
                        $qItem->correct_ans = isset($subQ['correct_ans']) ? (string)$subQ['correct_ans'] : '0';
                        $qItem->group_id = $groupId;
                        $qItem->direction = $direction;
                        $qItem->direction_hi = $directionHi;
                        $qItem->direction_table = $directionTable;
                        $qItem->sub_question_no = !empty($subQ['sub_question_no']) ? $subQ['sub_question_no'] : ($idx + 1);
                        $qItem->explanation = $normalizeField($subQ['explanation'] ?? null);
                        $qItem->explanation_hi = $normalizeField($subQ['explanation_hi'] ?? null);
                        $qItem->difficulty_level = $subQ['difficulty_level'] ?? 'Medium';
                        $qItem->is_pyq = $isPyq;
                        $qItem->pyq_exam = $pyqExam;
                        $qItem->pyq_year = $pyqYear;
                        $qItem->image = $subImgPath;
                        $qItem->updated_at = $now;
                        $qItem->save();

                        $retainedIds[] = $qItem->id;
                        $savedCount++;
                    }

                    if ($savedCount === 0) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Please enter at least one valid question statement in the directional set.')->withInput();
                    }

                    // Remove sub-questions that were removed during edit
                    if (!empty($groupId) && !empty($retainedIds)) {
                        Question::where('group_id', $groupId)
                            ->whereNotIn('id', $retainedIds)
                            ->delete();
                    }

                    DB::commit();

                    $this->syncChapterAndTopic($request->class_type_id, $request->subject_id, $chapterName, $topicName, $userId, $branchId, $sessionId);

                    return redirect('view/question?group_filter=' . urlencode($groupId))
                        ->with('message', "Directional Question Set [{$groupId}] updated successfully ({$savedCount} sub-questions)!");
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Failed to update directional set: ' . $e->getMessage())->withInput();
                }
            }

            // 2. STANDALONE SINGLE QUESTION UPDATE
            $request->validate([
                'class_type_id' => 'required',
                'subject_id' => 'required',
                'question_type_id' => 'required',
            ]);

            if (empty(trim($request->name ?? '')) && empty(trim($request->name_hi ?? ''))) {
                return redirect()->back()->with('error', 'Please enter question statement in English or Hindi.')->withInput();
            }

            $imagePath = $data->image;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'q_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/questions');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $filename);
                $this->mirrorSaveQuestionFile($filename, $destPath . '/' . $filename);
                $imagePath = 'uploads/questions/' . $filename;
            } elseif ($request->filled('image_path')) {
                $imagePath = $request->image_path;
            } elseif ($request->has('remove_image')) {
                $imagePath = null;
            }

            $finalName = $request->filled('name') ? $normalizeField($request->name) : $normalizeField($request->name_hi ?? '');

            $chapterId = $request->input('chapter_id');
            $topicId = $request->input('topic_id');
            $chapterName = $request->filled('chapter_name') ? trim($request->chapter_name) : null;
            $topicName = $request->filled('topic_name') ? trim($request->topic_name) : null;

            $syncRes = $this->syncChapterAndTopic($request->class_type_id, $request->subject_id, $chapterName, $topicName, null, null, null, $chapterId, $topicId);
            if ($syncRes) {
                $chapterId = $syncRes['chapter_id'];
                $chapterName = $syncRes['chapter'];
                $topicId = $syncRes['topic_id'];
                $topicName = $syncRes['topic'];
            }

            $data->class_type_id = (int)$request->class_type_id;
            $data->subject_id = (int)$request->subject_id;
            $data->chapter_id = $chapterId;
            $data->topic_id = $topicId;
            $data->chapter_name = $chapterName;
            $data->topic_name = $topicName;
            $data->language = $request->input('language', 'en');
            $data->name = $finalName;
            $data->name_hi = $request->filled('name_hi') ? $normalizeField($request->name_hi) : null;
            $data->question_type_id = (int)$request->question_type_id;
            if ($data->question_type_id === 2) {
                $data->ans1 = null;
                $data->ans2 = null;
                $data->ans3 = null;
                $data->ans4 = null;
                $data->ans5 = null;
                $data->ans1_hi = null;
                $data->ans2_hi = null;
                $data->ans3_hi = null;
                $data->ans4_hi = null;
                $data->ans5_hi = null;
            } else {
                $data->ans1 = $normalizeField($request->ans1);
                $data->ans2 = $normalizeField($request->ans2);
                $data->ans3 = $normalizeField($request->ans3);
                $data->ans4 = $normalizeField($request->ans4);
                $data->ans5 = $normalizeField($request->ans5);
                $data->ans1_hi = $normalizeField($request->ans1_hi);
                $data->ans2_hi = $normalizeField($request->ans2_hi);
                $data->ans3_hi = $normalizeField($request->ans3_hi);
                $data->ans4_hi = $normalizeField($request->ans4_hi);
                $data->ans5_hi = $normalizeField($request->ans5_hi);
            }
            $data->direction_hi = $normalizeField($request->direction_hi);
            $data->correct_ans = (string)$request->correct_ans;
            $data->explanation = $normalizeField($request->explanation);
            $data->explanation_hi = $normalizeField($request->explanation_hi);
            $data->difficulty_level = $request->difficulty_level ?: 'Medium';
            $data->is_pyq = (bool)$request->input('is_pyq', false);
            $data->pyq_exam = $request->filled('pyq_exam') ? trim($request->pyq_exam) : null;
            $data->pyq_year = $request->filled('pyq_year') ? trim($request->pyq_year) : null;
            $data->image = $imagePath;
            $data->save();

            $this->syncChapterAndTopic($request->class_type_id, $request->subject_id, $request->chapter_name, $request->topic_name);

            if (!empty($data->group_id)) {
                return redirect('view/question?group_filter=' . urlencode($data->group_id))
                    ->with('message', 'Question updated successfully.');
            }

            return redirect('view/question')->with('message', 'Question updated successfully.');
        }

        $classType = Helper::classType();
        $allSubjects = Helper::getSubject();
        $geminiApiKey = $this->aiService->getApiKey();
        $existingChapters = DB::table('exam_chapters')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'class_type_id', 'subject_id']);
        $existingTopics = DB::table('exam_topics')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'chapter_id', 'name', 'class_type_id', 'subject_id']);
        $isEdit = true;

        $directionalQuestions = [];
        if (!empty($data->group_id)) {
            $directionalQuestions = Question::where('group_id', $data->group_id)
                ->orderBy('sub_question_no', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('examination.offline_exam.question_bank.add', compact(
            'data',
            'isEdit',
            'classType',
            'allSubjects',
            'geminiApiKey',
            'directionalQuestions',
            'existingChapters',
            'existingTopics'
        ));
    }

    /**
     * Delete Question
     */
    public function deleteQuestion(Request $request)
    {
        $question = Question::find($request->delete_id);
        if ($question) {
            $question->delete();
        }
        return redirect('view/question')->with('message', 'Question deleted successfully.');
    }

    /**
     * Get Chapters dynamically for Course/Class & Subject
     */
    public function getChapters(Request $request)
    {
        $classTypeId = $request->input('class_type_id');
        $subjectId = $request->input('subject_id');

        if (empty($subjectId)) {
            return response()->json([
                'success' => true,
                'chapters' => []
            ]);
        }

        // Find all subject IDs that share the same subject name (e.g. "General English" exists with id 3, 60, etc. across classes)
        $matchingSubjectIds = [(int)$subjectId];
        $subjName = DB::table('subject')->where('id', $subjectId)->value('name');
        if (!empty($subjName)) {
            $allIds = DB::table('subject')->where('name', $subjName)->whereNull('deleted_at')->pluck('id')->toArray();
            if (!empty($allIds)) {
                $matchingSubjectIds = array_unique(array_merge($matchingSubjectIds, $allIds));
            }
        }

        $query = DB::table('exam_chapters')->whereNull('deleted_at')->whereIn('subject_id', $matchingSubjectIds);

        $chapters = $query->orderBy('name', 'asc')->get(['id', 'name'])->map(function($c) {
            return ['id' => $c->id, 'name' => $c->name];
        })->toArray();

        // Check if there are distinct chapter_name in questions table for this subject not yet in exam_chapters
        $existingNames = array_map(function($c) { return trim($c['name']); }, $chapters);
        $qQuery = DB::table('questions')
            ->whereNull('deleted_at')
            ->whereNotNull('chapter_name')
            ->where('chapter_name', '!=', '')
            ->whereIn('subject_id', $matchingSubjectIds);

        $qChapters = $qQuery->distinct()->pluck('chapter_name')->toArray();

        foreach ($qChapters as $qName) {
            $qName = trim($qName);
            if (!empty($qName) && !in_array($qName, $existingNames)) {
                $newId = DB::table('exam_chapters')->insertGetId([
                    'class_type_id' => $classTypeId ?: 0,
                    'subject_id' => $subjectId,
                    'name' => $qName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $chapters[] = ['id' => $newId, 'name' => $qName];
                $existingNames[] = $qName;
            }
        }

        usort($chapters, function($a, $b) { return strcasecmp($a['name'], $b['name']); });

        return response()->json([
            'success' => true,
            'chapters' => array_values($chapters)
        ]);
    }

    /**
     * Get Topics dynamically for Course/Class, Subject, and Chapter
     */
    public function getTopics(Request $request)
    {
        $classTypeId = $request->input('class_type_id');
        $subjectId = $request->input('subject_id');
        $chapterId = $request->input('chapter_id');
        $chapterName = trim($request->input('chapter_name') ?? '');

        if (empty($chapterId) && empty($chapterName)) {
            return response()->json([
                'success' => true,
                'topics' => []
            ]);
        }

        $query = DB::table('exam_topics')->whereNull('deleted_at');
        if (!empty($chapterId)) {
            $query->where('chapter_id', $chapterId);
        } elseif (!empty($chapterName)) {
            $cIds = DB::table('exam_chapters')
                ->where('name', $chapterName)
                ->pluck('id')
                ->toArray();
            if (!empty($cIds)) {
                $query->whereIn('chapter_id', $cIds);
            } else {
                return response()->json(['success' => true, 'topics' => []]);
            }
        }

        $topics = $query->orderBy('name', 'asc')->get(['id', 'chapter_id', 'name'])->map(function($t) {
            return ['id' => $t->id, 'chapter_id' => $t->chapter_id, 'name' => $t->name];
        })->toArray();

        return response()->json([
            'success' => true,
            'topics' => array_values($topics)
        ]);
    }

    /**
     * Store new Chapter & Topic on the fly via AJAX
     */
    public function storeChapterTopic(Request $request)
    {
        $classTypeId = $request->input('class_type_id');
        $subjectId = $request->input('subject_id');
        $chapterId = $request->input('chapter_id');
        $chapterName = trim($request->input('chapter_name') ?? '');
        $topicName = trim($request->input('topic_name') ?? '');

        if (empty($classTypeId) || empty($subjectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Target Course and Subject are required.'
            ], 422);
        }

        if (empty($chapterName) && empty($chapterId)) {
            return response()->json([
                'success' => false,
                'message' => 'Chapter Name is required.'
            ], 422);
        }

        $userId = Session::get('id') ?: 1;
        $branchId = Session::get('branch_id') ?: 1;
        $sessionId = Session::get('session_id') ?: 1;

        if (!empty($chapterId) && empty($chapterName)) {
            $chapterName = DB::table('exam_chapters')->where('id', $chapterId)->value('name') ?: '';
        }

        $res = $this->syncChapterAndTopic($classTypeId, $subjectId, $chapterName, $topicName, $userId, $branchId, $sessionId, $chapterId);

        return response()->json([
            'success' => true,
            'message' => 'Saved successfully.',
            'chapter_id' => $res['chapter_id'] ?? null,
            'chapter_name' => $res['chapter'] ?? $chapterName,
            'topic_id' => $res['topic_id'] ?? null,
            'topic_name' => $res['topic'] ?? $topicName
        ]);
    }

    /**
     * Internal helper to persist Chapter & Topic to exam_chapters & exam_topics tables
     */
    protected function syncChapterAndTopic($classTypeId, $subjectId, $chapterName, $topicName = null, $userId = null, $branchId = null, $sessionId = null, $chapterId = null, $topicId = null)
    {
        $chapterName = trim($chapterName ?? '');
        $topicName = trim($topicName ?? '');

        if (empty($classTypeId) || empty($subjectId)) {
            return null;
        }

        $userId = $userId ?: (Session::get('id') ?: 1);
        $branchId = $branchId ?: (Session::get('branch_id') ?: 1);
        $sessionId = $sessionId ?: (Session::get('session_id') ?: 1);

        $chapId = $chapterId;
        if (!empty($chapId)) {
            $chapter = DB::table('exam_chapters')->where('id', $chapId)->first();
            if ($chapter && empty($chapterName)) {
                $chapterName = $chapter->name;
            }
        } elseif (!empty($chapterName)) {
            $matchingSubjectIds = [(int)$subjectId];
            $subjName = DB::table('subject')->where('id', $subjectId)->value('name');
            if (!empty($subjName)) {
                $allIds = DB::table('subject')->where('name', $subjName)->whereNull('deleted_at')->pluck('id')->toArray();
                if (!empty($allIds)) {
                    $matchingSubjectIds = array_unique(array_merge($matchingSubjectIds, $allIds));
                }
            }

            $chapter = DB::table('exam_chapters')
                ->whereIn('subject_id', $matchingSubjectIds)
                ->where('name', $chapterName)
                ->first();

            if (!$chapter) {
                $chapId = DB::table('exam_chapters')->insertGetId([
                    'user_id' => $userId,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'class_type_id' => $classTypeId,
                    'subject_id' => $subjectId,
                    'name' => $chapterName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $chapId = $chapter->id;
            }
        }

        $topId = $topicId;
        if (!empty($topId)) {
            $topic = DB::table('exam_topics')->where('id', $topId)->first();
            if ($topic && empty($topicName)) {
                $topicName = $topic->name;
            }
        } elseif (!empty($topicName)) {
            $topicQuery = DB::table('exam_topics')->where([
                'class_type_id' => $classTypeId,
                'subject_id' => $subjectId,
                'name' => $topicName,
            ]);
            if ($chapId) {
                $topicQuery->where(function($q) use ($chapId) {
                    $q->where('chapter_id', $chapId)->orWhereNull('chapter_id');
                });
            }
            $topic = $topicQuery->first();

            if (!$topic) {
                $topId = DB::table('exam_topics')->insertGetId([
                    'user_id' => $userId,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'chapter_id' => $chapId,
                    'class_type_id' => $classTypeId,
                    'subject_id' => $subjectId,
                    'name' => $topicName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $topId = $topic->id;
            }
        }

        return [
            'chapter_id' => $chapId,
            'chapter' => $chapterName,
            'topic_id' => $topId,
            'topic' => $topicName
        ];
    }

    /**
     * Serve Question Diagram / Image securely with mime resolution and multi-directory fallback.
     * Ensures images load regardless of whether Apache DocumentRoot is public/ or the base directory.
     */
    public function serveQuestionImage($filename)
    {
        $filename = basename($filename);
        $candidates = [
            base_path('uploads/questions/' . $filename),
            public_path('uploads/questions/' . $filename),
            base_path('schoolimage/questions/' . $filename),
            public_path('schoolimage/questions/' . $filename),
        ];
        if (env('IMAGE_UPLOAD_PATH')) {
            $candidates[] = rtrim(env('IMAGE_UPLOAD_PATH'), '/\\') . DIRECTORY_SEPARATOR . 'questions' . DIRECTORY_SEPARATOR . $filename;
        }

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_readable($cand)) {
                $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                $mimes = [
                    'svg' => 'image/svg+xml; charset=utf-8',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'webp' => 'image/webp',
                    'gif' => 'image/gif',
                    'pdf' => 'application/pdf',
                ];
                $mime = $mimes[$ext] ?? 'application/octet-stream';
                return response()->file($cand, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=604800',
                ]);
            }
        }

        abort(404, 'Question diagram / image not found.');
    }

    /**
     * Mirror save an uploaded file or raw content into all candidate directories
     */
    protected function mirrorSaveQuestionFile(string $filename, $contentOrFile, bool $isRawContent = false): string
    {
        $targetDirs = [
            public_path('uploads/questions'),
            base_path('uploads/questions'),
            public_path('schoolimage/questions'),
            base_path('schoolimage/questions'),
        ];
        if (env('IMAGE_UPLOAD_PATH')) {
            $targetDirs[] = rtrim(env('IMAGE_UPLOAD_PATH'), '/\\') . DIRECTORY_SEPARATOR . 'questions';
        }

        foreach ($targetDirs as $d) {
            if (!file_exists($d)) {
                @mkdir($d, 0777, true);
            }
            $targetPath = $d . DIRECTORY_SEPARATOR . $filename;
            if ($isRawContent) {
                @file_put_contents($targetPath, $contentOrFile);
            } elseif ($contentOrFile instanceof \Illuminate\Http\UploadedFile) {
                @copy($contentOrFile->getRealPath(), $targetPath);
            } elseif (is_string($contentOrFile) && file_exists($contentOrFile)) {
                @copy($contentOrFile, $targetPath);
            }
        }

        return 'uploads/questions/' . $filename;
    }
}
