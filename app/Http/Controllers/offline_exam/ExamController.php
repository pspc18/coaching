<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\AssignQuestion;
use App\Models\Subject;
use App\Models\Admission;
use App\Models\OmrResult;
use App\Models\exam\FillMarks;
use App\Models\exam\FillMinMaxMarks;
use App\Models\CourseSubject;
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
use Illuminate\Support\Facades\Http;
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
        $batchId = $request->input('batch_id');
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
            $query->where('exams.class_type_id', $classTypeId);
        }

        if (!empty($batchId)) {
            $query->where('exams.batch_id', $batchId);
        }

        if ($status === 'published' && Schema::hasTable('exam_result_publications')) {
            $query->whereExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('exam_result_publications')
                  ->whereColumn('exam_result_publications.exam_id', 'exams.id')
                  ->where('exam_result_publications.session_id', $sessionId)
                  ->where('exam_result_publications.branch_id', $branchId);
            });
        } elseif ($status === 'pending' && Schema::hasTable('exam_result_publications')) {
            $query->whereNotExists(function($q) use ($sessionId, $branchId) {
                $q->select(DB::raw(1))
                  ->from('exam_result_publications')
                  ->whereColumn('exam_result_publications.exam_id', 'exams.id')
                  ->where('exam_result_publications.session_id', $sessionId)
                  ->where('exam_result_publications.branch_id', $branchId);
            });
        }

        // Total Count for Pagination
        $totalCount = $query->count('exams.id');
        $lastPage = $perPage > 0 ? (int) ceil($totalCount / $perPage) : 1;
        if ($page > $lastPage && $lastPage > 0) {
            $page = $lastPage;
        }
        $offset = ($page - 1) * $perPage;

        // Fetch ONLY Requested Page Slice with joined Class & Batch
        $data = $query->leftJoin('class_types', 'class_types.id', '=', 'exams.class_type_id')
            ->leftJoin('batches', 'batches.id', '=', 'exams.batch_id')
            ->select(
                'exams.id',
                'exams.name',
                'exams.code',
                'exams.exam_code',
                'exams.class_type_id',
                'exams.course_id',
                'exams.batch_id',
                'exams.exam_date',
                'exams.subject_id',
                'exams.subject_ids',
                'exams.number_of_questions',
                'exams.exam_maximum_marks',
                'exams.exam_term_id',
                'exams.created_at',
                'class_types.name as class_name',
                'batches.name as batch_name',
                'batches.shift as batch_shift',
                DB::raw("NULL as exam_term_name")
            )
            ->orderBy('exams.id', 'DESC')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $examIds = $data->pluck('id')->toArray();
        $examNames = $data->pluck('name');
        $allSubjects = \App\Models\CourseSubject::pluck('subject_name', 'id')->toArray() + \App\Models\CourseSubject::pluck('subject_name', 'subject_id')->toArray() + Subject::pluck('name', 'id')->toArray();

        // Batch load result publication statuses only for current page slice
        $publishedSet = [];
        if (!empty($examIds) && Schema::hasTable('exam_result_publications')) {
            $publishedRecords = DB::table('exam_result_publications')
                ->whereIn('exam_id', $examIds)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->select('exam_id', 'class_type_id', 'published_at')
                ->get();

            foreach ($publishedRecords as $pr) {
                $rawDate = $pr->published_at;
                $publishedSet[$pr->exam_id] = [
                    'is_published' => true,
                    'published_at' => $rawDate ? Carbon::parse($rawDate)->format('d M Y') : 'Published',
                    'published_date_iso' => $rawDate ? Carbon::parse($rawDate)->format('Y-m-d') : null,
                ];
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

            // Result publication status
            $pubInfo = $publishedSet[$exam->id] ?? null;
            $exam->is_published = !empty($pubInfo);
            $exam->published_at = $pubInfo['published_at'] ?? null;
            $exam->published_date_iso = $pubInfo['published_date_iso'] ?? null;

            // Subjects list
            $subIds = $exam->selected_subject_ids;
            $subNames = [];
            foreach ($subIds as $sId) {
                if (isset($allSubjects[$sId])) {
                    $subNames[] = $allSubjects[$sId];
                }
            }
            $exam->subject_names = $subNames;
        }

        // Overall Global KPI Stats
        $statsTotalExams = Exam::where('session_id', $sessionId)->where('branch_id', $branchId)->whereNull('deleted_at')->count();
        $statsPublished = 0;
        if (Schema::hasTable('exam_result_publications')) {
            $statsPublished = DB::table('exam_result_publications')->where('session_id', $sessionId)->where('branch_id', $branchId)->count();
        }
        $statsPending = max(0, $statsTotalExams - $statsPublished);

        $stats = [
            'total' => $statsTotalExams,
            'assigned' => $statsTotalExams,
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
            'search' => ['name' => $searchName, 'term_id' => $termId, 'class_type_id' => $classTypeId, 'batch_id' => $batchId, 'status' => $status],
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'perPage' => $perPageInput,
            'lastPage' => $lastPage,
            'stats' => $stats,
            'classType' => Helper::classType(),
            'batches' => !empty($classTypeId) ? DB::table('batches')->where('course_id', $classTypeId)->where('status', 1)->orderBy('orderBy', 'asc')->get() : collect([]),
            'examTerms' => collect([])
        ]);
    }

    public function uploadOmr(Request $request)
    {
        $classTypeId = (int) ($request->input('class_type_id') ?: $request->input('class_name', 0));
        $examId = (int) $request->input('exam_id', 0);
        $batchIdInput = $request->input('batch_id');
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        // If AJAX request or extract payload sent to upload_omr, delegate to processOmrSheet
        if ($request->isMethod('post') && ($request->ajax() || $request->expectsJson() || $request->hasFile('image'))) {
            return $this->processOmrSheet($request);
        }

        // Auto-detect class_type_id from exam_id if not explicitly provided
        if (empty($classTypeId) && !empty($examId)) {
            $examLookup = Exam::find($examId);
            if ($examLookup && !empty($examLookup->class_type_id)) {
                $classTypeId = (int) $examLookup->class_type_id;
            } else {
                $assignLookup = AssignExam::where('exam_id', $examId)->whereNull('deleted_at')->first();
                if ($assignLookup && !empty($assignLookup->class_type_id)) {
                    $classTypeId = (int) $assignLookup->class_type_id;
                }
            }
        }

        $classType = Helper::classType();
        $selectedExam = null;

        if ($examId > 0) {
            $selectedExam = Exam::with(['classType', 'batch'])->where('id', $examId)->whereNull('deleted_at')->first();
            if ($selectedExam && empty($classTypeId)) {
                $classTypeId = (int) $selectedExam->class_type_id;
            }
        }

        // Batches for the selected Course / Class
        $batches = collect();
        if ($classTypeId > 0) {
            $batches = DB::table('batches')
                ->where('class_type_id', $classTypeId)
                ->whereNull('deleted_at')
                ->orderBy('name', 'asc')
                ->get();
        }

        // Determine active batch selection
        $selectedBatchId = 0; // 0 = All Batches
        if ($batchIdInput !== null && $batchIdInput !== '') {
            $selectedBatchId = ($batchIdInput === 'all' || (int)$batchIdInput === 0) ? 0 : (int)$batchIdInput;
        } elseif ($selectedExam && !empty($selectedExam->batch_id)) {
            $selectedBatchId = (int) $selectedExam->batch_id;
        }

        // Fetch all exams for dropdown
        $allExamsQuery = Exam::with(['classType', 'batch'])
            ->whereNull('deleted_at')
            ->withCount('results')
            ->orderBy('id', 'desc');
        if ($branchId) {
            $allExamsQuery->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }
        if ($classTypeId > 0) {
            $allExamsQuery->where(function($q) use ($classTypeId) {
                $q->where('class_type_id', $classTypeId)->orWhereNull('class_type_id');
            });
        }
        $allExams = $allExamsQuery->get();

        // Fetch enrolled class students & any existing marks/evaluations
        $students = collect([]);
        $existingMarksMap = collect();
        $existingOmrMap = collect();

        if ($classTypeId > 0) {
            $studentsQuery = Admission::with(['classType', 'batch'])
                ->where('class_type_id', $classTypeId)
                ->whereNull('deleted_at');
            if ($branchId) {
                $studentsQuery->where(function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)->orWhereNull('branch_id');
                });
            }
            if (!empty($selectedBatchId)) {
                $studentsQuery->where('batch_id', $selectedBatchId);
            }
            $students = $studentsQuery
                ->orderByRaw('CASE WHEN roll_no IS NOT NULL AND roll_no != "" THEN 0 ELSE 1 END, CAST(roll_no AS UNSIGNED) ASC, CAST(admissionNo AS UNSIGNED) ASC, first_name ASC')
                ->get();

            if ($examId > 0 && $students->isNotEmpty()) {
                $studentIds = $students->pluck('id')->toArray();

                $existingMarksMap = DB::table('fill_marks')
                    ->where('exam_id', $examId)
                    ->whereIn('admission_id', $studentIds)
                    ->whereNull('deleted_at')
                    ->get()
                    ->keyBy('admission_id');

                $existingOmrMap = OmrResult::where('exam_id', $examId)
                    ->whereIn('admission_id', $studentIds)
                    ->get()
                    ->keyBy('admission_id');
            }
        }

        // Metrics & KPI calculations
        $totalScannedToday = OmrResult::whereDate('created_at', Carbon::today())->count();
        $avgAccuracy = round((float) (OmrResult::whereDate('created_at', Carbon::today())->avg('percentage') ?? 0), 1);
        $totalEvaluatedCount = OmrResult::count();

        // Recent Scans Queue
        $recentScans = OmrResult::with(['exam', 'student'])
            ->when($examId > 0, function($q) use ($examId) {
                $q->where('exam_id', $examId);
            })
            ->latest('id')
            ->take(25)
            ->get();

        return Helper::view('examination.offline_exam.omr.upload_omr', [
            'classType' => $classType,
            'batches' => $batches,
            'selectedBatchId' => $selectedBatchId,
            'allExams' => $allExams,
            'selectedExam' => $selectedExam,
            'students' => $students,
            'existingMarksMap' => $existingMarksMap,
            'existingOmrMap' => $existingOmrMap,
            'recentScans' => $recentScans,
            'totalScannedToday' => $totalScannedToday,
            'avgAccuracy' => $avgAccuracy,
            'totalEvaluatedCount' => $totalEvaluatedCount,
            'search' => [
                'class_type_id' => $classTypeId,
                'batch_id' => $selectedBatchId,
                'exam_id' => $examId,
            ],
        ]);
    }

    /**
     * Backward-compatible alias for processOmrSheet
     */
    public function extractOmr(Request $request)
    {
        return $this->processOmrSheet($request);
    }

    /**
     * Step 1: Process uploaded OMR sheet through AI Extractor API & evaluate against Exam key.
     * Staging ONLY: Does NOT save to database yet!
     */
    public function processOmrSheet(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|integer',
            'image' => 'required|file|image|max:25600',
        ]);

        $exam = Exam::where('id', $validated['exam_id'])->whereNull('deleted_at')->firstOrFail();

        $imageFile = $request->file('image');
        $originalFileName = $imageFile->getClientOriginalName();
        $tempPath = $imageFile->getRealPath();

        // 1. Call AI Dynamic Extractor API (https://extractor.rusoft.in/api/omr/extract)
        // Memory-efficient streaming: The raw input image is streamed directly from PHP temp memory.
        // It is NEVER saved to public disk to minimize storage consumption.
        $extractorBaseUrl = rtrim(config('services.extractor.url', 'https://extractor.rusoft.in'), '/');
        $extractorTimeout = (int) config('services.extractor.timeout', 60);

        $extractorData = null;
        $apiError = null;

        try {
            $fileStream = file_get_contents($tempPath);
            $response = Http::timeout($extractorTimeout)
                ->attach('file', $fileStream, $originalFileName)
                ->post($extractorBaseUrl . '/api/omr/extract');

            if ($response->successful()) {
                $extractorData = $response->json();
            } else {
                // Try fallback route /extract
                $fallbackResp = Http::timeout($extractorTimeout)
                    ->attach('file', $fileStream, $originalFileName)
                    ->post($extractorBaseUrl . '/extract');

                if ($fallbackResp->successful()) {
                    $extractorData = $fallbackResp->json();
                } else {
                    $apiError = 'Extractor API responded with HTTP ' . $response->status() . ': ' . Str::limit($response->body(), 120);
                }
            }
        } catch (\Exception $e) {
            $apiError = 'Connection to Extractor API failed: ' . $e->getMessage();
        }

        if (!$extractorData || empty($extractorData['success'])) {
            return response()->json([
                'status' => 'error',
                'message' => $apiError ?: ($extractorData['detail'] ?? 'OMR extraction could not be completed by extractor engine.'),
                'source_filename' => $originalFileName,
            ], 422);
        }

        // 2. Roll Number Resolution
        $rollNumber = trim((string)($extractorData['roll_number'] ?? ''));
        if (in_array(strtolower($rollNumber), ['', '------', '000000', '00000', 'null', 'undefined', 'auto'])) {
            $rollNumber = '';
        }

        // Fallback: extract candidate digits/patterns from filename (e.g. STU-1002 or 927401)
        if (empty($rollNumber)) {
            if (preg_match('/\b(STU[-_]?\d+|\d{3,8})\b/i', $originalFileName, $matches)) {
                $rollNumber = strtoupper(str_replace('_', '-', $matches[1]));
            }
        }

        // 3. Student Resolution from admissions table
        $student = $this->resolveStudentByRoll($rollNumber, (int)$exam->class_type_id);
        $studentData = $this->formatStudentData($student, $rollNumber);

        // 4. Answers Evaluation against Exam Answer Key
        $passedAnswers = $extractorData['answers'] ?? [];

        // Dynamically pick questions count from API response first, fallback to exam setting
        $dynamicQuestionsCount = 0;
        if (!empty($extractorData['total_questions']) && is_numeric($extractorData['total_questions'])) {
            $dynamicQuestionsCount = (int) $extractorData['total_questions'];
        } elseif (!empty($extractorData['summary']['total_questions']) && is_numeric($extractorData['summary']['total_questions'])) {
            $dynamicQuestionsCount = (int) $extractorData['summary']['total_questions'];
        } elseif (!empty($passedAnswers) && is_array($passedAnswers)) {
            $dynamicQuestionsCount = count($passedAnswers);
        }

        $totalQuestions = $dynamicQuestionsCount > 0 ? $dynamicQuestionsCount : (int) ($exam->number_of_questions ?: 50);

        $rawKey = $exam->answer_key;
        if (is_string($rawKey)) {
            $rawKey = json_decode($rawKey, true) ?: [];
        }
        $answerKey = is_array($rawKey) ? $rawKey : [];

        $marksPerCorrect = (float) ($exam->marks_per_correct ?? 1.0);
        $marksPerIncorrect = (float) ($exam->marks_per_incorrect ?? 0.0);
        if ($marksPerCorrect <= 0) $marksPerCorrect = 1.0;

        $evalResult = $this->evaluateAnswersAgainstKey($passedAnswers, $answerKey, $totalQuestions, $marksPerCorrect, $marksPerIncorrect, $exam);

        $summary = $extractorData['summary'] ?? [];

        // Save ONLY the annotated image to server disk (compressed & optimized for minimal storage)
        $annotatedStoredFile = null;
        $annotatedImageUrl = null;

        $rawB64 = $extractorData['annotated_image_base64'] ?? '';
        if (!empty($rawB64)) {
            if (strpos($rawB64, 'base64,') !== false) {
                $rawB64 = explode('base64,', $rawB64)[1];
            }
            $decodedImg = base64_decode($rawB64);
            if ($decodedImg !== false) {
                $annotatedStoredFile = $this->saveOptimizedAnnotatedImage($decodedImg);
                $annotatedImageUrl = asset($annotatedStoredFile);
            }
        } elseif (!empty($extractorData['annotated_image_url'])) {
            $remoteUrl = $extractorData['annotated_image_url'];
            if (!str_starts_with($remoteUrl, 'http://') && !str_starts_with($remoteUrl, 'https://')) {
                $remoteUrl = $extractorBaseUrl . '/' . ltrim($remoteUrl, '/');
            }
            try {
                $imgResp = Http::timeout(15)->get($remoteUrl);
                if ($imgResp->successful()) {
                    $annotatedStoredFile = $this->saveOptimizedAnnotatedImage($imgResp->body());
                    $annotatedImageUrl = asset($annotatedStoredFile);
                } else {
                    $annotatedImageUrl = $remoteUrl;
                }
            } catch (\Exception $ex) {
                $annotatedImageUrl = $remoteUrl;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OMR sheet extracted and evaluated successfully.',
            'sheet' => [
                'temp_id' => 'staged_' . Str::random(12),
                'source_filename' => $originalFileName,
                'stored_file' => $annotatedStoredFile,
                'image_url' => $annotatedImageUrl,
                'annotated_stored_file' => $annotatedStoredFile,
                'annotated_image_url' => $annotatedImageUrl,
                'annotated_image_base64' => $extractorData['annotated_image_base64'] ?? null,
                'roll_number' => $rollNumber ?: ($student ? ($student->roll_no ?: $student->admissionNo) : ''),
                'organization' => $extractorData['organization'] ?? null,
                'exam_name' => $extractorData['exam_name'] ?? null,
                'exam_code' => $extractorData['exam_code'] ?? null,
                'student' => $studentData,
                'summary' => [
                    'total_questions' => $totalQuestions,
                    'attempted' => $evalResult['correct_answers'] + $evalResult['incorrect_answers'],
                    'unattempted' => $evalResult['blank_answers'],
                    'multiple_marked' => (int) ($summary['multiple_marked'] ?? 0),
                ],
                'evaluation' => $evalResult,
            ]
        ]);
    }

    /**
     * Compress and save annotated OMR image with optimal dimensions and JPEG quality
     * to minimize server disk storage while ensuring bubble detections remain razor-sharp.
     */
    protected function saveOptimizedAnnotatedImage($rawImageBytes, $destDirectory = 'uploads/omr_scans')
    {
        $destPath = public_path($destDirectory);
        if (!file_exists($destPath)) {
            @mkdir($destPath, 0777, true);
        }

        $filename = 'annotated_' . time() . '_' . Str::random(8) . '.jpeg';
        $fullPath = $destPath . DIRECTORY_SEPARATOR . $filename;
        $relPath = $destDirectory . '/' . $filename;

        if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
            $src = @imagecreatefromstring($rawImageBytes);
            if ($src !== false) {
                $w = imagesx($src);
                $h = imagesy($src);
                $maxDim = 1400; // Optimal for sharp bubble readability + compact storage

                if ($w > $maxDim || $h > $maxDim) {
                    if ($w >= $h) {
                        $newW = $maxDim;
                        $newH = (int) round(($h / $w) * $maxDim);
                    } else {
                        $newH = $maxDim;
                        $newW = (int) round(($w / $h) * $maxDim);
                    }
                    $dst = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    imagejpeg($dst, $fullPath, 80); // 80% JPEG quality
                    imagedestroy($dst);
                    imagedestroy($src);
                    return $relPath;
                } else {
                    imagejpeg($src, $fullPath, 80);
                    imagedestroy($src);
                    return $relPath;
                }
            }
        }

        // Direct write fallback
        @file_put_contents($fullPath, $rawImageBytes);
        return $relPath;
    }

    /**
     * Resolve student by roll number / admission number.
     */
    protected function resolveStudentByRoll($rollNumber, $preferredClassTypeId = 0)
    {
        $rollNumber = trim((string)$rollNumber);
        if (in_array(strtolower($rollNumber), ['', '------', '000000', '00000', 'null', 'undefined', 'auto'])) {
            return null;
        }

        $cleanNumeric = preg_replace('/\D/', '', $rollNumber);
        $trimmedNumeric = ltrim($cleanNumeric, '0');

        $studentQuery = Admission::whereNull('deleted_at')
            ->where(function($q) use ($rollNumber, $cleanNumeric, $trimmedNumeric) {
                $q->where('roll_no', $rollNumber)
                  ->orWhere('exam_roll_no', $rollNumber)
                  ->orWhere('admissionNo', $rollNumber)
                  ->orWhere('id', $rollNumber);
                if (!empty($cleanNumeric)) {
                    $q->orWhere('roll_no', $cleanNumeric)
                      ->orWhere('admissionNo', $cleanNumeric);
                }
                if (!empty($trimmedNumeric) && $trimmedNumeric !== $cleanNumeric) {
                    $q->orWhere('roll_no', $trimmedNumeric)
                      ->orWhere('admissionNo', $trimmedNumeric)
                      ->orWhere('id', (int)$trimmedNumeric);
                }
            });

        if (!empty($preferredClassTypeId)) {
            $studentQuery->orderByRaw('CASE WHEN class_type_id = ? THEN 0 ELSE 1 END', [$preferredClassTypeId]);
        }
        return $studentQuery->first();
    }

    /**
     * Format student model to standard dictionary.
     */
    protected function formatStudentData($student, $fallbackRoll = '')
    {
        if ($student) {
            $class = $student->classType ? $student->classType->name : ($student->class_name ?? '');
            return [
                'id' => $student->id,
                'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'father_name' => $student->father_name ?? '',
                'admission_no' => $student->admissionNo ?? '',
                'roll_no' => $student->roll_no ?: ($student->admissionNo ?: ($student->exam_roll_no ?: 'STU-' . $student->id)),
                'class_name' => $class,
                'class_type_id' => $student->class_type_id,
                'mobile' => $student->mobile ?? '',
                'is_matched' => true,
            ];
        }
        return [
            'id' => null,
            'name' => 'Unregistered Student',
            'father_name' => '',
            'admission_no' => '',
            'roll_no' => $fallbackRoll,
            'class_name' => '',
            'class_type_id' => null,
            'mobile' => '',
            'is_matched' => false,
        ];
    }

    /**
     * Evaluate detected answers against exam answer key with subject-wise ranges and custom question marks.
     */
    protected function evaluateAnswersAgainstKey($passedAnswers, $answerKey, $totalQuestions, $marksPerCorrect, $marksPerIncorrect, $exam = null)
    {
        $extractedAnswers = [];
        $correctCount = 0;
        $incorrectCount = 0;
        $blankCount = 0;
        $options = ['A', 'B', 'C', 'D'];

        // Question ranges and per-question overrides
        $subjectRanges = [];
        $questionConfigs = [];
        $subjectMap = [];

        if ($exam) {
            $rawRanges = $exam->subject_question_ranges;
            if (is_string($rawRanges)) {
                $rawRanges = json_decode($rawRanges, true) ?: [];
            }
            $subjectRanges = is_array($rawRanges) ? $rawRanges : [];

            $rawConfigs = $exam->question_configs;
            if (is_string($rawConfigs)) {
                $rawConfigs = json_decode($rawConfigs, true) ?: [];
            }
            $questionConfigs = is_array($rawConfigs) ? $rawConfigs : [];

            // 1. Prime subject names from subject_question_ranges
            foreach ($subjectRanges as $r) {
                $sId = (int)($r['subject_id'] ?? 0);
                $sName = trim((string)($r['subject_name'] ?? ''));
                if ($sId > 0 && !empty($sName) && !str_starts_with($sName, 'Subject #')) {
                    $subjectMap[$sId] = $sName;
                }
            }

            // 2. Load subject names from current class / course
            if (!empty($exam->class_type_id)) {
                $csList = CourseSubject::where('course_id', $exam->class_type_id)->get();
                foreach ($csList as $cs) {
                    $sName = $cs->name ?? ($cs->subject_name ?? null);
                    if ($sName && (!isset($subjectMap[$cs->id]) || str_starts_with($subjectMap[$cs->id], 'Subject #'))) {
                        $subjectMap[$cs->id] = $sName;
                    }
                }
                $subList = Subject::where('class_type_id', $exam->class_type_id)->whereNull('deleted_at')->get();
                foreach ($subList as $s) {
                    $sName = $s->name ?? ($s->subject_name ?? null);
                    if ($sName && (!isset($subjectMap[$s->id]) || str_starts_with($subjectMap[$s->id], 'Subject #'))) {
                        $subjectMap[$s->id] = $sName;
                    }
                }
            }

            // 3. Fallback for any ranges or configs where subject_id is not in current class_type_id
            $allSubIds = [];
            foreach ($subjectRanges as $r) {
                if (!empty($r['subject_id'])) $allSubIds[] = (int)$r['subject_id'];
            }
            foreach ($questionConfigs as $qc) {
                if (!empty($qc['subject_id'])) $allSubIds[] = (int)$qc['subject_id'];
            }
            $missingIds = array_diff(array_unique($allSubIds), array_keys($subjectMap));
            if (!empty($missingIds)) {
                $csExtra = CourseSubject::whereIn('id', $missingIds)->get();
                foreach ($csExtra as $cs) {
                    $sName = $cs->name ?? ($cs->subject_name ?? null);
                    if ($sName) $subjectMap[$cs->id] = $sName;
                }
                $subExtra = Subject::whereIn('id', $missingIds)->whereNull('deleted_at')->get();
                foreach ($subExtra as $s) {
                    $sName = $s->name ?? ($s->subject_name ?? null);
                    if ($sName) $subjectMap[$s->id] = $sName;
                }
            }
        }

        // Initialize subject tracking
        $subjectBreakdown = [];
        if (!empty($subjectRanges)) {
            foreach ($subjectRanges as $r) {
                $sId = (int)($r['subject_id'] ?? 0);
                if ($sId > 0 && !isset($subjectBreakdown[$sId])) {
                    $sName = !empty($r['subject_name']) && !str_starts_with($r['subject_name'], 'Subject #')
                        ? $r['subject_name']
                        : ($subjectMap[$sId] ?? ('Subject #' . $sId));

                    $subjectBreakdown[$sId] = [
                        'subject_id' => $sId,
                        'subject_name' => $sName,
                        'from_q' => (int)($r['from_q'] ?? 1),
                        'to_q' => (int)($r['to_q'] ?? 1),
                        'total_questions' => 0,
                        'correct_answers' => 0,
                        'incorrect_answers' => 0,
                        'blank_answers' => 0,
                        'max_marks' => 0.0,
                        'obtained_marks' => 0.0,
                        'percentage' => 0.0,
                    ];
                }
            }
        }

        $overallTotalMarks = 0.0;
        $overallObtainedMarks = 0.0;

        // Detect if passed answers are 0-indexed list (e.g. [0=>"C", 1=>null, ...]) or 1-based map (e.g. ["1"=>"C", "3"=>"B", ...])
        $isZeroIndexed = is_array($passedAnswers) && !isset($passedAnswers[1]) && !isset($passedAnswers['1']) && (isset($passedAnswers[0]) || isset($passedAnswers['0']));

        for ($i = 1; $i <= $totalQuestions; $i++) {
            $correctOption = $answerKey[$i] ?? $answerKey[(string)$i] ?? null;
            if (!empty($correctOption)) {
                $correctOption = strtoupper(trim((string)$correctOption));
            }

            $userOption = null;
            if ($isZeroIndexed) {
                $idx = $i - 1;
                $rawVal = $passedAnswers[$idx] ?? $passedAnswers[(string)$idx] ?? null;
            } else {
                $rawVal = $passedAnswers[$i] ?? $passedAnswers[(string)$i] ?? null;
            }

            if (is_array($rawVal)) {
                $userOption = $rawVal['value'] ?? $rawVal['answer'] ?? $rawVal['detected'] ?? $rawVal['option'] ?? null;
            } else {
                $userOption = $rawVal;
            }

            if ($userOption === '—' || $userOption === '?' || $userOption === '' || $userOption === null) {
                $userOption = null;
            } else {
                $userOption = strtoupper(trim((string)$userOption));
                if (!in_array($userOption, $options)) {
                    $userOption = null;
                }
            }

            // Identify Subject for question $i
            $matchedSubjectId = null;
            $qCfg = $questionConfigs[$i] ?? $questionConfigs[(string)$i] ?? null;
            if (!empty($qCfg['subject_id'])) {
                $matchedSubjectId = (int)$qCfg['subject_id'];
            } else {
                foreach ($subjectRanges as $r) {
                    $f = (int)($r['from_q'] ?? 1);
                    $t = (int)($r['to_q'] ?? $f);
                    if ($i >= $f && $i <= $t) {
                        $matchedSubjectId = (int)($r['subject_id'] ?? 0);
                        break;
                    }
                }
            }

            if (!$matchedSubjectId && $exam) {
                $matchedSubjectId = (int)($exam->subject_id ?: ($exam->selected_subject_ids[0] ?? 0));
            }

            // Determine question scoring weight
            $rangeObj = null;
            if ($matchedSubjectId) {
                foreach ($subjectRanges as $r) {
                    if ((int)($r['subject_id'] ?? 0) === $matchedSubjectId) {
                        $rangeObj = $r;
                        break;
                    }
                }
            }

            $qCorrectWeight = isset($qCfg['marks_correct']) && is_numeric($qCfg['marks_correct'])
                ? (float)$qCfg['marks_correct']
                : (isset($rangeObj['marks_correct']) && is_numeric($rangeObj['marks_correct'])
                    ? (float)$rangeObj['marks_correct']
                    : $marksPerCorrect);

            $qIncorrectWeight = isset($qCfg['marks_incorrect']) && is_numeric($qCfg['marks_incorrect'])
                ? (float)$qCfg['marks_incorrect']
                : (isset($rangeObj['marks_incorrect']) && is_numeric($rangeObj['marks_incorrect'])
                    ? (float)$rangeObj['marks_incorrect']
                    : $marksPerIncorrect);

            $overallTotalMarks += $qCorrectWeight;

            // Ensure matched subject is tracked in breakdown
            if ($matchedSubjectId && !isset($subjectBreakdown[$matchedSubjectId])) {
                $sName = !empty($rangeObj['subject_name']) && !str_starts_with($rangeObj['subject_name'], 'Subject #')
                    ? $rangeObj['subject_name']
                    : ($subjectMap[$matchedSubjectId] ?? ('Subject #' . $matchedSubjectId));

                $subjectBreakdown[$matchedSubjectId] = [
                    'subject_id' => $matchedSubjectId,
                    'subject_name' => $sName,
                    'from_q' => $i,
                    'to_q' => $i,
                    'total_questions' => 0,
                    'correct_answers' => 0,
                    'incorrect_answers' => 0,
                    'blank_answers' => 0,
                    'max_marks' => 0.0,
                    'obtained_marks' => 0.0,
                    'percentage' => 0.0,
                ];
            }

            if ($matchedSubjectId) {
                $subjectBreakdown[$matchedSubjectId]['total_questions']++;
                $subjectBreakdown[$matchedSubjectId]['max_marks'] += $qCorrectWeight;
            }

            $status = 'blank';
            $isCorrect = false;
            $marksAwarded = 0.0;

            if (!empty($userOption)) {
                $status = 'detected';
                if (!empty($correctOption) && $userOption === $correctOption) {
                    $correctCount++;
                    $isCorrect = true;
                    $marksAwarded = $qCorrectWeight;
                    $overallObtainedMarks += $qCorrectWeight;
                    if ($matchedSubjectId) {
                        $subjectBreakdown[$matchedSubjectId]['correct_answers']++;
                        $subjectBreakdown[$matchedSubjectId]['obtained_marks'] += $qCorrectWeight;
                    }
                } else {
                    $incorrectCount++;
                    $isCorrect = false;
                    $penalty = abs($qIncorrectWeight);
                    $marksAwarded = -$penalty;
                    $overallObtainedMarks -= $penalty;
                    if ($matchedSubjectId) {
                        $subjectBreakdown[$matchedSubjectId]['incorrect_answers']++;
                        $subjectBreakdown[$matchedSubjectId]['obtained_marks'] -= $penalty;
                    }
                }
            } else {
                $blankCount++;
                if ($matchedSubjectId) {
                    $subjectBreakdown[$matchedSubjectId]['blank_answers']++;
                }
            }

            $qSubName = !empty($rangeObj['subject_name']) && !str_starts_with($rangeObj['subject_name'], 'Subject #')
                ? $rangeObj['subject_name']
                : ($subjectMap[$matchedSubjectId] ?? '');

            $extractedAnswers[] = [
                'question' => $i,
                'answer' => $userOption,
                'correct_answer' => $correctOption,
                'is_correct' => $isCorrect,
                'status' => $status,
                'subject_id' => $matchedSubjectId,
                'subject_name' => $qSubName,
                'marks_correct' => $qCorrectWeight,
                'marks_incorrect' => $qIncorrectWeight,
                'marks_awarded' => round($marksAwarded, 2),
                'confidence' => 0.95,
            ];
        }

        // Finalize subject breakdown formatting
        foreach ($subjectBreakdown as &$sb) {
            $sb['obtained_marks'] = max(0, round($sb['obtained_marks'], 2));
            $sb['max_marks'] = round($sb['max_marks'], 2);
            $sb['percentage'] = $sb['max_marks'] > 0 ? round(($sb['obtained_marks'] / $sb['max_marks']) * 100, 2) : 0;
            $sb['max_marks_formatted'] = number_format($sb['max_marks'], 2, '.', '');
            $sb['obtained_marks_formatted'] = number_format($sb['obtained_marks'], 2, '.', '');
        }
        unset($sb);

        $totalMarks = ($overallTotalMarks > 0) ? $overallTotalMarks : ($totalQuestions * $marksPerCorrect);
        $obtainedMarks = max(0, round($overallObtainedMarks, 2));
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;

        return [
            'correct_answers' => $correctCount,
            'incorrect_answers' => $incorrectCount,
            'blank_answers' => $blankCount,
            'total_marks' => number_format($totalMarks, 2, '.', ''),
            'obtained_marks' => number_format($obtainedMarks, 2, '.', ''),
            'percentage' => $percentage,
            'extracted_answers' => $extractedAnswers,
            'subject_breakdown' => array_values($subjectBreakdown),
        ];
    }

    /**
     * Real-time student lookup by roll number.
     */
    public function lookupStudent(Request $request)
    {
        $rollNumber = trim((string) $request->input('roll_number', ''));
        $classTypeId = (int) $request->input('class_type_id', 0);
        $student = $this->resolveStudentByRoll($rollNumber, $classTypeId);
        return response()->json([
            'status' => 'success',
            'matched' => (bool) $student,
            'student' => $this->formatStudentData($student, $rollNumber)
        ]);
    }

    /**
     * Step 2: Final Submit & Commit staged OMR evaluations to database & exam marks.
     */
    public function finalizeOmrSave(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|integer',
            'sheets' => 'required|array|min:1',
        ]);

        $exam = Exam::where('id', $validated['exam_id'])->whereNull('deleted_at')->firstOrFail();
        $sessionId = Session::get('session_id') ?: $exam->session_id;
        $branchId = Session::get('branch_id') ?: $exam->branch_id;
        $userId = Session::get('id') ?: 1;

        $sheets = $request->input('sheets', []);
        $savedCount = 0;
        $marksUploadedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($sheets as $s) {
                $rollNumber = trim((string) ($s['roll_number'] ?? ''));
                if (empty($rollNumber)) {
                    $rollNumber = 'STU-' . rand(1001, 9999);
                }

                $admissionId = !empty($s['student']['id']) ? (int) $s['student']['id'] : (!empty($s['admission_id']) ? (int) $s['admission_id'] : null);
                
                // If admissionId is empty but rollNumber is present, attempt lookup
                if (!$admissionId && !empty($rollNumber)) {
                    $matchedStudent = $this->resolveStudentByRoll($rollNumber, (int)$exam->class_type_id);
                    if ($matchedStudent) {
                        $admissionId = $matchedStudent->id;
                    }
                }

                $correctCount = (int) ($s['evaluation']['correct_answers'] ?? ($s['correct_answers'] ?? 0));
                $incorrectCount = (int) ($s['evaluation']['incorrect_answers'] ?? ($s['incorrect_answers'] ?? 0));
                $blankCount = (int) ($s['evaluation']['blank_answers'] ?? ($s['blank_answers'] ?? 0));
                $totalMarks = (float) ($s['evaluation']['total_marks'] ?? ($s['total_marks'] ?? 0));
                $obtainedMarks = (float) ($s['evaluation']['obtained_marks'] ?? ($s['obtained_marks'] ?? 0));
                $percentage = (float) ($s['evaluation']['percentage'] ?? ($s['percentage'] ?? 0));
                $extractedAnswers = $s['evaluation']['extracted_answers'] ?? ($s['extracted_answers'] ?? []);
                $sourceFilename = $s['source_filename'] ?? 'omr_sheet.jpg';
                $storedFile = $s['annotated_stored_file'] ?? ($s['stored_file'] ?? null);
                $annotatedImageUrl = $s['annotated_stored_file'] ?? ($s['annotated_image_url'] ?? ($s['image_url'] ?? null));

                // 1. Locate existing OmrResult to overwrite all columns
                $omrResult = null;
                if ($admissionId) {
                    $omrResult = OmrResult::where('exam_id', $exam->id)
                        ->where('admission_id', $admissionId)
                        ->first();
                }

                if (!$omrResult && !empty($rollNumber)) {
                    $omrResult = OmrResult::where('exam_id', $exam->id)
                        ->where('unique_id', $rollNumber)
                        ->first();

                    if (!$omrResult) {
                        $cleanRoll = preg_replace('/\D/', '', $rollNumber);
                        if ($cleanRoll !== '') {
                            $trimmedRoll = ltrim($cleanRoll, '0');
                            $omrResult = OmrResult::where('exam_id', $exam->id)
                                ->where(function($q) use ($cleanRoll, $trimmedRoll) {
                                    $q->where('unique_id', $cleanRoll)
                                      ->orWhere('unique_id', $trimmedRoll);
                                })->first();
                        }
                    }
                }

                if (!$omrResult) {
                    $omrResult = new OmrResult();
                    $omrResult->exam_id = $exam->id;
                }

                // Delete older replacement image file to keep storage footprint minimal
                if (!empty($omrResult->stored_file) && $storedFile && $omrResult->stored_file !== $storedFile) {
                    $oldFilePath = public_path($omrResult->stored_file);
                    if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }

                // OVERWRITE ALL COLUMNS with freshly fetched data
                $omrResult->unique_id = $rollNumber;
                $omrResult->admission_id = $admissionId;
                $omrResult->correct_answers = $correctCount;
                $omrResult->incorrect_answers = $incorrectCount;
                $omrResult->blank_answers = $blankCount;
                $omrResult->total_marks = $totalMarks;
                $omrResult->obtained_marks = $obtainedMarks;
                $omrResult->percentage = $percentage;
                $omrResult->extracted_answers = $extractedAnswers;
                $omrResult->subject_breakdown = $s['evaluation']['subject_breakdown'] ?? ($s['subject_breakdown'] ?? null);
                $omrResult->extraction_confidence = 0.9500;
                $omrResult->source_filename = $sourceFilename;
                if (!empty($storedFile)) {
                    $omrResult->stored_file = $storedFile;
                }
                if (!empty($annotatedImageUrl)) {
                    $omrResult->annotated_image_url = $annotatedImageUrl;
                }
                $omrResult->scanned_by_user_id = $userId;
                $omrResult->save();

                // Clean up any historical duplicate rows for this student in this exam
                if ($admissionId) {
                    $oldDuplicates = OmrResult::where('exam_id', $exam->id)
                        ->where('admission_id', $admissionId)
                        ->where('id', '!=', $omrResult->id)
                        ->get();

                    foreach ($oldDuplicates as $dup) {
                        if (!empty($dup->stored_file) && $dup->stored_file !== $omrResult->stored_file) {
                            $dupPath = public_path($dup->stored_file);
                            if (file_exists($dupPath) && is_file($dupPath)) {
                                @unlink($dupPath);
                            }
                        }
                        $dup->delete();
                    }
                }

                $savedCount++;

                // Upload marks directly to exam (FillMarks & FillMinMaxMarks)
                $student = null;
                if ($admissionId) {
                    $student = Admission::find($admissionId);
                }
                if ($student) {
                    $classTypeId = (int) ($student->class_type_id ?: ($exam->class_type_id ?: 0));
                    $subjectBreakdown = $s['evaluation']['subject_breakdown'] ?? ($s['subject_breakdown'] ?? []);

                    if (!empty($subjectBreakdown) && is_array($subjectBreakdown)) {
                        $updatedSubjectIds = [];

                        // Section-aware marks persistence: one ledger row per evaluated subject
                        foreach ($subjectBreakdown as $sb) {
                            $subId = (int) ($sb['subject_id'] ?? 0);
                            if ($subId <= 0) continue;
                            $updatedSubjectIds[] = $subId;

                            $subTotalMarks = (float) ($sb['max_marks'] ?? 0);
                            $subObtainedMarks = (float) ($sb['obtained_marks'] ?? 0);
                            $subCorrect = (int) ($sb['correct_answers'] ?? 0);
                            $subIncorrect = (int) ($sb['incorrect_answers'] ?? 0);
                            $subBlank = (int) ($sb['blank_answers'] ?? 0);

                            // FillMinMaxMarks for this subject
                            $minMax = FillMinMaxMarks::withTrashed()
                                ->where('exam_id', $exam->id)
                                ->where('class_type_id', $classTypeId)
                                ->where('subject_id', $subId)
                                ->where('session_id', $sessionId)
                                ->where('branch_id', $branchId)
                                ->first();

                            if (!$minMax) {
                                $minMax = new FillMinMaxMarks();
                                $minMax->exam_id = $exam->id;
                                $minMax->class_type_id = $classTypeId;
                                $minMax->subject_id = $subId;
                                $minMax->session_id = $sessionId;
                                $minMax->branch_id = $branchId;
                                $minMax->user_id = $userId;
                            }
                            if ($subTotalMarks > 0) {
                                $minMax->exam_maximum_marks = $subTotalMarks;
                                $minMax->exam_minimum_marks = round($subTotalMarks * 0.33, 2);
                            }
                            $minMax->deleted_at = null;
                            $minMax->save();

                            // FillMarks for student & subject: OVERWRITE ALL COLUMNS
                            $fillMark = FillMarks::withTrashed()
                                ->where('exam_id', $exam->id)
                                ->where('admission_id', $student->id)
                                ->where('subject_id', $subId)
                                ->first();

                            if (!$fillMark) {
                                $fillMark = new FillMarks();
                                $fillMark->exam_id = $exam->id;
                                $fillMark->admission_id = $student->id;
                                $fillMark->subject_id = $subId;
                                $fillMark->user_id = $userId;
                            }

                            $fillMark->class_type_id = $classTypeId;
                            $fillMark->course_id = $student->course_id ?: ($exam->course_id ?: $classTypeId);
                            $fillMark->batch_id = $student->batch_id ?: $exam->batch_id;
                            $fillMark->session_id = $sessionId;
                            $fillMark->branch_id = $branchId;
                            $fillMark->student_marks = $subObtainedMarks;
                            $fillMark->r_marks = (string)$subCorrect;
                            $fillMark->w_marks = (string)$subIncorrect;
                            $fillMark->l_marks = (string)$subBlank;
                            $fillMark->exam_maximum_marks = (string)$subTotalMarks;
                            $fillMark->fill_min_max_marks_id = $minMax->id;
                            $fillMark->updated_by = $userId;
                            $fillMark->deleted_at = null;
                            $fillMark->save();

                            $marksUploadedCount++;
                        }

                        // Clean up any stale FillMarks for subjects not in this updated evaluation
                        if (!empty($updatedSubjectIds)) {
                            FillMarks::where('exam_id', $exam->id)
                                ->where('admission_id', $student->id)
                                ->whereNotIn('subject_id', $updatedSubjectIds)
                                ->delete();
                        }
                    } else {
                        // Fallback: single exam subject
                        $subjectId = (int) ($exam->subject_id ?: ($exam->selected_subject_ids[0] ?? 0));

                        if ($subjectId <= 0 && $classTypeId > 0) {
                            $firstSub = Subject::where('class_type_id', $classTypeId)->whereNull('deleted_at')->first();
                            if ($firstSub) $subjectId = $firstSub->id;
                        }

                        if ($classTypeId > 0 && $subjectId > 0) {
                            // FillMinMaxMarks
                            $minMax = FillMinMaxMarks::withTrashed()
                                ->where('exam_id', $exam->id)
                                ->where('class_type_id', $classTypeId)
                                ->where('subject_id', $subjectId)
                                ->where('session_id', $sessionId)
                                ->where('branch_id', $branchId)
                                ->first();

                            if (!$minMax) {
                                $minMax = new FillMinMaxMarks();
                                $minMax->exam_id = $exam->id;
                                $minMax->class_type_id = $classTypeId;
                                $minMax->subject_id = $subjectId;
                                $minMax->session_id = $sessionId;
                                $minMax->branch_id = $branchId;
                                $minMax->user_id = $userId;
                            }
                            $minMax->exam_maximum_marks = $totalMarks;
                            $minMax->exam_minimum_marks = round($totalMarks * 0.33, 2);
                            $minMax->deleted_at = null;
                            $minMax->save();

                            // FillMarks: OVERWRITE ALL COLUMNS
                            $fillMark = FillMarks::withTrashed()
                                ->where('exam_id', $exam->id)
                                ->where('admission_id', $student->id)
                                ->where('subject_id', $subjectId)
                                ->first();

                            if (!$fillMark) {
                                $fillMark = new FillMarks();
                                $fillMark->exam_id = $exam->id;
                                $fillMark->admission_id = $student->id;
                                $fillMark->subject_id = $subjectId;
                                $fillMark->user_id = $userId;
                            }

                            $fillMark->class_type_id = $classTypeId;
                            $fillMark->course_id = $student->course_id ?: ($exam->course_id ?: $classTypeId);
                            $fillMark->batch_id = $student->batch_id ?: $exam->batch_id;
                            $fillMark->session_id = $sessionId;
                            $fillMark->branch_id = $branchId;
                            $fillMark->student_marks = $obtainedMarks;
                            $fillMark->r_marks = (string)$correctCount;
                            $fillMark->w_marks = (string)$incorrectCount;
                            $fillMark->l_marks = (string)$blankCount;
                            $fillMark->exam_maximum_marks = (string)$totalMarks;
                            $fillMark->fill_min_max_marks_id = $minMax->id;
                            $fillMark->updated_by = $userId;
                            $fillMark->deleted_at = null;
                            $fillMark->save();

                            $marksUploadedCount++;
                        }
                    }
                }
            }

            // Recalculate ranks across exam
            $allExamResults = OmrResult::where('exam_id', $exam->id)
                ->orderBy('obtained_marks', 'desc')
                ->get();
            $currentRank = 1;
            foreach ($allExamResults as $item) {
                $item->update(['rank' => $currentRank++]);
            }

            DB::commit();

            // Refetch saved results to return to frontend
            $savedRecords = OmrResult::with(['exam', 'student'])
                ->where('exam_id', $exam->id)
                ->orderBy('obtained_marks', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => "Successfully saved {$savedCount} OMR evaluations and updated marks for {$marksUploadedCount} student records!",
                'saved_count' => $savedCount,
                'marks_uploaded_count' => $marksUploadedCount,
                'saved_records' => $savedRecords,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save evaluated sheets: ' . $e->getMessage()
            ], 500);
        }
    }

    public function publishResult(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|integer',
            'class_type_id' => 'required|integer',
            'publish_date' => 'nullable|date',
            'action' => 'nullable|string',
        ]);

        if (!Schema::hasTable('exam_result_publications')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Result publication setup is pending. Please run database migrations.'], 400);
            }
            return Redirect::to('view/exam')->with('error', 'Result publication setup is pending. Please run database migrations.');
        }

        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $examId = (int) $request->exam_id;
        $classTypeId = (int) $request->class_type_id;
        $action = $request->input('action', 'publish');

        $selectedDate = $request->filled('publish_date') ? $request->input('publish_date') : date('Y-m-d');
        $publishDateTime = Carbon::parse($selectedDate)->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
        $publishDateOnly = Carbon::parse($selectedDate)->format('Y-m-d');
        $formattedDate = Carbon::parse($selectedDate)->format('d M Y');

        $exam = Exam::query()
            ->where('id', $examId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->first();

        $assignment = AssignExam::query()
            ->where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->first();

        if (!$exam || !$assignment) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Exam or assigned class not found.'], 404);
            }
            return Redirect::to('view/exam')->with('error', 'Exam or assigned class not found.');
        }

        // Action: update_date
        if ($action === 'update_date') {
            DB::beginTransaction();
            try {
                DB::table('exam_result_publications')
                    ->where('exam_id', $examId)
                    ->where('class_type_id', $classTypeId)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->update([
                        'published_at' => $publishDateTime,
                        'updated_at' => now(),
                    ]);

                $assignment->result_declaration_date = $publishDateOnly;
                $assignment->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => "Publish date updated to {$formattedDate} successfully.",
                    'is_published' => true,
                    'exam_id' => $examId,
                    'class_type_id' => $classTypeId,
                    'publish_date' => $formattedDate,
                    'publish_date_iso' => $publishDateOnly,
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Failed to update publish date: ' . $e->getMessage()], 500);
            }
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
            $msg = 'Result cannot be published because marks have not been entered for this class.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return Redirect::to('view/exam')->with('error', $msg);
        }

        $students = Admission::query()
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->where('status', 1)
            ->get();

        $title = 'Exam Result Published';
        $body = "Your result for {$exam->name} has been published on {$formattedDate}. Open Exam Result to view your marksheet.";
        $now = now();

        DB::beginTransaction();
        try {
            DB::table('exam_result_publications')->updateOrInsert(
                [
                    'exam_id' => $examId,
                    'class_type_id' => $classTypeId,
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                ],
                [
                    'published_by' => (int) Session::get('id'),
                    'published_at' => $publishDateTime,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // Keep assign_exams.result_declaration_date in sync
            $assignment->result_declaration_date = $publishDateOnly;
            $assignment->save();

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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Result could not be published: ' . $e->getMessage()], 500);
            }
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
                'message' => "Result published successfully on {$formattedDate}. Students notified: {$students->count()}.",
                'is_published' => true,
                'exam_id' => $examId,
                'class_type_id' => $classTypeId,
                'publish_date' => $formattedDate,
                'publish_date_iso' => $publishDateOnly,
            ]);
        }

        return Redirect::to('view/exam')->with(
            'message',
            "Result published successfully on {$formattedDate}. Students notified: {$students->count()}. Push sent: {$pushSent}. Failed: {$pushFailed}."
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

        AssignExam::where('exam_id', $examId)
            ->where('class_type_id', $classTypeId)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->update(['result_declaration_date' => null]);

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
            'class_type_id' => 'required|integer',
            'batch_id' => 'nullable|integer',
            'exam_date' => 'required|date',
        ]);

        $sourceExam = Exam::where('id', $request->exam_id)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->first();

        if (!$sourceExam) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Source examination not found.'], 404);
            }
            return Redirect::to('view/exam')->with('error', 'Examination not found.');
        }

        DB::beginTransaction();

        try {
            $targetClassId = (int) $request->class_type_id;
            $targetBatchId = !empty($request->batch_id) ? (int) $request->batch_id : null;
            $copiedExam = $sourceExam->replicate();
            $copiedExam->name = trim($request->name);
            $copiedExam->exam_date = $request->exam_date;
            $copiedExam->class_type_id = $targetClassId;
            $copiedExam->course_id = $targetClassId;
            $copiedExam->batch_id = $targetBatchId;

            // Generate fresh unique scanner code
            do {
                $newCode = 'EXAM-' . strtoupper(Str::random(6));
            } while (Exam::where('code', $newCode)->orWhere('exam_code', $newCode)->exists());

            $copiedExam->code = $newCode;
            $copiedExam->exam_code = $newCode;
            $copiedExam->result_status = 'not_scheduled';
            $copiedExam->user_id = Session::get('id') ?: 1;
            $copiedExam->session_id = Session::get('session_id');
            $copiedExam->branch_id = Session::get('branch_id');
            $copiedExam->deleted_at = null;
            $copiedExam->save();

            // Replicate questions if any (Question Bank or Online Exam)
            $sourceQuestions = AssignQuestion::where('exam_id', $sourceExam->id)
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->get();
            foreach ($sourceQuestions as $sq) {
                $copiedQ = $sq->replicate();
                $copiedQ->exam_id = $copiedExam->id;
                $copiedQ->class_type_id = $targetClassId;
                $copiedQ->user_id = Session::get('id') ?: 1;
                $copiedQ->session_id = Session::get('session_id');
                $copiedQ->branch_id = Session::get('branch_id');
                $copiedQ->deleted_at = null;
                $copiedQ->save();
            }

            // Create exactly ONE independent AssignExam record for the target class
            $copiedAssignment = new AssignExam();
            $copiedAssignment->exam_id = $copiedExam->id;
            $copiedAssignment->class_type_id = $targetClassId;
            $copiedAssignment->exam_date = $request->exam_date;
            $copiedAssignment->result_declaration_date = null;
            $copiedAssignment->user_id = Session::get('id') ?: 1;
            $copiedAssignment->session_id = Session::get('session_id');
            $copiedAssignment->branch_id = Session::get('branch_id');
            $copiedAssignment->deleted_at = null;
            $copiedAssignment->save();

            // Replicate fill_min_max_marks from sourceExam to copiedExam
            $sourceMinMax = DB::table('fill_min_max_marks')
                ->where('exam_id', $sourceExam->id)
                ->whereNull('deleted_at')
                ->get();

            foreach ($sourceMinMax as $smm) {
                DB::table('fill_min_max_marks')->insert([
                    'user_id'            => Session::get('id') ?: 1,
                    'exam_id'            => $copiedExam->id,
                    'fill_marks_id'      => null,
                    'branch_id'          => Session::get('branch_id'),
                    'session_id'         => Session::get('session_id'),
                    'class_type_id'      => $targetClassId,
                    'exam_minimum_marks' => $smm->exam_minimum_marks,
                    'exam_maximum_marks' => $smm->exam_maximum_marks,
                    'subject_id'         => $smm->subject_id,
                    'stream_id'          => $smm->stream_id ?? null,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            DB::commit();

            $targetClassModel = DB::table('courses')->where('id', $targetClassId)->first();
            $className = $targetClassModel->name ?? ('Course ' . $targetClassId);
            $batchInfo = '';
            if ($targetBatchId) {
                $batchModel = DB::table('batches')->where('id', $targetBatchId)->first();
                if ($batchModel) {
                    $batchInfo = ' (' . $batchModel->name . ')';
                }
            }
            $msg = 'Exam "' . $copiedExam->name . '" duplicated successfully for Course ' . $className . $batchInfo . ' with independent code ' . $newCode . '.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $msg,
                    'exam_id' => $copiedExam->id,
                ]);
            }

            return Redirect::to('view/exam')->with('message', $msg);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Exam could not be duplicated: ' . $e->getMessage()], 422);
            }
            return Redirect::to('view/exam')->with('error', 'Exam could not be duplicated: ' . $e->getMessage());
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
                'batch_id' => 'nullable|integer',
                'subject_id' => 'nullable|integer',
                'subject_ids' => 'nullable|array',
                'subject_ids.*' => 'integer',
                'subject_max_marks' => 'nullable|array',
                'subject_min_marks' => 'nullable|array',
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
                'subject_question_ranges' => 'nullable',
                'question_configs' => 'nullable',
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
                $perSubMarks = (float) ($request->input('exam_maximum_marks', $validated['exam_maximum_marks'] ?? ($isEdit ? ($add->exam_maximum_marks ?? 100) : 100)));
                $maxMarks = $perSubMarks;
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

                $finalSubjectIds = [];
                if ($isOnline) {
                    $singleSub = $request->subject_id ? (int)$request->subject_id : null;
                    $finalSubjectIds = $singleSub ? [$singleSub] : [];
                    $add->subject_id = $singleSub;
                    $add->subject_ids = !empty($finalSubjectIds) ? $finalSubjectIds : null;
                } else {
                    $rawSubjectIds = $request->input('subject_ids', []);
                    if (!empty($rawSubjectIds)) {
                        $finalSubjectIds = array_values(array_filter(array_map('intval', (array)$rawSubjectIds)));
                    } elseif ($request->filled('subject_id')) {
                        $finalSubjectIds = [(int)$request->subject_id];
                    }
                    $add->subject_ids = !empty($finalSubjectIds) ? $finalSubjectIds : null;
                    $add->subject_id = count($finalSubjectIds) === 1 ? $finalSubjectIds[0] : null;
                }

                $add->exam_term_id = $request->exam_term_id;
                $add->exam_date = $examDate;
                $add->number_of_questions = $numQuestions;
                $add->is_online = $isOnline ? 1 : 0;
                $add->exam_creation_mode = $creationMode;
                $add->marks_per_correct = $marksPerCorrect;
                $add->marks_per_incorrect = $marksPerIncorrect;
                $subjectMaxMarks = (array) $request->input('subject_max_marks', []);
                $subjectMinMarks = (array) $request->input('subject_min_marks', []);
                $totalExamMaxMarks = 0.0;
                $totalExamMinMarks = 0.0;

                $rawRanges = $request->input('subject_question_ranges');
                if (is_string($rawRanges)) {
                    $rawRanges = json_decode($rawRanges, true) ?: [];
                }
                $subjectQuestionRanges = is_array($rawRanges) ? $rawRanges : [];

                $rawConfigs = $request->input('question_configs');
                if (is_string($rawConfigs)) {
                    $rawConfigs = json_decode($rawConfigs, true) ?: [];
                }
                $questionConfigs = is_array($rawConfigs) ? $rawConfigs : [];

                // If subject_question_ranges is defined, auto-calculate subject max marks based on question counts and weights
                if (!empty($subjectQuestionRanges)) {
                    foreach ($subjectQuestionRanges as $r) {
                        $sId = (int)($r['subject_id'] ?? 0);
                        if ($sId <= 0) continue;
                        $fromQ = (int)($r['from_q'] ?? 1);
                        $toQ = (int)($r['to_q'] ?? $fromQ);
                        $subCorrectM = isset($r['marks_correct']) && is_numeric($r['marks_correct']) ? (float)$r['marks_correct'] : $marksPerCorrect;
                        $subTotalM = 0.0;
                        for ($q = $fromQ; $q <= $toQ; $q++) {
                            $qM = isset($questionConfigs[$q]['marks_correct']) ? (float)$questionConfigs[$q]['marks_correct'] : (isset($questionConfigs[(string)$q]['marks_correct']) ? (float)$questionConfigs[(string)$q]['marks_correct'] : $subCorrectM);
                            $subTotalM += $qM;
                        }
                        if ($subTotalM > 0) {
                            $subjectMaxMarks[$sId] = $subTotalM;
                            if (!isset($subjectMinMarks[$sId]) || empty($subjectMinMarks[$sId])) {
                                $subjectMinMarks[$sId] = round($subTotalM * 0.35, 2);
                            }
                        }
                    }
                }

                foreach ($finalSubjectIds as $subId) {
                    $sMax = isset($subjectMaxMarks[$subId]) && is_numeric($subjectMaxMarks[$subId])
                        ? (float) $subjectMaxMarks[$subId]
                        : ($perSubMarks ?: 100.0);
                    $sMin = isset($subjectMinMarks[$subId]) && is_numeric($subjectMinMarks[$subId])
                        ? (float) $subjectMinMarks[$subId]
                        : round($sMax * 0.33, 2);

                    $totalExamMaxMarks += $sMax;
                    $totalExamMinMarks += $sMin;
                }

                if ($totalExamMaxMarks > 0 && !empty($subjectMaxMarks)) {
                    $maxMarks = $totalExamMaxMarks;
                } elseif ($creationMode === 'normal') {
                    $subCount = count($finalSubjectIds);
                    $maxMarks = ($subCount > 1) ? round($perSubMarks * $subCount, 2) : $perSubMarks;
                }

                $add->subject_question_ranges = !empty($subjectQuestionRanges) ? $subjectQuestionRanges : null;
                $add->question_configs = !empty($questionConfigs) ? $questionConfigs : null;
                $add->exam_maximum_marks = $maxMarks;
                $add->exam_minimum_marks = $totalExamMinMarks;
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
                $add->class_type_id = $request->class_type_id;
                $add->course_id = $request->class_type_id;
                $add->batch_id = $request->filled('batch_id') ? (int)$request->input('batch_id') : null;
                $add->description = $request->description;
                $add->save();

                // Sync into fill_min_max_marks master table with guaranteed $add->id
                foreach ($finalSubjectIds as $subId) {
                    $sMax = isset($subjectMaxMarks[$subId]) && is_numeric($subjectMaxMarks[$subId])
                        ? (float) $subjectMaxMarks[$subId]
                        : ($perSubMarks ?: 100.0);
                    $sMin = isset($subjectMinMarks[$subId]) && is_numeric($subjectMinMarks[$subId])
                        ? (float) $subjectMinMarks[$subId]
                        : round($sMax * 0.33, 2);

                    DB::table('fill_min_max_marks')->updateOrInsert(
                        [
                            'exam_id'       => $add->id,
                            'class_type_id' => (int) $request->class_type_id,
                            'subject_id'    => (int) $subId,
                            'branch_id'     => $branchId,
                            'session_id'    => $sessionId,
                        ],
                        [
                            'exam_maximum_marks' => (int) round($sMax),
                            'exam_minimum_marks' => (int) round($sMin),
                            'user_id'            => Session::get('id') ?: 1,
                            'deleted_at'         => null,
                            'updated_at'         => now(),
                        ]
                    );
                }

                // Clean up unselected subjects for this exam
                if (!empty($finalSubjectIds)) {
                    DB::table('fill_min_max_marks')
                        ->where('exam_id', $add->id)
                        ->where('class_type_id', (int) $request->class_type_id)
                        ->where('branch_id', $branchId)
                        ->where('session_id', $sessionId)
                        ->whereNotIn('subject_id', $finalSubjectIds)
                        ->delete();
                }

                $assignment = AssignExam::withTrashed()
                    ->where('exam_id', $add->id)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$assignment) {
                    $assignment = new AssignExam;
                }

                $assignment->exam_id = $add->id;
                $assignment->class_type_id = $request->class_type_id;
                $assignment->exam_date = $examDate;
                $assignment->session_id = $sessionId;
                $assignment->branch_id = $branchId;
                $assignment->user_id = Session::get('id') ?: 1;
                $assignment->deleted_at = null;
                $assignment->save();

                // Enforce strict 1 Exam = 1 Class
                AssignExam::where('exam_id', $add->id)
                    ->where('id', '!=', $assignment->id)
                    ->delete();

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
                    $redirectUrl = 'edit/exam/' . $add->id;
                    if ($request->filled('class_type_id')) {
                        $redirectUrl .= '?class_type_id=' . $request->class_type_id;
                    }
                    return Redirect::to($redirectUrl)
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
        $preloadedSubjectIds = [];
        $preloadedExamDate = null;

        if (!empty($examId)) {
            $savedExam = Exam::where('id', $examId)->first();
            if ($savedExam) {
                $preloadedClassId = $request->filled('class_type_id') ? (int)$request->input('class_type_id') : $savedExam->class_type_id;
                $preloadedSubjectId = $savedExam->subject_id ?? null;
                $preloadedSubjectIds = $savedExam->selected_subject_ids;

                if (!empty($preloadedClassId)) {
                    $subjects = Subject::where('class_type_id', $preloadedClassId)->whereNull('deleted_at')->get();
                }

                $assignmentForClass = AssignExam::where('exam_id', $savedExam->id)
                    ->where('class_type_id', $preloadedClassId)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->first();
                $preloadedExamDate = ($assignmentForClass && !empty($assignmentForClass->exam_date)) ? $assignmentForClass->exam_date : null;

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
                    $subjects = \App\Models\CourseSubject::where('course_id', $preloadedClassId)->get();
                    if ($subjects->isEmpty()) {
                        $subjects = Subject::where('class_type_id', $preloadedClassId)->whereNull('deleted_at')->get();
                    }
                }
            }
        }

        $examCode = $savedExam ? ($savedExam->code ?: ($savedExam->exam_code ?: ('EXAM-' . strtoupper(Str::random(6))))) : ('EXAM-' . strtoupper(Str::random(6)));
        $classType = Helper::ClassType();

        $preloadedBatchId = $savedExam ? $savedExam->batch_id : ($request->input('batch_id') ?: null);
        $preloadedBatches = !empty($preloadedClassId)
            ? DB::table('batches')->where('course_id', $preloadedClassId)->where('status', 1)->orderBy('orderBy', 'asc')->get()
            : collect([]);

        $existingMinMaxMap = [];
        if ($savedExam) {
            $existingMinMaxMap = DB::table('fill_min_max_marks')
                ->where('exam_id', $savedExam->id)
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('subject_id')
                ->map(function($item) {
                    return [
                        'max' => (float)$item->exam_maximum_marks,
                        'min' => (float)$item->exam_minimum_marks,
                    ];
                })
                ->toArray();

            if (!empty($savedExam->subject_question_ranges)) {
                $ranges = is_string($savedExam->subject_question_ranges)
                    ? json_decode($savedExam->subject_question_ranges, true)
                    : $savedExam->subject_question_ranges;
                if (is_array($ranges) && !empty($subjects)) {
                    $subMap = $subjects->pluck('name', 'id')->toArray();
                    foreach ($ranges as &$r) {
                        $sId = (int)($r['subject_id'] ?? 0);
                        if ($sId > 0 && isset($subMap[$sId])) {
                            $r['subject_name'] = $subMap[$sId];
                        }
                    }
                    unset($r);
                    $savedExam->subject_question_ranges = $ranges;
                }
            }
        }

        return view('examination.offline_exam.exam.add', compact('examCode', 'classType', 'instituteName', 'savedExam', 'subjects', 'preloadedQuestions', 'preloadedClassId', 'preloadedSubjectId', 'preloadedSubjectIds', 'preloadedExamDate', 'preloadedBatches', 'preloadedBatchId', 'existingMinMaxMap'));
    } 
    
    public function editExam(Request $request, $id){
        $request->merge(['exam_id' => $id]);
        return $this->addExam($request);
    } 
    
    public function assignExam(Request $request, $id){
        if ($request->ajax()) {
            return response()->json(['status' => 'info', 'message' => 'Each examination belongs to a single class. To conduct this exam for another class, duplicate the exam.'], 200);
        }
        return Redirect::to('view/exam')->with('message', 'Each examination belongs to a single class. To conduct this exam for another class, duplicate the exam.');
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
