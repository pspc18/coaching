<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class AiQuestionExtractorService
{
    /**
     * Primary and fallback Gemini models
     */
    protected $primaryModel = 'gemini-flash-lite-latest';
    protected $fallbackModel = 'gemini-3.5-flash-lite';
    protected $proModel = 'gemini-3.8-flash';

    /**
     * Get active Google Gemini API Key
     */
    public function getApiKey(?string $providedKey = null): ?string
    {
        if (!empty($providedKey)) {
            return trim($providedKey);
        }

        // 1. Check settings table
        try {
            $dbKey = DB::table('settings')->value('gemini_api_key');
            if (!empty($dbKey)) {
                return trim($dbKey);
            }
        } catch (Exception $e) {
            // Ignore DB error
        }

        // 2. Check environment / config
        $envKey = env('GEMINI_API_KEY') ?: config('services.gemini.api_key');
        if (!empty($envKey)) {
            return trim($envKey);
        }

        return null;
    }

    /**
     * Get available generation models for the given API key.
     * Queries Google's ListModels endpoint and returns model IDs supporting 'generateContent'.
     */
    public function getAvailableModels(string $apiKey): array
    {
        $cacheKey = 'gemini_models_' . md5($apiKey);
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && !empty($cached)) {
                return $cached;
            }
        }

        try {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
            $response = Http::timeout(10)->get($endpoint);

            if ($response->successful()) {
                $data = $response->json('models', []);
                $available = [];

                foreach ($data as $m) {
                    $name = $m['name'] ?? '';
                    $cleanName = str_replace('models/', '', $name);
                    $methods = $m['supportedGenerationMethods'] ?? [];

                    if (in_array('generateContent', $methods)) {
                        $available[] = $cleanName;
                    }
                }

                if (!empty($available)) {
                    Cache::put($cacheKey, $available, now()->addHours(6));
                    return $available;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fetch Gemini model list: " . $e->getMessage());
        }

        // Fallback candidate list if ListModels is restricted
        return [
            'gemini-flash-lite-latest',
            'gemini-3.5-flash-lite',
            'gemini-3.8-flash',
            'gemini-3.7-flash',
            'gemini-3.6-flash',
            'gemini-3.5-flash',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-3-flash-preview',
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-flash-latest',
            'gemini-1.5-flash'
        ];
    }

    /**
     * Get prioritized list of models to try for generation
     */
    public function getPrioritizedModels(string $apiKey): array
    {
        $available = $this->getAvailableModels($apiKey);

        // Sorting preference: verified stable lightweight models first
        $preferences = [
            'gemini-flash-lite-latest',
            'gemini-3.5-flash-lite',
            'gemini-3.8-flash',
            'gemini-3.7-flash',
            'gemini-3.6-flash',
            'gemini-3.5-flash',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-3-flash-preview',
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-flash-latest',
            'gemini-1.5-flash'
        ];

        $sorted = [];
        foreach ($preferences as $pref) {
            if (in_array($pref, $available)) {
                $sorted[] = $pref;
            }
        }

        foreach ($available as $model) {
            if (!in_array($model, $sorted) && stripos($model, 'gemini') !== false) {
                $sorted[] = $model;
            }
        }

        return !empty($sorted) ? $sorted : $preferences;
    }

    /**
     * Test if a Gemini API key is valid and find working models (Cached to save API requests)
     */
    public function testConnection(?string $apiKey = null): array
    {
        $key = $this->getApiKey($apiKey);
        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'No Google Gemini API key provided. Please enter an API key.'
            ];
        }

        // Cache successful key validation for 24 hours to prevent wasteful repeated testing
        $testCacheKey = 'gemini_key_tested_' . md5($key);
        if (Cache::has($testCacheKey)) {
            $savedModel = Cache::get('gemini_active_working_model', $this->primaryModel);
            return [
                'success' => true,
                'model_used' => $savedModel,
                'message' => "Google Gemini API key is verified and connected! (Model: {$savedModel})",
                'from_cache' => true
            ];
        }

        // 1. Validate the API key against Google's ListModels endpoint
        try {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models?key={$key}";
            $listResponse = Http::timeout(12)->get($endpoint);

            if (!$listResponse->successful()) {
                $errorMsg = $listResponse->json('error.message') ?? $listResponse->body();
                return [
                    'success' => false,
                    'message' => 'Gemini API Error: ' . $errorMsg
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to reach Google Gemini API: ' . $e->getMessage()
            ];
        }

        // 2. Discover supported models for this specific API key
        $modelsToTry = $this->getPrioritizedModels($key);
        $lastError = 'No compatible generation model found for this key.';

        // 3. Test generateContent on the best available candidate
        foreach ($modelsToTry as $model) {
            try {
                $genEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
                $genResponse = Http::timeout(15)->post($genEndpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Respond with JSON only: {"status": "connected", "service": "Arise AI Question Engine"}']
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 8192,
                        'response_mime_type' => 'application/json'
                    ]
                ]);

                if ($genResponse->successful()) {
                    Cache::put($testCacheKey, true, now()->addHours(24));
                    Cache::forever('gemini_active_working_model', $model);

                    return [
                        'success' => true,
                        'model_used' => $model,
                        'message' => "Google Gemini API connected successfully! Active model: {$model}"
                    ];
                }

                $err = $genResponse->json('error.message') ?? $genResponse->body();
                $lastError = $err;
            } catch (Exception $e) {
                $lastError = $e->getMessage();
            }
        }

        // If listModels returned models successfully, the key itself is valid
        $firstAvailable = $modelsToTry[0] ?? 'gemini';
        Cache::put($testCacheKey, true, now()->addHours(24));
        Cache::forever('gemini_active_working_model', $firstAvailable);

        return [
            'success' => true,
            'model_used' => $firstAvailable,
            'message' => "Google Gemini API key is valid and connected! (Model: {$firstAvailable})"
        ];
    }

    /**
     * Extract a single question from an image (File or base64 data)
     */
    public function extractFromImage($imageInput, ?string $customApiKey = null, string $extraInstructions = ''): array
    {
        $key = $this->getApiKey($customApiKey);
        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'Google Gemini API key is not configured. Please enter your API key in AI Settings.'
            ];
        }

        $imagePayload = $this->prepareImagePayload($imageInput);
        if (!$imagePayload['success']) {
            return $imagePayload;
        }

        // 1. Content-Hash Cache (Zero API Token & Zero Call on Identical or Re-analyzed Images)
        $imageHash = md5($imagePayload['data']['inline_data']['data']);
        $cacheKey = 'ai_img_extract_' . $imageHash . '_' . md5($extraInstructions);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && !empty($cached['data'])) {
                return [
                    'success' => true,
                    'model_used' => $cached['model_used'] ?? 'Cache (0 Token)',
                    'data' => $cached['data'],
                    'from_cache' => true,
                    'message' => 'Question loaded instantly from cache without consuming API tokens.'
                ];
            }
        }

        $prompt = <<<EOT
You are an expert Academic Examination Parser AI for school & competitive exams (NEET, JEE, CBSE, State Boards).
Analyze this question image carefully.

TASK:
1. Extract the full question statement. If the question contains math formulas, equations, fractions, square roots, subscripts, superscripts, or Greek symbols, format them in standard LaTeX notation enclosed in dollar signs ($...$).
   Example: \$x^2 + y^2 = 25\$, \$\\frac{a}{b}\$, \$\\sqrt{2x + 1}\$, \$\\alpha, \\beta, \\theta\$.
2. Identify the Question Type:
   - 1 = Multiple Choice (Objective / MCQ with options)
   - 2 = Descriptive / Long answer (no options)
3. For Multiple Choice Questions, extract all 4 options (A, B, C, D). Strip prefix letters like "(A)", "A.", "1." from the option text. Retain any LaTeX math formulas inside options.
4. Detect or solve for the Correct Answer.
   - correct_ans: "0" for Option A, "1" for Option B, "2" for Option C, "3" for Option D, or null if cannot be determined.
   - correct_option_letter: "A", "B", "C", or "D".
5. Provide a concise, clear step-by-step Explanation / Solution.
6. Suggest a difficulty level: "Easy", "Medium", or "Hard".
7. Suggest a topic or concept title.

{$extraInstructions}

Return a STRICT, RAW JSON object matching this schema:
{
  "name": "Full question statement with LaTeX math",
  "question_type_id": 1,
  "ans1": "Option A text",
  "ans2": "Option B text",
  "ans3": "Option C text",
  "ans4": "Option D text",
  "correct_ans": "0",
  "correct_option_letter": "A",
  "explanation": "Step-by-step solution / hint",
  "difficulty_level": "Medium",
  "topic": "Topic / Subject concept"
}

Do NOT wrap the output in markdown code fences like ```json ```. Return pure JSON only.
EOT;

        $result = $this->callGeminiMultimodal([$imagePayload['data']], $prompt, $key);
        if ($result['success'] && !empty($result['data'])) {
            Cache::put($cacheKey, [
                'model_used' => $result['model_used'] ?? $this->primaryModel,
                'data' => $result['data']
            ], now()->addDays(30)); // 30-day cache for zero redundant API usage
        }

        return $result;
    }

    /**
     * Extract multiple questions from a PDF document
     */
    public function extractFromPdf(string $pdfPath, ?string $customApiKey = null, string $extraInstructions = ''): array
    {
        $key = $this->getApiKey($customApiKey);
        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'Google Gemini API key is not configured. Please enter your API key in AI Settings.'
            ];
        }

        if (!file_exists($pdfPath)) {
            return [
                'success' => false,
                'message' => 'Uploaded PDF file could not be found on server.'
            ];
        }

        // 1. Check Content-Hash Cache for PDF (Zero API Token & Zero Call on Identical or Re-uploaded Papers)
        $pdfHash = md5_file($pdfPath);
        $cacheKey = 'ai_pdf_extract_v3_' . $pdfHash . '_' . md5($extraInstructions);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && !empty($cached['data'])) {
                return [
                    'success' => true,
                    'model_used' => $cached['model_used'] ?? 'Cache (0 Token)',
                    'data' => $cached['data'],
                    'from_cache' => true,
                    'message' => 'Questions loaded instantly from local cache without consuming API tokens.'
                ];
            }
        }

        $pdfBytes = file_get_contents($pdfPath);
        if (empty($pdfBytes)) {
            return [
                'success' => false,
                'message' => 'Uploaded PDF file is empty.'
            ];
        }

        $base64Pdf = base64_encode($pdfBytes);
        $pdfPayload = [
            'inline_data' => [
                'mime_type' => 'application/pdf',
                'data' => $base64Pdf
            ]
        ];

        $prompt = <<<EOT
You are an expert Academic Question Paper & Exam Parser AI.
Scan this entire examination document with extreme care and extract EVERY single question present in it.

CRITICAL RULES:
1. COMPLETE EXTRACTION (NO SKIPPING):
   - You MUST extract EVERY SINGLE question on the page. Do NOT stop after the first question.
   - Extract standalone questions (e.g. Q85).
   - If there is a Direction set, Case Study, or Data Interpretation block (e.g. "Direction (86-90): ..."):
     * Extract EVERY question in that set (e.g. Q86, Q87, Q88, Q89, Q90).
     * If the individual sub-questions (Q86-Q90) follow in the document, extract EACH ONE separately, prepending the common Direction statement, table info, and context to the question statement.
     * If this page only shows the "Direction (86-90): [Data / Graph / Pie Chart / Table]" block without separate sub-questions, extract this Direction block itself as a comprehensive question so that the teacher/student gets the entire data, graph context, and chart!
     * NEVER omit, skip, or ignore any Direction block, passage, graph, or question!

2. MATHEMATICAL & SCIENTIFIC FORMULAS:
   - Format ALL math/scientific equations, fractions, square roots, powers, symbols strictly in standard LaTeX (\$...\$ for inline, \$\$...\$\$ for display).
   - E.g. \$x\\left(2x + \\frac{15}{x}\\right) - 11x = 2x\$ and \$y^2 - 4^2 + 2^2y = -2y\$.
   - Symbols: \$\\ge\$, \$\\le\$, \$\\ne\$, \$\\pm\$, \$\\sqrt{x}\$, \$\\frac{a}{b}\$, \$\\theta\$.

3. OPTIONS & ANSWER KEY:
   - For MCQ questions, extract all options without "(a)", "(1)" prefixes.
   - For 4-option questions: ans1, ans2, ans3, ans4.
   - If there is a 5th option (Option E, common in Indian exams: "यदि x = y या..."): extract ans5 if present.
   - Deduce or detect correct_ans: "0" for Option A, "1" for Option B, "2" for Option C, "3" for Option D, "4" for Option E.
   - Detect correct_option_letter: "A", "B", "C", "D", or "E".

4. DIAGRAMS & CHARTS:
   - If a question or Direction block has or refers to a diagram, graph, pie chart, curve, figure, or data table:
     set "has_diagram": true
     set "diagram_hint": "Pie chart showing degree distribution Q1-Q4" (short description)
     set "page_number": 1 (page number where the diagram or question appears)

5. SOLUTION & DIFFICULTY:
   - Provide a brief 1-2 sentence solution / explanation with LaTeX math.
   - Difficulty: "Easy", "Medium", or "Hard".

{$extraInstructions}

Return a STRICT, RAW JSON object matching this schema:
{
  "total_extracted": 2,
  "questions": [
    {
      "q_number": 85,
      "page_number": 1,
      "name": "I: \$x\\left(2x + \\frac{15}{x}\\right) - 11x = 2x\$\\nII: \$y^2 - 4^2 + 2^2y = -2y\$",
      "question_type_id": 1,
      "has_diagram": false,
      "diagram_hint": "",
      "ans1": "यदि \$x > y\$",
      "ans2": "यदि \$x \\ge y\$",
      "ans3": "यदि \$x < y\$",
      "ans4": "यदि \$x \\le y\$",
      "ans5": "यदि \$x = y\$ या कोई संबंध नहीं",
      "correct_ans": "0",
      "correct_option_letter": "A",
      "explanation": "Solving equation I yields \$x = 3/2, 5\$ and equation II yields \$y = -8, 2\$.",
      "difficulty_level": "Medium"
    }
  ]
}

Do NOT wrap the output in markdown code fences. Return pure JSON only.
EOT;

        $result = $this->callGeminiMultimodal([$pdfPayload], $prompt, $key, true);
        if ($result['success'] && !empty($result['data']['questions'])) {
            // Automatically extract diagrams from PDF and match them with diagram questions
            $extractedDiagrams = $this->extractDiagramsFromPdf($pdfPath);
            
            foreach ($result['data']['questions'] as &$q) {
                $page = isset($q['page_number']) ? (int)$q['page_number'] : 1;
                $hasDiagramFlag = !empty($q['has_diagram']);
                $qText = ($q['name'] ?? '') . ' ' . ($q['explanation'] ?? '');
                
                $isDiagramQuestion = $hasDiagramFlag 
                    || preg_match('/(ग्राफ़|graph|diagram|figure|pie chart|चित्र|तालिका|table|chart|direction|निर्देश)/iu', $qText);

                if ($isDiagramQuestion && !empty($extractedDiagrams)) {
                    $matchedDiag = null;
                    foreach ($extractedDiagrams as $diag) {
                        if ($diag['page'] === $page) {
                            $matchedDiag = $diag;
                            break;
                        }
                    }
                    if (!$matchedDiag && !empty($extractedDiagrams[0])) {
                        $matchedDiag = $extractedDiagrams[0];
                    }

                    if ($matchedDiag && empty($q['image'])) {
                        $q['image'] = $matchedDiag['image_path'];
                    }
                }
            }
            unset($q);

            Cache::put($cacheKey, [
                'model_used' => $result['model_used'] ?? $this->primaryModel,
                'data' => $result['data']
            ], now()->addDays(30)); // 30-day cache for zero redundant API usage
        }

        return $result;
    }

    /**
     * Automatically extract diagrams and graphs from a PDF using Python PyMuPDF
     */
    public function extractDiagramsFromPdf(string $pdfPath): array
    {
        try {
            $scriptPath = base_path('app/Scripts/extract_pdf_diagrams.py');
            $outputDir = public_path('uploads/questions');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            if (!file_exists($scriptPath)) {
                return [];
            }

            $cmd = "python " . escapeshellarg($scriptPath) . " " . escapeshellarg($pdfPath) . " " . escapeshellarg($outputDir);
            $output = [];
            $code = 0;
            exec($cmd, $output, $code);

            if ($code === 0 && !empty($output)) {
                $raw = implode('', $output);
                $data = json_decode($raw, true);
                return is_array($data) ? $data : [];
            }
        } catch (\Exception $e) {
            Log::warning("PDF diagram extraction warning: " . $e->getMessage());
        }
        return [];
    }

    /**
     * Core multimodal API call to Gemini
     */
    protected function callGeminiMultimodal(array $mediaParts, string $promptText, string $apiKey, bool $isBulk = false): array
    {
        $activeModel = Cache::get('gemini_active_working_model');
        $modelsToTry = $this->getPrioritizedModels($apiKey);
        if (!empty($activeModel) && in_array($activeModel, $modelsToTry)) {
            $modelsToTry = array_values(array_unique(array_merge([$activeModel], $modelsToTry)));
        }
        $lastError = 'Unknown error';

        foreach ($modelsToTry as $model) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                
                $parts = [];
                $parts[] = ['text' => $promptText];
                foreach ($mediaParts as $media) {
                    $parts[] = $media;
                }

                $timeout = $isBulk ? 120 : 60;

                $response = Http::timeout($timeout)->post($endpoint, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => $parts
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 8192,
                        'response_mime_type' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $jsonBody = $response->json();
                    $candidateText = $jsonBody['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (empty($candidateText)) {
                        $lastError = 'Gemini returned empty candidate content.';
                        continue;
                    }

                    $parsedData = $this->cleanAndParseJson($candidateText);
                    if ($parsedData !== null) {
                        Cache::forever('gemini_active_working_model', $model);
                        return [
                            'success' => true,
                            'model_used' => $model,
                            'data' => $parsedData
                        ];
                    }

                    $lastError = 'Failed to parse JSON response from Gemini.';
                } else {
                    $err = $response->json('error.message') ?? $response->body();
                    $lastError = "Model {$model} returned error: {$err}";
                    Log::warning("Gemini {$model} call failed: {$err}");
                }
            } catch (Exception $e) {
                $lastError = "Model {$model} exception: " . $e->getMessage();
                Log::warning($lastError);
            }
        }

        return [
            'success' => false,
            'message' => $lastError
        ];
    }

    /**
     * Prepare image payload from file path, uploaded file, or base64 string
     */
    protected function prepareImagePayload($input): array
    {
        $base64 = '';
        $mime = 'image/jpeg';

        if (is_string($input)) {
            // Check if data URI: data:image/png;base64,iVBORw...
            if (preg_match('/^data:(image\/[a-zA-Z+]+);base64,(.+)$/', $input, $matches)) {
                $mime = $matches[1];
                $base64 = $matches[2];
            } elseif (file_exists($input)) {
                $bytes = file_get_contents($input);
                $base64 = base64_encode($bytes);
                $ext = strtolower(pathinfo($input, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
            } else {
                // Raw base64 string
                $base64 = trim($input);
            }
        } elseif (is_object($input) && method_exists($input, 'getRealPath')) {
            $bytes = file_get_contents($input->getRealPath());
            $base64 = base64_encode($bytes);
            $mime = $input->getMimeType() ?: 'image/jpeg';
        }

        if (empty($base64)) {
            return [
                'success' => false,
                'message' => 'Invalid or empty image data received.'
            ];
        }

        // Optimize high-resolution images to drastically save Gemini API token and bandwidth costs
        if (extension_loaded('gd') && strlen($base64) > 400000) {
            $decodedBytes = @base64_decode($base64);
            if ($decodedBytes) {
                $img = @imagecreatefromstring($decodedBytes);
                if ($img !== false) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $maxDim = 1600;

                    if ($width > $maxDim || $height > $maxDim) {
                        $scale = min($maxDim / $width, $maxDim / $height);
                        $newWidth = (int)($width * $scale);
                        $newHeight = (int)($height * $scale);
                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($img);
                        $img = $resized;
                    }

                    ob_start();
                    imagejpeg($img, null, 85);
                    $optimizedBytes = ob_get_clean();
                    imagedestroy($img);

                    if ($optimizedBytes && strlen($optimizedBytes) < strlen($decodedBytes)) {
                        $base64 = base64_encode($optimizedBytes);
                        $mime = 'image/jpeg';
                    }
                }
            }
        }

        return [
            'success' => true,
            'data' => [
                'inline_data' => [
                    'mime_type' => $mime,
                    'data' => $base64
                ]
            ]
        ];
    }

    /**
     * Clean and safely parse JSON text from AI response
     */
    protected function cleanAndParseJson(string $rawText)
    {
        $cleaned = trim($rawText);

        // Remove Markdown code block syntax if present
        if (str_starts_with($cleaned, '```json')) {
            $cleaned = substr($cleaned, 7);
        } elseif (str_starts_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 3);
        }

        if (str_ends_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 0, -3);
        }

        $cleaned = trim($cleaned);

        // 1. First attempt: standard decode
        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2. Try extracting first valid JSON { ... } or [ ... ]
        if (preg_match('/(\{(?:[^{}]|(?R))*\}|\[(?:[^\[\]]|(?R))*\])/s', $cleaned, $matches)) {
            $subDecoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($subDecoded)) {
                return $subDecoded;
            }
        }

        // 3. Fix unescaped LaTeX backslashes (\left, \frac, \right, \ge, \le, \sqrt, etc.)
        $sanitized = preg_replace_callback('/(\\\\+)(.)/s', function ($matches) {
            $slashes = $matches[1];
            $char = $matches[2];
            // If even number of backslashes (already escaped), keep as is
            if (strlen($slashes) % 2 === 0) {
                return $slashes . $char;
            }
            // Odd number of backslashes
            if ($char === '"') {
                return $slashes . $char; // preserve valid JSON quote escape \"
            }
            // For LaTeX commands or other single backslashes in JSON strings, escape the backslash
            return $slashes . '\\' . $char;
        }, $cleaned);

        $decoded = json_decode($sanitized, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 4. Repair unclosed strings and brackets if AI token limit truncated JSON mid-output
        $inString = false;
        $escaped = false;
        $stack = [];
        $len = strlen($sanitized);
        for ($i = 0; $i < $len; $i++) {
            $c = $sanitized[$i];
            if ($c === '\\') {
                $escaped = !$escaped;
                continue;
            }
            if ($c === '"' && !$escaped) {
                $inString = !$inString;
            } elseif (!$inString) {
                if ($c === '{') {
                    $stack[] = '}';
                } elseif ($c === '[') {
                    $stack[] = ']';
                } elseif ($c === '}' || $c === ']') {
                    array_pop($stack);
                }
            }
            $escaped = false;
        }

        $repaired = $sanitized;
        if ($inString) {
            $repaired .= '"';
        }
        while (!empty($stack)) {
            $repaired .= array_pop($stack);
        }

        $decoded = json_decode($repaired, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        Log::error("Failed to parse Gemini JSON: " . substr($rawText, 0, 500) . " | Error: " . json_last_error_msg());
        return null;
    }
}
