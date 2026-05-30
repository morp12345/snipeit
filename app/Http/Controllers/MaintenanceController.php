<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Services\OrangeHRMOffboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $maintenanceRequests = MaintenanceRequest::with(['asset', 'requestedBy'])
            ->latest()
            ->paginate(25);

        return view('maintenance.index', compact('maintenanceRequests'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('maintenance.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'type'     => ['required', 'in:repair,inspection,scheduled_audit'],
            'notes'    => ['nullable', 'string'],
        ]);

        MaintenanceRequest::create([
            ...$validated,
            'requested_by' => Auth::id(),
            'status'       => 'open',
        ]);

        return redirect()->back()->with('success', 'Maintenance request submitted successfully.');
    }

    public function show(MaintenanceRequest $mr): \Illuminate\View\View
    {
        $mr->load(['asset', 'requestedBy']);
        return view('maintenance.show', compact('mr'));
    }

    public function resolve(Request $request, MaintenanceRequest $mr): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'max:2000'],
        ]);

        $mr->markResolved($validated['resolution_notes']);

        return redirect()->back()->with('success', 'Maintenance request marked as resolved.');
    }

    public function decommission(MaintenanceRequest $mr): RedirectResponse
    {
        $mr->flagForDecommission();

        return redirect()->back()->with('success', 'Asset flagged for decommissioning.');
    }

    public function returnToService(MaintenanceRequest $mr): RedirectResponse
    {
        if (! $mr->asset) {
            return redirect()->back()->with('error', 'No asset linked to this maintenance request.');
        }

        $mr->markResolved('Asset returned to active service after maintenance.');

        return redirect()->back()->with('success', "Asset [{$mr->asset->asset_tag}] returned to active service.");
    }

    public function syncOrangehrm(MaintenanceRequest $mr, OrangeHRMOffboardingService $offboarding): RedirectResponse
    {
        if (! $mr->asset || ! $mr->requested_by) {
            return redirect()->back()->with('error', 'Asset or requesting user is missing — cannot sync with OrangeHRM.');
        }

        $result = $offboarding->syncAssetReturn($mr->requested_by, $mr->asset_id);

        Log::info('OrangeHRM offboarding sync from MaintenanceRequest #'.$mr->id.': '.json_encode($result));

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'warning',
            $result['message']
        );
    }

    public function uploadDisposalCertificate(Request $request, MaintenanceRequest $mr): RedirectResponse
    {
        $request->validate([
            'certificate' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        // Only decommissioned requests should have a disposal certificate
        if ($mr->status !== 'decommissioned') {
            return redirect()->back()->with(
                'error',
                'A disposal certificate can only be uploaded for a decommissioned maintenance request.'
            );
        }

        $directory = 'private_uploads/disposal_certificates';
        Storage::makeDirectory($directory);

        // Filename: {mr-id}-{asset-id}-{timestamp}.pdf — unique and traceable
        $filename = implode('-', [
            'mr',
            $mr->id,
            'asset',
            $mr->asset_id,
            now()->format('YmdHis'),
        ]).'.pdf';

        $path = $request->file('certificate')->storeAs($directory, $filename);

        // Replace any previously uploaded certificate for this request
        if ($mr->disposal_certificate_path && Storage::exists($mr->disposal_certificate_path)) {
            Storage::delete($mr->disposal_certificate_path);
        }

        $mr->update(['disposal_certificate_path' => $path]);

        return redirect()->back()->with('success', 'Disposal certificate uploaded successfully.');
    }
}
