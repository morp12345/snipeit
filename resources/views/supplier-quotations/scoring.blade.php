@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.supplier_scoring') }}: {{ $po->po_number }}
    @parent
@stop

{{-- Page content --}}
@section('content')

@php
    $totalQuotes = $po->quotations()->count();
    $minRequired = 3;
    $minimumMet  = $totalQuotes >= $minRequired;
    $anyAwarded  = $po->quotations()->where('is_awarded', true)->exists();
@endphp

<div class="row">
    <div class="col-md-12">

        @include('partials.procurement-pipeline', ['pipelineStep' => 5])

        {{-- ── Minimum requirement notice ─────────────────────────────────── --}}
        @if (! $minimumMet)
            <div class="alert alert-warning fade in">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <strong>{{ trans('general.minimum_quotes_required_title') }}</strong>
                {{ trans('general.minimum_quotes_required_body', ['count' => $totalQuotes, 'min' => $minRequired]) }}
            </div>
        @else
            <div class="alert alert-success fade in">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                {{ trans('general.quotes_received_count', ['count' => $totalQuotes, 'min' => $minRequired]) }}
                @if ($anyAwarded)
                    &mdash; <strong>{{ trans('general.award_already_made') }}</strong>
                @endif
            </div>
        @endif

        {{-- ── Anomaly Detection ───────────────────────────────────────────── --}}
        <div class="box {{ empty($anomalies) ? 'box-success' : 'box-warning' }}">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ trans('general.anomaly_detection') }}
                </h3>
            </div>
            <div class="box-body" style="padding-bottom: 5px;">
                @if (empty($anomalies))
                    <div class="callout callout-success" style="margin-bottom: 10px;">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        {{ trans('general.no_anomalies_detected') }}
                    </div>
                @else
                    @foreach ($anomalies as $anomaly)
                    <div class="callout callout-warning" style="margin-bottom: 10px;">
                        <h4 style="margin-top: 0;">
                            {{ $anomaly['supplier_name'] }}
                            @foreach ($anomaly['flags'] as $flag)
                                <span class="label label-warning" style="font-size: 11px; margin-left: 4px;">
                                    {{ trans('general.anomaly_flag_' . $flag) }}
                                </span>
                            @endforeach
                        </h4>
                        <ul style="margin: 4px 0 6px 16px; padding: 0;">
                            @foreach ($anomaly['stat_details'] as $detail)
                                <li><small>{{ $detail }}</small></li>
                            @endforeach
                        </ul>
                        @if (! empty($anomaly['ai_explanation']))
                            <p style="margin: 6px 0 0; font-size: 12px;">
                                <i class="fas fa-robot text-info" aria-hidden="true"></i>
                                <em>{{ $anomaly['ai_explanation'] }}</em>
                            </p>
                        @endif
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-trophy" aria-hidden="true"></i>
                    {{ trans('general.supplier_scoring') }}
                    <small class="text-muted" style="font-size: 13px; margin-left: 8px;">
                        {{ trans('general.for_po') }}: <strong>{{ $po->po_number }}</strong>
                        &mdash; {{ trans('general.top_n_results', ['n' => count($ranked)]) }}
                    </small>
                </h2>
                <div class="box-tools pull-right">
                    <a href="{{ url()->previous() }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        {{ trans('general.back') }}
                    </a>
                </div>
            </div>

            <div class="box-body no-padding">

                @if (empty($ranked))
                    <div class="callout callout-info" style="margin: 15px;">
                        <p>{{ trans('general.no_quotations_to_score') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th class="col-md-1 text-center">{{ trans('general.rank') }}</th>
                                    <th class="col-md-2">{{ trans('general.supplier_name') }}</th>
                                    <th class="col-md-2 text-right">{{ trans('general.price') }}</th>
                                    <th class="col-md-1 text-center">{{ trans('general.lead_time_days') }}</th>
                                    <th class="col-md-1 text-center">{{ trans('general.warranty_months') }}</th>
                                    <th class="col-md-2 text-center">{{ trans('general.compliance_notes') }}</th>
                                    <th class="col-md-2 text-center">{{ trans('general.score') }}</th>
                                    <th class="col-md-1 text-center">{{ trans('button.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ranked as $entry)
                                @php $isTop = $entry['rank'] === 1; @endphp
                                <tr class="{{ $isTop ? 'success' : '' }}">

                                    {{-- Rank badge --}}
                                    <td class="text-center">
                                        @if ($isTop)
                                            <span class="label label-success" style="font-size: 13px;">
                                                <i class="fas fa-trophy" aria-hidden="true"></i>
                                                #{{ $entry['rank'] }}
                                            </span>
                                        @elseif ($entry['rank'] === 2)
                                            <span class="label label-primary" style="font-size: 13px;">
                                                #{{ $entry['rank'] }}
                                            </span>
                                        @else
                                            <span class="label label-default" style="font-size: 13px;">
                                                #{{ $entry['rank'] }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Supplier Name --}}
                                    <td>
                                        <strong>{{ $entry['supplier_name'] }}</strong>
                                        @if ($entry['is_awarded'])
                                            <span class="label label-success" style="margin-left:4px; font-size:10px;">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                                {{ trans('general.awarded') }}
                                            </span>
                                        @endif
                                        @if ($entry['supplier_email'])
                                            <br>
                                            <small class="text-muted">{{ $entry['supplier_email'] }}</small>
                                        @endif
                                    </td>

                                    {{-- Price --}}
                                    <td class="text-right">
                                        <strong>{{ $entry['currency'] }} {{ number_format((float) $entry['price'], 2) }}</strong>
                                        <br>
                                        <small class="text-muted" title="{{ trans('general.score_breakdown') }}: {{ $entry['score_breakdown']['price'] }}">
                                            {{ trans('general.weight') }}: {{ $entry['score_breakdown']['price'] * 100 }}%
                                        </small>
                                    </td>

                                    {{-- Lead Time --}}
                                    <td class="text-center">
                                        {{ $entry['lead_time_days'] !== null ? $entry['lead_time_days'].' '.trans('general.days') : '—' }}
                                        <br>
                                        <small class="text-muted">{{ $entry['score_breakdown']['lead_time'] * 100 }}%</small>
                                    </td>

                                    {{-- Warranty --}}
                                    <td class="text-center">
                                        {{ $entry['warranty_months'] !== null ? $entry['warranty_months'].' '.trans('general.months') : '—' }}
                                        <br>
                                        <small class="text-muted">{{ $entry['score_breakdown']['warranty'] * 100 }}%</small>
                                    </td>

                                    {{-- Compliance --}}
                                    <td class="text-center">
                                        @if ($entry['compliance_notes'])
                                            <span class="label label-info" title="{{ $entry['compliance_notes'] }}">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                                {{ trans('general.yes') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">+{{ $entry['score_breakdown']['compliance'] * 100 }}%</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Score with progress bar --}}
                                    <td class="text-center">
                                        <strong style="font-size: 15px;">{{ number_format($entry['score'] * 100, 1) }}%</strong>
                                        <div class="progress" style="margin: 4px 0 0; height: 8px;">
                                            <div class="progress-bar {{ $isTop ? 'progress-bar-success' : 'progress-bar-info' }}"
                                                 role="progressbar"
                                                 aria-valuenow="{{ round($entry['score'] * 100, 1) }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100"
                                                 style="width: {{ round($entry['score'] * 100, 1) }}%;">
                                                <span class="sr-only">{{ round($entry['score'] * 100, 1) }}%</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Award button --}}
                                    <td class="text-center">
                                        @if ($entry['is_awarded'])
                                            <span class="btn btn-success btn-sm disabled" aria-disabled="true">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                                {{ trans('general.awarded') }}
                                            </span>
                                        @elseif (! $minimumMet)
                                            <button type="button"
                                                    class="btn btn-default btn-sm"
                                                    disabled
                                                    title="{{ trans('general.minimum_quotes_required_title') }}">
                                                <i class="fas fa-lock" aria-hidden="true"></i>
                                                {{ trans('general.award') }}
                                            </button>
                                        @else
                                            <form method="POST"
                                                  action="{{ route('quotations.award', $entry['id']) }}"
                                                  onsubmit="return confirm({{ json_encode(trans('general.confirm_award_supplier', ['name' => $entry['supplier_name']])) }})">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-trophy" aria-hidden="true"></i>
                                                    {{ trans('general.award') }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>{{-- /.table-responsive --}}

                    {{-- AI reasoning for top pick --}}
                    @php $topEntry = collect($ranked)->firstWhere('rank', 1); @endphp
                    <div class="box-footer" style="font-size: 12px; color: #777;">
                        @if (! empty($topEntry['ai_reason']))
                            <i class="fas fa-robot text-info" aria-hidden="true"></i>
                            <strong>{{ $topEntry['supplier_name'] }}:</strong>
                            {{ $topEntry['ai_reason'] }}
                        @else
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            {{ trans('general.scoring_formula_legend') }}
                        @endif
                    </div>
                @endif

            </div>{{-- /.box-body --}}

        </div>{{-- /.box --}}

    </div>{{-- /.col-md-12 --}}
</div>{{-- /.row --}}

@stop
