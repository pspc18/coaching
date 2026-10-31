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
                'message' => 'Google Gemini API key is not configured. Please enter your API key in AI Settings.'
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

            // Store a copy in public uploads for diagram attachment
            $filename = 'paper_img_' . time() . '_' . Str::random(6) . '.' . ($ext ?: 'jpg');
            $destDir = public_path('uploads/questions');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }
            @copy($pdfPath, $destDir . '/' . $filename);
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
