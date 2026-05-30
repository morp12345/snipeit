@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.submit_maintenance_request') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        <form class="form-horizontal"
              method="POST"
              action="{{ route('maintenance-requests.store') }}"
              autocomplete="off"
              id="maintenanceForm">
            @csrf

            <div class="box box-default">

                <div class="box-header with-border">
                    <h2 class="box-title">
                        <i class="fas fa-tools" aria-hidden="true"></i>
                        {{ trans('general.submit_maintenance_request') }}
                    </h2>
                    <div class="box-tools">
                        <a href="{{ url()->previous() }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            {{ trans('general.back') }}
                        </a>
                    </div>
                </div>

                <div class="box-body">

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

                    {{-- Asset (searchable select2 AJAX) --}}
                    <div class="form-group {{ $errors->has('asset_id') ? 'has-error' : '' }}">
                        <label for="asset_select" class="col-md-3 control-label">
                            {{ trans('general.asset') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-7">
                            <select class="js-data-ajax"
                                    data-endpoint="hardware"
                                    data-placeholder="{{ trans('general.select_asset') }}"
                                    name="asset_id"
                                    id="asset_select"
                                    style="width: 100%"
                                    required
                                    aria-required="true"
                                    aria-label="{{ trans('general.asset') }}">
                                @if (old('asset_id'))
                                    <option value="{{ old('asset_id') }}" selected="selected">
                                        {{ \App\Models\Asset::find(old('asset_id'))?->present()->fullName ?? old('asset_id') }}
                                    </option>
                                @else
                                    <option value="">{{ trans('general.select_asset') }}</option>
                                @endif
                            </select>
                            {!! $errors->first('asset_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Type --}}
                    <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                        <label for="type" class="col-md-3 control-label">
                            {{ trans('general.type') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-7">
                            <select class="form-control"
                                    name="type"
                                    id="type"
                                    required
                                    aria-required="true"
                                    aria-label="{{ trans('general.type') }}">
                                <option value="">{{ trans('general.select_type') }}</option>
                                @foreach ([
                                    'repair'          => trans('general.maintenance_type_repair'),
                                    'inspection'      => trans('general.maintenance_type_inspection'),
                                    'scheduled_audit' => trans('general.maintenance_type_scheduled_audit'),
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('type', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                        <label for="notes" class="col-md-3 control-label">
                            {{ trans('general.notes') }}
                        </label>
                        <div class="col-md-7">
                            <textarea class="form-control"
                                      name="notes"
                                      id="notes"
                                      rows="4"
                                      placeholder="{{ trans('general.notes') }}"
                                      aria-label="{{ trans('general.notes') }}"
                            >{{ old('notes') }}</textarea>
                            {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Decommission flag --}}
                    <div class="form-group">
                        <div class="col-md-7 col-md-offset-3">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           name="decommission_needed"
                                           id="decommission_needed"
                                           value="1"
                                           {{ old('decommission_needed') ? 'checked' : '' }}
                                           aria-label="{{ trans('general.flag_for_decommission') }}">
                                    <strong>{{ trans('general.flag_for_decommission') }}</strong>
                                    <span class="help-block" style="margin-top: 2px; margin-bottom: 0;">
                                        {{ trans('general.flag_for_decommission_help') }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <div class="col-md-7 col-md-offset-3">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            {{ trans('general.submit_maintenance_request') }}
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-link btn-sm">
                            {{ trans('button.cancel') }}
                        </a>
                    </div>
                </div>

            </div>{{-- /.box --}}

        </form>

        {{-- ── Asset info card — shown when an asset is selected ────────── --}}
        <div id="asset-info-card" style="display: none;">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <i class="fas fa-laptop" aria-hidden="true"></i>
                        {{ trans('general.asset_details') }}
                    </h4>
                    <div class="box-tools pull-right">
                        <span id="asset-info-spinner" style="display:none;">
                            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-condensed" style="margin-bottom:0;">
                        <tbody>
                            <tr>
                                <th class="col-md-3 text-right">{{ trans('general.name') }}</th>
                                <td id="asset-info-name">—</td>
                            </tr>
                            <tr>
                                <th class="text-right">{{ trans('admin/hardware/form.tag') }}</th>
                                <td id="asset-info-tag">—</td>
                            </tr>
                            <tr>
                                <th class="text-right">{{ trans('general.serial') }}</th>
                                <td id="asset-info-serial">—</td>
                            </tr>
                            <tr>
                                <th class="text-right">{{ trans('general.status') }}</th>
                                <td id="asset-info-status">—</td>
                            </tr>
                            <tr>
                                <th class="text-right">{{ trans('general.model') }}</th>
                                <td id="asset-info-model">—</td>
                            </tr>
                            <tr>
                                <th class="text-right">{{ trans('general.location') }}</th>
                                <td id="asset-info-location">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Error state when asset fetch fails --}}
        <div id="asset-info-error" style="display: none;">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                {{ trans('general.asset_info_load_error') }}
            </div>
        </div>

    </div>{{-- /.col-md-8 --}}
</div>{{-- /.row --}}

@stop

@section('moar_scripts')
<script>
$(document).ready(function () {

    var apiBase = "{{ config('app.url') }}/api/v1/hardware";
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Triggered when the Select2 AJAX dropdown fires a selection change
    $('#asset_select').on('select2:select select2:clear', function () {
        var assetId = $(this).val();

        $('#asset-info-error').hide();

        if (!assetId) {
            $('#asset-info-card').fadeOut(150);
            clearAssetInfo();
            return;
        }

        showSpinner(true);
        $('#asset-info-card').fadeIn(200);
        clearAssetInfo();

        $.ajax({
            type: 'GET',
            url: apiBase + '/' + assetId,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function (data) {
                showSpinner(false);

                $('#asset-info-name').text(data.name || '—');
                $('#asset-info-tag').text(data.asset_tag || '—');
                $('#asset-info-serial').text(data.serial || '—');

                // Status badge
                if (data.status_label && data.status_label.name) {
                    var statusClass = 'label-default';
                    var sl = data.status_label.status_type;
                    if (sl === 'deployable')     statusClass = 'label-success';
                    else if (sl === 'archived')  statusClass = 'label-default';
                    else if (sl === 'undeployable') statusClass = 'label-danger';
                    else if (sl === 'pending')   statusClass = 'label-warning';

                    $('#asset-info-status').html(
                        '<span class="label ' + statusClass + '">' +
                        $('<span>').text(data.status_label.name).html() +
                        '</span>'
                    );
                } else {
                    $('#asset-info-status').text('—');
                }

                $('#asset-info-model').text(
                    (data.model && data.model.name) ? data.model.name : '—'
                );
                $('#asset-info-location').text(
                    (data.location && data.location.name) ? data.location.name : '—'
                );
            },
            error: function () {
                showSpinner(false);
                $('#asset-info-card').hide();
                $('#asset-info-error').fadeIn(200);
                clearAssetInfo();
            }
        });
    });

    function clearAssetInfo() {
        $('#asset-info-name, #asset-info-tag, #asset-info-serial, ' +
          '#asset-info-status, #asset-info-model, #asset-info-location').text('—');
    }

    function showSpinner(show) {
        show ? $('#asset-info-spinner').show() : $('#asset-info-spinner').hide();
    }

    // Re-trigger on page load if old() value is present (validation re-display)
    @if (old('asset_id'))
    $('#asset_select').trigger('select2:select');
    @endif

});
</script>
@stop
