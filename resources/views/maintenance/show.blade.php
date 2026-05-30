@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.maintenance_request') }} #{{ $mr->id }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        {{-- Flash messages --}}
        @foreach (['success', 'error', 'warning'] as $level)
            @if (session($level))
                <div class="alert alert-{{ $level === 'error' ? 'danger' : $level }} fade in">
                    <i class="fas fa-{{ $level === 'success' ? 'check' : ($level === 'warning' ? 'exclamation-triangle' : 'times') }}" aria-hidden="true"></i>
                    {{ session($level) }}
                </div>
            @endif
        @endforeach

        {{-- ── Main details box ─────────────────────────────────────────── --}}
        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-tools" aria-hidden="true"></i>
                    {{ trans('general.maintenance_request') }} #{{ $mr->id }}
                </h2>
                <div class="box-tools pull-right">
                    <a href="{{ url()->previous() }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        {{ trans('general.back') }}
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-condensed">
                    <tbody>

                        <tr>
                            <th class="col-md-3 text-right">{{ trans('general.asset') }}</th>
                            <td>
                                @if ($mr->asset)
                                    <strong>{{ $mr->asset->present()->fullName }}</strong>
                                    <span class="text-muted" style="margin-left: 6px;">
                                        {{ $mr->asset->asset_tag }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.requested_by') }}</th>
                            <td>
                                {{ $mr->requestedBy?->present()->fullName() ?? '—' }}
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.type') }}</th>
                            <td>
                                @php
                                    $typeLabels = [
                                        'repair'          => ['label-warning', trans('general.maintenance_type_repair')],
                                        'inspection'      => ['label-info',    trans('general.maintenance_type_inspection')],
                                        'scheduled_audit' => ['label-primary', trans('general.maintenance_type_scheduled_audit')],
                                    ];
                                    [$typeClass, $typeText] = $typeLabels[$mr->type] ?? ['label-default', $mr->type];
                                @endphp
                                <span class="label {{ $typeClass }}">{{ $typeText }}</span>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.status') }}</th>
                            <td>
                                @php
                                    $statusMap = [
                                        'open'           => ['label-warning', trans('general.open')],
                                        'in_progress'    => ['label-info',    trans('general.in_progress')],
                                        'resolved'       => ['label-success', trans('general.resolved')],
                                        'decommissioned' => ['label-danger',  trans('general.decommissioned')],
                                    ];
                                    [$statusClass, $statusText] = $statusMap[$mr->status] ?? ['label-default', $mr->status];
                                @endphp
                                <span class="label {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.notes') }}</th>
                            <td>{{ $mr->notes ?? '—' }}</td>
                        </tr>

                        @if ($mr->resolution_notes)
                        <tr>
                            <th class="text-right">{{ trans('general.resolution_notes') }}</th>
                            <td>{{ $mr->resolution_notes }}</td>
                        </tr>
                        @endif

                        <tr>
                            <th class="text-right">{{ trans('general.flag_for_decommission') }}</th>
                            <td>
                                @if ($mr->decommission_needed)
                                    <span class="label label-danger">
                                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ trans('general.yes') }}
                                    </span>
                                @else
                                    <span class="text-muted">{{ trans('general.no') }}</span>
                                @endif
                            </td>
                        </tr>

                        @if ($mr->resolved_at)
                        <tr>
                            <th class="text-right">{{ trans('general.resolved_at') }}</th>
                            <td title="{{ $mr->resolved_at }}">
                                {{ $mr->resolved_at->diffForHumans() }}
                            </td>
                        </tr>
                        @endif

                        @if ($mr->disposal_certificate_path)
                        <tr>
                            <th class="text-right">{{ trans('general.disposal_certificate') }}</th>
                            <td>
                                <span class="label label-success">
                                    <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                    {{ trans('general.uploaded') }}
                                </span>
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <th class="text-right">{{ trans('general.created_at') }}</th>
                            <td title="{{ $mr->created_at }}">{{ $mr->created_at->diffForHumans() }}</td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.updated_at') }}</th>
                            <td title="{{ $mr->updated_at }}">{{ $mr->updated_at->diffForHumans() }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>{{-- /.box --}}


        {{-- ═══════════════════════════════════════════════════════════════
             DECOMMISSION PATH — decommission_needed = true
        ═══════════════════════════════════════════════════════════════════ --}}
        @if ($mr->decommission_needed)

            <div class="box" style="border-top: 3px solid #dd4b39;">{{-- red accent --}}

                <div class="box-header with-border" style="background: #fff5f5;">
                    <h3 class="box-title" style="color: #dd4b39;">
                        <i class="fas fa-skull-crossbones" aria-hidden="true"></i>
                        {{ trans('general.decommission_path') }}
                    </h3>
                </div>

                <div class="box-body">

                    {{-- Step 1: Initiate decommissioning (status transition) --}}
                    @if ($mr->status !== 'decommissioned')
                    <div class="row" style="margin-bottom: 16px;">
                        <div class="col-md-9 col-md-offset-3">
                            <form method="POST"
                                  action="{{ route('maintenance-requests.decommission', $mr->id) }}"
                                  onsubmit="return confirm('{{ trans('general.confirm_decommission') }}')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban" aria-hidden="true"></i>
                                    {{ trans('general.initiate_decommissioning') }}
                                </button>
                                <span class="help-block" style="display:inline-block; margin-left:10px; font-size:12px;">
                                    {{ trans('general.initiate_decommissioning_help') }}
                                </span>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- Step 2: Upload disposal certificate --}}
                    <div class="row" style="margin-bottom: 16px;">
                        <div class="col-md-12">
                            <form class="form-horizontal"
                                  method="POST"
                                  action="{{ route('maintenance-requests.disposal-certificate', $mr->id) }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="form-group {{ $errors->has('certificate') ? 'has-error' : '' }}">
                                    <label class="col-md-3 control-label">
                                        {{ trans('general.disposal_certificate') }}
                                    </label>
                                    <div class="col-md-6">
                                        <label class="btn btn-sm btn-default" for="certificate" style="cursor:pointer;">
                                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                                            {{ trans('button.select_file') }}
                                            <input type="file"
                                                   name="certificate"
                                                   id="certificate"
                                                   accept="application/pdf"
                                                   style="display:none;"
                                                   onchange="document.getElementById('cert-filename').textContent = this.files[0] ? this.files[0].name : ''">
                                        </label>
                                        <span id="cert-filename" class="label label-default" style="font-size:12px; margin-left:4px;"></span>
                                        <p class="help-block">{{ trans('general.pdf_only_max', ['size' => '10MB']) }}</p>
                                        {!! $errors->first('certificate', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-upload" aria-hidden="true"></i>
                                            {{ trans('general.upload_disposal_certificate') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Step 3: Sync OrangeHRM offboarding --}}
                    <div class="row">
                        <div class="col-md-9 col-md-offset-3">
                            <form method="POST"
                                  action="{{ route('maintenance-requests.sync-orangehrm', $mr->id) }}"
                                  onsubmit="return confirm('{{ trans('general.confirm_sync_orangehrm') }}')">
                                @csrf
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-sync-alt" aria-hidden="true"></i>
                                    {{ trans('general.sync_orangehrm_offboarding') }}
                                </button>
                                <span class="help-block" style="display:inline-block; margin-left:10px; font-size:12px;">
                                    {{ trans('general.sync_orangehrm_offboarding_help') }}
                                </span>
                            </form>
                        </div>
                    </div>

                </div>{{-- /.box-body --}}

            </div>{{-- /.box decommission --}}


        {{-- ═══════════════════════════════════════════════════════════════
             REPAIR PATH — decommission_needed = false
        ═══════════════════════════════════════════════════════════════════ --}}
        @else

            <div class="box" style="border-top: 3px solid #00a65a;">{{-- green accent --}}

                <div class="box-header with-border" style="background: #f5fff8;">
                    <h3 class="box-title" style="color: #00a65a;">
                        <i class="fas fa-wrench" aria-hidden="true"></i>
                        {{ trans('general.repair_path') }}
                    </h3>
                </div>

                <div class="box-body">

                    {{-- Resolution notes + mark resolved --}}
                    @if (! in_array($mr->status, ['resolved', 'decommissioned']))
                    <form class="form-horizontal"
                          method="POST"
                          action="{{ route('maintenance-requests.resolve', $mr->id) }}"
                          style="margin-bottom: 20px;">
                        @csrf
                        @method('PATCH')
                        <div class="form-group {{ $errors->has('resolution_notes') ? 'has-error' : '' }}">
                            <label for="resolution_notes" class="col-md-3 control-label">
                                {{ trans('general.resolution_notes') }}
                                <span class="required" aria-hidden="true">*</span>
                            </label>
                            <div class="col-md-7">
                                <textarea class="form-control"
                                          name="resolution_notes"
                                          id="resolution_notes"
                                          rows="4"
                                          placeholder="{{ trans('general.resolution_notes_placeholder') }}"
                                          required>{{ old('resolution_notes', $mr->resolution_notes) }}</textarea>
                                {!! $errors->first('resolution_notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                                    {{ trans('general.mark_as_resolved') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif

                    {{-- Return to active service --}}
                    @if (in_array($mr->status, ['open', 'in_progress', 'resolved']))
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <form method="POST"
                                      action="{{ route('maintenance-requests.return-to-service', $mr->id) }}"
                                      onsubmit="return confirm('{{ trans('general.confirm_return_to_service') }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-undo-alt" aria-hidden="true"></i>
                                        {{ trans('general.return_asset_to_active_service') }}
                                    </button>
                                    <span class="help-block" style="display:inline-block; margin-left:10px; font-size:12px;">
                                        {{ trans('general.return_to_service_help') }}
                                    </span>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (in_array($mr->status, ['resolved', 'decommissioned']))
                    <div class="alert alert-success" style="margin-bottom:0;">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        {{ trans('general.maintenance_request_closed') }}
                    </div>
                    @endif

                </div>{{-- /.box-body --}}

            </div>{{-- /.box repair --}}

        @endif

    </div>{{-- /.col-md-8 --}}
</div>{{-- /.row --}}

@stop
