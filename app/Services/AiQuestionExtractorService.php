<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class AiQuestionExtractorService
{
    /**
     * Primary and fallback Gemini models
     */
    protected $primaryModel = 'gemini-1.5-flash';
    protected $fallbackModel = 'gemini-2.0-flash';
    protected $proModel = 'gemini-1.5-pro';

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
     * Test if a Gemini API key is valid
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

        try {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->primaryModel}:generateContent?key={$key}";
            $response = Http::timeout(15)->post($endpoint, [
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

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Google Gemini API connection successful! Model ' . $this->primaryModel . ' is ready.'
                ];
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            return [
                'success' => false,
                'message' => 'Gemini API Error: ' . $errorMsg
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
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

        return $this->callGeminiMultimodal([$imagePayload['data']], $prompt, $key);
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

        return $this->callGeminiMultimodal([$pdfPayload], $prompt, $key, true);
    }

    /**
     * Core multimodal API call to Gemini
     */
    protected function callGeminiMultimodal(array $mediaParts, string $promptText, string $apiKey, bool $isBulk = false): array
    {
        $modelsToTry = [$this->primaryModel, $this->fallbackModel, $this->proModel];
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
