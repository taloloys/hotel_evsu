@extends('layouts.app')

@section('title', 'Billing Management')
@section('pageTitle', 'Billing & Invoices')
@section('pageSubtitle', 'Manage guest bills, invoices, and payment status')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-1 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Sales ({{ $tab === 'pos' ? 'Coffee Shop' : 'Front Desk' }})</div>
                <div class="fw-bold fs-4" style="color: #504538;">₱{{ number_format($totalSales, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-1 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Cash Sales ({{ $tab === 'pos' ? 'Coffee Shop' : 'Front Desk' }})</div>
                <div class="fw-bold fs-4 text-success">₱{{ number_format($cashSales, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-1 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Credit Card Sales ({{ $tab === 'pos' ? 'Coffee Shop' : 'Front Desk' }})</div>
                <div class="fw-bold fs-4" style="color: #334c42;">₱{{ number_format($creditSales, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-1 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Outstanding Balance ({{ $tab === 'pos' ? 'POS Open Tabs' : 'Guest Folios' }})</div>
                <div class="fw-bold fs-4 text-danger">₱{{ number_format($unpaidBalance, 2) }}</div>
            </div>
        </div>
    </div>

</div>

<!-- TABS -->
<ul class="nav nav-pills gap-2 mb-4 coffeeshop-nav-pills">
    <li class="nav-item">
        <a class="nav-link rounded-pill {{ $tab === 'front_desk' ? 'active' : '' }}" href="{{ route('accounting.billing', ['tab' => 'front_desk']) }}">Front Desk Bills</a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill {{ $tab === 'pos' ? 'active' : '' }}" href="{{ route('accounting.billing', ['tab' => 'pos']) }}">Coffee Shop POS Bills</a>
    </li>
</ul>

<!-- ACTION BAR -->
<form action="{{ route('accounting.billing') }}" method="GET" class="card border-1 shadow-sm rounded-4 mb-3" id="billingFilterForm">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <div class="fw-bold" style="color: #504538;">Invoices</div>
            <small class="text-muted">All {{ $tab === 'pos' ? 'Coffee Shop' : 'Front Desk' }} billing records</small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

            {{-- Search (live) --}}
            <div style="width: 280px; max-width: 100%;">
                <div class="input-group shadow-sm" style="border: 1px solid #c2a889; border-radius: 0.5rem; overflow: hidden; height: 45px; background-color: #ffffff;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="billingSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1rem;"
                        placeholder="Search invoice no. or guest..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            {{-- Filter Dropdown --}}
            <div class="dropdown">
                <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem;">
                    <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                    <span class="fw-semibold">Filter</span>
                    @if($dateRange !== 'today' || $statusFilter !== 'ALL')
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 290px; border-radius: 0.75rem; z-index: 1055;">

                    <label class="form-label small mb-1 fw-semibold text-muted">Date Range</label>
                    <select name="date_range" id="dateRangeSelect" class="form-select mb-2 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ $dateRange === 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $dateRange === 'monthly' ? 'selected' : '' }}>This Month</option>
                        <option value="yearly" {{ $dateRange === 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $dateRange === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="specific" {{ $dateRange === 'specific' ? 'selected' : '' }}>Specific Date</option>
                    </select>

                    <label class="form-label small mb-1 fw-semibold text-muted">Specific Date</label>
                    <input type="date" name="date" class="form-control mb-3 shadow-none"
                           style="height:38px; border-radius:0.5rem; border: 1px solid #827567;"
                           value="{{ $date }}"
                           onchange="document.getElementById('dateRangeSelect').value = 'specific';">

                    <label class="form-label small mb-1 fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                        <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>All Status</option>
                        <option value="PAID" {{ $statusFilter === 'PAID' ? 'selected' : '' }}>Paid</option>
                        <option value="UNPAID" {{ $statusFilter === 'UNPAID' ? 'selected' : '' }}>Unpaid / Pending</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                        <a href="{{ route('accounting.billing', ['tab' => $tab]) }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<!-- TABLE -->
<div class="card border-1 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                <tr class="small fw-bold" style="color: #1a1a1a;">
                    @if($tab === 'pos')
                        <th class="ps-3">ORDER NO</th>
                        <th>CUSTOMER / GUEST</th>
                        <th>ROOM</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th class="text-end">TOTAL AMOUNT</th>
                        <th class="text-center pe-3">ACTION</th>
                    @else
                        <th class="ps-3">INVOICE / FOLIO NO</th>
                        <th>GUEST</th>
                        <th>ROOM</th>
                        <th>ARRIVAL DATE</th>
                        <th>STATUS</th>
                        <th class="text-end">TOTAL CHARGES</th>
                        <th class="text-end">BALANCE</th>
                        <th class="text-center pe-3">ACTION</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if($tab === 'pos')
                    @forelse($posOrders as $order)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="ps-3 fw-bold" style="color: #1a1a1a;">{{ $order->order_number }}</td>
                            <td class="fw-semibold" style="color: #1a1a1a;">{{ $order->customer_name }}</td>
                            <td style="color: #262626;">{{ $order->room_number ? 'Room ' . $order->room_number : 'Walk-in' }}</td>
                            <td style="color: #262626;">{{ $order->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if(in_array($order->status, ['closed', 'refunded']))
                                    <span class="badge bg-success-subtle text-success fw-semibold">{{ ucfirst($order->status) }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning fw-semibold">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold" style="color: #1a1a1a;">₱{{ number_format($order->total, 2) }}</td>
                            <td class="text-center pe-3">
                                <a href="{{ route('coffeeshop.orders.show', $order->order_id) }}" class="btn btn-sm px-3 fw-semibold shadow-sm" style="border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;">
                                    <i class="fa-solid fa-eye me-1"></i> View Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4" style="color: #6b7280;">No POS billing records found for this date.</td>
                        </tr>
                    @endforelse
                @else
                    @forelse($folios as $folio)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="ps-3 fw-bold" style="color: #1a1a1a;">{{ $folio->folio_number }}</td>
                            <td class="fw-semibold" style="color: #1a1a1a;">{{ $folio->guest_name }}</td>
                            <td style="color: #262626;">{{ str_starts_with($folio->room_number, 'Room') ? $folio->room_number : 'Room ' . $folio->room_number }}</td>
                            <td style="color: #262626;">{{ $folio->date }}</td>
                            <td>
                                @if($folio->display_status === 'Paid')
                                    <span class="badge bg-success-subtle text-success fw-semibold">Paid</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger fw-semibold">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold" style="color: #1a1a1a;">₱{{ number_format($folio->total_amount, 2) }}</td>
                            <td class="text-end fw-bold {{ $folio->balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₱{{ number_format($folio->balance, 2) }}
                            </td>
                            <td class="text-center pe-3">
                                <a href="{{ route('accounting.billing.show', $folio->folio_id) }}" class="btn btn-sm px-3 fw-semibold shadow-sm" style="border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;">
                                    <i class="fa-solid fa-eye me-1"></i> View Folio
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No front desk billing records found for this date.</td>
                        </tr>
                    @endforelse
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-end mb-5">
    @if($tab === 'pos')
        {{ $posOrders->links() }}
    @else
        {{ $folios->links() }}
    @endif
</div>

@push('scripts')
<script>
(function () {
    const input = document.getElementById('billingSearch');
    if (!input) return;
    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            input.closest('form').requestSubmit();
        }, 400);
    });
})();
</script>
@endpush

@endsection