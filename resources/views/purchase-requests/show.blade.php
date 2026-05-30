@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.purchase_request') }}: {{ $pr->title }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        {{-- Procurement lifecycle pipeline --}}
        @php
            $pipelineStep   = 1;
            $pipelineFailed = false;
            if ($pr->status === 'rejected') {
                $pipelineStep   = 2;
                $pipelineFailed = true;
            } elseif ($pr->status === 'approved') {
                if (! $pr->purchaseOrder) {
                    $pipelineStep = 3;
                } else {
                    $quotCount = $pr->purchaseOrder->quotations()->count();
                    $awarded   = $pr->purchaseOrder->quotations()->where('is_awarded', true)->exists();
                    $poStatus  = $pr->purchaseOrder->status;
                    if ($quotCount < 3) {
                        $pipelineStep = 4;
                    } elseif (! $awarded) {
                        $pipelineStep = 5;
                    } elseif ($poStatus === 'closed') {
                        $pipelineStep = 7;
                    } else {
                        $pipelineStep = 6;
                    }
                }
            }
        @endphp
        @include('partials.procurement-pipeline', ['pipelineStep' => $pipelineStep, 'pipelineFailed' => $pipelineFailed])

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check" aria-hidden="true"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times" aria-hidden="true"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Main details box ─────────────────────────────────────────── --}}
        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    {{ $pr->title }}
                </h2>
                <div class="box-tools pull-right">
                    <a href="{{ route('purchase-requests.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        {{ trans('general.back') }}
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-condensed">
                    <tbody>

                        <tr>
                            <th class="col-md-3 text-right">{{ trans('general.id') }}</th>
                            <td>{{ $pr->id }}</td>
                        </tr>

                        <tr>
                            <th class="col-md-3 text-right">{{ trans('general.title') }}</th>
                            <td>{{ $pr->title }}</td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('admin/users/table.requesting_user') }}</th>
                            <td>
                                @if ($pr->requestedBy)
                                    {{ $pr->requestedBy->full_name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.department') }}</th>
                            <td>{{ $pr->department ?? '—' }}</td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.status') }}</th>
                            <td>
                                @if ($pr->status === 'approved')
                                    <span class="label label-success">{{ trans('general.approved') }}</span>
                                @elseif ($pr->status === 'rejected')
                                    <span class="label label-danger">{{ trans('general.rejected') }}</span>
                                @else
                                    <span class="label label-warning">{{ trans('general.pending') }}</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.justification') }}</th>
                            <td>{{ $pr->justification ?? '—' }}</td>
                        </tr>

                        @if ($pr->notes)
                        <tr>
                            <th class="text-right">{{ trans('general.notes') }}</th>
                            <td>{{ $pr->notes }}</td>
                        </tr>
                        @endif

                        @if ($pr->status === 'rejected' && $pr->approvedBy)
                        <tr>
                            <th class="text-right">{{ trans('general.rejected_by') }}</th>
                            <td>{{ $pr->approvedBy->full_name }}</td>
                        </tr>
                        @elseif ($pr->status === 'approved' && $pr->approvedBy)
                        <tr>
                            <th class="text-right">{{ trans('general.approved_by') }}</th>
                            <td>
                                {{ $pr->approvedBy->full_name }}
                                @if ($pr->approved_at)
                                    <span class="text-muted" style="margin-left: 6px;">
                                        {{ $pr->approved_at->diffForHumans() }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <th class="text-right">{{ trans('general.created_at') }}</th>
                            <td title="{{ $pr->created_at }}">
                                {{ $pr->created_at->diffForHumans() }}
                            </td>
                        </tr>

                        <tr>
                            <th class="text-right">{{ trans('general.updated_at') }}</th>
                            <td title="{{ $pr->updated_at }}">
                                {{ $pr->updated_at->diffForHumans() }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>{{-- /.box-body --}}

            {{-- Approve / Reject actions while pending --}}
            @if ($pr->status === 'pending')
            <div class="box-footer">
                <form method="POST"
                      action="{{ route('purchase-requests.approve', $pr->id) }}"
                      class="d-inline"
                      onsubmit="return confirm({{ json_encode(trans('general.confirm_approve')) }})">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check" aria-hidden="true"></i>
                        {{ trans('general.approve') }}
                    </button>
                </form>

                <button type="button"
                        class="btn btn-danger btn-sm"
                        data-toggle="modal"
                        data-target="#rejectModal"
                        style="margin-left: 6px;">
                    <i class="fas fa-times" aria-hidden="true"></i>
                    {{ trans('general.reject') }}
                </button>
            </div>
            @endif

        </div>{{-- /.box --}}

        {{-- ── Purchase Order section (approved only) ───────────────────── --}}
        @if ($pr->status === 'approved')

            @if ($pr->purchaseOrder)

                {{-- PO already exists — show its details --}}
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fas fa-file-invoice" aria-hidden="true"></i>
                            {{ trans('general.purchase_order') }}
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <th class="col-md-3 text-right">{{ trans('general.po_number') }}</th>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $pr->purchaseOrder->id) }}">
                                            <strong>{{ $pr->purchaseOrder->po_number }}</strong>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-right">{{ trans('general.status') }}</th>
                                    <td>
                                        @php $poStatus = $pr->purchaseOrder->status @endphp
                                        @if ($poStatus === 'closed')
                                            <span class="label label-default">{{ trans('general.closed') }}</span>
                                        @elseif ($poStatus === 'received')
                                            <span class="label label-success">{{ trans('general.received') }}</span>
                                        @elseif ($poStatus === 'sent')
                                            <span class="label label-info">{{ trans('general.sent') }}</span>
                                        @else
                                            <span class="label label-warning">{{ trans('general.draft') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-right">{{ trans('general.created_at') }}</th>
                                    <td title="{{ $pr->purchaseOrder->created_at }}">
                                        {{ $pr->purchaseOrder->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            @else

                {{-- No PO yet — show the create button --}}
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fas fa-file-invoice" aria-hidden="true"></i>
                            {{ trans('general.purchase_order') }}
                        </h3>
                    </div>
                    <div class="box-body">
                        <p class="text-muted">
                            {{ trans('general.no_purchase_order_yet') }}
                        </p>
                    </div>
                    <div class="box-footer">
                        <form method="POST"
                              action="{{ route('purchase-orders.store', $pr->id) }}"
                              onsubmit="return confirm({{ json_encode(trans('general.confirm_create_po')) }})">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                                {{ trans('general.create_purchase_order') }}
                            </button>
                        </form>
                    </div>
                </div>

            @endif

        @endif

    </div>{{-- /.col-md-8 --}}
</div>{{-- /.row --}}


{{-- ── Reject modal ─────────────────────────────────────────────────────── --}}
@if ($pr->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <form method="POST" action="{{ route('purchase-requests.reject', $pr->id) }}">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('button.cancel') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="rejectModalLabel">
                        {{ trans('general.reject') }}: {{ $pr->title }}
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_note">
                            {{ trans('general.rejection_reason') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <textarea class="form-control"
                                  name="rejection_note"
                                  id="rejection_note"
                                  rows="3"
                                  required
                                  placeholder="{{ trans('general.rejection_reason') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        {{ trans('button.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times" aria-hidden="true"></i>
                        {{ trans('general.reject') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@stop
