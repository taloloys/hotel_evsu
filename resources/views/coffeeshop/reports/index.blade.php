@extends('layouts.app')

@section('title', 'Reports')
@section('pageTitle', 'POS Reports')
@section('pageSubtitle', 'Sales reports and payment breakdown')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Reporting overview</div>
                <div class="opacity-75 mt-1">Review sales totals and payment breakdowns with a clearer, calmer layout.</div>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row g-3">
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="text-muted small">Total Sales</div><h5 class="mb-0 text-brown">₱{{ number_format($summary['total_sales'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="text-muted small">Orders</div><h5 class="mb-0">{{ $summary['order_count'] }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="text-muted small">Cash</div><h5 class="mb-0">₱{{ number_format($summary['cash_total'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="text-muted small">GCash</div><h5 class="mb-0">₱{{ number_format($summary['gcash_total'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="text-muted small">Card</div><h5 class="mb-0">₱{{ number_format($summary['card_total'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="text-muted small">Room Charge</div><h5 class="mb-0">₱{{ number_format($summary['room_total'], 2) }}</h5></div></div>
    </div>

    {{-- FILTER TOOLBAR (outside any overflow:hidden container so dropdown floats freely) --}}
    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap justify-content-end" id="reportsFilterForm">

        <!-- FILTER DROPDOWN -->
        <div class="dropdown">
            <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 position-relative"
                    type="button"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                <i class="fa-solid fa-filter fs-5"></i>
                <span>Filter</span>
                @if($paymentMethod !== 'all' || $dateFrom !== Carbon\Carbon::today()->subDays(7)->toDateString() || $dateTo !== Carbon\Carbon::today()->toDateString())
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                <!-- Date From -->
                <label class="form-label small mb-1 fw-semibold text-muted">Date From</label>
                <input type="date"
                       name="date_from"
                       value="{{ $dateFrom }}"
                       class="form-control mb-3 shadow-none"
                       style="height:38px; border-radius:4px; border: 1px solid black;">

                <!-- Date To -->
                <label class="form-label small mb-1 fw-semibold text-muted">Date To</label>
                <input type="date"
                       name="date_to"
                       value="{{ $dateTo }}"
                       class="form-control mb-3 shadow-none"
                       style="height:38px; border-radius:4px; border: 1px solid black;">

                <!-- Payment Method -->
                <label class="form-label small mb-1 fw-semibold text-muted">Payment Method</label>
                <select name="payment_method" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                    <option value="all" @selected($paymentMethod === 'all')>All Payments</option>
                    <option value="cash" @selected($paymentMethod === 'cash')>Cash</option>
                    <option value="gcash" @selected($paymentMethod === 'gcash')>GCash</option>
                    <option value="card" @selected($paymentMethod === 'card')>Card</option>
                    <option value="room_charge" @selected($paymentMethod === 'room_charge')>Room Charge</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                    <a href="{{ route('coffeeshop.reports') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                </div>
            </div>
        </div>

        <!-- EXPORT CSV — passes current query string so export always respects the active date/payment filter -->
        <a href="{{ route('coffeeshop.reports.export', request()->query()) }}"
           class="btn btn-success d-flex align-items-center gap-1 px-3"
           style="height: 38px; border-radius: 4px;">
            <i class="fa-solid fa-file-export"></i>
            <span>Export CSV</span>
        </a>

    </form>

    {{-- RESULTS TABLE --}}
    <div class="coffeeshop-panel">
        <div class="table-responsive">

            <table class="table align-middle mb-0 coffeeshop-table">

                {{-- HEADER --}}
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th class="ps-3">Order</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th class="pe-3">Closed At</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody>

                @forelse($orders as $order)

                    <tr class="border-top">

                        {{-- ORDER --}}
                        <td class="ps-3">
                            <a href="{{ route('coffeeshop.orders.show', $order) }}"
                               class="fw-semibold text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>

                        {{-- CUSTOMER --}}
                        <td class="fw-semibold">
                            {{ $order->customer_name }}
                        </td>

                        {{-- ROOM --}}
                        <td>
                            <span class="text-muted">
                                {{ $order->room_number ?? '—' }}
                            </span>
                        </td>

                        {{-- PAYMENT --}}
                        <td>
                            @php
                                $payment = strtoupper(str_replace('_', ' ', $order->payment_method));

                                $badge = match($order->payment_method) {
                                    'cash' => 'bg-success',
                                    'gcash' => 'bg-primary',
                                    'card' => 'bg-info',
                                    'room_charge' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp

                            <span class="badge {{ $badge }}">
                                {{ $payment }}
                            </span>
                        </td>

                        {{-- TOTAL --}}
                        <td class="fw-bold text-primary">
                            ₱{{ number_format($order->total, 2) }}
                        </td>

                        {{-- DATE --}}
                        <td class="pe-3 text-muted small">
                            {{ optional($order->closed_at)->format('M d, Y H:i') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-file-circle-xmark d-block mb-2"></i>
                            No report data for selected filters.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($orders->hasPages())
            <div class="p-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Close dropdown on Apply submit
    (function () {
        const form = document.getElementById('reportsFilterForm');
        if (form) {
            form.addEventListener('submit', function () {
                const dropdownEl = form.querySelector('[data-bs-toggle="dropdown"]');
                if (dropdownEl) {
                    try {
                        const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownEl);
                        if (dropdown) {
                            dropdown.hide();
                        }
                    } catch (e) {}
                    dropdownEl.classList.remove('show');
                    dropdownEl.setAttribute('aria-expanded', 'false');
                    const menu = dropdownEl.nextElementSibling;
                    if (menu) {
                        menu.classList.remove('show');
                    }
                }
            });
        }
    })();
</script>
@endpush
