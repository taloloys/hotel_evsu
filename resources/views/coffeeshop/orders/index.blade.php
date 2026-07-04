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
                <div class="fw-bold fs-5">Orders overview</div>
                <div class="opacity-75 mt-1">Review every completed or adjusted purchase and keep transaction history easy to follow.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden mb-3">
        <div class="p-3 p-lg-4 border-bottom bg-white">
            <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills" id="ordersNav">

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
                <div class="input-group coffeeshop-form-control" style="width: 450px;">
                    <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="Search orders..." onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
                </div>
            </form>
        </div>

    {{-- ===================== TABLE ===================== --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="table-responsive">

            <table class="table mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                @forelse($orders as $order)

                    <tr>
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->room_number ?? '—' }}</td>
                        <td>{{ $order->payment_method ? str_replace('_', ' ', strtoupper($order->payment_method)) : '—' }}</td>
                        <td class="fw-bold text-primary">₱{{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ strtoupper($order->status) }}</span>
                            @php
                                $rejectedRefund = $order->approvalRequests->where('request_type', 'refund')->where('status', 'rejected')->first();
                                $rejectedCancel = $order->approvalRequests->where('request_type', 'cancel_order')->where('status', 'rejected')->first();
                            @endphp
                            @if($rejectedRefund)
                                <span class="badge bg-danger mt-1">REFUND REJECTED</span>
                            @endif
                            @if($rejectedCancel)
                                <span class="badge bg-danger mt-1">CANCEL REJECTED</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('coffeeshop.orders.show', $order) }}"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-muted text-center py-4">
                            No orders found.
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
