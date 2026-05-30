<?php

namespace App\Http\Controllers;

use App\Events\PurchaseRequestApproved;
use App\Mail\PurchaseRequestDecision;
use App\Mail\PurchaseRequestSubmitted;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchaseRequestController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $this->authorize('viewAny', PurchaseRequest::class);

        $purchaseRequests = PurchaseRequest::with(['requestedBy'])
            ->latest()
            ->paginate(25);

        return view('purchase-requests.index', compact('purchaseRequests'));
    }

    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', PurchaseRequest::class);

        return view('purchase-requests.create');
    }

    public function show(PurchaseRequest $pr): \Illuminate\View\View
    {
        $this->authorize('view', $pr);

        $pr->load(['requestedBy', 'approvedBy', 'purchaseOrder.quotations']);

        return view('purchase-requests.show', compact('pr'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', PurchaseRequest::class);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'department'    => ['nullable', 'string', 'max:255'],
            'justification' => ['nullable', 'string'],
            'notes'         => ['nullable', 'string'],
        ]);

        $purchaseRequest = PurchaseRequest::create([
            ...$validated,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ]);

        $this->notifyApprovers($purchaseRequest);

        return redirect()->back()->with('success', trans('general.purchase_request_submitted'));
    }

    public function approve(Request $request, PurchaseRequest $pr): RedirectResponse
    {
        $this->authorize('approve', $pr);

        $pr->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        PurchaseRequestApproved::dispatch($pr, Auth::user());

        // Notify the requester — queue so the redirect is not blocked
        $requesterEmail = $pr->requestedBy?->email;
        if ($requesterEmail) {
            try {
                Mail::to($requesterEmail)->queue(new PurchaseRequestDecision($pr, 'approved'));
            } catch (\Throwable $e) {
                Log::warning("PurchaseRequest approve: could not queue decision mail to {$requesterEmail}: ".$e->getMessage());
            }
        }

        return redirect()->back()->with('success', trans('general.purchase_request_approved'));
    }

    public function reject(Request $request, PurchaseRequest $pr): RedirectResponse
    {
        $this->authorize('reject', $pr);

        $validated = $request->validate([
            'rejection_note' => ['required', 'string', 'max:1000'],
        ]);

        $pr->update([
            'status' => 'rejected',
            'notes'  => $validated['rejection_note'],
        ]);

        // Notify the requester — queue so the redirect is not blocked
        $requesterEmail = $pr->requestedBy?->email;
        if ($requesterEmail) {
            try {
                Mail::to($requesterEmail)->queue(new PurchaseRequestDecision($pr, 'rejected'));
            } catch (\Throwable $e) {
                Log::warning("PurchaseRequest reject: could not queue decision mail to {$requesterEmail}: ".$e->getMessage());
            }
        }

        return redirect()->back()->with('success', trans('general.purchase_request_rejected'));
    }

    private function notifyApprovers(PurchaseRequest $purchaseRequest): void
    {
        // Build recipient list: all active superusers with an email address.
        // Also CC the Snipe-IT admin_cc_email if configured (Settings > General).
        $approvers = User::whereNull('deleted_at')
            ->where('activated', 1)
            ->where(function ($q) {
                $q->where('permissions', 'LIKE', '%"superuser":"1"%')
                  ->orWhere('permissions', 'LIKE', '%"superuser":1%');
            })
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();

        $adminEmail = Setting::getSettings()->admin_cc_email;
        if ($adminEmail && ! in_array($adminEmail, $approvers, true)) {
            $approvers[] = $adminEmail;
        }

        foreach ($approvers as $email) {
            try {
                Mail::to($email)->queue(new PurchaseRequestSubmitted($purchaseRequest));
            } catch (\Throwable $e) {
                Log::warning("PurchaseRequest: could not queue submission mail to {$email}: ".$e->getMessage());
            }
        }
    }
}
