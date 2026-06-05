@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.maintenance_requests') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="box box-default">

            <div class="box-header with-border">
                <h2 class="box-title">{{ trans('general.maintenance_requests') }}</h2>
                <div class="box-tools">
                    <a href="{{ route('maintenance-requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        {{ trans('general.create_maintenance_request') }}
                    </a>
                </div>
            </div>

            <div class="box-body">

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
                    <table class="table table-striped snipe-table">
                        <thead>
                            <tr>
                                <th class="col-md-1">{{ trans('general.id') }}</th>
                                <th class="col-md-3">{{ trans('general.asset') }}</th>
                                <th class="col-md-2">{{ trans('admin/users/table.requesting_user') }}</th>
                                <th class="col-md-1">{{ trans('general.maintenance_type') }}</th>
                                <th class="col-md-1">{{ trans('general.status') }}</th>
                                <th class="col-md-1">{{ trans('general.decommission_needed') }}</th>
                                <th class="col-md-2">{{ trans('general.date') }}</th>
                                <th class="col-md-1 text-right">{{ trans('button.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($maintenanceRequests as $mr)
                                <tr>
                                    <td>{{ $mr->id }}</td>

                                    <td>
                                        @if ($mr->asset)
                                            <a href="{{ route('hardware.show', $mr->asset_id) }}">
                                                {{ $mr->asset->asset_tag }}
                                                @if ($mr->asset->name)
                                                    <br><small class="text-muted">{{ $mr->asset->name }}</small>
                                                @endif
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($mr->requestedBy)
                                            {{ $mr->requestedBy->present()->fullName }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="label label-default">{{ ucfirst(str_replace('_', ' ', $mr->type)) }}</span>
                                    </td>

                                    <td>
                                        @if ($mr->status === 'open')
                                            <span class="label label-warning">{{ trans('general.open') }}</span>
                                        @elseif ($mr->status === 'in_progress')
                                            <span class="label label-info">{{ trans('general.in_progress') }}</span>
                                        @elseif ($mr->status === 'resolved')
                                            <span class="label label-success">{{ trans('general.resolved') }}</span>
                                        @elseif ($mr->status === 'decommissioned')
                                            <span class="label label-danger">{{ trans('general.decommissioned') }}</span>
                                        @else
                                            <span class="label label-default">{{ $mr->status }}</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($mr->decommission_needed)
                                            <i class="fas fa-exclamation-triangle text-danger" aria-label="{{ trans('general.yes') }}"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span title="{{ $mr->created_at }}">
                                            {{ $mr->created_at->diffForHumans() }}
                                        </span>
                                    </td>

                                    <td class="text-right">
                                        <a href="{{ route('maintenance-requests.show', $mr->id) }}"
                                           class="btn btn-info btn-sm"
                                           data-tooltip="true"
                                           title="{{ trans('button.view') }}">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('button.view') }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        {{ trans('general.no_maintenance_requests') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($maintenanceRequests->hasPages())
                    <div class="text-center">
                        {{ $maintenanceRequests->links() }}
                    </div>
                @endif

            </div>{{-- /.box-body --}}
        </div>{{-- /.box --}}

    </div>
</div>

@stop
