<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierQuotation extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'supplier_id',
        'supplier_name',
        'supplier_email',
        'price',
        'currency',
        'lead_time_days',
        'warranty_months',
        'compliance_notes',
        'document_path',
        'is_awarded',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'is_awarded' => 'boolean',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function scopeAwarded(Builder $query): Builder
    {
        return $query->where('is_awarded', true);
    }

    // Returns true if at least 3 quotations exist for the same PO,
    // satisfying a minimum 3-quote procurement requirement.
    public function isMinimumMet(): bool
    {
        return static::where('purchase_order_id', $this->purchase_order_id)
            ->count() >= 3;
    }
}
