@extends('layouts.app')

@section('title', 'Reports')
@section('pageTitle', 'POS Reports')
@section('pageSubtitle', 'Sales reports and payment breakdown')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="row g-3 mb-4">
    <div class="col-md-2"><div class="card border-0 shadow-sm h-100"><div class="card-body p-3"><div class="text-muted small">Total Sales</div><h5 class="mb-0">₱{{ number_format($summary['total_sales'], 2) }}</h5></div></div></div>
    <div class="col-md-2"><div class="card border-0 shadow-sm h-100"><div class="card-body p-3"><div class="text-muted small">Orders</div><h5 class="mb-0">{{ $summary['order_count'] }}</h5></div></div></div>
    <div class="col-md-2"><div class="card border-0 shadow-sm h-100"><div class="card-body p-3"><div class="text-muted small">Cash</div><h5 class="mb-0">₱{{ number_format($summary['cash_total'], 2) }}</h5></div></div></div>
    <div class="col-md-2"><div class="card border-0 shadow-sm h-100"><div class="card-body p-3"><div class="text-muted small">GCash</div><h5 class="mb-0">₱{{ number_format($summary['gcash_total'], 2) }}</h5></div></div></div>
    <div class="col-md-2"><div class="card border-0 shadow-sm h-100"><div class="card-body p-3"><div class="text-muted small">Card</div><h5 class="mb-0">₱{{ number_format($summary['card_total'], 2) }}</h5></div></div></div>
    <div class="col-md-2"><div class="card border-0 shadow-sm h-100"><div class="card-body p-3"><div class="text-muted small">Room Charge</div><h5 class="mb-0">₱{{ number_format($summary['room_total'], 2) }}</h5></div></div></div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="row g-3 align-items-end">

            {{-- DATE RANGE --}}
            <div class="col-lg-4 col-md-6">
                <label class="form-label small text-muted">Date From</label>
                <input type="date"
                       name="date_from"
                       value="{{ $dateFrom }}"
                       class="form-control">
            </div>

            <div class="col-lg-4 col-md-6">
                <label class="form-label small text-muted">Date To</label>
                <input type="date"
                       name="date_to"
                       value="{{ $dateTo }}"
                       class="form-control">
            </div>

            {{-- PAYMENT FILTER --}}
            <div class="col-lg-4 col-md-6">
                <label class="form-label small text-muted">Payment Method</label>
                <select name="payment_method" class="form-select">
                    <option value="all" @selected($paymentMethod === 'all')>All Payments</option>
                    <option value="cash" @selected($paymentMethod === 'cash')>Cash</option>
                    <option value="gcash" @selected($paymentMethod === 'gcash')>GCash</option>
                    <option value="card" @selected($paymentMethod === 'card')>Card</option>
                    <option value="room_charge" @selected($paymentMethod === 'room_charge')>Room Charge</option>
                </select>
            </div>

        </div>

        {{-- ACTION BUTTONS --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">

            <div class="text-muted small">
                Filter and generate updated sales report
            </div>

            <div class="d-flex gap-2">

                <a href="{{ route('coffeeshop.reports.export', request()->query()) }}"
                   class="btn btn-success px-3">
                    <i class="fa-solid fa-file-export me-1"></i>
                    Export CSV
                </a>

                <button class="btn btn-primary px-3">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Generate Report
                </button>

            </div>

        </div>

    </div>
    <hr>
    <div class="table-responsive card border-1">

        <table class="table align-middle mb-0">

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

</form>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">


</div>
@endsection
