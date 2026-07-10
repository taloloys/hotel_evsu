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
<form action="{{ route('accounting.billing') }}" method="GET" class="card border-1 shadow-sm mb-3">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Invoices</div>
            <small class="text-muted">All {{ $tab === 'pos' ? 'Coffee Shop' : 'Front Desk' }} billing records</small>
        </div>

        <div class="d-flex align-items-center gap-3">

            {{-- Date Range Dropdown --}}
            <div style="width: 140px; border: 1px solid #ced4da; border-radius: .25rem;">
                <select
                    name="date_range"
                    id="dateRangeSelect"
                    class="form-select border-0"
                    style="height: 38px;">
                    <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $dateRange === 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $dateRange === 'monthly' ? 'selected' : '' }}>This Month</option>
                    <option value="yearly" {{ $dateRange === 'yearly' ? 'selected' : '' }}>This Year</option>
                    <option value="all" {{ $dateRange === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="specific" {{ $dateRange === 'specific' ? 'selected' : '' }}>Specific Date:</option>
                </select>
            </div>

            {{-- Date Picker --}}
            <div style="width: 140px;">
                <input
                    type="date"
                    name="date"
                    class="form-control"
                    style="height: 38px;"
                    value="{{ $date }}"
                    onchange="document.getElementById('dateRangeSelect').value = 'specific';"
                    title="Pick a date to view its exact sales">
            </div>

            {{-- Filter --}}
            <div style="width: 140px; border: 1px solid #ced4da; border-radius: .25rem;">
                <select
                    name="status"
                    class="form-select border-0"
                    style="height: 38px;">

                    <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>
                        All Status
                    </option>
                    <option value="PAID" {{ $statusFilter === 'PAID' ? 'selected' : '' }}>
                        Paid
                    </option>
                    <option value="UNPAID" {{ $statusFilter === 'UNPAID' ? 'selected' : '' }}>
                        Unpaid / Pending
                    </option>
                </select>
            </div>

            {{-- Search --}}
            <div style="width: 250px; border: 1px solid #ced4da; border-radius: .25rem;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">
                        <i class="fa-solid fa-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        class="form-control border-0"
                        style="height: 38px;"
                        placeholder="Search invoice no. or guest..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            {{-- Search Button --}}
            <button type="submit" class="btn btn-primary px-4" style="height: 40px;">
                <i class="fa-solid fa-search me-1"></i>
                Search
            </button>
            <a href="{{ route('accounting.billing', ['tab' => $tab]) }}" class="btn btn-outline-secondary" style="height: 40px; display: flex; align-items: center;">Clear</a>

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

@endsection