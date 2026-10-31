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
        $search['name'] = $request->name;
        $search['subject_id'] = $request->subject_id;
        $search['class_type_id'] = $request->class_type_id;
        $search['question_type_id'] = $request->question_type_id;

        $data = Question::select(
            'questions.*',
            'subjects.name as subject_name',
            'class_types.name as class_name'
        )
        ->leftJoin('subject as subjects', 'subjects.id', '=', 'questions.subject_id')
        ->leftJoin('class_types', 'class_types.id', '=', 'questions.class_type_id');

        if (Session::get('role_id') > 1) {
            $data = $data->where('questions.branch_id', Session::get('branch_id'));
        }

        if ($request->isMethod('post') || $request->filled('filter')) {
            if (!empty($request->name)) {
                $data = $data->where("questions.name", 'like', '%' . $request->name . '%');
            }
            if (!empty($request->class_type_id)) {
                $data = $data->where("questions.class_type_id", $request->class_type_id);
            }
            if (!empty($request->subject_id)) {
                $data = $data->where("questions.subject_id", $request->subject_id);
            }
            if (!empty($request->question_type_id)) {
                $data = $data->where("questions.question_type_id", $request->question_type_id);
            }
        }

        $data = $data->orderBy('questions.id', 'DESC')->paginate(25);
        $classType = Helper::classType();
        $allSubjects = Helper::getSubject();

        return view('examination.offline_exam.question_bank.view', [
            'data' => $data,
            'search' => $search,
            'classType' => $classType,
            'allSubjects' => $allSubjects
        ]);
    }

    /**
     * Create / Add Question (Multi-Mode: Manual, AI Single Image, AI Bulk PDF)
     */
    public function addQuestion(Request $request)
    {
        if ($request->isMethod('post')) {
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
            $add->correct_ans = $request->correct_ans;
            $add->explanation = $request->explanation;
            $add->difficulty_level = $request->difficulty_level ?: 'Medium';
            $add->marks = $request->filled('marks') ? (float)$request->marks : 1.00;
            $add->negative_marks = $request->filled('negative_marks') ? (float)$request->negative_marks : 0.00;
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
                $result['data']['image_path'] = 'uploads/questions/' . $filename;
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
        try {
            $request->validate([
                'pdf_file' => 'required|file|mimes:pdf|max:20480', // up to 20MB
            ]);

            $customKey = $request->input('api_key');
            $extraPrompt = $request->input('extra_instructions', '');
            $file = $request->file('pdf_file');

            $tempName = 'temp_exam_' . time() . '_' . Str::random(8) . '.pdf';
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
            foreach ($questionsData as $q) {
                if (empty($q['name'])) {
                    continue;
                }

                $add = new Question;
                $add->user_id = $userId;
                $add->session_id = $sessionId;
                $add->branch_id = $branchId;
                $add->class_type_id = $classTypeId;
                $add->subject_id = $subjectId;
                $add->name = $q['name'];
                $add->question_type_id = isset($q['question_type_id']) ? (int)$q['question_type_id'] : 1;
                $add->ans1 = $q['ans1'] ?? null;
                $add->ans2 = $q['ans2'] ?? null;
                $add->ans3 = $q['ans3'] ?? null;
                $add->ans4 = $q['ans4'] ?? null;
                $add->correct_ans = isset($q['correct_ans']) ? (string)$q['correct_ans'] : null;
                $add->explanation = $q['explanation'] ?? null;
                $add->difficulty_level = $q['difficulty_level'] ?? 'Medium';
                $add->marks = isset($q['marks']) ? (float)$q['marks'] : 1.00;
                $add->negative_marks = isset($q['negative_marks']) ? (float)$q['negative_marks'] : 0.00;
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

            return response()->json([
                'success' => true,
                'message' => 'Google Gemini API key validated and saved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to persist API key: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit Question
     */
    public function editQuestion(Request $request, $id)
    {
        $data = Question::find($id);
        if (!$data) {
            return redirect('view/question')->with('error', 'Question not found.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'class_type_id' => 'required',
                'subject_id' => 'required',
                'name' => 'required',
                'question_type_id' => 'required',
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'q_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/questions');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                $file->move($destPath, $filename);
                $data->image = 'uploads/questions/' . $filename;
            }

            $data->class_type_id = $request->class_type_id;
            $data->subject_id = $request->subject_id;
            $data->name = $request->name;
            $data->question_type_id = (int)$request->question_type_id;
            $data->ans1 = $request->ans1;
            $data->ans2 = $request->ans2;
            $data->ans3 = $request->ans3;
            $data->ans4 = $request->ans4;
            $data->correct_ans = $request->correct_ans;
            $data->explanation = $request->explanation;
            $data->difficulty_level = $request->difficulty_level ?: 'Medium';
            $data->marks = $request->filled('marks') ? (float)$request->marks : 1.00;
            $data->negative_marks = $request->filled('negative_marks') ? (float)$request->negative_marks : 0.00;
            $data->save();

            return redirect('view/question')->with('message', 'Question updated successfully.');
        }

        $classType = Helper::classType();
        $allSubjects = Helper::getSubject();

        return view('examination.offline_exam.question_bank.edit', [
            'data' => $data,
            'classType' => $classType,
            'allSubjects' => $allSubjects
        ]);
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
