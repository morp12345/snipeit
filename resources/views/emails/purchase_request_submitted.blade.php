A new purchase request requires your approval.

Title:        {{ $purchaseRequest->title }}
Department:   {{ $purchaseRequest->department ?? 'N/A' }}
Requested by: {{ $purchaseRequest->requestedBy->present()->fullName() ?? $purchaseRequest->requestedBy->username }}
Submitted at: {{ $purchaseRequest->created_at->toDateTimeString() }}

Justification:
{{ $purchaseRequest->justification ?? 'None provided.' }}

--
{{ config('app.name') }}
