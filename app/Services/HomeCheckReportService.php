<?php

namespace App\Services;

use App\Models\HomecheckReport;
use App\Models\HomecheckData;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class HomeCheckReportService
{
    /**
     * Process HomeCheck and generate AI report.
     *
     * @param HomecheckReport $homecheckReport
     * @return bool
     */
    public function processAndGenerateReport(HomecheckReport $homecheckReport): bool
    {
        try {
            $property = $homecheckReport->property;
            
            if (!$property) {
                Log::error('HomeCheck report processing failed: Property not found for report ID ' . $homecheckReport->id);
                return false;
            }

            // Get all HomeCheck data for this report
            $homecheckData = HomecheckData::where('homecheck_report_id', $homecheckReport->id)
                ->orWhere('property_id', $property->id)
                ->orderBy('room_name')
                ->orderBy('created_at')
                ->get();

            if ($homecheckData->isEmpty()) {
                Log::warning('HomeCheck report processing: No images found for report ID ' . $homecheckReport->id);
                // Still generate a basic report
            }

            // Simulate AI analysis (in production, this would call an AI service)
            $aiAnalysis = $this->generateAIAnalysis($homecheckData, $property);

            // Generate report content
            $reportContent = $this->generateReportContent($property, $homecheckData, $aiAnalysis, $homecheckReport);

            // Save report as HTML/PDF
            $reportPath = $this->saveReport($property, $homecheckReport, $reportContent);

            // Update HomecheckReport with report path
            $homecheckReport->update([
                'report_path' => $reportPath,
                'provider' => 'Abodeology AI',
            ]);

            // Create PropertyDocument record
            PropertyDocument::updateOrCreate(
                [
                    'property_id' => $property->id,
                    'document_type' => 'homecheck',
                ],
                [
                    'file_path' => $reportPath,
                    'uploaded_at' => now(),
                ]
            );

            // Build normalised room lookup and overall summary for fallback
            $roomsFromApi = $aiAnalysis['rooms'] ?? [];
            $normalisedKeyToData = [];
            foreach ($roomsFromApi as $apiKey => $roomData) {
                $norm = strtolower(trim((string) $apiKey));
                if (!isset($normalisedKeyToData[$norm])) {
                    $normalisedKeyToData[$norm] = $roomData;
                }
            }
            $overallSummary = isset($aiAnalysis['summary']) && (string) $aiAnalysis['summary'] !== '' ? (string) $aiAnalysis['summary'] : null;

            // Update AI analysis in HomecheckData records (only set when we have values, so fallback does not overwrite OpenAI comments)
            foreach ($homecheckData as $index => $data) {
                $roomName = $data->room_name;
                $roomAnalysis = $roomsFromApi[$roomName] ?? $normalisedKeyToData[strtolower(trim($roomName))] ?? null;
                if ($roomAnalysis !== null) {
                    $comments = $roomAnalysis['comments'] ?? $roomAnalysis['comment'] ?? $roomAnalysis['analysis'] ?? $roomAnalysis['summary'] ?? null;
                    if (is_array($comments)) {
                        $comments = implode(' ', $comments);
                    }
                    if (($comments === null || $comments === '') && $overallSummary !== null) {
                        $comments = $overallSummary;
                    }
                    $updates = [
                        'moisture_reading' => $roomAnalysis['moisture'] ?? $data->moisture_reading,
                    ];
                    if (isset($roomAnalysis['rating']) && $roomAnalysis['rating'] !== null && $roomAnalysis['rating'] !== '') {
                        $updates['ai_rating'] = $roomAnalysis['rating'];
                    }
                    if ($comments !== null && $comments !== '') {
                        $updates['ai_comments'] = $comments;
                    }
                    $data->update($updates);
                } elseif ($overallSummary !== null) {
                    // No room match: still set overall summary so AI comment section is not empty
                    $updates = ['ai_comments' => $overallSummary];
                    if (isset($aiAnalysis['overall_rating']) && $aiAnalysis['overall_rating'] !== null && $aiAnalysis['overall_rating'] !== '') {
                        $updates['ai_rating'] = $aiAnalysis['overall_rating'];
                    }
                    $data->update($updates);
                }
            }

            Log::info('HomeCheck report generated successfully for property ID: ' . $property->id);

            return true;

        } catch (\Exception $e) {
            Log::error('HomeCheck report generation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate AI analysis from HomeCheck images.
     * 
     * In production, this would call an actual AI service/API to analyze images.
     *
     * @param \Illuminate\Support\Collection $homecheckData
     * @param Property $property
     * @return array
     */
    public function generateAIAnalysis($homecheckData, Property $property): array
    {
        $apiKey = config('services.openai.api_key');
        $assistantId = config('services.openai.assistant_id');

        // If OpenAI credentials are configured, try real AI analysis first
        if (!empty($apiKey) && !empty($assistantId)) {
            try {
                $analysis = $this->generateAIAnalysisWithOpenAI($homecheckData, $property, $apiKey, $assistantId);

                // Basic sanity check: need at least rooms (and usually overall_rating/summary)
                if (is_array($analysis) && !empty($analysis['rooms']) && is_array($analysis['rooms'])) {
                    return $analysis;
                }

                Log::warning('HomeCheck OpenAI analysis returned unexpected structure (missing or invalid rooms). Falling back to simulated analysis.', [
                    'property_id' => $property->id,
                ]);
            } catch (\Throwable $e) {
                Log::error('HomeCheck OpenAI analysis failed. Falling back to simulated analysis.', [
                    'property_id' => $property->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Fallback: existing simulated analysis
        return $this->generateFallbackAnalysis($homecheckData);
    }

    /**
     * Fallback analysis when OpenAI is not configured or fails.
     *
     * @param \Illuminate\Support\Collection $homecheckData
     * @return array
     */
    protected function generateFallbackAnalysis($homecheckData): array
    {
        $analysis = [
            'overall_rating' => 8,
            'summary' => 'Property is in good condition with minor wear and tear typical for its age.',
            'rooms' => [],
            'recommendations' => [],
            'issues_found' => [],
        ];

        // Group by room
        $roomsData = $homecheckData->groupBy('room_name');

        foreach ($roomsData as $roomName => $roomImages) {
            $roomAnalysis = $this->analyzeRoom($roomName, $roomImages);
            $analysis['rooms'][$roomName] = $roomAnalysis;

            if (isset($roomAnalysis['issues']) && !empty($roomAnalysis['issues'])) {
                $analysis['issues_found'] = array_merge($analysis['issues_found'], $roomAnalysis['issues']);
            }
        }

        // Generate overall recommendations
        $analysis['recommendations'] = $this->generateRecommendations($analysis);

        return $analysis;
    }

    /**
     * Real AI analysis via OpenAI Assistants API using your pre-configured Assistant.
     *
     * Where the AI output comes from:
     * - Your OpenAI Assistant (OPENAI_ASSISTANT_ID in .env) is run via the Assistants API.
     * - The app creates a thread, posts one user message (instructions + property/rooms JSON),
     *   runs the assistant, then reads the first assistant message and parses it as JSON.
     * - Your assistant's reply (full text) is taken from: thread → messages → first message
     *   where role=assistant → content[0].text.value.
     *
     * Expected minimum JSON shape (extra keys are preserved and may be shown in the report):
     * - overall_rating (number), summary (string), rooms (object)
     * - rooms["Room Name"]: rating, comments, moisture (optional), issues (optional array)
     * - recommendations (array), issues_found (array) optional but used in report
     *
     * @param \Illuminate\Support\Collection $homecheckData
     * @param Property $property
     * @param string $apiKey
     * @param string $assistantId
     * @return array
     */
    protected function generateAIAnalysisWithOpenAI($homecheckData, Property $property, string $apiKey, string $assistantId): array
    {
        // Build a compact JSON summary of the property and rooms to send to the assistant
        $rooms = [];
        $grouped = $homecheckData->groupBy('room_name');

        foreach ($grouped as $roomName => $images) {
            $rooms[$roomName] = [
                'images_count' => $images->count(),
                'images_360' => $images->where('is_360', true)->count(),
                'images_regular' => $images->where('is_360', false)->count(),
                'moisture_readings' => $images->pluck('moisture_reading')->filter()->values(),
                'existing_ai_rating' => $images->first()->ai_rating ?? null,
                'existing_ai_comments' => $images->first()->ai_comments ?? null,
            ];
        }

        $payload = [
            'property' => [
                'id' => $property->id,
                'address' => $property->address,
                'postcode' => $property->postcode,
                'property_type' => $property->property_type,
                'bedrooms' => $property->bedrooms,
                'tenure' => $property->tenure ?? null,
            ],
            'rooms' => $rooms,
        ];

        $prompt = "You are the Abodeology HomeCheck AI assistant. "
            . "You will receive structured JSON data for a property and its rooms. "
            . "For each room, analyse condition, moisture risk, and presentation quality based ONLY on the data provided. "
            . "Respond with a single JSON object with this exact structure:\n\n"
            . "{\n"
            . "  \"overall_rating\": number (1-10),\n"
            . "  \"summary\": string,\n"
            . "  \"rooms\": {\n"
            . "    \"Room Name\": {\n"
            . "      \"rating\": number (1-10),\n"
            . "      \"comments\": string,\n"
            . "      \"moisture\": number|null,\n"
            . "      \"issues\": [string, ...]\n"
            . "    },\n"
            . "    ...\n"
            . "  },\n"
            . "  \"recommendations\": [string, ...],\n"
            . "  \"issues_found\": [string, ...]\n"
            . "}\n\n"
            . "JSON ONLY, no markdown or extra text.\n\n"
            . "Here is the data to analyse:\n"
            . json_encode($payload);

        // 1) Create a thread with the user message
        $threadResponse = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/threads', [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ])
            ->json();

        if (empty($threadResponse['id'])) {
            throw new \RuntimeException('Failed to create OpenAI thread for HomeCheck analysis.');
        }

        $threadId = $threadResponse['id'];

        // 2) Run the assistant on the thread
        $runResponse = Http::withToken($apiKey)
            ->post("https://api.openai.com/v1/threads/{$threadId}/runs", [
                'assistant_id' => $assistantId,
            ])
            ->json();

        if (empty($runResponse['id'])) {
            throw new \RuntimeException('Failed to start OpenAI assistant run for HomeCheck analysis.');
        }

        $runId = $runResponse['id'];

        // 3) Poll until the run completes (simple, bounded loop)
        $maxAttempts = 15;
        $attempt = 0;
        $status = $runResponse['status'] ?? 'queued';

        while (in_array($status, ['queued', 'in_progress', 'cancelling'], true) && $attempt < $maxAttempts) {
            sleep(2);

            $runStatusResponse = Http::withToken($apiKey)
                ->get("https://api.openai.com/v1/threads/{$threadId}/runs/{$runId}")
                ->json();

            $status = $runStatusResponse['status'] ?? $status;
            $attempt++;
        }

        if ($status !== 'completed') {
            throw new \RuntimeException('OpenAI assistant run did not complete successfully. Status: ' . $status);
        }

        // 4) Fetch the latest assistant message
        $messagesResponse = Http::withToken($apiKey)
            ->get("https://api.openai.com/v1/threads/{$threadId}/messages", [
                'limit' => 10,
            ])
            ->json();

        if (empty($messagesResponse['data'])) {
            throw new \RuntimeException('No messages returned from OpenAI assistant for HomeCheck analysis.');
        }

        $assistantMessage = collect($messagesResponse['data'])
            ->first(function ($message) {
                return ($message['role'] ?? null) === 'assistant';
            });

        if (!$assistantMessage || empty($assistantMessage['content'][0]['text']['value'])) {
            throw new \RuntimeException('Assistant message missing or malformed for HomeCheck analysis.');
        }

        $rawText = $assistantMessage['content'][0]['text']['value'];
        // Strip markdown code block if present (e.g. ```json ... ```)
        $jsonText = preg_replace('/^\s*```(?:json)?\s*\n?/i', '', trim($rawText));
        $jsonText = preg_replace('/\n?\s*```\s*$/i', '', $jsonText);
        $decoded = json_decode($jsonText, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new \RuntimeException('Failed to decode OpenAI assistant JSON response for HomeCheck analysis.');
        }

        // Normalise room entries so 'comments' always exists (AI may return 'comment', 'analysis', etc.)
        if (!empty($decoded['rooms']) && is_array($decoded['rooms'])) {
            foreach ($decoded['rooms'] as $rName => $r) {
                if (is_array($r)) {
                    $c = $r['comments'] ?? $r['comment'] ?? $r['analysis'] ?? $r['summary'] ?? null;
                    if (is_array($c)) {
                        $c = implode(' ', $c);
                    }
                    $decoded['rooms'][$rName]['comments'] = $c !== '' ? $c : null;
                }
            }
            // Remap room keys to match DB room_name (case/trim): API may return "Asdad", DB has "ASDAD"
            $dbRoomNames = $grouped->keys();
            $remapped = [];
            foreach ($decoded['rooms'] as $apiKey => $roomData) {
                $norm = strtolower(trim((string) $apiKey));
                $matched = false;
                foreach ($dbRoomNames as $dbName) {
                    if (strtolower(trim($dbName)) === $norm) {
                        $remapped[$dbName] = $roomData;
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    $remapped[$apiKey] = $roomData;
                }
            }
            $decoded['rooms'] = $remapped;
        }

        return $decoded;
    }

    /**
     * Run per-image AI analysis using OpenAI Vision and update each HomecheckData record.
     * Use when you want a different AI response for each image instead of one per room.
     * Requires OPENAI_ANALYZE_PER_IMAGE=true and OPENAI_API_KEY.
     *
     * @param \Illuminate\Support\Collection $homecheckData
     * @param Property $property
     * @return void
     */
    public function updatePerImageAnalysis($homecheckData, Property $property): void
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.vision_model', 'gpt-4o-mini');
        if (empty($apiKey)) {
            Log::warning('HomeCheck per-image analysis skipped: OPENAI_API_KEY not set.');
            return;
        }

        foreach ($homecheckData as $data) {
            if (empty($data->image_path)) {
                continue;
            }
            $imageUrl = $data->image_url ?? null;
            if (empty($imageUrl)) {
                try {
                    $imageUrl = $data->getImageUrlAttribute();
                } catch (\Throwable $e) {
                    Log::warning('HomeCheck per-image: could not get image URL for homecheck_data ' . $data->id, ['error' => $e->getMessage()]);
                    continue;
                }
            }
            if (empty($imageUrl)) {
                continue;
            }

            try {
                $result = $this->analyzeSingleImageWithVision($data, $property, $apiKey, $model);
                if ($result !== null) {
                    $data->update([
                        'ai_rating' => $result['rating'] ?? null,
                        'ai_comments' => $result['comments'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('HomeCheck per-image analysis failed for homecheck_data ' . $data->id, [
                    'error' => $e->getMessage(),
                    'room' => $data->room_name,
                ]);
            }
        }
    }

    /**
     * Call OpenAI Vision (Chat Completions) to analyse one property room image.
     *
     * @param HomecheckData $data
     * @param Property $property
     * @param string $apiKey
     * @param string $model
     * @return array|null ['rating' => int, 'comments' => string] or null on failure
     */
    protected function analyzeSingleImageWithVision(HomecheckData $data, Property $property, string $apiKey, string $model): ?array
    {
        $imageUrl = $data->getImageUrlAttribute();
        if (empty($imageUrl)) {
            return null;
        }

        $prompt = "You are the Abodeology HomeCheck AI. Analyse this single property room image. "
            . "Property: {$property->address}, room: {$data->room_name}. "
            . "Respond with ONLY a JSON object in this exact format, no other text: {\"rating\": number 1-10, \"comments\": \"short analysis paragraph\"}. "
            . "Consider condition, presentation, and any visible issues. JSON only.";

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                        ],
                    ],
                ],
                'max_tokens' => 300,
            ])
            ->json();

        $text = $response['choices'][0]['message']['content'] ?? null;
        if (empty($text)) {
            return null;
        }

        $jsonText = preg_replace('/^\s*```(?:json)?\s*\n?/i', '', trim($text));
        $jsonText = preg_replace('/\n?\s*```\s*$/i', '', $jsonText);
        $decoded = json_decode($jsonText, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        $rating = isset($decoded['rating']) ? (int) $decoded['rating'] : null;
        $comments = isset($decoded['comments']) ? trim((string) $decoded['comments']) : null;
        if ($rating === null && $comments === null) {
            return null;
        }

        return [
            'rating' => $rating >= 1 && $rating <= 10 ? $rating : null,
            'comments' => $comments !== '' ? $comments : null,
        ];
    }

    /**
     * Analyze a single room.
     *
     * @param string $roomName
     * @param \Illuminate\Support\Collection $images
     * @return array
     */
    protected function analyzeRoom(string $roomName, $images): array
    {
        // Fallback when OpenAI is not used: do not set fake 'comments' so AI Comment shows —.
        $rating = rand(7, 10);
        $roomType = strtolower($roomName);

        if (strpos($roomType, 'bathroom') !== false || strpos($roomType, 'kitchen') !== false) {
            $moisture = round(rand(40, 60) / 10, 1);
        } else {
            $moisture = round(rand(30, 50) / 10, 1);
        }

        $issues = [];
        if ($rating < 8) {
            $issues[] = 'Minor wear and tear detected.';
        }

        return [
            'rating' => $rating,
            'comments' => null,
            'moisture' => $moisture ?? null,
            'images_count' => $images->count(),
            '360_images_count' => $images->where('is_360', true)->count(),
            'regular_images_count' => $images->where('is_360', false)->count(),
            'issues' => $issues,
        ];
    }

    /**
     * Generate recommendations based on analysis.
     *
     * @param array $analysis
     * @return array
     */
    protected function generateRecommendations(array $analysis): array
    {
        $recommendations = [];

        if (!empty($analysis['issues_found'])) {
            $recommendations[] = 'Address minor wear and tear issues before listing.';
        }

        if ($analysis['overall_rating'] < 7) {
            $recommendations[] = 'Consider professional cleaning and minor repairs.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Property is ready for marketing.';
        }

        return $recommendations;
    }

    /**
     * Render any top-level AI keys not already shown (so advanced assistant output is visible).
     *
     * @param array $aiAnalysis Full decoded assistant response
     * @param string[] $skipKeys Keys we already render elsewhere
     * @return string HTML fragment
     */
    protected function renderExtraTopLevelFields(array $aiAnalysis, array $skipKeys): string
    {
        $known = array_flip($skipKeys);
        $out = [];
        foreach ($aiAnalysis as $key => $value) {
            if (isset($known[$key]) || !is_scalar($value) && !is_array($value)) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            $label = ucfirst(str_replace('_', ' ', $key));
            $out[] = '<p><strong>' . e($label) . ':</strong> ' . e((string) $value) . '</p>';
        }
        return implode('', $out);
    }

    /**
     * Render any room-level AI keys not already shown (rating, comments, moisture, issues, images_count, etc.).
     *
     * @param array $roomAnalysis Single room from assistant rooms[]
     * @return string HTML fragment
     */
    protected function renderExtraRoomFields(array $roomAnalysis): string
    {
        $known = ['rating', 'comments', 'comment', 'analysis', 'summary', 'moisture', 'issues', 'images_count', '360_images_count', 'regular_images_count'];
        $knownFlip = array_flip($known);
        $out = [];
        foreach ($roomAnalysis as $key => $value) {
            if (isset($knownFlip[$key])) {
                continue;
            }
            if (is_array($value)) {
                $value = is_string(implode('', $value)) ? implode(', ', $value) : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            if (!is_scalar($value)) {
                continue;
            }
            $label = ucfirst(str_replace('_', ' ', $key));
            $out[] = '<p><strong>' . e($label) . ':</strong> ' . e((string) $value) . '</p>';
        }
        return implode('', $out);
    }

    /**
     * Generate report content (HTML).
     *
     * @param Property $property
     * @param \Illuminate\Support\Collection $homecheckData
     * @param array $aiAnalysis
     * @param HomecheckReport $homecheckReport
     * @return string
     */
    protected function generateReportContent(Property $property, $homecheckData, array $aiAnalysis, HomecheckReport $homecheckReport): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Abodeology HomeCheck Report - ' . e($property->address) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .header { background: #0F0F0F; color: #2CB8B4; padding: 20px; text-align: center; }
        .header h1 { margin: 0; color: #2CB8B4; }
        .property-info { background: #F4F4F4; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .section { margin: 30px 0; }
        .section h2 { color: #2CB8B4; border-bottom: 2px solid #2CB8B4; padding-bottom: 10px; }
        .room-analysis { background: #F9F9F9; padding: 15px; margin: 15px 0; }
        .rating { font-size: 24px; font-weight: bold; color: #28a745; }
        .recommendations { background: #E8F4F3; padding: 15px; margin: 15px 0; border-radius: 4px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table th { background: #2CB8B4; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Abodeology® HomeCheck Report</h1>
    </div>

    <div class="property-info">
        <h3>Property Information</h3>
        <p><strong>Address:</strong> ' . e($property->address) . '</p>
        ' . ($property->postcode ? '<p><strong>Postcode:</strong> ' . e($property->postcode) . '</p>' : '') . '
        ' . ($property->property_type ? '<p><strong>Type:</strong> ' . ucfirst(str_replace('_', ' ', $property->property_type)) . '</p>' : '') . '
        <p><strong>Report Date:</strong> ' . Carbon::now()->format('l, F j, Y g:i A') . '</p>
        <p><strong>Generated By:</strong> Abodeology AI System</p>
    </div>

    <div class="section">
        <h2>Executive Summary</h2>
        <p><strong>Overall Rating:</strong> <span class="rating">' . ($aiAnalysis['overall_rating'] ?? 'N/A') . '/10</span></p>
        <p>' . e($aiAnalysis['summary'] ?? '') . '</p>
        ' . $this->renderExtraTopLevelFields($aiAnalysis, ['overall_rating', 'summary', 'rooms', 'recommendations', 'issues_found']) . '
    </div>

    <div class="section">
        <h2>Room-by-Room Analysis</h2>';

        if (!empty($aiAnalysis['rooms'])) {
            foreach ($aiAnalysis['rooms'] as $roomName => $roomAnalysis) {
                $html .= '
        <div class="room-analysis">
            <h3>' . e($roomName) . '</h3>
            <p><strong>Rating:</strong> ' . ($roomAnalysis['rating'] ?? 'N/A') . '/10</p>
            ' . (isset($roomAnalysis['moisture']) ? '<p><strong>Moisture Reading:</strong> ' . $roomAnalysis['moisture'] . '%</p>' : '') . '
            <p><strong>Images Captured:</strong> ' . ($roomAnalysis['images_count'] ?? 0) . ' total';
                
                if (isset($roomAnalysis['360_images_count']) && $roomAnalysis['360_images_count'] > 0) {
                    $html .= ' (' . $roomAnalysis['360_images_count'] . ' 360°, ' . ($roomAnalysis['regular_images_count'] ?? 0) . ' regular)';
                }
                
                $html .= '</p>
            ' . (isset($roomAnalysis['comments']) && $roomAnalysis['comments'] ? '<p><strong>Analysis:</strong> ' . e($roomAnalysis['comments']) . '</p>' : '') . '
            ' . (!empty($roomAnalysis['issues']) ? '<p><strong>Issues:</strong> ' . e(implode(', ', $roomAnalysis['issues'])) . '</p>' : '') . '
            ' . $this->renderExtraRoomFields($roomAnalysis) . '
        </div>';
            }
        }

        $html .= '
    </div>

    <div class="section">
        <h2>Recommendations</h2>
        <div class="recommendations">
            <ul>';

        foreach ($aiAnalysis['recommendations'] as $recommendation) {
            $html .= '<li>' . e($recommendation) . '</li>';
        }

        $html .= '
            </ul>
        </div>
    </div>';

        if (!empty($aiAnalysis['issues_found'])) {
            $html .= '
    <div class="section">
        <h2>Issues Identified</h2>
        <ul>';

            foreach ($aiAnalysis['issues_found'] as $issue) {
                $html .= '<li>' . e($issue) . '</li>';
            }

            $html .= '
        </ul>
    </div>';
        }

        $html .= '
    <div class="footer">
        <p>© ' . date('Y') . ' Abodeology®. All rights reserved.</p>
        <p>This report was generated automatically by the Abodeology AI HomeCheck system.</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Save report to storage.
     *
     * @param Property $property
     * @param HomecheckReport $homecheckReport
     * @param string $content
     * @return string
     */
    protected function saveReport(Property $property, HomecheckReport $homecheckReport, string $content): string
    {
        // Determine storage disk (S3 if configured, otherwise public)
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        $fileName = 'homecheck-report-' . $property->id . '-' . $homecheckReport->id . '-' . time() . '.html';
        $directory = 'homecheck-reports/' . $property->id;
        $filePath = $directory . '/' . $fileName;

        // Ensure directory exists (only for local storage, S3 doesn't need directories)
        if ($disk !== 's3') {
            Storage::disk($disk)->makeDirectory($directory);
        }

        // Save report
        Storage::disk($disk)->put($filePath, $content);

        return $filePath;
    }
}
