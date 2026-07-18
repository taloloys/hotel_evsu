@extends('layouts.app')

@section('title', 'Billing Management')
@section('pageTitle', 'Billing & Invoices')
@section('pageSubtitle', 'Manage guest bills, invoices, and payment status')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Total Sales</div>
                <div class="fw-bold fs-3 text-primary">₱{{ number_format($totalSales, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Cash Sales</div>
                <div class="fw-bold fs-3 text-success">₱{{ number_format($cashSales, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Credit Card Sales</div>
                <div class="fw-bold fs-3 text-info">₱{{ number_format($creditSales, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Outstanding Balance</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($unpaidBalance, 2) }}</div>
            </div>
        </div>
    </div>

</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'front_desk' ? 'active' : '' }}" href="{{ route('accounting.billing', ['tab' => 'front_desk']) }}">Front Desk Bills</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'pos' ? 'active' : '' }}" href="{{ route('accounting.billing', ['tab' => 'pos']) }}">Coffee Shop POS Bills</a>
    </li>
</ul>

<!-- ACTION BAR -->
<form action="{{ route('accounting.billing') }}" method="GET" class="card border-1 shadow-sm mb-3" id="billingFilterForm">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Invoices</div>
            <small class="text-muted">All {{ $tab === 'pos' ? 'Coffee Shop' : 'Front Desk' }} billing records</small>
        </div>

        <div class="d-flex align-items-center gap-2">

            {{-- Search (live) --}}
            <div style="width: 280px; border: 1px solid #ced4da; border-radius: .375rem; height: 45px;">
                <div class="input-group" style="height: 100%;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="billingSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1.05rem;"
                        placeholder="Search invoice no. or guest..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            {{-- Filter Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 position-relative"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                    <i class="fa-solid fa-filter fs-5"></i>
                    <span>Filter</span>
                    @if($dateRange !== 'today' || $statusFilter !== 'ALL')
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 290px; border-radius: 8px; z-index: 1055;">

                    <label class="form-label small mb-1 fw-semibold text-muted">Date Range</label>
                    <select name="date_range" id="dateRangeSelect" class="form-select mb-2 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ $dateRange === 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $dateRange === 'monthly' ? 'selected' : '' }}>This Month</option>
                        <option value="yearly" {{ $dateRange === 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $dateRange === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="specific" {{ $dateRange === 'specific' ? 'selected' : '' }}>Specific Date</option>
                    </select>

                    <label class="form-label small mb-1 fw-semibold text-muted">Specific Date</label>
                    <input type="date" name="date" class="form-control mb-3 shadow-none"
                           style="height:38px; border-radius:4px; border: 1px solid black;"
                           value="{{ $date }}"
                           onchange="document.getElementById('dateRangeSelect').value = 'specific';">

                    <label class="form-label small mb-1 fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>All Status</option>
                        <option value="PAID" {{ $statusFilter === 'PAID' ? 'selected' : '' }}>Paid</option>
                        <option value="UNPAID" {{ $statusFilter === 'UNPAID' ? 'selected' : '' }}>Unpaid / Pending</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                        <a href="{{ route('accounting.billing', ['tab' => $tab]) }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<!-- TABLE -->
<div class="card border-1 shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @if($tab === 'pos')
                        <th>Order No</th>
                        <th>Customer / Guest</th>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Action</th>
                    @else
                        <th>Invoice / Folio No</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Arrival Date</th>
                        <th>Status</th>
                        <th class="text-end">Total Charges</th>
                        <th class="text-end">Balance</th>
                        <th class="text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if($tab === 'pos')
                    @forelse($posOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->room_number ? 'Room ' . $order->room_number : 'Walk-in' }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if(in_array($order->status, ['closed', 'refunded']))
                                    <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">₱{{ number_format($order->total, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('coffeeshop.orders.show', $order->order_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> View Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No POS billing records found for this date.</td>
                        </tr>
                    @endforelse
                @else
                    @forelse($folios as $folio)
                        <tr>
                            <td>{{ $folio->folio_number }}</td>
                            <td>{{ $folio->guest_name }}</td>
                            <td>{{ str_starts_with($folio->room_number, 'Room') ? $folio->room_number : 'Room ' . $folio->room_number }}</td>
                            <td>{{ $folio->date }}</td>
                            <td>
                                @if($folio->display_status === 'Paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">₱{{ number_format($folio->total_amount, 2) }}</td>
                            <td class="text-end fw-bold {{ $folio->balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₱{{ number_format($folio->balance, 2) }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('accounting.billing.show', $folio->folio_id) }}" class="btn btn-sm btn-outline-primary">
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