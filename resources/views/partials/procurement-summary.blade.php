{{--
    Procurement Summary Stat Cards
    Include this partial wherever the procurement overview is needed:
        @include('partials.procurement-summary')
    Requires: PurchaseRequest, PurchaseOrder, SupplierQuotation models.
--}}
@php
    use App\Models\PurchaseOrder;
    use App\Models\PurchaseRequest;
    use App\Models\SupplierQuotation;

    $pendingPrCount   = PurchaseRequest::pending()->count();

    $approvedThisMonth = PurchaseRequest::approved()
        ->whereMonth('approved_at', now()->month)
        ->whereYear('approved_at', now()->year)
        ->count();

    $openPoCount = PurchaseOrder::open()->count();

    // POs where fewer than 3 quotations have been submitted
    $awaitingQuotesCount = PurchaseOrder::open()
        ->withCount('quotations')
        ->get()
        ->filter(fn ($po) => $po->quotations_count < 3)
        ->count();
@endphp

<div class="row">

    {{-- Pending Purchase Requests (yellow) --}}
    <div class="col-lg-3 col-xs-6">
        <a href="{{ route('purchase-requests.index', ['status' => 'pending']) }}" aria-label="{{ trans('general.pending_purchase_requests') }}">
            <div class="dashboard small-box bg-yellow">
                <div class="inner">
                    <h3>{{ number_format($pendingPrCount) }}</h3>
                    <p>{{ trans('general.pending_purchase_requests') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
            </div>
        </a>
    </div>

    {{-- Approved PRs this month (green) --}}
    <div class="col-lg-3 col-xs-6">
        <a href="{{ route('purchase-requests.index', ['status' => 'approved']) }}" aria-label="{{ trans('general.approved_this_month') }}">
            <div class="dashboard small-box bg-green">
                <div class="inner">
                    <h3>{{ number_format($approvedThisMonth) }}</h3>
                    <p>{{ trans('general.approved_this_month') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
            </div>
        </a>
    </div>

    {{-- Open Purchase Orders (blue/aqua) --}}
    <div class="col-lg-3 col-xs-6">
        <a href="{{ route('purchase-requests.index') }}" aria-label="{{ trans('general.open_purchase_orders') }}">
            <div class="dashboard small-box bg-aqua">
                <div class="inner">
                    <h3>{{ number_format($openPoCount) }}</h3>
                    <p>{{ trans('general.open_purchase_orders') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <span class="small-box-footer">
                    {{ trans('general.view_all') }}
                    <x-icon type="arrow-circle-right" />
                </span>
            </div>
        </a>
    </div>

    {{-- POs awaiting minimum quotations (orange) --}}
    <div class="col-lg-3 col-xs-6">
        <a href="{{ route('purchase-requests.index') }}" aria-label="{{ trans('general.awaiting_quotations') }}">
            <div class="dashboard small-box bg-orange">
                <div class="inner">
                    <h3>{{ number_format($awaitingQuotesCount) }}</h3>
                    <p>{{ trans('general.awaiting_quotations') }}</p>
                </div>
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <span class="small-box-footer">
                    {{ trans('general.minimum_3_quotes_required') }}
                    <x-icon type="arrow-circle-right" />
                </span>
            </div>
        </a>
    </div>

</div>{{-- /.row --}}
