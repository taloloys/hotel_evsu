@extends('layouts.app')

@section('title', 'Orders')
@section('pageTitle', 'Order Management')
@section('pageSubtitle', 'Review closed, cancelled, and refunded orders with their current status')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-4" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Orders Overview</div>
                <div class="opacity-75 mt-1" style="font-size: 0.95rem;">Review every completed or adjusted purchase and keep transaction history easy to follow.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden mb-3">
        <div class="p-3 p-lg-4 border-bottom bg-white">
            <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills" id="ordersNav" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">

            @foreach([
                'all' => 'All Orders',
                'closed' => 'Closed',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded'
            ] as $key => $label)

                <li class="nav-item">

                    <a href="{{ route('coffeeshop.orders', ['status' => $key]) }}"
                    class="nav-link rounded-pill {{ $status === $key ? 'active' : '' }}">

                        {{ $label }}

                    </a>

                </li>

            @endforeach

        </ul>
    </div>

    <div class="p-3 p-lg-4">
        <div class="d-flex justify-content-end mb-3">
            <form method="GET">
                <div style="width: 360px;">
                    <div class="input-group coffeeshop-form-control" style="border: 1px solid #827567; border-radius: 0.5rem; overflow: hidden; height: 45px;">
                        <span class="input-group-text bg-white border-0 px-3"><i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 shadow-none py-2" placeholder="Search orders..." autocomplete="off" style="font-size: 1rem; font-family: 'Lucida Fax', 'Georgia', serif;" onkeydown="if(event.key==='Enter'){ event.preventDefault(); if(this.form.requestSubmit){ this.form.requestSubmit(); }else{ this.form.submit(); } }">
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== TABLE ===================== --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0 coffeeshop-table">

                <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                    <tr>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ORDER</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">CUSTOMER</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ROOM</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">PAYMENT</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TOTAL</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">STATUS</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                @forelse($orders as $order)

                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $order->order_number }}</td>
                        <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $order->customer_name }}</td>
                        <td style="padding: 1.05rem 1rem; color: #554d46; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $order->room_number ?? '—' }}</td>
                        <td style="padding: 1.05rem 1rem;">
                            @if($order->payment_method)
                                @php
                                    $paymentMethod = strtolower($order->payment_method);
                                    $paymentBadgeClass = match($paymentMethod) {
                                        'cash' => 'badge-payment-cash',
                                        'gcash' => 'badge-payment-gcash',
                                        'maya' => 'badge-payment-maya',
                                        'card', 'credit_card', 'debit_card' => 'badge-payment-card',
                                        'room_charge', 'account_charge' => 'badge-payment-account',
                                        default => 'badge-payment-other',
                                    };
                                @endphp
                                <span class="badge-payment {{ $paymentBadgeClass }}">
                                    @if($paymentMethod === 'account_charge')
                                        <i class="fa-solid fa-crown me-1" style="color: #c2a889;"></i>
                                    @endif
                                    {{ str_replace('_', ' ', strtoupper($order->payment_method)) }}
                                </span>
                            @else
                                <span style="color: #554d46; font-size: 0.92rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">—</span>
                            @endif
                        </td>
                        <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.08rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">₱{{ number_format($order->total, 2) }}</td>
                        <td style="padding: 1.05rem 1rem;">
                            @php
                                $statusBadgeClass = match(strtolower($order->status)) {
                                    'closed', 'paid', 'completed' => 'badge-status-closed',
                                    'cancelled' => 'bg-danger text-white',
                                    'refunded' => 'bg-info text-white',
                                    default => 'badge-status-open'
                                };
                            @endphp
                            <span class="coffeeshop-pill fw-semibold {{ $statusBadgeClass }}" style="font-size: 0.88rem; padding: 0.35rem 0.85rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ strtoupper($order->status) }}</span>
                            @php
                                $rejectedRefund = $order->approvalRequests->where('request_type', 'refund')->where('status', 'rejected')->first();
                                $rejectedCancel = $order->approvalRequests->where('request_type', 'cancel_order')->where('status', 'rejected')->first();
                            @endphp
                            @if($rejectedRefund)
                                <span class="coffeeshop-pill bg-danger text-white ms-1 fw-semibold" style="font-size: 0.78rem;">REFUND REJECTED</span>
                            @endif
                            @if($rejectedCancel)
                                <span class="coffeeshop-pill bg-danger text-white ms-1 fw-semibold" style="font-size: 0.78rem;">CANCEL REJECTED</span>
                            @endif
                        </td>
                        <td class="text-end pe-4" style="padding: 1.05rem 1rem;">
                            <a href="{{ route('coffeeshop.orders.show', $order) }}"
                            class="btn btn-sm rounded-pill px-3 fw-semibold" style="border: 1px solid #827567; color: #2c241d; font-size: 0.88rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fa-solid fa-receipt fa-3x text-muted mb-3"></i>
                            <div class="fw-bold" style="color: #504538; font-size: 1.1rem; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                                No orders found.
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
</div>

@endsection
