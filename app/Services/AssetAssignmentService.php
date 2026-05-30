<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetAssignmentService
{
    /**
     * Check an asset out to a Snipe-IT user via the native Asset::checkOut() model
     * method, then close the linked PurchaseOrder.
     *
     * Hook point: Asset::checkOut() on line 521 of Asset.php handles association,
     * location sync, and fires CheckoutableCheckedOut internally — no need to
     * duplicate that logic or touch AssetCheckoutController.
     *
     * @throws \RuntimeException when the asset, user, or PO cannot be found,
     *                           the asset is unavailable for checkout, or
     *                           the checkout itself fails.
     */
    public function assignToEmployee(int $assetId, int $userId, int $poId): Asset
    {
        $asset = Asset::whereNull('deleted_at')->find($assetId);

        if (! $asset) {
            throw new \RuntimeException("Asset #{$assetId} does not exist or has been deleted.");
        }

        if (! $asset->availableForCheckout()) {
            throw new \RuntimeException(
                "Asset [{$asset->asset_tag}] is not available for checkout (current status: {$asset->status_id})."
            );
        }

        $user = User::whereNull('deleted_at')->where('activated', 1)->find($userId);

        if (! $user) {
            throw new \RuntimeException("User #{$userId} does not exist or is inactive.");
        }

        $po = PurchaseOrder::find($poId);

        if (! $po) {
            throw new \RuntimeException("Purchase order #{$poId} does not exist.");
        }

        DB::transaction(function () use ($asset, $user, $po) {
            $checkoutAt = now()->format('Y-m-d H:i:s');
            $admin      = Auth::user();
            $note       = "Assigned via purchase order {$po->po_number}.";

            // Delegate to Asset::checkOut() — handles assignedTo association,
            // location sync, and fires the CheckoutableCheckedOut event internally.
            $success = $asset->checkOut(
                $user,
                $admin,
                $checkoutAt,
                null,           // expected_checkin
                $note,
                $asset->name,   // preserve existing asset name
                null,           // location — let checkOut resolve from user
                false           // sign_in_place
            );

            if (! $success) {
                throw new \RuntimeException(
                    "Checkout failed for asset [{$asset->asset_tag}]: ".$asset->getErrors()->toJson()
                );
            }

            // Close the PO now that the asset has been assigned
            $po->update(['status' => 'closed']);

            Log::info(
                "AssetAssignmentService: asset [{$asset->asset_tag}] (id={$asset->id}) ".
                "checked out to user [{$user->username}] (id={$user->id}) ".
                "and PO {$po->po_number} (id={$po->id}) closed."
            );
        });

        return $asset->fresh();
    }
}
