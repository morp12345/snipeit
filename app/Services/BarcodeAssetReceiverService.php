<?php

namespace App\Services;

use App\Events\CheckoutableCheckedIn;
use App\Models\Actionlog;
use App\Models\Asset;
use App\Models\PurchaseOrder;
use App\Models\Statuslabel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarcodeAssetReceiverService
{
    /**
     * Locate an asset by barcode/asset_tag, link it to the given PurchaseOrder,
     * advance the PO status to 'received', and write a Snipe-IT action log entry.
     *
     * This is the integration point identified in AssetCheckinController::store()
     * (after $asset->save() succeeds, before CheckoutableCheckedIn is dispatched).
     * It can also be called standalone from a dedicated barcode-scan endpoint.
     *
     * @throws \RuntimeException when the asset or PO cannot be found, or the PO
     *                           is already in a terminal state.
     */
    public function receiveByBarcode(string $barcode, int $poId): PurchaseOrder
    {
        $asset = Asset::where('asset_tag', $barcode)
            ->whereNull('deleted_at')
            ->first();

        if (! $asset) {
            throw new \RuntimeException("No active asset found with barcode/asset_tag '{$barcode}'.");
        }

        $po = PurchaseOrder::find($poId);

        if (! $po) {
            throw new \RuntimeException("Purchase order #{$poId} does not exist.");
        }

        if (in_array($po->status, ['received', 'closed'], true)) {
            throw new \RuntimeException(
                "Purchase order {$po->po_number} is already '{$po->status}' and cannot be updated."
            );
        }

        DB::transaction(function () use ($asset, $po) {
            $receivedAt = now();

            // Link the asset to the PO and stamp the receiving timestamp
            $po->update([
                'asset_id'    => $asset->id,
                'status'      => 'received',
                'received_at' => $receivedAt,
            ]);

            // Write a Snipe-IT native action log entry so the receiving event
            // appears in the asset's history timeline alongside check-ins/outs
            $log = new Actionlog([
                'item_type'   => Asset::class,
                'item_id'     => $asset->id,
                'action_type' => 'received',
                'note'        => "Received against purchase order {$po->po_number} via barcode scan.",
                'user_id'     => Auth::id(),
                'created_at'  => $receivedAt,
            ]);
            $log->save();

            Log::info(
                "BarcodeAssetReceiver: asset [{$asset->asset_tag}] (id={$asset->id}) ".
                "received against PO {$po->po_number} (id={$po->id}) at {$receivedAt}."
            );
        });

        return $po->fresh();
    }

    /**
     * Create a brand-new Snipe-IT asset from scanned/submitted data, link it
     * to the given PurchaseOrder, and fire CheckoutableCheckedIn so all native
     * Snipe-IT listeners (audit log, notifications, etc.) treat it as a normal
     * check-in event.
     *
     * Expected keys in $assetData:
     *   - asset_tag  (string, required, must be unique)
     *   - name       (string, required)
     *   - model_id   (int, required — must exist in models table)
     *   - status_id  (int, optional — defaults to first pending status label)
     *
     * @throws \RuntimeException when the PO is not found, asset validation fails,
     *                           or no pending status label exists in the database.
     */
    public function autoRegisterAsset(array $assetData, int $poId): Asset
    {
        $po = PurchaseOrder::find($poId);

        if (! $po) {
            throw new \RuntimeException("Purchase order #{$poId} does not exist.");
        }

        // Resolve the pending status label — use caller-supplied ID or fall back
        // to the first Statuslabel that has pending=1 in the database.
        $statusId = $assetData['status_id'] ?? null;

        if (! $statusId) {
            $pendingLabel = Statuslabel::pending()->first();

            if (! $pendingLabel) {
                throw new \RuntimeException(
                    'No pending status label found in Snipe-IT. Create one under Admin > Status Labels before auto-registering assets.'
                );
            }

            $statusId = $pendingLabel->id;
        }

        $asset = DB::transaction(function () use ($assetData, $statusId, $po) {
            $asset = new Asset([
                'asset_tag' => $assetData['asset_tag'],
                'name'      => $assetData['name'],
                'model_id'  => $assetData['model_id'],
                'status_id' => $statusId,
            ]);

            // Validate using Snipe-IT's own asset rules before persisting
            if (! $asset->save()) {
                throw new \RuntimeException(
                    'Auto-registration failed: '.$asset->getErrors()->toJson()
                );
            }

            // Link the newly created asset to the PO and mark it received
            $receivedAt = now();

            $po->update([
                'asset_id'    => $asset->id,
                'status'      => 'received',
                'received_at' => $receivedAt,
            ]);

            Log::info(
                "BarcodeAssetReceiver: auto-registered asset [{$asset->asset_tag}] (id={$asset->id}) ".
                "against PO {$po->po_number} (id={$po->id}) at {$receivedAt}."
            );

            // Fire the native Snipe-IT check-in event.
            // $checkedOutTo is null — the asset was never assigned before this moment.
            event(new CheckoutableCheckedIn(
                $asset,
                null,
                Auth::user(),
                "Auto-registered via barcode scan against PO {$po->po_number}.",
                $receivedAt->format('Y-m-d H:i:s'),
                []
            ));

            return $asset;
        });

        return $asset->fresh();
    }
}
