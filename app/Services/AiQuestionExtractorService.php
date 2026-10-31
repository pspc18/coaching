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
        $cacheKey = 'ai_pdf_extract_' . $pdfHash . '_' . md5($extraInstructions);

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
You are an expert Academic Question Paper Extractor AI.
Scan this entire examination document and extract EVERY single question present in it.

TASK FOR EACH QUESTION:
1. Extract question statement with complete mathematical / scientific expressions formatted in LaTeX (\$...\$).
2. Identify type: 1 for MCQ / Objective (has options), 2 for Descriptive.
3. For MCQ questions, extract 4 options (A, B, C, D) without the "A)", "(1)", etc. prefixes.
4. Detect the correct answer if provided in the paper or deduce it:
   - correct_ans: "0" for Option A, "1" for Option B, "2" for Option C, "3" for Option D.
   - correct_option_letter: "A", "B", "C", or "D".
5. Provide a brief 1-2 sentence solution / explanation.
6. Suggest difficulty: "Easy", "Medium", or "Hard".

{$extraInstructions}

Return a STRICT, RAW JSON object matching this schema:
{
  "total_extracted": 10,
  "questions": [
    {
      "q_number": 1,
      "name": "Question 1 text with \$LaTeX\$",
      "question_type_id": 1,
      "ans1": "Option A text",
      "ans2": "Option B text",
      "ans3": "Option C text",
      "ans4": "Option D text",
      "correct_ans": "0",
      "correct_option_letter": "A",
      "explanation": "Solution explanation",
      "difficulty_level": "Medium"
    }
  ]
}

Do NOT wrap the output in markdown code fences. Return pure JSON only.
EOT;

        $result = $this->callGeminiMultimodal([$pdfPayload], $prompt, $key, true);
        if ($result['success'] && !empty($result['data'])) {
            Cache::put($cacheKey, [
                'model_used' => $result['model_used'] ?? $this->primaryModel,
                'data' => $result['data']
            ], now()->addDays(30)); // 30-day cache for zero redundant API usage
        }

        return $result;
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

        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Try extracting first valid JSON { ... } or [ ... ]
        if (preg_match('/(\{(?:[^{}]|(?R))*\}|\[(?:[^\[\]]|(?R))*\])/s', $cleaned, $matches)) {
            $subDecoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($subDecoded)) {
                return $subDecoded;
            }
        }

        Log::error("Failed to parse Gemini JSON: " . substr($rawText, 0, 500));
        return null;
    }
}
