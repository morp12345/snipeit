<?php

namespace App\Events;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseRequestApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PurchaseRequest $purchaseRequest,
        public User $approvedBy,
    ) {}
}
