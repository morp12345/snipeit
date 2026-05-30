<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoSQLDocumentService
{
    private const CONNECTION = 'nosql_docs';
    private const COLLECTION = 'supplier_quotes';

    /**
     * Store a full supplier quotation payload as a JSON document in MongoDB.
     *
     * Document key format: quotes:{po_id}:{supplier_name}
     * This key is stored on the document as '_key' alongside the payload so
     * it can be queried or retrieved without knowing the MongoDB ObjectId.
     *
     * Prerequisites (NOT ACTIVE until fulfilled):
     *   1. composer require mongodb/laravel-mongodb
     *   2. Fill MONGODB_* credentials in .env
     *   3. Ensure the 'nosql_docs' connection in config/database.php is reachable
     *
     * @param  array{
     *     po_id: int,
     *     supplier_name: string,
     *     supplier_email: string|null,
     *     price: float|string,
     *     currency: string,
     *     lead_time_days: int|null,
     *     warranty_months: int|null,
     *     compliance_notes: string|null,
     *     document_path: string|null,
     *     is_awarded: bool,
     * } $quoteData
     *
     * @return array{success: bool, key: string|null, message: string}
     */
    public function storeSupplierQuote(array $quoteData): array
    {
        $poId         = $quoteData['po_id'] ?? null;
        $supplierName = $quoteData['supplier_name'] ?? null;

        if (! $poId || ! $supplierName) {
            $msg = 'NoSQLDocumentService: po_id and supplier_name are required to build the document key.';
            Log::warning($msg);

            return ['success' => false, 'key' => null, 'message' => $msg];
        }

        // Normalise the supplier name for the key: lowercase, spaces→underscores
        $supplierSlug = strtolower(str_replace(' ', '_', trim($supplierName)));
        $documentKey  = "quotes:{$poId}:{$supplierSlug}";

        $document = array_merge($quoteData, [
            '_key'       => $documentKey,
            'stored_at'  => now()->toIso8601String(),
        ]);

        try {
            DB::connection(self::CONNECTION)
                ->collection(self::COLLECTION)
                ->updateOrInsert(
                    ['_key' => $documentKey],   // match condition — upsert on key
                    $document                   // full document to write
                );

            Log::info("NoSQLDocumentService: stored quote document [{$documentKey}].");

            return [
                'success' => true,
                'key'     => $documentKey,
                'message' => "Quote stored successfully under key [{$documentKey}].",
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = "NoSQLDocumentService: DB query error storing [{$documentKey}] — ".$e->getMessage();
            Log::error($msg);

            return ['success' => false, 'key' => $documentKey, 'message' => $msg];
        } catch (\RuntimeException $e) {
            // Thrown by Laravel when the 'mongodb' driver is not registered
            // (i.e. mongodb/laravel-mongodb not yet installed)
            $msg = "NoSQLDocumentService: MongoDB driver unavailable — ".$e->getMessage()
                .'. Run: composer require mongodb/laravel-mongodb';
            Log::error($msg);

            return ['success' => false, 'key' => $documentKey, 'message' => $msg];
        } catch (\Throwable $e) {
            $msg = "NoSQLDocumentService: unexpected error storing [{$documentKey}] — ".$e->getMessage();
            Log::error($msg);

            return ['success' => false, 'key' => $documentKey, 'message' => $msg];
        }
    }
}
