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
     * AI-enhanced ranking via Claude API.
     *
     * Runs the deterministic scoreAndRank() first to get base scores, then
     * sends a structured prompt to Claude asking for a qualitative ranking
     * with reasoning. The Claude response is merged into the base entries so
     * the caller receives both the numeric score and the AI-generated reason.
     *
     * Falls back gracefully to the base scores if the API key is missing,
     * the call fails, or the response cannot be parsed — so this method is
     * always safe to call even in environments without a key configured.
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
        // Step 1 — get the deterministic base scores
        $baseRanked = $this->scoreAndRank($poId);

        if (empty($baseRanked)) {
            return [];
        }

        // Attach ai_* placeholders so the return shape is always consistent
        $withPlaceholders = array_map(fn (array $entry) => array_merge($entry, [
            'ai_rank'      => null,
            'ai_reason'    => null,
            'ai_available' => false,
        ]), $baseRanked);

        $apiKey = env('ANTHROPIC_API_KEY');

        if (! $apiKey) {
            Log::info('SupplierScoringService: ANTHROPIC_API_KEY not set — returning base scores only.');
            return $withPlaceholders;
        }

        // Step 2 — build a concise prompt from the ranked quotations
        $prompt = $this->buildPrompt($baseRanked);

        // Step 3 — call the Claude API
        try {
            $client   = new HttpClient(['timeout' => 30, 'http_errors' => true]);
            $response = $client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key'         => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => 'claude-haiku-4-5-20251001',
                    'max_tokens' => 512,
                    'messages'   => [
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            $text = $body['content'][0]['text'] ?? '';

            Log::info('SupplierScoringService: Claude response received.', ['po_id' => $poId]);

            // Step 4 — parse the JSON array Claude returns
            $aiRanking = $this->parseClaudeResponse($text);

            if (empty($aiRanking)) {
                Log::warning('SupplierScoringService: Could not parse Claude JSON — returning base scores.', ['raw' => $text]);
                return $withPlaceholders;
            }

            // Step 5 — merge AI rank + reason into the base entries by supplier_name
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
            Log::error('SupplierScoringService: Claude API call failed — '.$e->getMessage(), ['po_id' => $poId]);
            return $withPlaceholders;
        }
    }

    /**
     * Build the prompt string sent to Claude.
     * Returns a compact, unambiguous description of the quotations
     * and a strict JSON-only instruction so parsing is reliable.
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
Format: [{"rank": 1, "supplier_name": "...", "reason": "..."}, ...]
PROMPT;
    }

    /**
     * Extract the JSON array from Claude's text response.
     * Handles cases where the model wraps the JSON in markdown fences
     * despite being instructed not to.
     */
    private function parseClaudeResponse(string $text): array
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
