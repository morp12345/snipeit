@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.purchase_requests') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-12">

        {{-- Collapsible lifecycle overview --}}
        <div class="box box-info collapsed-box">
            <div class="box-header with-border" data-widget="collapse" style="cursor:pointer;">
                <h3 class="box-title">
                    <i class="fas fa-stream" aria-hidden="true"></i>
                    {{ trans('general.pl_lifecycle') }}
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" aria-label="{{ trans('general.pl_lifecycle') }}">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="box-body" style="display:none;">
                @include('partials.procurement-pipeline', ['pipelineStep' => 0])
            </div>
        </div>

        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">{{ trans('general.purchase_requests') }}</h2>
                <div class="box-tools">
                    <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        {{ trans('general.create') }}
                    </a>
                </div>
            </div>

            <div class="box-body">

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

                <div class="table-responsive">
                    <table class="table table-striped snipe-table" id="purchaseRequestsTable">
                        <thead>
                            <tr>
                                <th class="col-md-1">{{ trans('general.id') }}</th>
                                <th class="col-md-3">{{ trans('general.title') }}</th>
                                <th class="col-md-2">{{ trans('admin/users/table.requesting_user') }}</th>
                                <th class="col-md-2">{{ trans('general.department') }}</th>
                                <th class="col-md-1">{{ trans('general.status') }}</th>
                                <th class="col-md-2">{{ trans('general.date') }}</th>
                                <th class="col-md-1 text-right">{{ trans('button.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchaseRequests as $pr)
                                <tr>

                                    {{-- ID --}}
                                    <td>{{ $pr->id }}</td>

                                    {{-- Title --}}
                                    <td>{{ $pr->title }}</td>

                                    {{-- Requested By --}}
                                    <td>
                                        @if ($pr->requestedBy)
                                            {{ $pr->requestedBy->full_name }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Department --}}
                                    <td>{{ $pr->department ?? '—' }}</td>

                                    {{-- Status badge --}}
                                    <td>
                                        @if ($pr->status === 'approved')
                                            <span class="label label-success">
                                                {{ trans('general.approved') }}
                                            </span>
                                        @elseif ($pr->status === 'rejected')
                                            <span class="label label-danger">
                                                {{ trans('general.rejected') }}
                                            </span>
                                        @else
                                            <span class="label label-warning">
                                                {{ trans('general.pending') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td>
                                        <span title="{{ $pr->created_at }}">
                                            {{ $pr->created_at->diffForHumans() }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-right">
                                        <div class="btn-group" role="group">

                                            {{-- View (always shown) --}}
                                            <a href="{{ route('purchase-requests.show', $pr->id) }}"
                                               class="btn btn-info btn-sm"
                                               data-tooltip="true"
                                               title="{{ trans('button.view') }}">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                                <span class="sr-only">{{ trans('button.view') }}</span>
                                            </a>

                                            {{-- Approve / Reject — only when pending --}}
                                            @if ($pr->status === 'pending')

                                                <form method="POST"
                                                      action="{{ route('purchase-requests.approve', $pr->id) }}"
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ trans('general.confirm_approve') }}')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-success btn-sm"
                                                            data-tooltip="true"
                                                            title="{{ trans('general.approve') }}">
                                                        <i class="fas fa-check" aria-hidden="true"></i>
                                                        <span class="sr-only">{{ trans('general.approve') }}</span>
                                                    </button>
                                                </form>

                                                <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#rejectModal-{{ $pr->id }}"
                                                        data-tooltip="true"
                                                        title="{{ trans('general.reject') }}">
                                                    <i class="fas fa-times" aria-hidden="true"></i>
                                                    <span class="sr-only">{{ trans('general.reject') }}</span>
                                                </button>

                                            @endif

                                        </div>
                                    </td>

                                </tr>

                                {{-- Reject modal for this row --}}
                                @if ($pr->status === 'pending')
                                <div class="modal fade" id="rejectModal-{{ $pr->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="rejectModalLabel-{{ $pr->id }}">
                                    <div class="modal-dialog modal-sm" role="document">
                                        <form method="POST"
                                              action="{{ route('purchase-requests.reject', $pr->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('button.cancel') }}">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <h4 class="modal-title" id="rejectModalLabel-{{ $pr->id }}">
                                                        {{ trans('general.reject') }}: {{ $pr->title }}
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="rejection_note_{{ $pr->id }}">
                                                            {{ trans('general.rejection_reason') }}
                                                            <span class="required" aria-hidden="true">*</span>
                                                        </label>
                                                        <textarea class="form-control"
                                                                  name="rejection_note"
                                                                  id="rejection_note_{{ $pr->id }}"
                                                                  rows="3"
                                                                  required
                                                                  placeholder="{{ trans('general.rejection_reason') }}"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default btn-sm"
                                                            data-dismiss="modal">
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

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        {{ trans('general.no_results') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>{{-- /.table-responsive --}}

                {{-- Pagination --}}
                @if ($purchaseRequests->hasPages())
                    <div class="text-center">
                        {{ $purchaseRequests->links() }}
                    </div>
                @endif

            </div>{{-- /.box-body --}}
        </div>{{-- /.box --}}

    </div>{{-- /.col-md-12 --}}
</div>{{-- /.row --}}

@stop
