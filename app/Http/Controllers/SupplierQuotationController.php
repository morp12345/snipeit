<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierQuotation;
use App\Services\SupplierScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplierQuotationController extends Controller
{
    public function create(PurchaseOrder $po): \Illuminate\View\View
    {
        $po->load('quotations');

        return view('supplier-quotations.create', compact('po'));
    }

    public function scoring(PurchaseOrder $po, SupplierScoringService $scorer): \Illuminate\View\View
    {
        $ranked    = $scorer->aiEnhancedRank($po->id);
        $anomalies = $scorer->detectAnomalies($po->id);
        $po->load('quotations');

        return view('supplier-quotations.scoring', compact('po', 'ranked', 'anomalies'));
    }

    public function store(Request $request, PurchaseOrder $po): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id'      => ['required', 'integer', 'exists:suppliers,id'],
            'price'            => ['required', 'numeric', 'min:0'],
            'currency'         => ['nullable', 'string', 'size:3'],
            'lead_time_days'   => ['nullable', 'integer', 'min:0'],
            'warranty_months'  => ['nullable', 'integer', 'min:0'],
            'compliance_notes' => ['nullable', 'string'],
            'document'         => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);

        // Prevent duplicate quote from the same supplier for this PO
        if (SupplierQuotation::where('purchase_order_id', $po->id)->where('supplier_id', $supplier->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', "{$supplier->name} has already submitted a quotation for this purchase order.");
        }

        $directory = 'private_uploads/quotations/'.$po->id;
        Storage::makeDirectory($directory);

        $filename = Str::slug($supplier->name).'-'.now()->format('YmdHis').'.pdf';
        $path     = $request->file('document')->storeAs($directory, $filename);

        SupplierQuotation::create([
            'purchase_order_id' => $po->id,
            'supplier_id'       => $supplier->id,
            'supplier_name'     => $supplier->name,
            'supplier_email'    => $supplier->email,
            'price'             => $validated['price'],
            'currency'          => $validated['currency'] ?? 'PHP',
            'lead_time_days'    => $validated['lead_time_days'] ?? null,
            'warranty_months'   => $validated['warranty_months'] ?? null,
            'compliance_notes'  => $validated['compliance_notes'] ?? null,
            'document_path'     => $path,
            'is_awarded'        => false,
        ]);

        return redirect()->back()->with('success', "Quotation from {$supplier->name} saved successfully.");
    }

    public function award(SupplierQuotation $quotation): RedirectResponse
    {
        if (! $quotation->isMinimumMet()) {
            $count = SupplierQuotation::where('purchase_order_id', $quotation->purchase_order_id)->count();

            return redirect()->back()->with(
                'error',
                "At least 3 quotations are required before awarding. This PO currently has {$count}."
            );
        }

        DB::transaction(function () use ($quotation) {
            // Clear any previously awarded quotation for this PO
            SupplierQuotation::where('purchase_order_id', $quotation->purchase_order_id)
                ->where('id', '!=', $quotation->id)
                ->update(['is_awarded' => false]);

            // Award this quotation
            $quotation->update(['is_awarded' => true]);

            // Advance the parent PO to 'sent'
            PurchaseOrder::where('id', $quotation->purchase_order_id)
                ->update(['status' => 'sent']);
        });

        return redirect()->back()->with('success', "Quotation from {$quotation->supplier_name} has been awarded.");
    }
}
