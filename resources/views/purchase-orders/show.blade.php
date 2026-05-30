@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.purchase_order') }}: {{ $po->po_number }}
    @parent
@stop

{{-- Page content --}}
@section('content')

@php
    $quoteCount    = $po->quotations->count();
    $minRequired   = 3;
    $minimumMet    = $quoteCount >= $minRequired;
    $anyAwarded    = $po->quotations->where('is_awarded', true)->isNotEmpty();
    $assetAssigned = $po->asset && $po->asset->assigned_to;

    $pipelineStep = 3; // PO created
    if ($quoteCount > 0 && ! $minimumMet) {
        $pipelineStep = 4; // collecting quotes
    } elseif ($minimumMet && ! $anyAwarded) {
        $pipelineStep = 5; // ready to score/award
    } elseif ($anyAwarded && ! in_array($po->status, ['received', 'closed'])) {
        $pipelineStep = 6; // awarded — waiting to receive
    } elseif (in_array($po->status, ['received', 'closed'])) {
        $pipelineStep = $assetAssigned ? 7 : 6;
        // step 7 = assign is active (received but not yet checked out)
        // when asset IS checked out, step 7 stays active as the final completed step
    }
@endphp

<div class="row">
    <div class="col-md-10 col-md-offset-1">

        @include('partials.procurement-pipeline', ['pipelineStep' => $pipelineStep])

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check" aria-hidden="true"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times" aria-hidden="true"></i> {{ session('error') }}
            </div>
        @endif

        {{-- ── PO Details ──────────────────────────────────────────────────── --}}
        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i>
                    {{ $po->po_number }}
                </h2>
                <div class="box-tools pull-right">
                    @if ($po->purchaseRequest)
                        <a href="{{ route('purchase-requests.show', $po->purchaseRequest->id) }}"
                           class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            {{ trans('general.back_to_pr') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-condensed">
                    <tbody>

                        <tr>
                            <th class="col-md-3 text-right">{{ trans('general.po_number') }}</th>
                            <td><strong>{{ $po->po_number }}</strong></td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.status') }}</th>
                            <td>
                                @if ($po->status === 'closed')
                                    <span class="label label-default">{{ trans('general.closed') }}</span>
                                @elseif ($po->status === 'received')
                                    <span class="label label-success">{{ trans('general.received') }}</span>
                                @elseif ($po->status === 'sent')
                                    <span class="label label-info">{{ trans('general.sent') }}</span>
                                @else
                                    <span class="label label-warning">{{ trans('general.draft') }}</span>
                                @endif
                            </td>
                        </tr>

                        @if ($po->purchaseRequest)
                        <tr>
                            <th class="text-right">{{ trans('general.purchase_request') }}</th>
                            <td>
                                <a href="{{ route('purchase-requests.show', $po->purchaseRequest->id) }}">
                                    {{ $po->purchaseRequest->title }}
                                </a>
                            </td>
                        </tr>
                        @endif

                        @if ($po->createdBy)
                        <tr>
                            <th class="text-right">{{ trans('general.created_by') }}</th>
                            <td>{{ $po->createdBy->full_name }}</td>
                        </tr>
                        @endif

                        <tr>
                            <th class="text-right">{{ trans('general.created_at') }}</th>
                            <td title="{{ $po->created_at }}">{{ $po->created_at->diffForHumans() }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="box-footer">
                {{-- Add Quotation --}}
                @if ($po->status !== 'closed')
                    <a href="{{ route('quotations.create', $po->id) }}" class="btn btn-default btn-sm">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        {{ trans('general.add_quotation') }}
                    </a>
                @endif

                {{-- Score Suppliers --}}
                @if ($minimumMet)
                    <a href="{{ route('quotations.scoring', $po->id) }}"
                       class="btn btn-success btn-sm"
                       style="margin-left: 6px;">
                        <i class="fas fa-trophy" aria-hidden="true"></i>
                        {{ trans('general.score_suppliers') }}
                    </a>
                @else
                    <span class="btn btn-success btn-sm disabled"
                          style="margin-left: 6px;"
                          title="{{ trans('general.minimum_quotes_required_title') }}">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        {{ trans('general.score_suppliers') }}
                        <span class="badge">{{ $quoteCount }}/{{ $minRequired }}</span>
                    </span>
                @endif

                {{-- Receive Assets --}}
                @if ($anyAwarded && ! in_array($po->status, ['received', 'closed']))
                    <a href="{{ route('purchase-orders.receive', $po->id) }}"
                       class="btn btn-primary btn-sm"
                       style="margin-left: 6px;">
                        <i class="fas fa-box-open" aria-hidden="true"></i>
                        {{ trans('general.receive_assets') }}
                    </a>
                @elseif (in_array($po->status, ['received', 'closed']))
                    <a href="{{ route('purchase-orders.receive', $po->id) }}"
                       class="btn btn-default btn-sm"
                       style="margin-left: 6px;">
                        <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                        {{ trans('general.received') }}
                        @if ($po->received_at)
                            <small style="margin-left: 4px;">{{ $po->received_at->format('d M Y') }}</small>
                        @endif
                    </a>
                @endif

                {{-- Checkout / Assign --}}
                @if (in_array($po->status, ['received', 'closed']) && $po->asset)
                    @if ($assetAssigned)
                        <span class="btn btn-success btn-sm disabled" style="margin-left: 6px;">
                            <i class="fas fa-user-check" aria-hidden="true"></i>
                            {{ trans('general.asset_checked_out') }}
                        </span>
                    @else
                        <a href="{{ route('hardware.checkout.create', $po->asset->id) }}"
                           class="btn btn-primary btn-sm"
                           style="margin-left: 6px;">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                            {{ trans('general.checkout') }}
                        </a>
                    @endif
                @endif

                {{-- Close PO --}}
                @if ($assetAssigned && $po->status !== 'closed')
                    <form method="POST"
                          action="{{ route('purchase-orders.close', $po->id) }}"
                          style="display:inline-block; margin-left: 6px;"
                          onsubmit="return confirm({{ json_encode(trans('general.confirm_close_po')) }})">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-default btn-sm">
                            <i class="fas fa-archive" aria-hidden="true"></i>
                            {{ trans('general.close_po') }}
                        </button>
                    </form>
                @elseif ($po->status === 'closed')
                    <span class="label label-default" style="margin-left: 10px; font-size: 12px; vertical-align: middle;">
                        <i class="fas fa-archive" aria-hidden="true"></i>
                        {{ trans('general.closed') }}
                    </span>
                @endif
            </div>

        </div>{{-- /.box --}}

        {{-- ── Quotations Table ────────────────────────────────────────────── --}}
        <div class="box box-default">

            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fas fa-quote-left" aria-hidden="true"></i>
                    {{ trans('general.supplier_quotations') }}
                    <span class="badge" style="margin-left: 6px;">{{ $quoteCount }}</span>
                    @if (! $minimumMet)
                        <small class="text-muted" style="font-size: 12px; margin-left: 6px;">
                            ({{ $minRequired - $quoteCount }} {{ trans('general.more_needed') }})
                        </small>
                    @endif
                </h3>
            </div>

            <div class="box-body {{ $quoteCount ? 'no-padding' : '' }}">

                @if ($quoteCount === 0)
                    <div class="callout callout-info" style="margin: 0;">
                        <p>
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            {{ trans('general.no_quotations_yet') }}
                            {{ trans('general.minimum_3_quotes_required') }}
                        </p>
                    </div>
                @else
                    @if (! $minimumMet)
                        <div class="alert alert-info" style="margin: 15px 15px 0;">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            {{ trans('general.quotes_received_count', ['count' => $quoteCount, 'min' => $minRequired]) }}
                        </div>
                    @else
                        <div class="alert alert-success" style="margin: 15px 15px 0;">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            {{ trans('general.minimum_quotes_met') }}
                            &mdash; {{ trans('general.quotes_received_count', ['count' => $quoteCount, 'min' => $minRequired]) }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>{{ trans('general.supplier_name') }}</th>
                                    <th class="text-right">{{ trans('general.price') }}</th>
                                    <th class="text-center">{{ trans('general.lead_time_days') }}</th>
                                    <th class="text-center">{{ trans('general.warranty_months') }}</th>
                                    <th class="text-center">{{ trans('general.compliance_notes') }}</th>
                                    <th class="text-center">{{ trans('general.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($po->quotations as $q)
                                <tr class="{{ $q->is_awarded ? 'success' : '' }}">
                                    <td>
                                        <strong>{{ $q->supplier_name }}</strong>
                                        @if ($q->supplier_email)
                                            <br><small class="text-muted">{{ $q->supplier_email }}</small>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        {{ $q->currency }} {{ number_format((float) $q->price, 2) }}
                                    </td>
                                    <td class="text-center">
                                        {{ $q->lead_time_days !== null ? $q->lead_time_days.' '.trans('general.days') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $q->warranty_months !== null ? $q->warranty_months.' '.trans('general.months') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($q->compliance_notes)
                                            <span class="label label-info">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                                {{ trans('general.yes') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($q->is_awarded)
                                            <span class="label label-success">
                                                <i class="fas fa-trophy" aria-hidden="true"></i>
                                                {{ trans('general.awarded') }}
                                            </span>
                                        @else
                                            <span class="label label-default">{{ trans('general.pending') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>{{-- /.box-body --}}

        </div>{{-- /.box --}}

    </div>{{-- /.col-md-10 --}}
</div>{{-- /.row --}}

@stop
