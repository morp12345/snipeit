@component('mail::message')

# New Purchase Request Pending Approval

A new purchase request has been submitted and is awaiting your approval.

@component('mail::table')
|                    |                                                                 |
| ------------------ | --------------------------------------------------------------- |
| **Title**          | {{ $pr->title }}                                                |
| **Requested By**   | {{ $requester?->present()->fullName() ?? $requester?->username ?? 'N/A' }} |
| **Department**     | {{ $pr->department ?? 'N/A' }}                                  |
| **Submitted At**   | {{ $pr->created_at->toDateTimeString() }}                       |
@endcomponent

@if ($pr->justification)
**Justification**

{{ $pr->justification }}
@endif

@component('mail::button', ['url' => $approvalUrl, 'color' => 'primary'])
Review & Approve Request
@endcomponent

If the button above does not work, copy and paste the link below into your browser:

{{ $approvalUrl }}

{{ trans('mail.best_regards') }}

{{ config('app.name') }}

@endcomponent
