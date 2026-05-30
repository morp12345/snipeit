@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.add_quotation') }}: {{ $po->po_number }}
    @parent
@stop

{{-- Page content --}}
@section('content')

@php
    $quoteCount    = $po->quotations()->count();
    $minRequired   = 3;
    $hasAwarded    = $po->quotations()->where('is_awarded', true)->exists();
    $minimumMet    = $quoteCount >= $minRequired;
    $locked        = $minimumMet && $hasAwarded;
@endphp

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        @include('partials.procurement-pipeline', ['pipelineStep' => 4])

        {{-- ── Quote progress banner ─────────────────────────────────────── --}}
        <div class="alert {{ $minimumMet ? 'alert-success' : 'alert-info' }} fade in">
            <i class="fas {{ $minimumMet ? 'fa-check-circle' : 'fa-info-circle' }}" aria-hidden="true"></i>
            @if ($minimumMet)
                <strong>{{ trans('general.minimum_quotes_met') }}</strong>
                {{ trans('general.quotes_received_count', ['count' => $quoteCount, 'min' => $minRequired]) }}
            @else
                {{ trans('general.quotes_received_count', ['count' => $quoteCount, 'min' => $minRequired]) }}
            @endif
        </div>

        {{-- ── Locked warning (min met + a quote is already awarded) ─────── --}}
        @if ($locked)
            <div class="alert alert-warning fade in">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <strong>{{ trans('general.quotation_awarded_warning_title') }}</strong>
                {{ trans('general.quotation_awarded_warning_body') }}
            </div>
        @endif

        {{-- ── Form ──────────────────────────────────────────────────────── --}}
        <form class="form-horizontal"
              method="POST"
              action="{{ route('quotations.store', $po->id) }}"
              enctype="multipart/form-data"
              autocomplete="off">
            @csrf

            <div class="box box-default">

                <div class="box-header with-border">
                    <h2 class="box-title">
                        <i class="fas fa-quote-left" aria-hidden="true"></i>
                        {{ trans('general.add_quotation') }}
                        <small class="text-muted" style="font-size: 13px; margin-left: 8px;">
                            {{ trans('general.for_po') }}: <strong>{{ $po->po_number }}</strong>
                        </small>
                    </h2>
                    <div class="box-tools">
                        <a href="{{ url()->previous() }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            {{ trans('general.back') }}
                        </a>
                    </div>
                </div>

                <div class="box-body">

                    {{-- Global error summary --}}
                    @if ($errors->any())
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                    {{ trans('general.form_has_errors') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Supplier (select2 from supplier library) --}}
                    <div class="form-group {{ $errors->has('supplier_id') ? 'has-error' : '' }}">
                        <label for="supplier_id" class="col-md-3 control-label">
                            {{ trans('general.supplier') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-6">
                            <select class="js-data-ajax"
                                    data-endpoint="suppliers"
                                    data-placeholder="{{ trans('general.select_supplier') }}"
                                    name="supplier_id"
                                    id="supplier_id"
                                    style="width: 100%"
                                    required
                                    aria-required="true"
                                    aria-label="{{ trans('general.supplier') }}">
                                @if (old('supplier_id'))
                                    <option value="{{ old('supplier_id') }}" selected="selected">
                                        {{ \App\Models\Supplier::find(old('supplier_id'))?->name ?? old('supplier_id') }}
                                    </option>
                                @endif
                            </select>
                            {!! $errors->first('supplier_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                        <div class="col-md-1">
                            @can('create', \App\Models\Supplier::class)
                                <a href="{{ route('modal.show', 'supplier') }}"
                                   data-toggle="modal"
                                   data-target="#createModal"
                                   data-select="supplier_id"
                                   class="btn btn-sm btn-theme">
                                    {{ trans('button.new') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    {{-- Price + Currency (inline) --}}
                    <div class="form-group {{ $errors->has('price') || $errors->has('currency') ? 'has-error' : '' }}">
                        <label for="price" class="col-md-3 control-label">
                            {{ trans('general.price') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-4">
                            <input type="number"
                                   class="form-control"
                                   name="price"
                                   id="price"
                                   value="{{ old('price') }}"
                                   placeholder="0.00"
                                   min="0"
                                   step="0.01"
                                   required
                                   aria-required="true">
                            {!! $errors->first('price', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="currency" id="currency" aria-label="{{ trans('general.currency') }}">
                                @foreach (['PHP', 'USD', 'EUR'] as $cur)
                                    <option value="{{ $cur }}" {{ old('currency', 'PHP') === $cur ? 'selected' : '' }}>
                                        {{ $cur }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('currency', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Lead Time Days --}}
                    <div class="form-group {{ $errors->has('lead_time_days') ? 'has-error' : '' }}">
                        <label for="lead_time_days" class="col-md-3 control-label">
                            {{ trans('general.lead_time_days') }}
                        </label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       name="lead_time_days"
                                       id="lead_time_days"
                                       value="{{ old('lead_time_days') }}"
                                       placeholder="0"
                                       min="0">
                                <span class="input-group-addon">{{ trans('general.days') }}</span>
                            </div>
                            {!! $errors->first('lead_time_days', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Warranty Months --}}
                    <div class="form-group {{ $errors->has('warranty_months') ? 'has-error' : '' }}">
                        <label for="warranty_months" class="col-md-3 control-label">
                            {{ trans('general.warranty_months') }}
                        </label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       name="warranty_months"
                                       id="warranty_months"
                                       value="{{ old('warranty_months') }}"
                                       placeholder="0"
                                       min="0">
                                <span class="input-group-addon">{{ trans('general.months') }}</span>
                            </div>
                            {!! $errors->first('warranty_months', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Compliance Notes --}}
                    <div class="form-group {{ $errors->has('compliance_notes') ? 'has-error' : '' }}">
                        <label for="compliance_notes" class="col-md-3 control-label">
                            {{ trans('general.compliance_notes') }}
                        </label>
                        <div class="col-md-7">
                            <textarea class="form-control"
                                      name="compliance_notes"
                                      id="compliance_notes"
                                      rows="4"
                                      placeholder="{{ trans('general.compliance_notes_placeholder') }}"
                            >{{ old('compliance_notes') }}</textarea>
                            {!! $errors->first('compliance_notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- PDF Document Upload --}}
                    <div class="form-group {{ $errors->has('document') ? 'has-error' : '' }}">
                        <label for="document" class="col-md-3 control-label">
                            {{ trans('general.quote_document') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-7">
                            <label class="btn btn-sm btn-default" for="document" style="cursor:pointer;">
                                <i class="fas fa-paperclip" aria-hidden="true"></i>
                                {{ trans('button.select_file') }}
                                <input type="file"
                                       name="document"
                                       id="document"
                                       accept="application/pdf"
                                       style="display:none;"
                                       aria-label="{{ trans('general.quote_document') }}"
                                       onchange="document.getElementById('document-filename').textContent = this.files[0] ? this.files[0].name : ''">
                            </label>
                            <span id="document-filename" class="label label-default" style="font-size:12px; margin-left:6px;"></span>
                            <p class="help-block">
                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                                {{ trans('general.pdf_only_max', ['size' => '5MB']) }}
                            </p>
                            {!! $errors->first('document', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <div class="col-md-7 col-md-offset-3">

                        <button type="submit"
                                class="btn btn-primary"
                                {{ $locked ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            {{ trans('general.submit_quotation') }}
                        </button>

                        @if ($locked)
                            <span class="text-warning" style="margin-left: 10px; font-size: 12px;">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                {{ trans('general.quotation_submit_locked') }}
                            </span>
                        @endif

                        <a href="{{ url()->previous() }}" class="btn btn-link btn-sm">
                            {{ trans('button.cancel') }}
                        </a>

                    </div>
                </div>{{-- /.box-footer --}}

            </div>{{-- /.box --}}

        </form>

    </div>{{-- /.col-md-8 --}}
</div>{{-- /.row --}}

@stop
