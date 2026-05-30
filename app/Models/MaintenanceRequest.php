<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'asset_id',
        'requested_by',
        'type',
        'status',
        'notes',
        'resolution_notes',
        'decommission_needed',
        'resolved_at',
        'disposal_certificate_path',
    ];

    protected $casts = [
        'decommission_needed' => 'boolean',
        'resolved_at'         => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function markResolved(string $notes): bool
    {
        return $this->update([
            'status'           => 'resolved',
            'resolution_notes' => $notes,
            'resolved_at'      => now(),
        ]);
    }

    public function flagForDecommission(): bool
    {
        return $this->update([
            'status'              => 'decommissioned',
            'decommission_needed' => true,
            'resolved_at'         => now(),
        ]);
    }
}
