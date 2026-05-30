<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function show(PurchaseOrder $po): \Illuminate\View\View
    {
        $po->load(['purchaseRequest', 'createdBy', 'quotations', 'asset']);

        return view('purchase-orders.show', compact('po'));
    }

    public function receiveForm(PurchaseOrder $po): \Illuminate\View\View
    {
        $po->load(['purchaseRequest', 'quotations', 'asset']);
        $awarded = $po->quotations->where('is_awarded', true)->first();

        return view('purchase-orders.receive', compact('po', 'awarded'));
    }

    public function markReceived(Request $request, PurchaseOrder $po): RedirectResponse
    {
        if ($po->status === 'received' || $po->status === 'closed') {
            return redirect()->route('purchase-orders.show', $po->id)
                ->with('error', trans('general.already_received'));
        }

        $validated = $request->validate([
            'asset_id'       => ['nullable', 'integer', 'exists:assets,id'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $po->update([
            'status'      => 'received',
            'received_at' => now(),
            'asset_id'    => $validated['asset_id'] ?? $po->asset_id,
            'notes'       => $validated['delivery_notes'] ?? $po->notes,
        ]);

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', trans('general.receipt_confirmed'));
    }

    public function store(PurchaseRequest $pr): RedirectResponse
    {
        if ($pr->status !== 'approved') {
            return redirect()->back()->with('error', 'A purchase order can only be created for an approved purchase request.');
        }

        if ($pr->purchaseOrder()->exists()) {
            return redirect()->back()->with('error', 'A purchase order already exists for this request.');
        }

        $po = DB::transaction(function () use ($pr) {
            $po = PurchaseOrder::create([
                'purchase_request_id' => $pr->id,
                'po_number'           => $this->generatePoNumber(),
                'created_by'          => Auth::id(),
                'status'              => 'draft',
                'total_amount'        => 0.00,
                'currency'            => 'USD',
            ]);

            return $po;
        });

        return redirect()->back()->with('success', "Purchase order {$po->po_number} created successfully.");
    }

    public function createAssetForm(PurchaseOrder $po): \Illuminate\View\View
    {
        $po->load(['purchaseRequest', 'quotations', 'asset']);
        $awarded = $po->quotations->where('is_awarded', true)->first();

        $statusLabels = Helper::deployableStatusLabelList();
        $suggestedTag = Asset::autoincrement_asset();

        return view('purchase-orders.create-asset', compact('po', 'awarded', 'statusLabels', 'suggestedTag'));
    }

    public function storeAsset(Request $request, PurchaseOrder $po): RedirectResponse
    {
        $this->authorize('create', Asset::class);

        $po->load('quotations');
        $awarded = $po->quotations->where('is_awarded', true)->first();

        $validated = $request->validate([
            'asset_tag'   => ['required', 'string', 'min:1', 'max:255', 'unique:assets,asset_tag'],
            'model_id'    => ['required', 'integer', 'exists:models,id'],
            'status_id'   => ['required', 'integer', 'exists:status_labels,id'],
            'name'        => ['nullable', 'string', 'max:255'],
            'serial'      => ['nullable', 'string', 'max:255'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
        ]);

        $asset = new Asset;
        $asset->asset_tag     = $validated['asset_tag'];
        $asset->model_id      = $validated['model_id'];
        $asset->status_id     = $validated['status_id'];
        $asset->name          = $validated['name'] ?? null;
        $asset->serial        = $validated['serial'] ?? null;
        $asset->purchase_cost = $validated['purchase_cost'] ?? ($awarded?->price ?? null);
        $asset->purchase_date = $validated['purchase_date'] ?? now()->toDateString();
        $asset->supplier_id   = $awarded?->supplier_id ?? null;

        $asset->save();

        $po->update(['asset_id' => $asset->id]);

        return redirect()->route('purchase-orders.receive', $po->id)
            ->with('success', trans('general.asset_created_linked', ['tag' => $asset->asset_tag]));
    }

    public function close(PurchaseOrder $po): RedirectResponse
    {
        if (! in_array($po->status, ['received', 'sent'])) {
            return redirect()->route('purchase-orders.show', $po->id)
                ->with('error', 'Only received or sent purchase orders can be closed.');
        }

        $po->update(['status' => 'closed']);

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', "Purchase order {$po->po_number} has been closed.");
    }

    private function generatePoNumber(): string
    {
        $year = now()->format('Y');

        // Count existing POs for this year and use the next sequence number.
        // Wrapped in the same transaction as the INSERT so no two concurrent
        // requests can generate the same number.
        $sequence = PurchaseOrder::whereYear('created_at', $year)->lockForUpdate()->count() + 1;

        return sprintf('PO-%s-%04d', $year, $sequence);
    }
}
