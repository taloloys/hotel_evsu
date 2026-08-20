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
                <div class="fw-bold fs-4" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Reporting overview</div>
                <div class="opacity-75 mt-1" style="font-size: 0.95rem;">Review sales totals and payment breakdowns with a clearer, calmer layout.</div>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-3">
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="small mb-1" style="font-family: 'Lucida Fax', serif; color: #827567;">Total Sales</div><h5 class="mb-0 fw-bold" style="font-family: 'Lucida Fax', serif; color: #504538;">₱{{ number_format($summary['total_sales'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="small mb-1" style="font-family: 'Lucida Fax', serif; color: #827567;">Orders</div><h5 class="mb-0 fw-bold" style="font-family: 'Lucida Fax', serif; color: #504538;">{{ $summary['order_count'] }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="small mb-1" style="font-family: 'Lucida Fax', serif; color: #827567;">Cash</div><h5 class="mb-0 fw-bold" style="font-family: 'Lucida Fax', serif; color: #504538;">₱{{ number_format($summary['cash_total'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="small mb-1" style="font-family: 'Lucida Fax', serif; color: #827567;">GCash</div><h5 class="mb-0 fw-bold" style="font-family: 'Lucida Fax', serif; color: #504538;">₱{{ number_format($summary['gcash_total'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="small mb-1" style="font-family: 'Lucida Fax', serif; color: #827567;">Card</div><h5 class="mb-0 fw-bold" style="font-family: 'Lucida Fax', serif; color: #504538;">₱{{ number_format($summary['card_total'], 2) }}</h5></div></div>
        <div class="col-md-2"><div class="coffeeshop-card p-3 h-100"><div class="small mb-1" style="font-family: 'Lucida Fax', serif; color: #827567;">Room Charge</div><h5 class="mb-0 fw-bold" style="font-family: 'Lucida Fax', serif; color: #504538;">₱{{ number_format($summary['room_total'], 2) }}</h5></div></div>
    </div>

    {{-- FILTER TOOLBAR (outside any overflow:hidden container so dropdown floats freely) --}}
    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap justify-content-end mb-3" id="reportsFilterForm">

        <!-- FILTER DROPDOWN -->
        <div class="dropdown">
            <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                    type="button"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem; font-family: 'Lucida Fax', serif;">
                <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                <span class="fw-semibold">Filter</span>
                @if($paymentMethod !== 'all' || $dateFrom !== Carbon\Carbon::today()->subDays(7)->toDateString() || $dateTo !== Carbon\Carbon::today()->toDateString())
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 280px; border-radius: 0.75rem; z-index: 1055; font-family: 'Lucida Fax', 'Georgia', serif;">

                <!-- Date From -->
                <label class="form-label small mb-1 fw-semibold text-muted" style="font-family: 'Franklin Gothic Medium', sans-serif;">Date From</label>
                <input type="date"
                       name="date_from"
                       value="{{ $dateFrom }}"
                       class="form-control mb-3 shadow-none"
                       style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">

                <!-- Date To -->
                <label class="form-label small mb-1 fw-semibold text-muted" style="font-family: 'Franklin Gothic Medium', sans-serif;">Date To</label>
                <input type="date"
                       name="date_to"
                       value="{{ $dateTo }}"
                       class="form-control mb-3 shadow-none"
                       style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">

                <!-- Payment Method -->
                <label class="form-label small mb-1 fw-semibold text-muted" style="font-family: 'Franklin Gothic Medium', sans-serif;">Payment Method</label>
                <select name="payment_method" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                    <option value="all" @selected($paymentMethod === 'all')>All Payments</option>
                    <option value="cash" @selected($paymentMethod === 'cash')>Cash</option>
                    <option value="gcash" @selected($paymentMethod === 'gcash')>GCash</option>
                    <option value="maya" @selected($paymentMethod === 'maya')>Maya</option>
                    <option value="card" @selected($paymentMethod === 'card')>Card</option>
                    <option value="room_charge" @selected($paymentMethod === 'room_charge')>Room Charge</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                    <a href="{{ route('coffeeshop.reports') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                </div>
            </div>
        </div>

        <!-- EXPORT EXCEL -->
        <a href="{{ route('coffeeshop.reports.export', request()->query()) }}"
           class="btn text-white d-flex align-items-center gap-2 px-3 fw-semibold shadow-sm"
           style="height: 45px; border-radius: 0.5rem; background-color: #334c42; border: none; font-size: 1rem; font-family: 'Lucida Fax', serif;"
           title="Export filtered sales report to Excel spreadsheet">
            <i class="fa-solid fa-file-excel me-1" style="color: #c2a889;"></i>
            <span>Export Excel</span>
        </a>

    </form>

    {{-- RESULTS TABLE --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden coffeeshop-panel">
        <div class="table-responsive">

            <table class="table align-middle mb-0 coffeeshop-table">

                {{-- HEADER --}}
                <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                    <tr>
                        <th class="ps-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ORDER</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">CUSTOMER</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ROOM</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">PAYMENT</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TOTAL</th>
                        <th class="pe-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">CLOSED AT</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                @forelse($orders as $order)

                    <tr style="border-bottom: 1px solid #f0f0f0;">

                        {{-- ORDER --}}
                        <td class="ps-4" style="padding: 1.05rem 1rem;">
                            <a href="{{ route('coffeeshop.orders.show', $order) }}"
                               class="fw-semibold text-decoration-none hover-opacity" style="color: #627e71; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; font-size: 1.02rem;">
                                {{ $order->order_number }}
                            </a>
                        </td>

                        {{-- CUSTOMER --}}
                        <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            {{ $order->customer_name }}
                        </td>

                        {{-- ROOM --}}
                        <td style="padding: 1.05rem 1rem; color: #554d46; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            {{ $order->room_number ?? '—' }}
                        </td>

                        {{-- PAYMENT --}}
                        <td style="padding: 1.05rem 1rem;">
                            @php
                                $payment = strtoupper(str_replace('_', ' ', $order->payment_method));
                                $paymentLower = strtolower($order->payment_method);

                                $badgeStyle = match($paymentLower) {
                                    'room_charge' => 'background-color: #627e71; color: #ffffff;',
                                    'account_charge' => 'background-color: #827567; color: #ffffff;',
                                    'gcash' => 'background-color: #334c42; color: #ffffff;',
                                    'maya' => 'background-color: #827567; color: #ffffff;',
                                    'card' => 'border: 1px solid #c2a889; color: #382e25; background: transparent;',
                                    'cash' => 'border: 1px solid #c2a889; color: #382e25; background: transparent;',
                                    default => 'background-color: #827567; color: #ffffff;'
                                };
                            @endphp

                            <span class="badge px-2.5 py-1 rounded-pill fw-semibold" style="{{ $badgeStyle }} font-size: 0.78rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                {{ $payment }}
                            </span>
                        </td>

                        {{-- TOTAL --}}
                        <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.08rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            ₱{{ number_format($order->total, 2) }}
                        </td>

                        {{-- DATE --}}
                        <td class="pe-4" style="padding: 1.05rem 1rem; color: #554d46; font-size: 0.92rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            {{ optional($order->closed_at)->format('M d, Y H:i') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fa-solid fa-file-circle-xmark fa-3x text-muted mb-3"></i>
                            <div class="fw-bold" style="color: #2c241d; font-size: 1.1rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                No report data for selected filters.
                            </div>
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
