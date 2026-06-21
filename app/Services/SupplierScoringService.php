<?php

namespace App\Services;

use App\Models\SupplierQuotation;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;

class SupplierScoringService
{
    // Scoring weights — must sum to 1.0
    private const WEIGHT_PRICE       = 0.40;
    private const WEIGHT_LEAD_TIME   = 0.30;
    private const WEIGHT_WARRANTY    = 0.20;
    private const WEIGHT_COMPLIANCE  = 0.10;

    /**
     * Score and rank all quotations for a given PurchaseOrder.
     *
     * Scoring formula (all sub-scores normalised 0–1 before weighting):
     *   price         40%  lower is better  → normalised as min/value
     *   lead_time_days 30%  lower is better  → normalised as min/value
     *   warranty_months 20% higher is better → normalised as value/max
     *   compliance_notes 10% bonus if not empty
     *
     * Missing values (null) are treated conservatively:
     *   - null lead_time_days → score 0 for that dimension
     *   - null warranty_months → score 0 for that dimension
     *
     * @return array<int, array{
     *     rank: int,
     *     id: int,
     *     supplier_name: string,
     *     supplier_email: string|null,
     *     price: string,
     *     currency: string,
     *     lead_time_days: int|null,
     *     warranty_months: int|null,
     *     compliance_notes: string|null,
     *     is_awarded: bool,
     *     score: float,
     *     score_breakdown: array{price: float, lead_time: float, warranty: float, compliance: float},
     * }>
     */
    public function scoreAndRank(int $poId): array
    {
        $quotations = SupplierQuotation::where('purchase_order_id', $poId)
            ->get();

        if ($quotations->isEmpty()) {
            return [];
        }

        // ----------------------------------------------------------------
        // Pre-compute range anchors needed for normalisation
        // ----------------------------------------------------------------
        $prices    = $quotations->pluck('price')->map(fn ($v) => (float) $v);
        $leadTimes = $quotations->pluck('lead_time_days')->filter()->map(fn ($v) => (int) $v);
        $warranties = $quotations->pluck('warranty_months')->filter()->map(fn ($v) => (int) $v);

        $minPrice    = $prices->min();
        $minLeadTime = $leadTimes->isNotEmpty() ? $leadTimes->min() : null;
        $maxWarranty = $warranties->isNotEmpty() ? $warranties->max() : null;

        // ----------------------------------------------------------------
        // Score each quotation
        // ----------------------------------------------------------------
        $scored = $quotations->map(function (SupplierQuotation $q) use (
            $minPrice, $minLeadTime, $maxWarranty
        ) {
            $price = (float) $q->price;

            // price sub-score: min / this price → 1.0 for the cheapest
            $priceScore = ($price > 0 && $minPrice > 0)
                ? $minPrice / $price
                : 0.0;

            // lead_time sub-score: min / this lead_time → 1.0 for the fastest
            $leadScore = ($q->lead_time_days && $minLeadTime)
                ? $minLeadTime / $q->lead_time_days
                : 0.0;

            // warranty sub-score: this warranty / max → 1.0 for the longest
            $warrantyScore = ($q->warranty_months && $maxWarranty)
                ? $q->warranty_months / $maxWarranty
                : 0.0;

            // compliance bonus: 1.0 if notes present, 0.0 otherwise
            $complianceScore = (! empty(trim((string) $q->compliance_notes)))
                ? 1.0
                : 0.0;

            $totalScore = round(
                ($priceScore      * self::WEIGHT_PRICE)
                + ($leadScore     * self::WEIGHT_LEAD_TIME)
                + ($warrantyScore * self::WEIGHT_WARRANTY)
                + ($complianceScore * self::WEIGHT_COMPLIANCE),
                4
            );

            return [
                'id'              => $q->id,
                'supplier_name'   => $q->supplier_name,
                'supplier_email'  => $q->supplier_email,
                'price'           => $q->price,
                'currency'        => $q->currency,
                'lead_time_days'  => $q->lead_time_days,
                'warranty_months' => $q->warranty_months,
                'compliance_notes' => $q->compliance_notes,
                'is_awarded'      => $q->is_awarded,
                'score'           => $totalScore,
                'score_breakdown' => [
                    'price'      => round($priceScore      * self::WEIGHT_PRICE,      4),
                    'lead_time'  => round($leadScore        * self::WEIGHT_LEAD_TIME,  4),
                    'warranty'   => round($warrantyScore    * self::WEIGHT_WARRANTY,   4),
                    'compliance' => round($complianceScore  * self::WEIGHT_COMPLIANCE, 4),
                ],
            ];
        });

        // ----------------------------------------------------------------
        // Sort descending by total score, take top 3, attach rank
        // ----------------------------------------------------------------
        return $scored
            ->sortByDesc('score')
            ->take(3)
            ->values()
            ->map(fn (array $entry, int $index) => array_merge(['rank' => $index + 1], $entry))
            ->all();
    }

    /**
     * AI-enhanced ranking via the configured AI provider (see config/ai.php).
     *
     * Runs the deterministic scoreAndRank() first to get base scores, then
     * sends a structured prompt to the active provider asking for a qualitative
     * ranking with reasoning. The AI response is merged into the base entries
     * so the caller receives both the numeric score and the AI-generated reason.
     *
     * Falls back gracefully to the base scores if the API key is missing,
     * the call fails, or the response cannot be parsed.
     *
     * @return array<int, array{
     *     rank: int,
     *     id: int,
     *     supplier_name: string,
     *     price: string,
     *     currency: string,
     *     lead_time_days: int|null,
     *     warranty_months: int|null,
     *     compliance_notes: string|null,
     *     is_awarded: bool,
     *     score: float,
     *     score_breakdown: array,
     *     ai_rank: int|null,
     *     ai_reason: string|null,
     *     ai_available: bool,
     * }>
     */
    public function aiEnhancedRank(int $poId): array
    {
        $baseRanked = $this->scoreAndRank($poId);

        if (empty($baseRanked)) {
            return [];
        }

        $withPlaceholders = array_map(fn (array $entry) => array_merge($entry, [
            'ai_rank'      => null,
            'ai_reason'    => null,
            'ai_available' => false,
        ]), $baseRanked);

        $provider = config('ai.provider', 'anthropic');
        $cfg      = config('ai.providers.' . $provider, []);
        $apiKey   = $cfg['api_key']  ?? null;
        $model    = $cfg['model']    ?? null;
        $baseUrl  = $cfg['base_url'] ?? null;

        // Ollama is local and needs no API key; all other providers require one
        if ($provider !== 'ollama' && ! $apiKey) {
            Log::info("SupplierScoringService: {$provider} API key not set — returning base scores only.");
            return $withPlaceholders;
        }

        $prompt = $this->buildPrompt($baseRanked);

        try {
            $text = $this->callProvider($provider, $prompt, $apiKey, $model, $baseUrl);

            Log::info("SupplierScoringService: {$provider} response received.", ['po_id' => $poId]);

            $aiRanking = $this->parseAiResponse($text);

            if (empty($aiRanking)) {
                Log::warning("SupplierScoringService: Could not parse {$provider} JSON — returning base scores.", ['raw' => $text]);
                return $withPlaceholders;
            }

            $aiByName = collect($aiRanking)->keyBy(fn ($r) => strtolower(trim($r['supplier_name'] ?? '')));

            return array_map(function (array $entry) use ($aiByName) {
                $key   = strtolower(trim($entry['supplier_name']));
                $aiRow = $aiByName->get($key);

                return array_merge($entry, [
                    'ai_rank'      => $aiRow['rank']   ?? null,
                    'ai_reason'    => $aiRow['reason']  ?? null,
                    'ai_available' => $aiRow !== null,
                ]);
            }, $baseRanked);

        } catch (\Throwable $e) {
            Log::error("SupplierScoringService: {$provider} API call failed — " . $e->getMessage(), ['po_id' => $poId]);
            return $withPlaceholders;
        }
    }

    /**
     * Dispatch to the correct provider's API based on the provider name.
     */
    private function callProvider(string $provider, string $prompt, ?string $apiKey, ?string $model, ?string $baseUrl): string
    {
        return match ($provider) {
            'anthropic' => $this->callAnthropic($prompt, $apiKey, $model, $baseUrl),
            'openai'    => $this->callOpenAi($prompt, $apiKey, $model, $baseUrl),
            'gemini'    => $this->callGemini($prompt, $apiKey, $model, $baseUrl),
            'ollama'    => $this->callOllama($prompt, $model, $baseUrl),
            default     => throw new \InvalidArgumentException("Unsupported AI provider: {$provider}"),
        };
    }

    private function callAnthropic(string $prompt, string $apiKey, string $model, string $baseUrl): string
    {
        $client   = new HttpClient(['timeout' => 30, 'http_errors' => true]);
        $response = $client->post(rtrim($baseUrl, '/') . '/messages', [
            'headers' => [
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ],
            'json' => [
                'model'      => $model,
                'max_tokens' => 512,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);
        return $body['content'][0]['text'] ?? '';
    }

    private function callOpenAi(string $prompt, string $apiKey, string $model, string $baseUrl): string
    {
        $client   = new HttpClient(['timeout' => 30, 'http_errors' => true]);
        $response = $client->post(rtrim($baseUrl, '/') . '/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'content-type'  => 'application/json',
            ],
            'json' => [
                'model'      => $model,
                'max_tokens' => 512,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);
        return $body['choices'][0]['message']['content'] ?? '';
    }

    private function callGemini(string $prompt, string $apiKey, string $model, string $baseUrl): string
    {
        $client   = new HttpClient(['timeout' => 30, 'http_errors' => true]);
        $url      = rtrim($baseUrl, '/') . '/models/' . $model . ':generateContent?key=' . $apiKey;
        $response = $client->post($url, [
            'headers' => ['content-type' => 'application/json'],
            'json'    => [
                'contents' => [['parts' => [['text' => $prompt]]]],
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);
        return $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    private function callOllama(string $prompt, string $model, string $baseUrl): string
    {
        $client   = new HttpClient(['timeout' => 60, 'http_errors' => true]);
        $response = $client->post(rtrim($baseUrl, '/') . '/api/chat', [
            'headers' => ['content-type' => 'application/json'],
            'json'    => [
                'model'    => $model,
                'stream'   => false,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);
        return $body['message']['content'] ?? '';
    }

    /**
     * Build the prompt string sent to the AI provider.
     */
    private function buildPrompt(array $ranked): string
    {
        $lines = [];
        foreach ($ranked as $entry) {
            $compliance = ! empty($entry['compliance_notes']) ? 'yes' : 'no';
            $lines[]    = sprintf(
                '- %s: price=%s %s, lead_time=%s days, warranty=%s months, compliance_notes=%s',
                $entry['supplier_name'],
                number_format((float) $entry['price'], 2),
                $entry['currency'],
                $entry['lead_time_days']  ?? 'unknown',
                $entry['warranty_months'] ?? 'unknown',
                $compliance,
            );
        }

        $supplierList = implode("\n", $lines);

        return <<<PROMPT
You are a procurement assistant. Rank these suppliers for a purchase order.
Consider price (lower is better), delivery speed (lower lead time is better),
warranty duration (longer is better), and compliance notes (provided is better).

Suppliers:
{$supplierList}

Respond with ONLY a valid JSON array — no explanation, no markdown, no code fences.
Each "reason" must be at least 2 complete sentences explaining why the supplier was ranked at that position.
Format: [{"rank": 1, "supplier_name": "...", "reason": "..."}, ...]
PROMPT;
    }

    /**
     * Hybrid anomaly detection for a PO's quotations.
     *
     * Step 1 — statistical: flags each quotation using z-scores and heuristics.
     * Step 2 — AI: sends flagged entries + statistical context to the active
     *              provider for a 2-sentence procurement-risk explanation.
     *
     * Flags checked:
     *   price_too_low        — price z-score < -1.5 (bid dumping risk)
     *   price_too_high       — price z-score >  1.5
     *   price_collusion      — price within 2% of another quotation
     *   lead_time_suspicious — lead time z-score < -1.5 (unrealistically fast)
     *   warranty_mismatch    — low price z (<-1.0) paired with high warranty z (>1.0)
     *
     * Returns an empty array when fewer than 2 quotations exist or none are flagged.
     *
     * @return array<int, array{
     *     supplier_name: string,
     *     supplier_email: string|null,
     *     flags: string[],
     *     stat_details: string[],
     *     ai_explanation: string|null,
     * }>
     */
    public function detectAnomalies(int $poId): array
    {
        $quotations = SupplierQuotation::where('purchase_order_id', $poId)->get();

        if ($quotations->count() < 2) {
            return [];
        }

        $prices     = $quotations->pluck('price')->map(fn ($v) => (float) $v);
        $leadTimes  = $quotations->pluck('lead_time_days')->filter()->map(fn ($v) => (int) $v);
        $warranties = $quotations->pluck('warranty_months')->filter()->map(fn ($v) => (int) $v);

        $priceMean = $prices->average();
        $priceStd  = $this->stdDev($prices->values()->all());
        $leadMean  = $leadTimes->isNotEmpty()  ? $leadTimes->average()  : null;
        $leadStd   = $leadTimes->isNotEmpty()  ? $this->stdDev($leadTimes->values()->all())  : 0.0;
        $warMean   = $warranties->isNotEmpty() ? $warranties->average() : null;
        $warStd    = $warranties->isNotEmpty() ? $this->stdDev($warranties->values()->all()) : 0.0;

        // Pre-compute which quotation IDs are in a collusion cluster (price within 2% of another)
        $collusionIds = [];
        $qList = $quotations->values()->all();
        for ($i = 0; $i < count($qList); $i++) {
            for ($j = $i + 1; $j < count($qList); $j++) {
                $pi = (float) $qList[$i]->price;
                $pj = (float) $qList[$j]->price;
                if ($pi > 0 && abs($pi - $pj) / $pi < 0.02) {
                    $collusionIds[$qList[$i]->id] = true;
                    $collusionIds[$qList[$j]->id] = true;
                }
            }
        }

        $flagged = [];

        foreach ($quotations as $q) {
            $price   = (float) $q->price;
            $flags   = [];
            $details = [];

            $priceZ   = ($priceStd > 0) ? ($price - $priceMean) / $priceStd : 0.0;
            $pricePct = ($priceMean > 0) ? (($price - $priceMean) / $priceMean) * 100 : 0.0;

            if ($priceZ < -1.5) {
                $flags[]   = 'price_too_low';
                $details[] = sprintf('Price is %.1f%% below average (z=%.2f).', abs($pricePct), $priceZ);
            }

            if ($priceZ > 1.5) {
                $flags[]   = 'price_too_high';
                $details[] = sprintf('Price is %.1f%% above average (z=%.2f).', $pricePct, $priceZ);
            }

            if (isset($collusionIds[$q->id])) {
                $flags[]   = 'price_collusion';
                $details[] = 'Price is within 2% of another quotation — possible bid coordination.';
            }

            if ($q->lead_time_days && $leadMean && $leadStd > 0) {
                $leadZ = ($q->lead_time_days - $leadMean) / $leadStd;
                if ($leadZ < -1.5) {
                    $flags[]   = 'lead_time_suspicious';
                    $details[] = sprintf('Lead time of %d days is unusually short for this group (z=%.2f).', $q->lead_time_days, $leadZ);
                }
            }

            if ($q->warranty_months && $warMean && $warStd > 0) {
                $warZ = ($q->warranty_months - $warMean) / $warStd;
                if ($priceZ < -1.0 && $warZ > 1.0) {
                    $flags[]   = 'warranty_mismatch';
                    $details[] = sprintf('Unusually low price paired with high warranty (%d months) — possible quality risk.', $q->warranty_months);
                }
            }

            if (! empty($flags)) {
                $flagged[] = [
                    'supplier_name'  => $q->supplier_name,
                    'supplier_email' => $q->supplier_email,
                    'flags'          => $flags,
                    'stat_details'   => $details,
                    'ai_explanation' => null,
                ];
            }
        }

        if (empty($flagged)) {
            return [];
        }

        // Step 2 — AI explanation for each flagged supplier
        $provider = config('ai.provider', 'anthropic');
        $cfg      = config('ai.providers.' . $provider, []);
        $apiKey   = $cfg['api_key']  ?? null;
        $model    = $cfg['model']    ?? null;
        $baseUrl  = $cfg['base_url'] ?? null;

        if ($provider !== 'ollama' && ! $apiKey) {
            return $flagged;
        }

        try {
            $prompt = $this->buildAnomalyPrompt($flagged);
            $text   = $this->callProvider($provider, $prompt, $apiKey, $model, $baseUrl);

            Log::info('SupplierScoringService: anomaly AI response received.', ['po_id' => $poId]);

            $aiResults = $this->parseAnomalyResponse($text);
            $aiByName  = collect($aiResults)->keyBy(fn ($r) => strtolower(trim($r['supplier_name'] ?? '')));

            return array_map(function (array $entry) use ($aiByName) {
                $key   = strtolower(trim($entry['supplier_name']));
                $aiRow = $aiByName->get($key);
                $entry['ai_explanation'] = $aiRow['explanation'] ?? null;
                return $entry;
            }, $flagged);

        } catch (\Throwable $e) {
            Log::error('SupplierScoringService: anomaly AI call failed — ' . $e->getMessage(), ['po_id' => $poId]);
            return $flagged;
        }
    }

    private function buildAnomalyPrompt(array $flagged): string
    {
        $lines = [];
        foreach ($flagged as $entry) {
            $lines[] = sprintf(
                '- %s: %s',
                $entry['supplier_name'],
                implode(' ', $entry['stat_details'])
            );
        }

        $list = implode("\n", $lines);

        return <<<PROMPT
You are a procurement fraud analyst. The following suppliers have been flagged by statistical analysis for suspicious bidding patterns.

Flagged suppliers:
{$list}

For each flagged supplier, write exactly 2 sentences explaining the specific procurement risk in plain business language.

Respond with ONLY a valid JSON array — no explanation, no markdown, no code fences.
Format: [{"supplier_name": "...", "explanation": "..."}, ...]
PROMPT;
    }

    private function parseAnomalyResponse(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);

        $start = strpos($text, '[');
        $end   = strrpos($text, ']');

        if ($start === false || $end === false || $end <= $start) {
            return [];
        }

        $parsed = json_decode(substr($text, $start, $end - $start + 1), true);

        if (! is_array($parsed)) {
            return [];
        }

        return array_filter($parsed, fn ($r) => isset($r['supplier_name'], $r['explanation']));
    }

    private function stdDev(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }
        $mean     = array_sum($values) / $n;
        $variance = array_sum(array_map(fn ($v) => ($v - $mean) ** 2, $values)) / ($n - 1);
        return sqrt($variance);
    }

    /**
     * Extract the JSON array from the AI response text.
     * Handles cases where the model wraps the JSON in markdown fences.
     */
    private function parseAiResponse(string $text): array
    {
        $text = trim($text);

        // Strip markdown code fences if present (```json ... ``` or ``` ... ```)
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);

        // Find the first JSON array in the response
        $start = strpos($text, '[');
        $end   = strrpos($text, ']');

        if ($start === false || $end === false || $end <= $start) {
            return [];
        }

        $json   = substr($text, $start, $end - $start + 1);
        $parsed = json_decode($json, true);

        if (! is_array($parsed)) {
            return [];
        }

        // Validate each entry has the required keys
        return array_filter($parsed, fn ($r) =>
            isset($r['rank'], $r['supplier_name'], $r['reason'])
        );
    }
}
