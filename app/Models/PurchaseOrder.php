<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'po_number',
        'created_by',
        'status',
        'total_amount',
        'currency',
        'notes',
        'asset_id',
        'received_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'received_at'  => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(SupplierQuotation::class, 'purchase_order_id');
    }

    // draft + sent = still in progress
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    // received + closed = no longer active
    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereIn('status', ['received', 'closed']);
    }
}
