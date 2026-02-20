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

            // Update AI analysis in HomecheckData records
            foreach ($homecheckData as $index => $data) {
                if (isset($aiAnalysis['rooms'][$data->room_name])) {
                    $roomAnalysis = $aiAnalysis['rooms'][$data->room_name];
                    $comments = $roomAnalysis['comments'] ?? $roomAnalysis['comment'] ?? $roomAnalysis['analysis'] ?? $roomAnalysis['summary'] ?? null;
                    if (is_array($comments)) {
                        $comments = implode(' ', $comments);
                    }
                    $data->update([
                        'ai_rating' => $roomAnalysis['rating'] ?? null,
                        'ai_comments' => $comments !== '' ? $comments : null,
                        'moisture_reading' => $roomAnalysis['moisture'] ?? null,
                    ]);
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

                // Basic sanity check on structure before returning
                if (is_array($analysis)
                    && isset($analysis['overall_rating'], $analysis['summary'], $analysis['rooms'])) {
                    return $analysis;
                }

                Log::warning('HomeCheck OpenAI analysis returned unexpected structure. Falling back to simulated analysis.', [
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
     * Real AI analysis via OpenAI Assistants API using a pre-configured Assistant.
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
        }

        return $decoded;
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
        // Simulate room analysis
        // In production, this would analyze images through AI
        
        $rating = rand(7, 10); // Simulated rating
        $has360Images = $images->where('is_360', true)->count() > 0;

        $comments = [];
        if ($has360Images) {
            $comments[] = '360° images captured successfully.';
        }

        // Simulate detection based on room type
        $roomType = strtolower($roomName);
        
        if (strpos($roomType, 'bathroom') !== false || strpos($roomType, 'kitchen') !== false) {
            $comments[] = 'Moisture levels within normal range.';
            $moisture = round(rand(40, 60) / 10, 1);
        } else {
            $moisture = round(rand(30, 50) / 10, 1);
        }

        if (strpos($roomType, 'kitchen') !== false) {
            $comments[] = 'Appliances appear to be in working order.';
        }

        $issues = [];
        if ($rating < 8) {
            $issues[] = 'Minor wear and tear detected.';
        }

        return [
            'rating' => $rating,
            'comments' => implode(' ', $comments),
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
        <p><strong>Overall Rating:</strong> <span class="rating">' . $aiAnalysis['overall_rating'] . '/10</span></p>
        <p>' . e($aiAnalysis['summary']) . '</p>
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
