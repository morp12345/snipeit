<?php

namespace App\Policies;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy for PurchaseRequest.
 *
 * Snipe-IT permission levels (from highest to lowest):
 *   superuser — full access; handled globally in AuthServiceProvider::boot()
 *               via Gate::before(), so it never reaches these methods.
 *   admin     — full access to all data but not app settings; used here as
 *               the gate for approve/reject, matching the role described as
 *               "admin or manager" since Snipe-IT has no native manager role.
 *   (any authenticated user) — can view and submit purchase requests.
 *
 * Note: the Gate::before() hook in AuthServiceProvider grants superusers
 * access to everything before any policy method is evaluated.
 */
final class PurchaseRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Any authenticated, active user can view purchase requests.
     */
    public function view(User $user, ?PurchaseRequest $purchaseRequest = null): bool
    {
        return true;
    }

    /**
     * Any authenticated, active user can submit a purchase request.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only admins (and superusers, handled by Gate::before) may approve.
     * hasAccess('admin') returns true for users with the 'admin' permission bit set.
     */
    public function approve(User $user, ?PurchaseRequest $purchaseRequest = null): bool
    {
        return $user->hasAccess('admin');
    }

    /**
     * Only admins (and superusers, handled by Gate::before) may reject.
     */
    public function reject(User $user, ?PurchaseRequest $purchaseRequest = null): bool
    {
        return $user->hasAccess('admin');
    }
}
