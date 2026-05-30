@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.receive_assets') }}: {{ $po->po_number }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        @include('partials.procurement-pipeline', [
            'pipelineStep' => in_array($po->status, ['received', 'closed']) ? 7 : 6
        ])

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success fade in">
                <i class="fas fa-check" aria-hidden="true"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger fade in">
                <i class="fas fa-times" aria-hidden="true"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Already received banner --}}
        @if ($po->status === 'received' || $po->status === 'closed')
            <div class="alert alert-success fade in">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <strong>{{ trans('general.already_received') }}</strong>
                @if ($po->received_at)
                    &mdash; {{ $po->received_at->format('d M Y, H:i') }}
                @endif
            </div>
        @endif

        {{-- No award yet warning --}}
        @if (! $awarded)
            <div class="alert alert-warning fade in">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                {{ trans('general.awaiting_award') }}
            </div>
        @endif

        {{-- ── PO Summary ──────────────────────────────────────────────────── --}}
        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-box-open" aria-hidden="true"></i>
                    {{ trans('general.receive_assets') }}
                    <small class="text-muted" style="font-size: 13px; margin-left: 8px;">
                        {{ trans('general.for_po') }}: <strong>{{ $po->po_number }}</strong>
                    </small>
                </h2>
                <div class="box-tools">
                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        {{ trans('general.back_to_po') }}
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-condensed">
                    <tbody>

                        <tr>
                            <th class="col-md-4 text-right">{{ trans('general.po_number') }}</th>
                            <td><strong>{{ $po->po_number }}</strong></td>
                        </tr>

                        @if ($po->purchaseRequest)
                        <tr>
                            <th class="text-right">{{ trans('general.purchase_request') }}</th>
                            <td>
                                <a href="{{ route('purchase-requests.show', $po->purchaseRequest->id) }}">
                                    {{ $po->purchaseRequest->title }}
                                </a>
                                @if ($po->purchaseRequest->requestedBy)
                                    <br>
                                    <small class="text-muted">
                                        {{ trans('general.requested_by') }}: {{ $po->purchaseRequest->requestedBy->full_name }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                        @endif

                        @if ($awarded)
                        <tr>
                            <th class="text-right">{{ trans('general.awarded_supplier') }}</th>
                            <td>
                                <strong>{{ $awarded->supplier_name }}</strong>
                                @if ($awarded->supplier_email)
                                    <br><small class="text-muted">{{ $awarded->supplier_email }}</small>
                                @endif
                                <br>
                                <span class="label label-success" style="margin-top: 4px; display: inline-block;">
                                    <i class="fas fa-trophy" aria-hidden="true"></i>
                                    {{ trans('general.awarded') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-right">{{ trans('general.price') }}</th>
                            <td>
                                <strong>{{ $awarded->currency }} {{ number_format((float) $awarded->price, 2) }}</strong>
                            </td>
                        </tr>
                        @if ($awarded->lead_time_days !== null)
                        <tr>
                            <th class="text-right">{{ trans('general.lead_time_days') }}</th>
                            <td>{{ $awarded->lead_time_days }} {{ trans('general.days') }}</td>
                        </tr>
                        @endif
                        @endif

                        <tr>
                            <th class="text-right">{{ trans('general.status') }}</th>
                            <td>
                                @if ($po->status === 'received')
                                    <span class="label label-success">{{ trans('general.received') }}</span>
                                    @if ($po->received_at)
                                        <small class="text-muted" style="margin-left: 6px;">{{ $po->received_at->format('d M Y, H:i') }}</small>
                                    @endif
                                @elseif ($po->status === 'closed')
                                    <span class="label label-default">{{ trans('general.closed') }}</span>
                                @elseif ($po->status === 'sent')
                                    <span class="label label-info">{{ trans('general.sent') }}</span>
                                @else
                                    <span class="label label-warning">{{ trans('general.draft') }}</span>
                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>{{-- /.box --}}

        {{-- ── Receive Form ────────────────────────────────────────────────── --}}
        @if ($po->status !== 'received' && $po->status !== 'closed')
        <form class="form-horizontal"
              method="POST"
              action="{{ route('purchase-orders.mark-received', $po->id) }}"
              autocomplete="off"
              onsubmit="return confirm({{ json_encode(trans('general.confirm_receive_po')) }})">
            @csrf
            @method('PATCH')

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                        {{ trans('general.confirm_receipt') }}
                    </h3>
                </div>

                <div class="box-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-times" aria-hidden="true"></i>
                            {{ trans('general.form_has_errors') }}
                        </div>
                    @endif

                    {{-- Link to Asset (optional) --}}
                    <div class="form-group {{ $errors->has('asset_id') ? 'has-error' : '' }}">
                        <label for="asset_id" class="col-md-3 control-label">
                            {{ trans('general.link_asset_optional') }}
                        </label>
                        <div class="col-md-7">
                            <select class="js-data-ajax"
                                    data-endpoint="hardware"
                                    data-placeholder="{{ trans('general.select_asset') }}"
                                    name="asset_id"
                                    id="asset_id"
                                    style="width: 100%"
                                    aria-label="{{ trans('general.link_asset_optional') }}">
                                @if ($po->asset_id && $po->asset)
                                    <option value="{{ $po->asset->id }}" selected="selected">
                                        {{ $po->asset->asset_tag }} — {{ $po->asset->name }}
                                    </option>
                                @elseif (old('asset_id'))
                                    <option value="{{ old('asset_id') }}" selected="selected">
                                        {{ old('asset_id') }}
                                    </option>
                                @endif
                            </select>
                            <p class="help-block">
                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                                {{ trans('general.link_asset_help') }}
                            </p>
                            {!! $errors->first('asset_id', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Delivery Notes --}}
                    <div class="form-group {{ $errors->has('delivery_notes') ? 'has-error' : '' }}">
                        <label for="delivery_notes" class="col-md-3 control-label">
                            {{ trans('general.delivery_notes') }}
                        </label>
                        <div class="col-md-7">
                            <textarea class="form-control"
                                      name="delivery_notes"
                                      id="delivery_notes"
                                      rows="4"
                                      placeholder="{{ trans('general.delivery_notes_placeholder') }}"
                            >{{ old('delivery_notes', $po->notes) }}</textarea>
                            {!! $errors->first('delivery_notes', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <div class="col-md-7 col-md-offset-3">
                        @if ($awarded)
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                {{ trans('general.mark_as_received') }}
                            </button>
                        @else
                            <button type="submit" class="btn btn-success" disabled>
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                {{ trans('general.mark_as_received') }}
                            </button>
                            <small class="text-muted" style="margin-left: 8px;">
                                {{ trans('general.awaiting_award') }}
                            </small>
                        @endif
                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-link btn-sm">
                            {{ trans('button.cancel') }}
                        </a>
                    </div>
                </div>{{-- /.box-footer --}}

            </div>{{-- /.box --}}

        </form>
        @endif

        {{-- ── Post-receive: Assign step ───────────────────────────────────── --}}
        @if ($po->status === 'received' || $po->status === 'closed')
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fas fa-user-tag" aria-hidden="true"></i>
                    {{ trans('general.pl_assign') }}
                </h3>
            </div>
            <div class="box-body">
                @if ($po->asset)
                    <p>{{ trans('general.assign_asset_help') }}</p>
                    <table class="table table-condensed table-bordered" style="margin-bottom: 12px;">
                        <tbody>
                            <tr>
                                <th class="col-md-3 text-right">{{ trans('general.asset_tag') }}</th>
                                <td>
                                    <a href="{{ route('hardware.show', $po->asset->id) }}">
                                        <strong>{{ $po->asset->asset_tag }}</strong>
                                    </a>
                                </td>
                            </tr>
                            @if ($po->asset->name)
                            <tr>
                                <th class="text-right">{{ trans('general.asset_name') }}</th>
                                <td>{{ $po->asset->name }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <a href="{{ route('hardware.checkout.create', $po->asset->id) }}"
                       class="btn btn-primary">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        {{ trans('general.checkout') }} {{ $po->asset->asset_tag }}
                    </a>
                    <a href="{{ route('hardware.show', $po->asset->id) }}"
                       class="btn btn-default"
                       style="margin-left: 6px;">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                        {{ trans('general.view_asset') }}
                    </a>
                @else
                    <p class="text-muted">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        {{ trans('general.create_asset_from_po_help') }}
                    </p>
                    <a href="{{ route('purchase-orders.asset.create', $po->id) }}"
                       class="btn btn-success">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i>
                        {{ trans('general.create_asset_from_po') }}
                    </a>
                    <a href="{{ route('hardware.index') }}"
                       class="btn btn-default"
                       style="margin-left: 6px;"
                       title="{{ trans('general.link_asset_help') }}">
                        <i class="fas fa-server" aria-hidden="true"></i>
                        {{ trans('general.assets') }}
                    </a>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- /.col-md-8 --}}
</div>{{-- /.row --}}

@stop
