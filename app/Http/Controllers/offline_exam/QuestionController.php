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
            'subjects.name as subject_name',
            'class_types.name as class_name'
        )
        ->leftJoin('subject as subjects', 'subjects.id', '=', 'questions.subject_id')
        ->leftJoin('class_types', 'class_types.id', '=', 'questions.class_type_id');

        if (Session::get('role_id') > 1) {
            $data = $data->where('questions.branch_id', Session::get('branch_id'));
        }

        // 1. Keyword search (statement, explanation, direction, group_id)
        if (!empty($request->name)) {
            $kw = '%' . trim($request->name) . '%';
            $data = $data->where(function($q) use ($kw) {
                $q->where('questions.name', 'like', $kw)
                  ->orWhere('questions.explanation', 'like', $kw)
                  ->orWhere('questions.direction', 'like', $kw)
                  ->orWhere('questions.group_id', 'like', $kw);
            });
        }

        // 2. Class Type filter
        if (!empty($request->class_type_id)) {
            $data = $data->where('questions.class_type_id', $request->class_type_id);
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
                    'direction' => 'required',
                ]);

                $groupId = trim($request->input('group_id') ?: ('DIR-' . date('Ymd-His') . '-' . strtoupper(Str::random(4))));
                $direction = $request->input('direction');
                $directionTable = $request->input('direction_table');

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
                    $dirImagePath = 'uploads/questions/' . $filename;
                } elseif ($request->filled('direction_image_path')) {
                    $dirImagePath = $request->input('direction_image_path');
                }

                $subQuestions = $request->input('directional_questions', []);
                if (empty($subQuestions) || !is_array($subQuestions)) {
                    return redirect()->back()->with('error', 'Please add at least one sub-question to the directional set.')->withInput();
                }

                $userId = Session::get('id') ?: 1;
                $sessionId = Session::get('session_id') ?: 1;
                $branchId = Session::get('branch_id') ?: 1;
                $classTypeId = (int)$request->class_type_id;
                $subjectId = (int)$request->subject_id;
                $now = Carbon::now();

                DB::beginTransaction();
                try {
                    $savedCount = 0;
                    foreach ($subQuestions as $idx => $subQ) {
                        $qName = trim($subQ['name'] ?? '');
                        if (empty($qName)) {
                            continue;
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
                        $add->name = $qName;
                        $add->question_type_id = isset($subQ['question_type_id']) ? (int)$subQ['question_type_id'] : 1;
                        $add->ans1 = $subQ['ans1'] ?? null;
                        $add->ans2 = $subQ['ans2'] ?? null;
                        $add->ans3 = $subQ['ans3'] ?? null;
                        $add->ans4 = $subQ['ans4'] ?? null;
                        $add->ans5 = $subQ['ans5'] ?? null;
                        $add->correct_ans = isset($subQ['correct_ans']) ? (string)$subQ['correct_ans'] : '0';
                        $add->group_id = $groupId;
                        $add->direction = $direction;
                        $add->direction_table = $directionTable;
                        $add->sub_question_no = !empty($subQ['sub_question_no']) ? $subQ['sub_question_no'] : ($idx + 1);
                        $add->explanation = $subQ['explanation'] ?? null;
                        $add->difficulty_level = $subQ['difficulty_level'] ?? 'Medium';
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
                            ->withInput($request->only('class_type_id', 'subject_id'))
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
                'name' => 'required',
                'question_type_id' => 'required',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'q_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/questions');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $filename);
                $imagePath = 'uploads/questions/' . $filename;
            } elseif ($request->filled('image_path')) {
                $imagePath = $request->image_path;
            }

            $add = new Question;
            $add->user_id = Session::get('id') ?: 1;
            $add->session_id = Session::get('session_id') ?: 1;
            $add->branch_id = Session::get('branch_id') ?: 1;
            $add->class_type_id = $request->class_type_id;
            $add->subject_id = $request->subject_id;
            $add->name = $request->name;
            $add->question_type_id = (int)$request->question_type_id;
            $add->ans1 = $request->ans1;
            $add->ans2 = $request->ans2;
            $add->ans3 = $request->ans3;
            $add->ans4 = $request->ans4;
            $add->ans5 = $request->ans5;
            $add->group_id = $request->group_id;
            $add->direction = $request->direction;
            $add->direction_table = $request->direction_table;
            $add->sub_question_no = $request->sub_question_no;
            $add->correct_ans = $request->correct_ans;
            $add->explanation = $request->explanation;
            $add->difficulty_level = $request->difficulty_level ?: 'Medium';
            $add->image = $imagePath;
            $add->ai_extracted = (bool)$request->input('ai_extracted', false);
            $add->save();

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
                    ->withInput($request->only('class_type_id', 'subject_id', 'question_type_id'))
                    ->with('message', 'Question added successfully! You can now add the next question.');
            }

            return redirect('view/question')->with('message', 'Question added successfully.');
        }

        $classType = Helper::classType();
        $allSubjects = Helper::getSubject();
        $geminiApiKey = $this->aiService->getApiKey();

        return view('examination.offline_exam.question_bank.add', compact('classType', 'allSubjects', 'geminiApiKey'));
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
            $className = null;
            $subjectName = null;

            if (!empty($classTypeId)) {
                $className = DB::table('class_types')->where('id', $classTypeId)->value('name');
            }
            if (!empty($subjectId)) {
                $subjectName = DB::table('subject')->where('id', $subjectId)->value('name');
            }

            $context = [
                'class_name' => $className,
                'subject_name' => $subjectName,
                'difficulty' => $request->input('difficulty', 'Mixed'),
                'options_count' => (int)$request->input('options_count', 4),
            ];

            $result = $this->aiService->generateQuestionsWithAi($userPrompt, $filePath, $customKey, $context);

            // Clean up temporary file
            if ($filePath && file_exists($filePath)) {
                @unlink($filePath);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            if ($filePath && file_exists($filePath)) {
                @unlink($filePath);
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
                $add->question_type_id = isset($q['question_type_id']) ? (int)$q['question_type_id'] : 1;
                $add->ans1 = $ans1;
                $add->ans2 = $ans2;
                $add->ans3 = $ans3;
                $add->ans4 = $ans4;
                $add->ans5 = $ans5;
                $add->group_id = $q['group_id'] ?? null;
                $add->direction = $direction;
                $add->direction_table = $q['direction_table'] ?? null;
                $add->sub_question_no = $q['sub_question_no'] ?? null;
                $add->correct_ans = isset($q['correct_ans']) ? (string)$q['correct_ans'] : null;
                $add->explanation = $explanation;
                $add->difficulty_level = $q['difficulty_level'] ?? 'Medium';
                $add->image = $q['image'] ?? null;
                $add->source_file = $q['source_file'] ?? null;
                $add->ai_extracted = true;
                $add->created_at = $now;
                $add->updated_at = $now;
                $add->save();

                $savedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'count' => $savedCount,
                'message' => "Successfully imported {$savedCount} questions into Question Bank!",
                'redirect' => url('view/question')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
                    'direction' => 'required',
                ]);

                $groupId = trim($request->input('group_id') ?: ($data->group_id ?: ('DIR-' . date('Ymd-His') . '-' . strtoupper(Str::random(4)))));
                $direction = $normalizeField($request->input('direction'));
                $directionTable = $request->input('direction_table');

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

                $userId = Session::get('id') ?: 1;
                $sessionId = Session::get('session_id') ?: 1;
                $branchId = Session::get('branch_id') ?: 1;
                $classTypeId = (int)$request->class_type_id;
                $subjectId = (int)$request->subject_id;
                $now = Carbon::now();

                DB::beginTransaction();
                try {
                    $savedCount = 0;
                    $retainedIds = [];

                    foreach ($subQuestions as $idx => $subQ) {
                        $qName = $normalizeField($subQ['name'] ?? '');
                        if (empty($qName)) {
                            continue;
                        }

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
                        $qItem->name = $qName;
                        $qItem->question_type_id = isset($subQ['question_type_id']) ? (int)$subQ['question_type_id'] : 1;
                        $qItem->ans1 = $normalizeField($subQ['ans1'] ?? null);
                        $qItem->ans2 = $normalizeField($subQ['ans2'] ?? null);
                        $qItem->ans3 = $normalizeField($subQ['ans3'] ?? null);
                        $qItem->ans4 = $normalizeField($subQ['ans4'] ?? null);
                        $qItem->ans5 = $normalizeField($subQ['ans5'] ?? null);
                        $qItem->correct_ans = isset($subQ['correct_ans']) ? (string)$subQ['correct_ans'] : '0';
                        $qItem->group_id = $groupId;
                        $qItem->direction = $direction;
                        $qItem->direction_table = $directionTable;
                        $qItem->sub_question_no = !empty($subQ['sub_question_no']) ? $subQ['sub_question_no'] : ($idx + 1);
                        $qItem->explanation = $normalizeField($subQ['explanation'] ?? null);
                        $qItem->difficulty_level = $subQ['difficulty_level'] ?? 'Medium';
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
                'name' => 'required',
                'question_type_id' => 'required',
            ]);

            $imagePath = $data->image;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'q_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/questions');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $filename);
                $imagePath = 'uploads/questions/' . $filename;
            } elseif ($request->filled('image_path')) {
                $imagePath = $request->image_path;
            } elseif ($request->has('remove_image')) {
                $imagePath = null;
            }

            $data->class_type_id = (int)$request->class_type_id;
            $data->subject_id = (int)$request->subject_id;
            $data->name = $normalizeField($request->name);
            $data->question_type_id = (int)$request->question_type_id;
            $data->ans1 = $normalizeField($request->ans1);
            $data->ans2 = $normalizeField($request->ans2);
            $data->ans3 = $normalizeField($request->ans3);
            $data->ans4 = $normalizeField($request->ans4);
            $data->ans5 = $normalizeField($request->ans5);
            $data->correct_ans = (string)$request->correct_ans;
            $data->explanation = $normalizeField($request->explanation);
            $data->difficulty_level = $request->difficulty_level ?: 'Medium';
            $data->image = $imagePath;
            $data->save();

            if (!empty($data->group_id)) {
                return redirect('view/question?group_filter=' . urlencode($data->group_id))
                    ->with('message', 'Question updated successfully.');
            }

            return redirect('view/question')->with('message', 'Question updated successfully.');
        }

        $classType = Helper::classType();
        $allSubjects = Helper::getSubject();
        $geminiApiKey = $this->aiService->getApiKey();
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
            'directionalQuestions'
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
}
