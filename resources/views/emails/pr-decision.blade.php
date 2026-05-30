@component('mail::message')

@if ($decision === 'approved')
# ✅ Purchase Request Approved
@else
# ❌ Purchase Request Rejected
@endif

Your purchase request has been **{{ ucfirst($decision) }}**.

@component('mail::table')
|                  |                                                                     |
| ---------------- | ------------------------------------------------------------------- |
| **Title**        | {{ $pr->title }}                                                    |
| **Decision**     | {{ ucfirst($decision) }}                                            |
| **Decided By**   | {{ $approver?->present()->fullName() ?? $approver?->username ?? 'N/A' }} |
| **Decided At**   | {{ $pr->approved_at?->toDateTimeString() ?? now()->toDateTimeString() }} |
| **Department**   | {{ $pr->department ?? 'N/A' }}                                      |
@endcomponent

@if ($pr->notes)
**Notes from approver**

{{ $pr->notes }}
@endif

@if ($decision === 'approved')

Your request has been approved. A purchase order will be raised.

@if ($poUrl)
@component('mail::button', ['url' => $poUrl, 'color' => 'success'])
View Purchase Request & PO
@endcomponent

If the button above does not work, copy and paste the link below into your browser:

{{ $poUrl }}
@endif

@else

Your request was not approved at this time. If you have questions, please
contact the approver or resubmit with additional justification.

@if ($pr->notes)
**Rejection reason:** {{ $pr->notes }}
@endif

@endif

{{ trans('mail.best_regards') }}

{{ config('app.name') }}

@endcomponent
