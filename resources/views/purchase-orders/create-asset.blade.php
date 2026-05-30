@extends('layouts/default')

@section('title')
    {{ trans('general.create_asset_from_po') }}: {{ $po->po_number }}
    @parent
@stop

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        @include('partials.procurement-pipeline', ['pipelineStep' => 7])

        @if (session('error'))
            <div class="alert alert-danger fade in">
                <i class="fas fa-times" aria-hidden="true"></i> {{ session('error') }}
            </div>
        @endif

        <form class="form-horizontal"
              method="POST"
              action="{{ route('purchase-orders.asset.store', $po->id) }}"
              autocomplete="off">
            @csrf

            <div class="box box-default">

                <div class="box-header with-border">
                    <h2 class="box-title">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i>
                        {{ trans('general.create_asset_from_po') }}
                        <small class="text-muted" style="font-size: 13px; margin-left: 8px;">
                            {{ trans('general.for_po') }}: <strong>{{ $po->po_number }}</strong>
                        </small>
                    </h2>
                    <div class="box-tools">
                        <a href="{{ route('purchase-orders.receive', $po->id) }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            {{ trans('general.back') }}
                        </a>
                    </div>
                </div>

                <div class="box-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-times" aria-hidden="true"></i>
                            {{ trans('general.form_has_errors') }}
                        </div>
                    @endif

                    {{-- Pre-fill notice --}}
                    @if ($awarded)
                        <div class="alert alert-info fade in" style="margin-bottom: 20px;">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            {{ trans('general.prefilled_from_po') }}:
                            <strong>{{ $awarded->supplier_name }}</strong>
                            &mdash;
                            {{ $awarded->currency }} {{ number_format((float) $awarded->price, 2) }}
                        </div>
                    @endif

                    {{-- Asset Tag --}}
                    <div class="form-group {{ $errors->has('asset_tag') ? 'has-error' : '' }}">
                        <label for="asset_tag" class="col-md-3 control-label">
                            {{ trans('general.asset_tag') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-6">
                            <input type="text"
                                   class="form-control"
                                   name="asset_tag"
                                   id="asset_tag"
                                   value="{{ old('asset_tag', $suggestedTag) }}"
                                   required
                                   aria-required="true">
                            {!! $errors->first('asset_tag', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Name --}}
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name" class="col-md-3 control-label">
                            {{ trans('general.asset_name') }}
                        </label>
                        <div class="col-md-6">
                            <input type="text"
                                   class="form-control"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $po->purchaseRequest?->title) }}">
                            {!! $errors->first('name', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Model --}}
                    <div class="form-group {{ $errors->has('model_id') ? 'has-error' : '' }}">
                        <label for="model_id" class="col-md-3 control-label">
                            {{ trans('general.asset_model') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-6">
                            <select class="js-data-ajax"
                                    data-endpoint="models"
                                    data-placeholder="{{ trans('general.select_model') }}"
                                    name="model_id"
                                    id="model_id"
                                    style="width: 100%"
                                    required
                                    aria-required="true">
                                @if (old('model_id'))
                                    <option value="{{ old('model_id') }}" selected="selected">{{ old('model_id') }}</option>
                                @endif
                            </select>
                            {!! $errors->first('model_id', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Status Label --}}
                    <div class="form-group {{ $errors->has('status_id') ? 'has-error' : '' }}">
                        <label for="status_id" class="col-md-3 control-label">
                            {{ trans('general.status') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-6">
                            <select class="form-control select2"
                                    name="status_id"
                                    id="status_id"
                                    required
                                    aria-required="true"
                                    style="width: 100%">
                                <option value="">{{ trans('general.select_statuslabel') }}</option>
                                @foreach ($statusLabels as $id => $label)
                                    <option value="{{ $id }}" {{ old('status_id') == $id ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('status_id', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Serial Number --}}
                    <div class="form-group {{ $errors->has('serial') ? 'has-error' : '' }}">
                        <label for="serial" class="col-md-3 control-label">
                            {{ trans('general.serial_number') }}
                        </label>
                        <div class="col-md-6">
                            <input type="text"
                                   class="form-control"
                                   name="serial"
                                   id="serial"
                                   value="{{ old('serial') }}"
                                   placeholder="{{ trans('general.serial_number') }}">
                            {!! $errors->first('serial', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Purchase Cost --}}
                    <div class="form-group {{ $errors->has('purchase_cost') ? 'has-error' : '' }}">
                        <label for="purchase_cost" class="col-md-3 control-label">
                            {{ trans('general.purchase_cost') }}
                        </label>
                        <div class="col-md-4">
                            <input type="number"
                                   class="form-control"
                                   name="purchase_cost"
                                   id="purchase_cost"
                                   value="{{ old('purchase_cost', $awarded?->price) }}"
                                   step="0.01"
                                   min="0">
                            {!! $errors->first('purchase_cost', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                        @if ($awarded)
                            <div class="col-md-2">
                                <p class="form-control-static text-muted">{{ $awarded->currency }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Purchase Date --}}
                    <div class="form-group {{ $errors->has('purchase_date') ? 'has-error' : '' }}">
                        <label for="purchase_date" class="col-md-3 control-label">
                            {{ trans('general.purchase_date') }}
                        </label>
                        <div class="col-md-4">
                            <input type="date"
                                   class="form-control"
                                   name="purchase_date"
                                   id="purchase_date"
                                   value="{{ old('purchase_date', now()->toDateString()) }}">
                            {!! $errors->first('purchase_date', '<span class="alert-msg"><i class="fas fa-times"></i> :message</span>') !!}
                        </div>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <div class="col-md-7 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle" aria-hidden="true"></i>
                            {{ trans('general.create_asset_from_po') }}
                        </button>
                        <a href="{{ route('purchase-orders.receive', $po->id) }}" class="btn btn-link btn-sm">
                            {{ trans('button.cancel') }}
                        </a>
                    </div>
                </div>

            </div>{{-- /.box --}}

        </form>

    </div>
</div>

@stop
