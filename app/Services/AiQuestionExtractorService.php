<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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
                    'message' => 'AI Engine Error: ' . $errorMsg
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to reach AI Engine: ' . $e->getMessage()
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
                        'message' => "AI Engine connected successfully!"
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
            'message' => "AI API key is valid and connected!"
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
                'message' => 'AI API key is not configured. Please enter your API key in AI Settings.'
            ];
        }

        $imagePayload = $this->prepareImagePayload($imageInput);
        if (!$imagePayload['success']) {
            return $imagePayload;
        }

        $prompt = <<<EOT
You are an expert Academic Examination Parser AI for school & competitive exams (NEET, JEE, CBSE, State Boards, Banking, SSC).
Analyze this question image carefully.

TASK:
1. Extract the full question statement. If the question contains math formulas, equations, fractions, square roots, subscripts, superscripts, or Greek symbols, format them in standard LaTeX notation enclosed in dollar signs ($...$).
   Example: \$x^2 + y^2 = 25\$, \$\\frac{a}{b}\$, \$\\sqrt{2x + 1}\$, \$\\alpha, \\beta, \\theta\$.
2. If there is a Direction set (e.g. "Direction (108-110): ..."):
   - "direction": Full introductory instructions / passage.
   - "direction_table": If there is a table, convert to clean HTML (<table>...</table>).
   - "group_id": e.g. "dir_108_110".
   - "sub_question_no": e.g. "108".
3. Identify the Question Type:
   - 1 = Multiple Choice (Objective / MCQ with options)
   - 2 = Descriptive / Long answer (no options)
4. For Multiple Choice Questions, extract all options (ans1, ans2, ans3, ans4, and ans5 for Option E if present). Strip prefix letters like "(A)", "A.", "1." from the option text. Retain any LaTeX math formulas inside options.
5. Detect or solve for the Correct Answer:
   - correct_ans: "0" for Option A, "1" for Option B, "2" for Option C, "3" for Option D, "4" for Option E.
   - correct_option_letter: "A", "B", "C", "D", or "E".
6. Provide a concise, clear step-by-step Explanation / Solution.
7. Suggest a difficulty level: "Easy", "Medium", or "Hard".
8. Suggest a topic or concept title.

{$extraInstructions}

Return a STRICT, RAW JSON object matching this schema:
{
  "name": "Full question statement with LaTeX math",
  "direction": "Optional direction / passage text if part of a set",
  "direction_table": "<table class='table table-bordered table-sm'>...</table>",
  "group_id": "dir_108_110",
  "sub_question_no": "108",
  "question_type_id": 1,
  "ans1": "Option A text",
  "ans2": "Option B text",
  "ans3": "Option C text",
  "ans4": "Option D text",
  "ans5": "Option E text (if present)",
  "correct_ans": "0",
  "correct_option_letter": "A",
  "explanation": "Step-by-step solution / hint",
  "difficulty_level": "Medium",
  "topic": "Topic / Subject concept"
}

Do NOT wrap the output in markdown code fences like ```json ```. Return pure JSON only.
EOT;

        $result = $this->callGeminiMultimodal([$imagePayload['data']], $prompt, $key);
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
                'message' => 'AI API key is not configured. Please enter your API key in AI Settings.'
            ];
        }

        if (!file_exists($pdfPath)) {
            return [
                'success' => false,
                'message' => 'Uploaded PDF file could not be found on server.'
            ];
        }

        $ext = strtolower(pathinfo($pdfPath, PATHINFO_EXTENSION));
        $extractedDiagrams = [];

        $pdfToProcess = $pdfPath;
        $focusedInfo = null;

        if ($ext === 'pdf') {
            // Intelligent Target Page Focus: If specific question numbers or direction range are requested,
            // slice the PDF to those exact pages for maximum AI vision clarity and complete token budget!
            if (!empty($extraInstructions)) {
                $focusRes = $this->focusPdfPages($pdfPath, $extraInstructions);
                if (!empty($focusRes['focused']) && !empty($focusRes['focused_pdf']) && file_exists($focusRes['focused_pdf'])) {
                    $pdfToProcess = $focusRes['focused_pdf'];
                    $focusedInfo = $focusRes;
                }
            }

            $pdfBytes = file_get_contents($pdfToProcess);
            if (empty($pdfBytes)) {
                return [
                    'success' => false,
                    'message' => 'Uploaded PDF file could not be read.'
                ];
            }
            $base64Pdf = base64_encode($pdfBytes);
            $mediaPayload = [
                'inline_data' => [
                    'mime_type' => 'application/pdf',
                    'data' => $base64Pdf
                ]
            ];
            // Extract diagrams from target PDF
            $extractedDiagrams = $this->extractDiagramsFromPdf($pdfToProcess);
        } else {
            // Document is an image (PNG, JPG, WEBP)
            $imgPayload = $this->prepareImagePayload($pdfPath);
            if (!$imgPayload['success']) {
                return $imgPayload;
            }
            $mediaPayload = $imgPayload['data'];

            // Store a copy in uploads for diagram attachment
            $filename = 'paper_img_' . time() . '_' . Str::random(6) . '.' . ($ext ?: 'jpg');
            $this->mirrorSaveQuestionFile($filename, $pdfPath, false);
            $extractedDiagrams = [
                [
                    'id' => 'diag_img_1',
                    'page' => 1,
                    'type' => 'image',
                    'image_path' => 'uploads/questions/' . $filename
                ]
            ];
        }

        $diagramsCatalogPrompt = "";
        if (!empty($extractedDiagrams)) {
            $diagramsCatalogPrompt = "DOCUMENT EXTRACTED DIAGRAMS CATALOG:\n";
            $diagramsCatalogPrompt .= "The document was pre-scanned and the following diagrams were detected and extracted:\n";
            foreach ($extractedDiagrams as $d) {
                $diagId = $d['id'] ?? ('diag_p' . ($d['page'] ?? 1));
                $dPage = $d['page'] ?? 1;
                $dType = $d['type'] ?? 'diagram';
                $dirs = !empty($d['directions']) ? implode(', ', array_column($d['directions'], 'label')) : 'None';
                $qCovered = !empty($d['question_numbers']) ? implode(', ', array_slice($d['question_numbers'], 0, 10)) : 'None';
                $hint = !empty($d['context_hint']) ? (' | Context: ' . mb_substr($d['context_hint'], 0, 80)) : '';
                $diagramsCatalogPrompt .= "- ID: \"{$diagId}\" | Page: {$dPage} | Type: {$dType} | Associated Directions: {$dirs} | Applicable Questions: {$qCovered}{$hint}\n";
            }
            $diagramsCatalogPrompt .= "\nDIAGRAM MAPPING RULES:\n";
            $diagramsCatalogPrompt .= "1. For every question or direction set, specify \"page_number\" (the exact 1-based page number where it appears).\n";
            $diagramsCatalogPrompt .= "2. If a question or direction group references or belongs to one of the diagrams above, set \"diagram_id\" to its exact ID (e.g. \"{$extractedDiagrams[0]['id']}\"). Set to null if no diagram belongs to it.\n";
            $diagramsCatalogPrompt .= "3. DO NOT map a diagram from a different direction set.\n\n";
        }

        $prompt = <<<EOT
You are an expert Academic Question Paper & Exam Parser AI for competitive exams (Bank PO/Clerk, SSC, UPSC, NEET, JEE, State PSC).
Scan this examination document with extreme precision and extract EVERY question present in it.

{$diagramsCatalogPrompt}
COMPREHENSIVE MULTI-PATTERN PARSING INTELLIGENCE:
Questions in competitive exams vary widely in structure. You MUST identify and extract every component into its designated field:

1. DIRECTION SETS / COMPREHENSION PASSAGES / DATA INTERPRETATION (DI):
   When questions belong to a Direction block, Data Interpretation, Caselet, or Passage (e.g. "Direction (86-90): ...", "Direction (108-110): ..."):
   * "group_id": A consistent unique slug, e.g. "dir_86_90" or "dir_108_110".
   * "direction": Extract ONLY the introductory paragraph / narrative statement.
     CRITICAL: NEVER put raw data tables or "नोट / Note" constraint blocks inside "direction"!
   * "direction_table": If the set contains ANY data table, matrix, or tabular values (e.g. quarterly projects, city data, percentages):
     - You MUST convert it into a COMPLETE, FULL HTML table with all headers and every single row:
       <table class="table table-bordered table-sm text-center"><thead><tr><th>Col1</th><th>Col2</th></tr></thead><tbody><tr><td>Val1</td><td>Val2</td></tr></tbody></table>
     - NEVER leave <tbody> empty. All rows MUST be present with their exact numbers/percentages.
     - If the set does NOT have a table, set to null.
   * "direction_notes": If there are notes, conditions, mathematical equations, or variable rules given below the table or graph (e.g. "नोट: पाई (x° + y°)... x° = 21.6°... अंतर 108 है"):
     - Extract them as clean, well-formatted HTML with standard LaTeX math:
       <div class="alert alert-warning-light border mt-2 py-2 px-3 small font-weight-normal text-dark" style="background:#fffbeb; border-left: 3px solid #f59e0b !important;"><strong class="text-dark"><i class="fa fa-info-circle text-warning mr-1"></i> नोट:</strong> ...</div>
     - If there are no notes, set to null.
   * "has_diagram": true if the direction contains a graph, chart, pie chart, or illustration.
   * "diagram_id": Exact ID from the catalog above if applicable, otherwise null.
   * EXTRACT EVERY SUB-QUESTION: E.g., for Direction (86-90), extract ALL 5 sub-questions: Q86, Q87, Q88, Q89, and Q90. Never omit any sub-question!
   * In each sub-question:
     - "sub_question_no": Question number e.g. "86", "87", "88", "89", "90".
     - "name": Clean statement of the specific sub-question. Do NOT repeat the entire direction passage inside "name".
     - "group_id", "direction", "direction_table", "direction_notes" MUST be set on all sub-questions in that group.

2. STANDALONE QUESTIONS:
   - For standalone questions without a direction passage, leave "group_id", "direction", "direction_table", "direction_notes" as null.
   - If a standalone question contains a data table, embed the complete HTML <table> directly inside "name".
   - If it has its own diagram/figure, set "has_diagram": true and "diagram_id" if matched.

3. MATHEMATICAL & SCIENTIFIC FORMULAS:
   - Format ALL equations, fractions, square roots, powers, symbols strictly in standard LaTeX notation (\$...\$ for inline, \$\$...\$\$ for block).
   - E.g. \$x\\left(2x + \\frac{15}{x}\\right) - 11x = 2x\$, \$x^\\circ = 21.6^\\circ\$, \$\\ge\$, \$\\le\$, \$\\frac{a}{b}\$, \$\\sqrt{x}\$.

4. OPTIONS (A TO E):
   - Extract all options without "(a)", "(1)", "(A)" prefixes.
   - Support up to 5 options: ans1 (A), ans2 (B), ans3 (C), ans4 (D), and ans5 (E) if present.
   - correct_ans: "0" for A, "1" for B, "2" for C, "3" for D, "4" for E.
   - correct_option_letter: "A", "B", "C", "D", or "E".

5. DETAILED STEP-BY-STEP EXPLANATION:
   - Provide an accurate step-by-step mathematical explanation / derivation for the answer.
   - Difficulty: "Easy", "Medium", or "Hard".

{$extraInstructions}

Return a STRICT, RAW JSON object matching this schema:
{
  "total_extracted": 5,
  "questions": [
    {
      "q_number": 86,
      "sub_question_no": "86",
      "page_number": 12,
      "group_id": "dir_86_90",
      "direction": "Direction (86-90): नीचे दिया गया ग्राफ़ 4 तिमाहियों Q1, Q2, Q3 और Q4 में संचालित परियोजनाओं (निजी + सार्वजनिक) की कुल संख्या का डिग्री वितरण दर्शाता है। तालिका संबंधित वर्षों में संचालित परियोजनाओं की तुलना में संचालित निजी परियोजनाओं का प्रतिशत दर्शाती है।",
      "direction_table": "<table class='table table-bordered table-sm text-center'><thead><tr><th>तिमाही</th><th>कुल संचालित परियोजनाओं की तुलना में निजी परियोजनाओं का प्रतिशत</th></tr></thead><tbody><tr><td>Q1</td><td>3a%</td></tr><tr><td>Q2</td><td>35%</td></tr><tr><td>Q3</td><td>50%</td></tr><tr><td>Q4</td><td>a%</td></tr></tbody></table>",
      "direction_notes": "<div class='alert alert-warning-light border mt-2 py-2 px-3 small font-weight-normal text-dark' style='background:#fffbeb; border-left: 3px solid #f59e0b !important;'><strong class='text-dark'><i class='fa fa-info-circle text-warning mr-1'></i> नोट:</strong> पाई (\$x^\\circ + y^\\circ\$) के पाई का केंद्रीय कोण का माप 360^\\circ के बराबर नहीं है।<br>\$x^\\circ = 21.6^\\circ\$ का मान \$y^\\circ\$ से अधिक है।<br>Q2 में संचालित निजी और सार्वजनिक परियोजनाओं के बीच का अंतर 108 है।</div>",
      "name": "यदि Q1 और Q3 में सार्वजनिक परियोजना संचालन का औसत 150 है, तो Q1 द्वारा निजी परियोजना संचालन, Q2 द्वारा कुल परियोजना संचालन का कितना प्रतिशत है?",
      "question_type_id": 1,
      "has_diagram": true,
      "diagram_id": "diag_p12_2",
      "diagram_hint": "Pie chart showing degree distribution of projects across quarters",
      "ans1": "120",
      "ans2": "50",
      "ans3": "100",
      "ans4": "60",
      "ans5": "70",
      "correct_ans": "0",
      "correct_option_letter": "A",
      "explanation": "Step-by-step mathematical derivation...",
      "difficulty_level": "Medium"
    }
  ]
}

Do NOT wrap the output in markdown code fences. Return pure JSON only.
EOT;

        $result = $this->callGeminiMultimodal([$mediaPayload], $prompt, $key, true);

        // Clean up temporary sliced PDF if one was created for focused zoom
        if (!empty($focusedInfo['focused_pdf']) && file_exists($focusedInfo['focused_pdf']) && $focusedInfo['focused_pdf'] !== $pdfPath) {
            @unlink($focusedInfo['focused_pdf']);
        }

        if ($result['success'] && !empty($result['data']['questions'])) {
            // Index extracted diagrams by id
            $diagramsById = [];
            foreach ($extractedDiagrams as $d) {
                if (!empty($d['id'])) {
                    $diagramsById[$d['id']] = $d;
                }
            }

            // Track diagrams already assigned to groups
            $groupDiagramMap = [];

            foreach ($result['data']['questions'] as &$q) {
                // Run post-processing sanitizer to clean table, direction text, and notes
                $this->sanitizeDirectionAndTable($q);
                $this->sanitizeQuestionName($q);

                $groupId = $q['group_id'] ?? null;
                $hasDiagramFlag = !empty($q['has_diagram']);
                $qText = ($q['name'] ?? '') . ' ' . ($q['direction'] ?? '') . ' ' . ($q['explanation'] ?? '');
                
                $isDiagramQuestion = $hasDiagramFlag 
                    || !empty($q['diagram_id'])
                    || preg_match('/(ग्राफ़|graph|diagram|figure|pie chart|चित्र|तालिका|chart|bar graph|line graph)/iu', $qText);

                if ($isDiagramQuestion && !empty($extractedDiagrams)) {
                    // Check if group already has an assigned diagram
                    if ($groupId && isset($groupDiagramMap[$groupId])) {
                        $q['image'] = $groupDiagramMap[$groupId];
                        continue;
                    }

                    $matchedDiag = null;

                    // Priority 1: Direct diagram_id returned by Gemini
                    if (!empty($q['diagram_id']) && isset($diagramsById[$q['diagram_id']])) {
                        $matchedDiag = $diagramsById[$q['diagram_id']];
                    }

                    // Priority 2: Match by Direction range or Question Number
                    if (!$matchedDiag) {
                        $qNum = null;
                        if (!empty($q['sub_question_no']) && is_numeric($q['sub_question_no'])) {
                            $qNum = (int)$q['sub_question_no'];
                        } elseif (!empty($q['q_number']) && is_numeric($q['q_number'])) {
                            $qNum = (int)$q['q_number'];
                        }

                        // Extract direction numbers e.g. "86-90" or "dir_86_90"
                        $dirStart = null;
                        $dirEnd = null;
                        if (!empty($q['direction']) && preg_match('/(?:Direction|Directions|निर्देश)\s*[\(:\s]*(\d+)\s*[-–to]+\s*(\d+)/iu', $q['direction'], $dm)) {
                            $dirStart = (int)$dm[1];
                            $dirEnd = (int)$dm[2];
                        } elseif (!empty($groupId) && preg_match('/(\d+)[_-](\d+)/', $groupId, $gm)) {
                            $dirStart = (int)$gm[1];
                            $dirEnd = (int)$gm[2];
                        }

                        foreach ($extractedDiagrams as $d) {
                            // Check matching direction range
                            if (!empty($d['directions'])) {
                                foreach ($d['directions'] as $dd) {
                                    if ($dirStart !== null && $dirEnd !== null) {
                                        if ($dd['start'] === $dirStart && $dd['end'] === $dirEnd) {
                                            $matchedDiag = $d;
                                            break 2;
                                        }
                                    }
                                    if ($qNum !== null && $qNum >= $dd['start'] && $qNum <= $dd['end']) {
                                        $matchedDiag = $d;
                                        break 2;
                                    }
                                }
                            }

                            // Check question_numbers list
                            if ($qNum !== null && !empty($d['question_numbers']) && in_array($qNum, $d['question_numbers'], true)) {
                                $matchedDiag = $d;
                                break;
                            }
                        }
                    }

                    // Priority 3: Match by exact page number
                    if (!$matchedDiag && !empty($q['page_number'])) {
                        $qPage = (int)$q['page_number'];
                        $pageDiags = array_values(array_filter($extractedDiagrams, fn($d) => (int)$d['page'] === $qPage));
                        if (!empty($pageDiags)) {
                            $matchedDiag = $pageDiags[0];
                        }
                    }

                    // Assign matched diagram
                    if ($matchedDiag) {
                        $q['image'] = $matchedDiag['image_path'];
                        if ($groupId) {
                            $groupDiagramMap[$groupId] = $matchedDiag['image_path'];
                        }
                    } else {
                        // CRITICAL: If no diagram matches by ID, Direction Range, or Page Number,
                        // NEVER grab an arbitrary diagram from an unrelated page!
                        $q['image'] = null;
                    }
                }
            }
            unset($q);
        }

        return $result;
    }

    /**
     * Generate original exam questions and/or directional question sets using AI from prompt and optional file reference (PDF / Image)
     *
     * @param string $userPrompt Instructions provided by user
     * @param string|null $filePath Path to uploaded reference PDF or Image, or null if text-only
     * @param string|null $customApiKey
     * @param array $context Additional academic context (class_name, subject_name, difficulty, options_count)
     * @return array
     */
    public function generateQuestionsWithAi(string $userPrompt, ?string $filePath = null, ?string $customApiKey = null, array $context = []): array
    {
        $key = $this->getApiKey($customApiKey);
        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'AI API key is not configured. Please enter your API key in AI Settings.'
            ];
        }

        $mediaParts = [];
        $extractedDiagrams = [];
        $referenceDocNote = "";
        $uploadedRefImage = null;

        $lowerPrompt = mb_strtolower($userPrompt);

        // Detect if user prompt requests creation of SIMILAR / NEW / FRESH questions inspired by reference
        $isSimilarRequest = (
            strpos($lowerPrompt, 'similar') !== false ||
            strpos($lowerPrompt, 'dusra') !== false ||
            strpos($lowerPrompt, 'doosra') !== false ||
            strpos($lowerPrompt, 'naya') !== false ||
            strpos($lowerPrompt, 'naye') !== false ||
            strpos($lowerPrompt, 'new') !== false ||
            strpos($lowerPrompt, 'fresh') !== false ||
            strpos($lowerPrompt, 'create') !== false ||
            strpos($lowerPrompt, 'generate') !== false ||
            strpos($lowerPrompt, 'banao') !== false ||
            strpos($lowerPrompt, 'like this') !== false ||
            strpos($lowerPrompt, 'jaisa') !== false ||
            strpos($lowerPrompt, 'jaise') !== false ||
            strpos($lowerPrompt, 'is pattern par') !== false ||
            strpos($lowerPrompt, 'is tarah ka') !== false ||
            strpos($lowerPrompt, 'another') !== false ||
            strpos($lowerPrompt, 'different') !== false ||
            strpos($lowerPrompt, 'based on this') !== false ||
            strpos($lowerPrompt, 'inspired') !== false
        );

        $isExactExtractRequest = (
            strpos($lowerPrompt, 'extract') !== false ||
            strpos($lowerPrompt, 'transcribe') !== false ||
            strpos($lowerPrompt, 'ocr') !== false ||
            strpos($lowerPrompt, 'as it is') !== false ||
            strpos($lowerPrompt, 'same to same') !== false ||
            strpos($lowerPrompt, 'nikalo') !== false ||
            strpos($lowerPrompt, 'nikal do') !== false ||
            strpos($lowerPrompt, 'copy') !== false
        );

        if (!empty($filePath) && file_exists($filePath)) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                $pdfBytes = file_get_contents($filePath);
                if (!empty($pdfBytes)) {
                    $mediaParts[] = [
                        'inline_data' => [
                            'mime_type' => 'application/pdf',
                            'data' => base64_encode($pdfBytes)
                        ]
                    ];
                    $extractedDiagrams = $this->extractDiagramsFromPdf($filePath);
                    if ($isSimilarRequest && !$isExactExtractRequest) {
                        $referenceDocNote = <<<'NOTE'
A REFERENCE PDF DOCUMENT IS ATTACHED AS INSPIRATION ONLY.
CRITICAL MANDATE - SYNTHESIZE BRAND NEW SIMILAR QUESTIONS (DO NOT EXTRACT / TRANSCRIBE):
The user explicitly asked to create a "SIMILAR" / "NEW" question based on this reference.
1. DO NOT extract, copy, or transcribe the existing questions printed in the document!
2. Create BRAND NEW, ORIGINAL questions with fresh context, new numerical data, and original options following this pattern.
3. If this is a Directional Set (Data Interpretation, Case Study), provide the complete new dataset in "direction_table" as a clean HTML table AND generate a clean vector SVG chart in "diagram_svg". Set "diagram_id": null.
NOTE;
                    } elseif ($isExactExtractRequest) {
                        $referenceDocNote = "A REFERENCE PDF DOCUMENT IS ATTACHED. The user requested to extract questions from it. Carefully extract and transcribe the exact questions, passages, tables, and options as printed in the document.";
                    } else {
                        $referenceDocNote = "A REFERENCE PDF DOCUMENT IS ATTACHED. Carefully analyze its syllabus, structure, question difficulty, patterns, and terminology. If the user prompt asks for similar/new questions, synthesize new questions with fresh data (do not copy). If the user asks to extract, transcribe verbatim.";
                    }
                }
            } else {
                $imgPayload = $this->prepareImagePayload($filePath);
                if ($imgPayload['success']) {
                    $mediaParts[] = $imgPayload['data'];

                    // Save a copy in uploads for diagram attachment if extract mode
                    $filename = 'ai_ref_' . time() . '_' . Str::random(6) . '.' . ($ext ?: 'jpg');
                    $uploadedRefImage = $this->mirrorSaveQuestionFile($filename, $filePath, false);
                    $extractedDiagrams = [
                        [
                            'id' => 'diag_img_1',
                            'page' => 1,
                            'type' => 'image',
                            'image_path' => $uploadedRefImage
                        ]
                    ];

                    if ($isSimilarRequest && !$isExactExtractRequest) {
                        $referenceDocNote = <<<'NOTE'
A REFERENCE IMAGE IS ATTACHED AS INSPIRATION ONLY.
CRITICAL MANDATE - SYNTHESIZE BRAND NEW SIMILAR QUESTIONS (STRICTLY NO TRANSCRIPTION / EXTRACTION):
The user explicitly asked to CREATE A NEW / SIMILAR QUESTION inspired by this reference (e.g. "similar question ka ek dusra question similar create kro", "create a similar question based on this").

1. STRICTLY FORBIDDEN TO EXTRACT OR TRANSCRIBE:
   - Do NOT translate, OCR, extract, or copy the exact questions or numbers printed in the attached image!
   - The user already has the questions in the image. They want a FRESH, ORIGINAL, PARALLEL question set testing similar concepts!

2. BRAND NEW CONTEXT, SCENARIO & VALUES:
   - Create a NEW story/context with DIFFERENT entities and DIFFERENT numerical data.
   - For example:
     * If the reference shows a Double Bar Graph of "Admissions in School A and School B from 2012 to 2016", your new question must use a NEW context like "Number of Vehicles Produced by Plants X and Y from 2018 to 2022" or "College Enrollments in Science vs Arts", with completely new, clean, realistic numbers!
     * If the reference is a Geometry triangle, change the vertex labels, dimensions, and angle measures!
     * If the reference is a Number System divisibility question, change the numbers and divisor constraints!

3. FOR DIRECTIONAL SETS (DATA INTERPRETATION / PASSAGES):
   - "direction": A clear narrative introducing the NEW context and dataset.
   - "direction_table": You MUST provide the full new tabular data in a clean HTML table:
     <table class="table table-bordered table-sm text-center"><thead>...</thead><tbody>...</tbody></table>
   - "diagram_svg": You MUST generate a clean, responsive vector SVG chart (e.g. double bar chart, line graph, or pie chart) with viewBox="0 0 600 380" (ensure adequate vertical height so all bars, baseline axes, and labels fit comfortably without clipping) displaying the NEW data visually!
   - DO NOT set "diagram_id": "diag_img_1" (set "diagram_id": null and "has_diagram": true). The attached image is an old photo with printed test questions; attaching it to the new question set is strictly forbidden!

4. SUB-QUESTIONS:
   - Formulate new sub-questions covering similar analytical angles (e.g. Percentage More/Less, Ratio between categories, Average, Trend differences).
   - Ensure all options (ans1-ans4), correct answer, and LaTeX explanations are 100% mathematically accurate and derived from your NEW dataset.
NOTE;
                    } elseif ($isExactExtractRequest) {
                        $referenceDocNote = "A REFERENCE IMAGE IS ATTACHED. The user requested to EXTRACT / TRANSCRIBE questions from it. Carefully extract and transcribe the exact questions, passages, tables, and options as printed in the image. If any question relies on this image/diagram, set \"diagram_id\": \"diag_img_1\" and \"has_diagram\": true.";
                    } else {
                        $referenceDocNote = "A REFERENCE IMAGE IS ATTACHED. If the user prompt asks to create 'similar' / 'new' / 'another' questions: DO NOT copy or extract the printed questions; synthesize fresh questions with new data, an HTML table and/or new SVG diagram, and set \"diagram_id\": null. If the user prompt asks to extract or transcribe the document's questions: Extract the exact questions as printed.";
                    }
                }
            }
        }

        $diagramsCatalogPrompt = "";
        if (!empty($extractedDiagrams) && (!$isSimilarRequest || $isExactExtractRequest)) {
            $diagramsCatalogPrompt = "DOCUMENT EXTRACTED DIAGRAMS CATALOG:\n";
            $diagramsCatalogPrompt .= "The reference document was pre-scanned and the following diagrams were extracted:\n";
            foreach ($extractedDiagrams as $d) {
                $diagId = $d['id'] ?? ('diag_p' . ($d['page'] ?? 1));
                $dPage = $d['page'] ?? 1;
                $dType = $d['type'] ?? 'diagram';
                $hint = !empty($d['context_hint']) ? (' | Context: ' . mb_substr($d['context_hint'], 0, 80)) : '';
                $diagramsCatalogPrompt .= "- ID: \"{$diagId}\" | Page: {$dPage} | Type: {$dType}{$hint}\n";
            }
            $diagramsCatalogPrompt .= "\nDIAGRAM & TABLE MAPPING RULES:\n";
            $diagramsCatalogPrompt .= "1. If a question or directional set refers to or depends on an extracted diagram/chart/figure above, set \"diagram_id\" to its exact ID (e.g. \"{$extractedDiagrams[0]['id']}\") and \"has_diagram\": true.\n";
            $diagramsCatalogPrompt .= "2. If questions contain data tables, matrix, or charts, convert all tabular data to clean HTML <table>...</table> as specified below.\n\n";
        } elseif ($isSimilarRequest && !empty($extractedDiagrams)) {
            $diagramsCatalogPrompt = "NOTE ON ATTACHED REFERENCE: The user has requested a BRAND NEW SIMILAR question. DO NOT reference or link the attached old image/diagram ID. Instead, synthesize your own new data table in 'direction_table' and/or new vector SVG chart in 'diagram_svg'. Set 'diagram_id': null.\n\n";
        }

        $className = !empty($context['class_name']) ? $context['class_name'] : 'Standard / Competitive Level';
        $subjectName = !empty($context['subject_name']) ? $context['subject_name'] : 'General Examination';
        $chapterName = !empty($context['chapter_name']) ? trim($context['chapter_name']) : '';
        $topicName = !empty($context['topic_name']) ? trim($context['topic_name']) : '';
        $mode = !empty($context['mode']) ? strtolower(trim($context['mode'])) : 'standalone';
        $language = !empty($context['language']) ? strtolower(trim($context['language'])) : 'en';
        $diffPref = !empty($context['difficulty']) ? $context['difficulty'] : 'Mixed (Easy, Medium, Hard)';
        $optionsCount = !empty($context['options_count']) ? (int)$context['options_count'] : 4;
        $questionTypeId = !empty($context['question_type_id']) ? (int)$context['question_type_id'] : 1;

        $chapterTopicContext = "";
        if (!empty($chapterName)) {
            $chapterTopicContext .= "- Chapter (अध्याय): {$chapterName}\n";
        }
        if (!empty($topicName)) {
            $chapterTopicContext .= "- Topic / Sub-topic: {$topicName}\n";
        }
        if (!empty($chapterName) || !empty($topicName)) {
            $chapterTopicContext .= "CRITICAL SYLLABUS CONSTRAINT: All generated questions MUST strictly cover Chapter '{$chapterName}' and Topic '{$topicName}'. Treat these as mandatory primary keywords and core concepts for questions, formulas, and solutions.\n";
        }

        if ($mode === 'directional') {
            $modeInstruction = "- Target Mode: DIRECTIONAL SET MODE (COMMON PASSAGE + LINKED SUB-QUESTIONS).\n  CRITICAL MODE CONSTRAINT: You MUST generate ONLY Directional Question Set(s) with a rich Common Narrative / Comprehension Passage / Case Study / Data Interpretation Table in 'direction' and linked sub-questions in 'sub_questions' array. 'standalone_questions' array MUST BE EMPTY ([]). DO NOT generate standalone questions.";
        } elseif ($mode === 'standalone') {
            $modeInstruction = "- Target Mode: STANDALONE QUESTIONS MODE.\n  CRITICAL MODE CONSTRAINT: You MUST generate ONLY independent questions in 'standalone_questions' array. 'directional_sets' array MUST BE EMPTY ([]). DO NOT generate directional sets.";
        } else {
            $modeInstruction = "- Target Mode: Balanced (Standalone & Directional Sets as requested in user prompt).";
        }

        if ($language === 'both' || $language === 'bilingual') {
            $languageInstruction = "- Medium / Language: BILINGUAL (द्विभाषी - English + Hindi).\n  CRITICAL BILINGUAL REQUIREMENT: Provide both English and authentic Hindi for all items:\n  - English statement in 'name' AND Hindi translation in 'name_hi'.\n  - English options in 'ans1'..'ans5' AND Hindi options in 'ans1_hi'..'ans5_hi'.\n  - English explanation in 'explanation' AND Hindi in 'explanation_hi'.\n  - For directional sets, English passage in 'direction' AND Hindi in 'direction_hi'.";
        } elseif ($language === 'hi') {
            $languageInstruction = "- Medium / Language: HINDI MEDIUM (हिन्दी माध्यम).\n  CRITICAL HINDI REQUIREMENT: All questions, options, and explanations MUST be written in authentic Hindi (Devanagari script) with standard technical / mathematical terminology. Set 'name_hi' (and 'name'), 'ans1_hi'..'ans5_hi' (and 'ans1'..'ans5'), 'explanation_hi', and 'direction_hi' in Hindi.";
        } else {
            $languageInstruction = "- Medium / Language: English Medium.";
        }

        // Detect if user prompt requests diagrams / figures / charts
        $lowerPrompt = mb_strtolower($userPrompt);
        $isDiagramRequested = (
            strpos($lowerPrompt, 'diagram') !== false ||
            strpos($lowerPrompt, 'figure') !== false ||
            strpos($lowerPrompt, 'fig') !== false ||
            strpos($lowerPrompt, 'image') !== false ||
            strpos($lowerPrompt, 'drawing') !== false ||
            strpos($lowerPrompt, 'chart') !== false ||
            strpos($lowerPrompt, 'graph') !== false ||
            strpos($lowerPrompt, 'visual') !== false ||
            strpos($lowerPrompt, 'flowchart') !== false ||
            strpos($lowerPrompt, 'venn') !== false ||
            strpos($lowerPrompt, 'geometry') !== false ||
            strpos($lowerPrompt, 'triangle') !== false ||
            strpos($lowerPrompt, 'circle') !== false ||
            strpos($lowerPrompt, 'polygon') !== false ||
            strpos($lowerPrompt, 'circuit') !== false ||
            strpos($lowerPrompt, 'chitra') !== false ||
            strpos($lowerPrompt, 'aakriti') !== false ||
            strpos($lowerPrompt, 'rekha') !== false
        );

        if ($isDiagramRequested) {
            $diagramInstruction = <<<'DIAG'
- SPECIAL INSTRUCTION - MANDATORY VECTOR SVG DIAGRAM GENERATION:
  THE USER HAS EXPLICITLY REQUESTED DIAGRAMS / FIGURES / VISUAL QUESTIONS!
  YOU MUST GENERATE ONE OR MORE QUESTIONS WITH HIGH-QUALITY EMBEDDED VECTOR SVG DIAGRAMS!
  Do NOT say you cannot create images. You CAN generate clean, complete, professional Scalable Vector Graphics (SVG) code!
  For every diagrammatic question:
  - Provide complete, valid SVG vector graphics inside the "diagram_svg" JSON property (e.g. "<svg viewBox=\"0 0 600 360\" xmlns=\"http://www.w3.org/2000/svg\">...</svg>").
  - Set "has_diagram": true and "diagram_id": null.
  - The SVG must be clean, responsive with viewBox (e.g. viewBox="0 0 600 360" or "0 0 650 400" with sufficient vertical height for all axes, bars, tick marks, and bottom labels so nothing is cut off), preserveAspectRatio="xMidYMid meet", xmlns="http://www.w3.org/2000/svg", dark clear outlines (stroke="#0f172a", stroke-width="2"), clean soft fills (#f8fafc, #eff6ff, #fef2f2), clean shapes (<circle>, <rect>, <polygon>, <line>, <path>), and legible bold typography (<text font-family="sans-serif" font-size="14" font-weight="bold" fill="#0f172a">).
  - Explicitly refer to the diagram in the question statement (e.g., "In the given figure / diagram below...", "Refer to the diagram below to find...").
  - Make the diagram academically meaningful to solve the question:
    * For Number System / Divisibility / Arithmetic: Draw a Division Ladder, Factor Tree diagram, Number Line with tick marks and intervals, or Remainder Venn diagram.
    * For Geometry / Mensuration: Draw triangles, circles, chords, parallel lines with transversals, labeled vertices (A, B, C, D), angle arcs, and dimension labels.
    * For Reasoning: Draw Venn diagrams, series pattern figures, or dice projections.
    * For Physics / Chemistry: Draw circuit diagrams, ray diagrams, or molecular layouts.
DIAG;
        } else {
            $diagramInstruction = <<<'DIAG'
- DIAGRAM / VISUAL FIGURES GUIDELINE:
  If a question naturally requires or strongly benefits from a visual diagram (e.g. Geometry, Mensuration, Coordinate Geometry, Trigonometric Heights & Distances, Circuits, Ray Diagrams, Venn Diagrams, Flowcharts, or Data Interpretation Charts):
  Generate a clean vector SVG graphic inside the "diagram_svg" field (with viewBox, xmlns, clean shapes and labels), set "has_diagram": true, and reference the figure in the question.
DIAG;
        }

        // Detect if user prompt or context requires Descriptive / Numerical (No Options)
        $lowerPrompt = mb_strtolower($userPrompt);
        $isDescriptiveByPrompt = (
            strpos($lowerPrompt, 'descriptive') !== false ||
            strpos($lowerPrompt, 'numerical') !== false ||
            strpos($lowerPrompt, 'subjective') !== false ||
            strpos($lowerPrompt, 'without options') !== false ||
            strpos($lowerPrompt, 'without option') !== false ||
            strpos($lowerPrompt, 'no options') !== false ||
            strpos($lowerPrompt, 'no option') !== false ||
            strpos($lowerPrompt, 'options ki need nahi') !== false ||
            strpos($lowerPrompt, 'option ki need nahi') !== false ||
            strpos($lowerPrompt, 'bina option') !== false ||
            strpos($lowerPrompt, 'bina options') !== false ||
            strpos($lowerPrompt, 'options nahi') !== false ||
            strpos($lowerPrompt, 'option nahi') !== false ||
            strpos($lowerPrompt, 'only answer') !== false ||
            strpos($lowerPrompt, 'direct answer') !== false
        );

        $isDescriptiveMode = ($questionTypeId === 2 || $isDescriptiveByPrompt);

        // Use Nowdoc <<<'PROMPT_TEMPLATE' so PHP NEVER attempts variable interpolation on $LaTeX or $ math expressions!
        $template = <<<'PROMPT_TEMPLATE'
You are an elite, highly trained Academic Examination Question Creator AI for school boards (CBSE, ICSE, State Boards) and competitive exams (NEET, JEE Main/Advanced, Banking PO/Clerk, SSC CGL/CHSL, UPSC, NDA, Railway, Olympiads).

{{REFERENCE_DOC_NOTE}}

{{DIAGRAMS_CATALOG_PROMPT}}

USER INSTRUCTIONS / PROMPT:
"{{USER_PROMPT}}"

ACADEMIC CONTEXT & KEYWORDS:
- Target Class / Level: {{CLASS_NAME}}
- Subject: {{SUBJECT_NAME}}
{{CHAPTER_TOPIC_CONTEXT}}
- Target Difficulty: {{DIFF_PREF}}
{{MODE_INSTRUCTION}}
{{LANGUAGE_INSTRUCTION}}
{{FORMAT_ACADEMIC_CONTEXT}}
{{DIAGRAM_INSTRUCTION}}

CORE OBJECTIVE:
Synthesize high-quality, authentic, examination-grade questions adhering strictly to the user's prompt.
The user may ask for:
- Standalone Multiple Choice Questions (e.g. "10 standalone questions")
- Directional Question Sets with a common passage / caselet / data table and multiple linked sub-questions (e.g. "2 directional sets with 3-3 questions")
- A combination of both standalone and directional questions
- Specific topic focus, difficulty breakdown, or following the pattern of the attached reference file

STRICT GUIDELINES:
1. MATHEMATICAL & SCIENTIFIC FORMULAS (LATEX):
   - You MUST format ALL mathematical expressions, physical equations, chemical reactions, formulas, fractions, powers, square roots, integrals, matrices, and Greek letters using standard LaTeX syntax enclosed in dollar signs ($...$ for inline).
   - Examples: $rac{x^2 + 1}{2x}$, $\sqrt{3x - 5}$, $\int_0^1 x^2 dx$, $lpha + eta = -rac{b}{a}$, $ec{F} = mec{a}$.
   - Never write plain text math like "x^2" or "sqrt(x)". Always use LaTeX.

2. TABLES & DATA STRUCTURES:
   - For Directional Sets: If the context contains a data table, schedule, quarterly breakdown, or matrix, you MUST provide the complete HTML table inside "direction_table":
     <table class="table table-bordered table-sm text-center"><thead><tr><th>Header 1</th>...</tr></thead><tbody><tr><td>Data</td>...</tr></tbody></table>
   - For Standalone Questions: If a standalone question contains a table, provide it in the "table" property:
     <table class="table table-bordered table-sm text-center"><thead><tr>...</tr></thead><tbody>...</tbody></table>
     (or embed it directly inside "name").
   - Never leave tables empty or summarized with "...". Include complete rows and numbers.

3. DIAGRAMS & VECTOR ILLUSTRATIONS (MANDATORY WHEN REQUESTED OR FOR VISUAL TOPICS):
   - GENERATING NEW VECTOR DIAGRAMS (SVG):
     When the user prompt requests diagrams, figures, charts, drawings, or when the subject/topic naturally requires or benefits from visual illustration (e.g. Geometry triangles/circles/angles, Mensuration 2D/3D shapes, Coordinate Geometry, Number Lines, Factor Trees, Division Ladders, Venn Diagrams, Flowcharts, Data Interpretation Charts, Physics circuits/optics):
     YOU MUST GENERATE A STANDALONE, COMPLETE, CLEAN VECTOR SVG IN "diagram_svg"!
     - The SVG must be responsive with viewBox (e.g. viewBox="0 0 600 360" or "0 0 650 400" with sufficient vertical height for all axes, bars, tick marks, and bottom labels so nothing is cut off), preserveAspectRatio="xMidYMid meet", xmlns="http://www.w3.org/2000/svg", dark clear outlines (stroke="#0f172a", stroke-width="2"), clean soft fills (#f8fafc, #eff6ff, #fef2f2), clean shapes (<circle>, <rect>, <polygon>, <line>, <path>), and legible bold typography (<text font-family="sans-serif" font-size="14" font-weight="bold" fill="#0f172a">).
     - Set "has_diagram": true and "diagram_id": null.
     - The question statement ("name") must refer to the figure (e.g. "In the given diagram below...", "Refer to the figure below...").
   - REFERENCE DOCUMENT DIAGRAMS:
     * EXTRACT MODE: If the user asked to EXTRACT questions from an attached reference, set "diagram_id" to the matching ID (e.g. "diag_img_1") and "has_diagram": true.
     * SIMILAR QUESTION MODE: If the user asked to CREATE SIMILAR / NEW questions, DO NOT link the reference document ("diagram_id": null). Instead, generate your own new data in "direction_table" and/or new vector SVG chart in "diagram_svg".
   - If no diagram is required or generated, set "diagram_svg": null, "diagram_id": null, and "has_diagram": false.

4. EXPLANATION QUALITY & ZERO INTERNAL SCRATCHPAD (STRICT!):
   - The "explanation" field must be a 100% finished, authoritative, textbook-grade pedagogical solution.
   - STRICTLY FORBIDDEN: Do NOT include internal stream-of-consciousness, trial-and-error thoughts, calculations testing multiple values, or self-correction monologue (e.g. "Wait, let us re-evaluate...", "Let's change question values...", "Wait! 41+4=45...", "Let's test x=5..."). Solve and verify all math internally FIRST, and output ONLY the clean, final step-by-step mathematical proof and explanation.

{{FORMAT_GUIDELINES_STANDALONE}}

5. DIRECTIONAL QUESTION SETS (PASSAGE / CASE STUDY / DATA INTERPRETATION):
   - "direction": The full common passage, comprehension story, or problem statement that introduces the context for the set.
   - "direction_table": If the set is based on a data table or matrix, provide a clean, complete HTML table:
     <table class="table table-bordered table-sm text-center"><thead><tr>...</tr></thead><tbody>...</tbody></table>
     (If no table is needed, set to null).
   - "has_diagram": boolean.
   - "diagram_svg": Complete SVG vector string or null.
   - "diagram_id": string or null.
   - "sub_questions": An array of questions that depend on this common direction/passage.
{{FORMAT_GUIDELINES_DIRECTIONAL}}

6. SCHEMA INTEGRITY:
   - Return pure, valid JSON matching the exact schema below.
   - Do NOT wrap in markdown fences. Output raw JSON only.

SCHEMA:
{
  "summary": "Concise summary of generated questions",
  "topic": "Primary Topic / Chapter Name",
  "standalone_questions": [
{{STANDALONE_SAMPLE_JSON}}
  ],
  "directional_sets": [
    {
      "direction": "Common narrative passage or context text...",
      "direction_table": null,
      "has_diagram": false,
      "diagram_id": null,
      "diagram_svg": null,
      "sub_questions": [
{{SUBQ_SAMPLE_JSON}}
      ]
    }
  ]
}
PROMPT_TEMPLATE;

        if ($isDescriptiveMode) {
            $formatAcademicContext = "- Target Question Format: DESCRIPTIVE / NUMERICAL VALUE (STRICTLY NO OPTIONS!)\n- Options: NONE. Do NOT generate options under any circumstances.";
            $formatGuidelinesStandalone = <<<'GUIDELINE'
4. STANDALONE QUESTIONS (DESCRIPTIVE / NUMERICAL - STRICTLY NO OPTIONS):
   - "name": Clear, self-contained question statement (use LaTeX $...$ for all mathematical equations).
   - "question_type_id": 2 (Descriptive / Numerical).
   - "table": HTML <table> string if this question contains a data table, otherwise null.
   - "has_diagram": boolean.
   - "diagram_id": string or null.
   - "diagram_svg": Complete clean SVG string if diagram is present, otherwise null.
   - "ans1", "ans2", "ans3", "ans4", "ans5": MUST STRICTLY BE NULL! DO NOT GENERATE OPTIONS!
   - "correct_ans": Provide the exact mathematical result, numerical answer (e.g. "10", "42.5", "-7"), single formula/fraction (e.g. "x/10", "3/4"), or short concise string answer. NEVER output option indices like "0", "1", "2" or option letters!
   - "correct_option_letter": MUST BE NULL.
   - "explanation": Comprehensive, pedagogically sound, step-by-step mathematical solution / derivation leading directly to this exact answer (NO internal scratchpad notes).
   - "difficulty_level": "Easy", "Medium", or "Hard".
GUIDELINE;

            $formatGuidelinesDirectional = <<<'GUIDELINE'
     Each sub-question must have:
     - "sub_question_no": 1, 2, 3, etc.
     - "name": Specific question statement for this sub-question (do NOT repeat the common passage).
     - "question_type_id": 2 (Descriptive / Numerical).
     - "has_diagram": boolean.
     - "diagram_id": string or null.
     - "diagram_svg": SVG string or null.
     - "ans1", "ans2", "ans3", "ans4", "ans5": MUST STRICTLY BE NULL! NO OPTIONS!
     - "correct_ans": Exact expected numerical value or formula answer string (e.g. "10", "42.5", "x/10").
     - "correct_option_letter": MUST BE NULL.
     - "explanation": Detailed step-by-step mathematical solution for this specific sub-question.
     - "difficulty_level": "Easy", "Medium", or "Hard".
GUIDELINE;

            $standaloneSampleJson = <<<'JSON'
    {
      "name": "In the given diagram below, determine the numerical value of $x$: $3x + 15 = 45$...",
      "question_type_id": 2,
      "table": null,
      "has_diagram": true,
      "diagram_id": null,
      "diagram_svg": "<svg viewBox=\"0 0 500 200\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"500\" height=\"200\" fill=\"#f8fafc\" rx=\"8\" stroke=\"#cbd5e1\" stroke-width=\"1.5\"/><text x=\"250\" y=\"50\" font-family=\"sans-serif\" font-size=\"16\" font-weight=\"bold\" fill=\"#002c54\" text-anchor=\"middle\">Algebraic Representation</text></svg>",
      "ans1": null,
      "ans2": null,
      "ans3": null,
      "ans4": null,
      "ans5": null,
      "correct_ans": "10",
      "correct_option_letter": null,
      "explanation": "Step-by-step derivation: $3x = 45 - 15 = 30 \implies x = 10$.",
      "difficulty_level": "Medium"
    }
JSON;

            $subqSampleJson = <<<'JSON'
        {
          "sub_question_no": 1,
          "name": "Calculate the value of $P$ using the data table above...",
          "question_type_id": 2,
          "has_diagram": false,
          "diagram_id": null,
          "diagram_svg": null,
          "ans1": null,
          "ans2": null,
          "ans3": null,
          "ans4": null,
          "ans5": null,
          "correct_ans": "25.5",
          "correct_option_letter": null,
          "explanation": "Detailed solution for sub-question 1...",
          "difficulty_level": "Medium"
        }
JSON;
        } else {
            $formatAcademicContext = "- Target Question Format: OBJECTIVE (MULTIPLE CHOICE QUESTIONS - MCQ)\n- Default Options Count: {$optionsCount} options per MCQ";
            $formatGuidelinesStandalone = <<<'GUIDELINE'
4. STANDALONE QUESTIONS (OBJECTIVE / MCQ):
   - "name": Clear, self-contained question statement (reference diagram if present).
   - "question_type_id": 1 (Objective / MCQ).
   - "table": HTML <table> string if this question contains a data table, otherwise null.
   - "has_diagram": boolean.
   - "diagram_id": string or null.
   - "diagram_svg": Complete clean SVG string if diagram is present, otherwise null.
   - "ans1", "ans2", "ans3", "ans4": Clear, distinct, unambiguous option texts (with LaTeX if mathematical).
   - "ans5": Provide Option E if 5 options are requested (e.g. Banking exams), otherwise null.
   - Do NOT include letter prefixes like "(A)" or "A." inside the option text.
   - "correct_ans": Zero-based index string ("0" for ans1/A, "1" for ans2/B, "2" for ans3/C, "3" for ans4/D, "4" for ans5/E).
   - "correct_option_letter": Corresponding letter ("A", "B", "C", "D", or "E").
   - "explanation": Comprehensive, pedagogically sound, step-by-step solution / derivation (NO internal scratchpad notes).
   - "difficulty_level": "Easy", "Medium", or "Hard".
GUIDELINE;

            $formatGuidelinesDirectional = <<<'GUIDELINE'
     Each sub-question must have:
     - "sub_question_no": 1, 2, 3, etc.
     - "name": Specific question statement for this sub-question (do NOT repeat the common passage).
     - "question_type_id": 1.
     - "has_diagram": boolean.
     - "diagram_id": string or null.
     - "diagram_svg": SVG string or null.
     - "ans1", "ans2", "ans3", "ans4", "ans5" (if applicable).
     - "correct_ans": "0", "1", "2", "3", or "4".
     - "correct_option_letter": "A", "B", "C", "D", or "E".
     - "explanation": Detailed solution for this specific sub-question.
     - "difficulty_level": "Easy", "Medium", or "Hard".
GUIDELINE;

            $standaloneSampleJson = <<<'JSON'
    {
      "name": "In the given figure / diagram below, find the missing digit $x$ such that the number is divisible by 72: $789x5314y$...",
      "question_type_id": 1,
      "table": null,
      "has_diagram": true,
      "diagram_id": null,
      "diagram_svg": "<svg viewBox=\"0 0 500 200\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"500\" height=\"200\" fill=\"#f8fafc\" rx=\"8\" stroke=\"#cbd5e1\" stroke-width=\"1.5\"/><text x=\"250\" y=\"45\" font-family=\"sans-serif\" font-size=\"16\" font-weight=\"bold\" fill=\"#002c54\" text-anchor=\"middle\">Divisibility Rule of 72</text><line x1=\"80\" y1=\"80\" x2=\"420\" y2=\"80\" stroke=\"#2563eb\" stroke-width=\"2\"/><text x=\"250\" y=\"120\" font-family=\"sans-serif\" font-size=\"14\" fill=\"#0f172a\" text-anchor=\"middle\">Number: 789x5314y</text></svg>",
      "ans1": "-1",
      "ans2": "0",
      "ans3": "2",
      "ans4": "3",
      "ans5": null,
      "correct_ans": "2",
      "correct_option_letter": "C",
      "explanation": "To check divisibility by 72, the number must be divisible by both 8 and 9 since 8 and 9 are coprime factors...",
      "difficulty_level": "Hard"
    }
JSON;

            $subqSampleJson = <<<'JSON'
        {
          "sub_question_no": 1,
          "name": "Sub-question 1 statement...",
          "question_type_id": 1,
          "has_diagram": false,
          "diagram_id": null,
          "diagram_svg": null,
          "ans1": "Option A text",
          "ans2": "Option B text",
          "ans3": "Option C text",
          "ans4": "Option D text",
          "ans5": null,
          "correct_ans": "1",
          "correct_option_letter": "B",
          "explanation": "Solution for sub-question 1...",
          "difficulty_level": "Medium"
        }
JSON;
        }

        $prompt = str_replace(
            [
                '{{REFERENCE_DOC_NOTE}}',
                '{{DIAGRAMS_CATALOG_PROMPT}}',
                '{{USER_PROMPT}}',
                '{{CLASS_NAME}}',
                '{{SUBJECT_NAME}}',
                '{{CHAPTER_TOPIC_CONTEXT}}',
                '{{DIFF_PREF}}',
                '{{MODE_INSTRUCTION}}',
                '{{LANGUAGE_INSTRUCTION}}',
                '{{FORMAT_ACADEMIC_CONTEXT}}',
                '{{DIAGRAM_INSTRUCTION}}',
                '{{FORMAT_GUIDELINES_STANDALONE}}',
                '{{FORMAT_GUIDELINES_DIRECTIONAL}}',
                '{{STANDALONE_SAMPLE_JSON}}',
                '{{SUBQ_SAMPLE_JSON}}'
            ],
            [
                $referenceDocNote,
                $diagramsCatalogPrompt,
                $userPrompt,
                $className,
                $subjectName,
                $chapterTopicContext,
                $diffPref,
                $modeInstruction,
                $languageInstruction,
                $formatAcademicContext,
                $diagramInstruction,
                $diagramsCatalogPrompt,
                $userPrompt,
                $className,
                $subjectName,
                $chapterTopicContext,
                $diffPref,
                $modeInstruction,
                $languageInstruction,
                $formatAcademicContext,
                $formatGuidelinesStandalone,
                $formatGuidelinesDirectional,
                $standaloneSampleJson,
                $subqSampleJson
            ],
            $template
        );

        $result = $this->callGeminiMultimodal($mediaParts, $prompt, $key, true);

        if ($result['success'] && !empty($result['data'])) {
            $data = $result['data'];

            // Normalization: Ensure standalone_questions and directional_sets are arrays
            if (!isset($data['standalone_questions']) || !is_array($data['standalone_questions'])) {
                $data['standalone_questions'] = [];
            }
            if (!isset($data['directional_sets']) || !is_array($data['directional_sets'])) {
                $data['directional_sets'] = [];
            }

            // If the model returned flat questions array instead of separate keys, sort them out
            if (empty($data['standalone_questions']) && empty($data['directional_sets']) && !empty($data['questions']) && is_array($data['questions'])) {
                $grouped = [];
                foreach ($data['questions'] as $q) {
                    if (!empty($q['direction']) || !empty($q['group_id'])) {
                        $gKey = $q['group_id'] ?: md5($q['direction'] ?? '');
                        if (!isset($grouped[$gKey])) {
                            $grouped[$gKey] = [
                                'direction' => $q['direction'] ?? '',
                                'direction_table' => $q['direction_table'] ?? null,
                                'has_diagram' => $q['has_diagram'] ?? false,
                                'diagram_id' => $q['diagram_id'] ?? null,
                                'sub_questions' => []
                            ];
                        }
                        $subQ = $q;
                        unset($subQ['direction'], $subQ['direction_table'], $subQ['group_id']);
                        $subQ['sub_question_no'] = count($grouped[$gKey]['sub_questions']) + 1;
                        $grouped[$gKey]['sub_questions'][] = $subQ;
                    } else {
                        $data['standalone_questions'][] = $q;
                    }
                }
                $data['directional_sets'] = array_values($grouped);
            }

            // Index extracted diagrams by id
            $diagramsById = [];
            foreach ($extractedDiagrams as $d) {
                if (!empty($d['id'])) {
                    $diagramsById[$d['id']] = $d;
                }
            }

            // Post-processing 1: Standalone Questions (Tables & Diagrams)
            foreach ($data['standalone_questions'] as &$sq) {
                // Newly generated vector SVG diagram takes top priority
                $svgSavedPath = $this->processAndSaveSvgDiagram($sq, 'ai_diag_sq');
                if ($svgSavedPath) {
                    $sq['image'] = $svgSavedPath;
                    $sq['has_diagram'] = true;
                } elseif (!empty($sq['diagram_id']) && isset($diagramsById[$sq['diagram_id']]) && !$isSimilarRequest) {
                    $sq['image'] = $diagramsById[$sq['diagram_id']]['image_path'];
                    $sq['has_diagram'] = true;
                } elseif (!empty($sq['has_diagram']) && !empty($extractedDiagrams) && !$isSimilarRequest) {
                    $sq['image'] = $extractedDiagrams[0]['image_path'];
                    $sq['has_diagram'] = true;
                } else {
                    $sq['image'] = $sq['image'] ?? null;
                }

                // Clean explanation scratchpad if model leaked internal monologue
                if (!empty($sq['explanation'])) {
                    $sq['explanation'] = $this->cleanAiExplanation($sq['explanation']);
                }

                // Append table to name if standalone question has a separate table property
                if (!empty($sq['table']) && is_string($sq['table'])) {
                    if (strpos($sq['name'], '<table') === false) {
                        $sq['name'] .= '<div class="table-responsive my-2">' . $sq['table'] . '</div>';
                    }
                }

                // Normalize options and correct answer
                $this->normalizeQuestionOptionsAndAnswers($sq, $isDescriptiveMode);

                $sq['chapter_name'] = !empty($chapterName) ? $chapterName : ($sq['chapter_name'] ?? null);
                $sq['topic_name'] = !empty($topicName) ? $topicName : ($sq['topic_name'] ?? null);
                $sq['language'] = !empty($language) ? $language : ($sq['language'] ?? 'en');
                if ($language === 'hi' && !empty($sq['name_hi']) && empty($sq['name'])) {
                    $sq['name'] = $sq['name_hi'];
                }
            }
            unset($sq);

            // Post-processing 2: Directional Sets (Tables, Unique Group IDs, Diagrams)
            $timestamp = date('Ymd-His');
            foreach ($data['directional_sets'] as $sIdx => &$set) {
                $set['chapter_name'] = !empty($chapterName) ? $chapterName : ($set['chapter_name'] ?? null);
                $set['topic_name'] = !empty($topicName) ? $topicName : ($set['topic_name'] ?? null);
                $set['language'] = !empty($language) ? $language : ($set['language'] ?? 'en');
                if ($language === 'hi' && !empty($set['direction_hi']) && empty($set['direction'])) {
                    $set['direction'] = $set['direction_hi'];
                }

                if (empty($set['group_id'])) {
                    $set['group_id'] = 'DIR-' . $timestamp . '-' . strtoupper(Str::random(4)) . '-' . ($sIdx + 1);
                }
                if (!isset($set['sub_questions']) || !is_array($set['sub_questions'])) {
                    $set['sub_questions'] = [];
                }

                // Newly generated vector SVG diagram for the set takes top priority
                $setSvgPath = $this->processAndSaveSvgDiagram($set, 'ai_diag_set');
                if ($setSvgPath) {
                    $set['image'] = $setSvgPath;
                    $set['has_diagram'] = true;
                } elseif (!empty($set['diagram_id']) && isset($diagramsById[$set['diagram_id']]) && !$isSimilarRequest) {
                    $set['image'] = $diagramsById[$set['diagram_id']]['image_path'];
                    $set['has_diagram'] = true;
                } elseif (!empty($set['has_diagram']) && !empty($extractedDiagrams) && !$isSimilarRequest) {
                    $set['image'] = $extractedDiagrams[0]['image_path'];
                    $set['has_diagram'] = true;
                } else {
                    $set['image'] = $set['image'] ?? null;
                }

                foreach ($set['sub_questions'] as $qIdx => &$sq) {
                    $sq['group_id'] = $set['group_id'];
                    $sq['sub_question_no'] = !empty($sq['sub_question_no']) ? $sq['sub_question_no'] : ($qIdx + 1);
                    $sq['direction'] = $set['direction'] ?? null;
                    $sq['direction_table'] = $set['direction_table'] ?? null;

                    // Match sub-question specific diagram, newly generated SVG, or fallback to set image
                    $subQSvgPath = $this->processAndSaveSvgDiagram($sq, 'ai_diag_subq');
                    if ($subQSvgPath) {
                        $sq['image'] = $subQSvgPath;
                        $sq['has_diagram'] = true;
                    } elseif (!empty($sq['diagram_id']) && isset($diagramsById[$sq['diagram_id']]) && !$isSimilarRequest) {
                        $sq['image'] = $diagramsById[$sq['diagram_id']]['image_path'];
                        $sq['has_diagram'] = true;
                    } elseif (!empty($set['image'])) {
                        $sq['image'] = $set['image'];
                        $sq['has_diagram'] = true;
                    } else {
                        $sq['image'] = $sq['image'] ?? null;
                    }

                    // Clean sub-question explanation scratchpad
                    if (!empty($sq['explanation'])) {
                        $sq['explanation'] = $this->cleanAiExplanation($sq['explanation']);
                    }

                    // Normalize options and correct answer
                    $this->normalizeQuestionOptionsAndAnswers($sq, $isDescriptiveMode);

                    $sq['chapter_name'] = !empty($chapterName) ? $chapterName : ($sq['chapter_name'] ?? null);
                    $sq['topic_name'] = !empty($topicName) ? $topicName : ($sq['topic_name'] ?? null);
                    $sq['language'] = !empty($language) ? $language : ($sq['language'] ?? 'en');
                    if ($language === 'hi' && !empty($sq['name_hi']) && empty($sq['name'])) {
                        $sq['name'] = $sq['name_hi'];
                    }
                }
                unset($sq);
            }
            unset($set);

            $result['data'] = $data;
        }

        return $result;
    }

    /**
     * Intelligently focus and slice PDF to the target question/direction pages.
     */
    public function focusPdfPages(string $pdfPath, string $extraInstructions): array
    {
        try {
            $scriptPath = base_path('app/Scripts/focus_pdf_pages.py');
            $outputDir = storage_path('app/temp_ai');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            if (!file_exists($scriptPath) || empty(trim($extraInstructions))) {
                return ['focused' => false, 'pages' => [], 'focused_pdf' => $pdfPath];
            }

            $cmd = "python " . escapeshellarg($scriptPath) . " " . escapeshellarg($pdfPath) . " " . escapeshellarg($extraInstructions) . " " . escapeshellarg($outputDir);
            $output = [];
            $code = 0;
            exec($cmd, $output, $code);

            if ($code === 0 && !empty($output)) {
                $raw = implode('', $output);
                $data = json_decode($raw, true);
                if (is_array($data) && !empty($data['focused']) && file_exists($data['focused_pdf'])) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Focus PDF pages warning: " . $e->getMessage());
        }

        return ['focused' => false, 'pages' => [], 'focused_pdf' => $pdfPath];
    }

    /**
     * Clean and sanitize direction text, table HTML, and note blocks.
     */
    protected function sanitizeDirectionAndTable(array &$q): void
    {
        $direction = trim($q['direction'] ?? '');
        $tableHtml = trim($q['direction_table'] ?? '');
        $notesHtml = trim($q['direction_notes'] ?? '');

        if (empty($direction) && empty($tableHtml)) {
            return;
        }

        // 1. Separate Note / Conditions block if found in direction text
        if (empty($notesHtml) && preg_match('/(?:नोट|Note|Important Note)[\s:]+(.+)$/isu', $direction, $nm)) {
            $noteBody = trim($nm[1]);
            $noteLabel = strpos($nm[0], 'Note') !== false ? 'Note' : 'नोट';

            // Format math symbols in notes
            $formattedBody = preg_replace('/(\w+)0\s*=\s*([0-9\.]+)°/', '$$1^\circ = $2^\circ$', $noteBody);
            $formattedBody = str_replace(
                ['x°', 'y°', '360°', 'x0', 'y0'],
                ['$x^\circ$', '$y^\circ$', '$360^\circ$', '$x^\circ$', '$y^\circ$'],
                $formattedBody
            );

            $notesHtml = '<div class="alert alert-warning-light border mt-2 py-2 px-3 small font-weight-normal text-dark" style="background:#fffbeb; border-left: 3px solid #f59e0b !important;"><strong class="text-dark"><i class="fa fa-info-circle text-warning mr-1"></i> ' . $noteLabel . ':</strong> ' . nl2br($formattedBody) . '</div>';

            // Remove the note block from direction text
            $direction = trim(str_replace($nm[0], '', $direction));
        }

        // 2. Table Rescuer: If tableHtml is empty or has empty tbody, rescue from direction text
        $hasEmptyTbody = (strpos($tableHtml, '<tbody></tbody>') !== false || strpos($tableHtml, '<tbody> </tbody>') !== false);
        if (empty($tableHtml) || $hasEmptyTbody) {
            $lines = explode("\n", $direction);
            $tableLines = [];
            foreach ($lines as $line) {
                $tLine = trim($line);
                if (strpos($tLine, '|') !== false) {
                    $tableLines[] = $tLine;
                }
            }

            if (count($tableLines) >= 2) {
                $headerCols = array_map('trim', explode('|', array_shift($tableLines)));
                $thHtml = '';
                foreach ($headerCols as $col) {
                    if (!empty($col)) {
                        $thHtml .= "<th>{$col}</th>";
                    }
                }
                $trHtml = '';
                foreach ($tableLines as $row) {
                    $cols = array_map('trim', explode('|', $row));
                    $tdHtml = '';
                    foreach ($cols as $c) {
                        $tdHtml .= "<td>{$c}</td>";
                    }
                    $trHtml .= "<tr>{$tdHtml}</tr>";
                }
                $tableHtml = "<table class='table table-bordered table-sm text-center'><thead><tr>{$thHtml}</tr></thead><tbody>{$trHtml}</tbody></table>";
            }
        }

        // 3. Remove raw table pipe-delimited text from direction text
        if (!empty($tableHtml)) {
            $lines = explode("\n", $direction);
            $cleanLines = [];
            foreach ($lines as $line) {
                $tLine = trim($line);
                if (strpos($tLine, '|') !== false || preg_match('/^(Q\d|\d+|A|B|C|D)\s*\|/i', $tLine)) {
                    continue;
                }
                if (!empty($tLine)) {
                    $cleanLines[] = $tLine;
                }
            }
            $direction = trim(implode("\n", $cleanLines));
        }

        // 4. Combine table and notes into direction_table
        if (!empty($notesHtml)) {
            $tableHtml = trim($tableHtml . "\n" . $notesHtml);
        }

        $q['direction'] = $direction;
        $q['direction_table'] = $tableHtml ?: null;
    }

    /**
     * Clean and format tables inside standalone question statements.
     */
    protected function sanitizeQuestionName(array &$q): void
    {
        $name = trim($q['name'] ?? '');
        if (empty($name) || strpos($name, '<table') !== false) {
            return;
        }

        if (substr_count($name, '|') >= 4) {
            $lines = explode("\n", $name);
            $tableLines = [];
            $textLines = [];
            foreach ($lines as $line) {
                $tLine = trim($line);
                if (strpos($tLine, '|') !== false) {
                    $tableLines[] = $tLine;
                } else {
                    $textLines[] = $line;
                }
            }

            if (count($tableLines) >= 2) {
                $headerCols = array_map('trim', explode('|', array_shift($tableLines)));
                $thHtml = '';
                foreach ($headerCols as $col) {
                    if (!empty($col)) $thHtml .= "<th>{$col}</th>";
                }
                $trHtml = '';
                foreach ($tableLines as $row) {
                    $cols = array_map('trim', explode('|', $row));
                    $tdHtml = '';
                    foreach ($cols as $c) $tdHtml .= "<td>{$c}</td>";
                    $trHtml .= "<tr>{$tdHtml}</tr>";
                }
                $tbl = "<table class='table table-bordered table-sm text-center my-2'><thead><tr>{$thHtml}</tr></thead><tbody>{$trHtml}</tbody></table>";
                $q['name'] = trim(implode("\n", $textLines) . "\n" . $tbl);
            }
        }
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
     * Inspects question or directional set payload for vector SVG/HTML diagram code, decodes HTML entities,
     * validates and formats the SVG, saves it into public/uploads/questions, and assigns the relative path.
     */
    protected function processAndSaveSvgDiagram(array &$item, string $prefix = 'ai_diag'): ?string
    {
        $raw = $item['diagram_svg'] ?? ($item['diagram_html'] ?? null);

        // If not found in dedicated diagram keys, check if embedded in name or direction
        if (empty($raw)) {
            $haystack = ($item['name'] ?? '') . ' ' . ($item['direction'] ?? '');
            if (preg_match('/(<svg[\s\S]*?<\/svg>)/i', $haystack, $m)) {
                $raw = $m[1];
            } elseif (preg_match('/(&lt;svg[\s\S]*?&lt;\/svg&gt;)/i', $haystack, $m)) {
                $raw = $m[1];
            }
        }

        if (empty($raw) || !is_string($raw)) {
            return null;
        }

        // Strip markdown code fences if present (```xml, ```svg, ```html, or ```)
        $raw = trim($raw);
        $raw = preg_replace('/^```(?:xml|svg|html)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);

        // Decode HTML entities (handles &lt;svg, &quot;, &#39;, &amp;, etc.)
        $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (strpos($decoded, '&lt;svg') !== false) {
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Locate <svg ... </svg>
        if (preg_match('/<svg[\s\S]*?<\/svg>/i', $decoded, $match)) {
            $svg = trim($match[0]);

            // Ensure XML namespace xmlns="http://www.w3.org/2000/svg"
            if (stripos($svg, 'xmlns=') === false) {
                $svg = preg_replace('/<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $svg, 1);
            }

            // Ensure preserveAspectRatio="xMidYMid meet" so entire graphic scales proportionally
            if (stripos($svg, 'preserveAspectRatio=') === false) {
                $svg = preg_replace('/<svg\b/i', '<svg preserveAspectRatio="xMidYMid meet"', $svg, 1);
            }

            // Strip fixed width/height attributes from <svg> root that could break responsive card fitting
            $svg = preg_replace('/<svg\b([^>]*?)\bheight\s*=\s*["\']\d+px?["\']/i', '<svg$1', $svg, 1);
            $svg = preg_replace('/<svg\b([^>]*?)\bwidth\s*=\s*["\']\d+px?["\']/i', '<svg$1', $svg, 1);

            // Parse existing viewBox or set a comfortable default
            $vbW = 600;
            $vbH = 360;
            if (preg_match('/viewBox\s*=\s*["\']([-\d\.\s]+)["\']/i', $svg, $vbMatch)) {
                $parts = preg_split('/\s+/', trim($vbMatch[1]));
                if (count($parts) === 4) {
                    $vbW = (float)$parts[2];
                    $vbH = (float)$parts[3];
                }
            } else {
                $svg = preg_replace('/<svg\b/i', '<svg viewBox="0 0 600 360"', $svg, 1);
            }

            // Check actual maximum Y coordinate to prevent bottom cut-off (bars, baseline axes, bottom labels)
            $maxY = $vbH;
            if (preg_match_all('/\b(?:y|y1|y2|cy)\s*=\s*["\']?(-?\d+(?:\.\d+)?)/i', $svg, $yMatches)) {
                foreach ($yMatches[1] as $yVal) {
                    $yFloat = (float)$yVal;
                    if ($yFloat > $maxY) {
                        $maxY = $yFloat;
                    }
                }
            }
            if (preg_match_all('/<rect[^>]*?\by\s*=\s*["\']?(-?\d+(?:\.\d+)?)[^>]*?\bheight\s*=\s*["\']?(-?\d+(?:\.\d+)?)/i', $svg, $rectMatches, PREG_SET_ORDER)) {
                foreach ($rectMatches as $rm) {
                    $bottom = (float)$rm[1] + (float)$rm[2];
                    if ($bottom > $maxY) {
                        $maxY = $bottom;
                    }
                }
            }
            if ($maxY > $vbH) {
                $newH = (int)ceil($maxY + 35);
                if (isset($vbMatch[0])) {
                    $svg = str_replace($vbMatch[0], 'viewBox="0 0 ' . (int)$vbW . ' ' . $newH . '"', $svg);
                } else {
                    $svg = preg_replace('/<svg\b/i', '<svg viewBox="0 0 ' . (int)$vbW . ' ' . $newH . '"', $svg, 1);
                }
            }

            $filename = $prefix . '_' . time() . '_' . Str::random(8) . '.svg';
            $relativePath = $this->mirrorSaveQuestionFile($filename, $svg, true);

            $item['image'] = $relativePath;
            $item['has_diagram'] = true;
            $item['diagram_svg'] = $svg;

            // Clean out embedded SVG from name / direction so question text stays clean and focused
            if (!empty($item['name'])) {
                $item['name'] = trim(preg_replace('/<svg[\s\S]*?<\/svg>/i', '', $item['name']));
                $item['name'] = trim(preg_replace('/&lt;svg[\s\S]*?&lt;\/svg&gt;/i', '', $item['name']));
            }
            if (!empty($item['direction'])) {
                $item['direction'] = trim(preg_replace('/<svg[\s\S]*?<\/svg>/i', '', $item['direction']));
                $item['direction'] = trim(preg_replace('/&lt;svg[\s\S]*?&lt;\/svg&gt;/i', '', $item['direction']));
            }

            return $relativePath;
        }

        return null;
    }

    /**
     * Mirror save an image or raw SVG content into all candidate directories
     * so that assets are directly accessible regardless of whether DocumentRoot
     * is public/ or the project root.
     */
    public function mirrorSaveQuestionFile(string $filename, $contentOrPath, bool $isRawContent = true): string
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
                @file_put_contents($targetPath, $contentOrPath);
            } else {
                @copy($contentOrPath, $targetPath);
            }
        }

        return 'uploads/questions/' . $filename;
    }

    /**
     * Cleans up any trailing scratchpad, trial-and-error reasoning, or internal monologue
     * that LLMs might sometimes leak into the solution/explanation field.
     */
    protected function cleanAiExplanation(?string $text): ?string
    {
        if (empty($text) || !is_string($text)) {
            return $text;
        }

        // If model dumped internal monologue/scratchpad starting with "Wait, let us..." or "Let's change question values..."
        $scratchpadPattern = '/\s*(?:Wait[,!]?\s*(?:let\s+us|let\'s|if|we)|Let\'s\s+(?:change\s+question|stick\s+to|re-check|re-evaluate|adjust\s+options|set\s+option|test\s+x))[\s\S]*$/i';
        $cleaned = preg_replace($scratchpadPattern, '', $text);

        return !empty(trim($cleaned)) ? trim($cleaned) : $text;
    }

    /**
     * Normalizes MCQ options and correct answer across various Gemini output shapes.
     */
    protected function normalizeQuestionOptionsAndAnswers(array &$q, bool $isDescriptiveMode): void
    {
        // 1. If options returned as array under 'options' or 'choices'
        if (empty($q['ans1']) && (!empty($q['options']) || !empty($q['choices']))) {
            $opts = !empty($q['options']) && is_array($q['options']) ? $q['options'] : ($q['choices'] ?? []);
            if (is_array($opts)) {
                $q['ans1'] = isset($opts[0]) ? (string)$opts[0] : null;
                $q['ans2'] = isset($opts[1]) ? (string)$opts[1] : null;
                $q['ans3'] = isset($opts[2]) ? (string)$opts[2] : null;
                $q['ans4'] = isset($opts[3]) ? (string)$opts[3] : null;
                $q['ans5'] = isset($opts[4]) ? (string)$opts[4] : null;
            }
        }

        // 2. Descriptive / Numerical mode
        if ($isDescriptiveMode || (!empty($q['question_type_id']) && (int)$q['question_type_id'] === 2)) {
            $q['question_type_id'] = 2;
            $rawAns = $q['correct_ans'] ?? ($q['correct_answer'] ?? ($q['correct_option'] ?? ($q['answer'] ?? null)));
            $cAnsStr = trim((string)$rawAns);
            if (in_array($cAnsStr, ['0', '1', '2', '3', '4', 'A', 'B', 'C', 'D', 'E'], true)) {
                $idxMap = [
                    '0' => 'ans1', '1' => 'ans2', '2' => 'ans3', '3' => 'ans4', '4' => 'ans5',
                    'A' => 'ans1', 'B' => 'ans2', 'C' => 'ans3', 'D' => 'ans4', 'E' => 'ans5'
                ];
                $key = $idxMap[$cAnsStr] ?? null;
                if ($key && !empty($q[$key])) {
                    $q['correct_ans'] = (string)$q[$key];
                } else {
                    $q['correct_ans'] = $cAnsStr;
                }
            } else {
                $q['correct_ans'] = $cAnsStr;
            }
            $q['ans1'] = null;
            $q['ans2'] = null;
            $q['ans3'] = null;
            $q['ans4'] = null;
            $q['ans5'] = null;
            $q['correct_option_letter'] = null;
            return;
        }

        // 3. Objective / MCQ mode
        $q['question_type_id'] = 1;
        $rawAns = $q['correct_ans'] ?? ($q['correct_answer'] ?? ($q['correct_option'] ?? ($q['answer'] ?? null)));
        if ($rawAns !== null && $rawAns !== '') {
            $rawAnsStr = trim((string)$rawAns);
            $upper = strtoupper($rawAnsStr);
            if (in_array($upper, ['A', 'B', 'C', 'D', 'E'], true)) {
                $letterMap = ['A' => '0', 'B' => '1', 'C' => '2', 'D' => '3', 'E' => '4'];
                $q['correct_ans'] = $letterMap[$upper];
                $q['correct_option_letter'] = $upper;
            } elseif (in_array($rawAnsStr, ['0', '1', '2', '3', '4'], true)) {
                $idxMap = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                $q['correct_ans'] = $rawAnsStr;
                $q['correct_option_letter'] = $idxMap[$rawAnsStr];
            } else {
                // Check if raw answer matches the text of one of the options
                $matchedIdx = null;
                for ($i = 1; $i <= 5; $i++) {
                    if (isset($q['ans' . $i]) && trim((string)$q['ans' . $i]) === $rawAnsStr) {
                        $matchedIdx = $i - 1;
                        break;
                    }
                }
                if ($matchedIdx !== null) {
                    $idxMap = ['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D', '4' => 'E'];
                    $q['correct_ans'] = (string)$matchedIdx;
                    $q['correct_option_letter'] = $idxMap[(string)$matchedIdx];
                } else {
                    $q['correct_ans'] = '0';
                    $q['correct_option_letter'] = 'A';
                }
            }
        } else {
            $q['correct_ans'] = '0';
            $q['correct_option_letter'] = 'A';
        }
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

    /**
     * AI Chart Reproduction: Analyze chart image, extract numerical data, and recreate clean HD chart.
     */
    public function recreateChartFromImage(string $imageFullPath, ?string $customApiKey = null): array
    {
        $key = $this->getApiKey($customApiKey);
        if (empty($key)) {
            return [
                'success' => false,
                'message' => 'No working AI API Key found.'
            ];
        }

        if (!file_exists($imageFullPath)) {
            return [
                'success' => false,
                'message' => 'Image file not found: ' . $imageFullPath
            ];
        }

        $ext = strtolower(pathinfo($imageFullPath, PATHINFO_EXTENSION) ?: 'png');
        $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
        $bytes = file_get_contents($imageFullPath);
        $mediaPart = [
            'inline_data' => [
                'mime_type' => $mime,
                'data' => base64_encode($bytes)
            ]
        ];

        $prompt = <<<EOT
You are an expert data visualization and chart reproduction AI.
Analyze this exam question chart image carefully. It may have a watermark, logo, or institute branding, but ignore the watermark completely.
Extract the underlying chart's exact numerical values, labels, and structure with 100% precision.

Return a STRICT JSON object matching this schema:
{
  "is_chart": true,
  "chart_type": "grouped_bar",
  "title": null,
  "x_label": null,
  "y_label": "प्रवेश की संख्या",
  "categories": ["2012", "2013", "2014", "2015", "2016"],
  "series": [
    {
      "name": "विद्यालय A",
      "values": [170, 60, 140, 200, 70]
    },
    {
      "name": "विद्यालय B",
      "values": [240, 210, 160, 240, 150]
    }
  ]
}

- Supported chart_type: "grouped_bar", "bar", "line", "pie"
- If the image is a table or generic non-statistical diagram, return: {"is_chart": false}
EOT;

        $res = $this->callGeminiMultimodal([$mediaPart], $prompt, $key, false);
        if (!$res['success'] || empty($res['data'])) {
            return $this->cleanWatermarkImage($imageFullPath);
        }

        $spec = $res['data'];
        if (empty($spec['is_chart']) || empty($spec['categories'])) {
            return $this->cleanWatermarkImage($imageFullPath);
        }

        // Render chart using Python Matplotlib script
        $tempDir = storage_path('app/temp_ai');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $specFile = $tempDir . '/chart_spec_' . time() . '_' . Str::random(6) . '.json';
        file_put_contents($specFile, json_encode($spec, JSON_UNESCAPED_UNICODE));

        $outDir = public_path('uploads/questions');
        if (!file_exists($outDir)) {
            mkdir($outDir, 0755, true);
        }
        $outFilename = 'recreated_chart_' . time() . '_' . Str::random(6) . '.png';
        $outFullPath = $outDir . '/' . $outFilename;

        $scriptPath = base_path('app/Scripts/recreate_chart.py');
        $cmd = "python " . escapeshellarg($scriptPath) . " " . escapeshellarg($specFile) . " " . escapeshellarg($outFullPath);
        $output = [];
        $code = 0;
        exec($cmd, $output, $code);

        @unlink($specFile);

        if (file_exists($outFullPath) && filesize($outFullPath) > 1000) {
            $this->mirrorSaveQuestionFile($outFilename, $outFullPath, false);
            return [
                'success' => true,
                'is_recreated' => true,
                'image_path' => 'uploads/questions/' . $outFilename,
                'url' => asset('uploads/questions/' . $outFilename),
                'chart_data' => $spec,
                'message' => 'Chart recreated with 100% clean HD vector graphics!'
            ];
        }

        // If Python script failed, fallback to clean watermark
        return $this->cleanWatermarkImage($imageFullPath);
    }

    /**
     * Clean watermark from an image file using clean_watermark.py
     */
    public function cleanWatermarkImage(string $imageFullPath): array
    {
        if (!file_exists($imageFullPath)) {
            return ['success' => false, 'message' => 'Image not found'];
        }

        $cleanScript = base_path('app/Scripts/clean_watermark.py');
        $destPath = public_path('uploads/questions');
        if (!file_exists($destPath)) {
            mkdir($destPath, 0755, true);
        }
        $ext = strtolower(pathinfo($imageFullPath, PATHINFO_EXTENSION) ?: 'png');
        $outFilename = 'cleaned_' . time() . '_' . Str::random(6) . '.' . $ext;
        $outFullPath = $destPath . '/' . $outFilename;

        $cmd = "python " . escapeshellarg($cleanScript) . " " . escapeshellarg($imageFullPath) . " " . escapeshellarg($outFullPath);
        $output = [];
        $code = 0;
        exec($cmd, $output, $code);

        $res = json_decode(implode('', $output), true);
        if (!empty($res['success']) && file_exists($outFullPath)) {
            $this->mirrorSaveQuestionFile($outFilename, $outFullPath, false);
            return [
                'success' => true,
                'is_cleaned' => true,
                'has_watermark' => $res['has_watermark'] ?? false,
                'original_image_path' => str_replace('\\', '/', str_replace(public_path() . DIRECTORY_SEPARATOR, '', $imageFullPath)),
                'image_path' => 'uploads/questions/' . $outFilename,
                'url' => asset('uploads/questions/' . $outFilename),
                'message' => 'Watermark removed successfully!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Watermark removal failed: ' . ($res['error'] ?? 'Unknown error')
        ];
    }
}
