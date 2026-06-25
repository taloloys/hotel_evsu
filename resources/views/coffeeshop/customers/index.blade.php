@extends('layouts.app')

@section('title', 'Customers')
@section('pageTitle', 'Customer History')
@section('pageSubtitle', 'Order history, frequent customers, open and closed tabs')

@section('content')
@include('coffeeshop.partials.alerts')

<form method="GET" class="row g-2 mb-4">
    <div class="col-md-4"><input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search customer or order"></div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Records</option>
            <option value="open" @selected($status === 'open')>Open Tabs</option>
            <option value="closed" @selected($status === 'closed')>Closed</option>
            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
            <option value="refunded" @selected($status === 'refunded')>Refunded</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
</form>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Most Frequent Customers</div>
            <div class="list-group list-group-flush">
                @forelse($frequentCustomers as $customer)
                <div class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $customer->customer_name }}</div>
                        <small class="text-muted">{{ $customer->order_count }} orders</small>
                    </div>
                    <span>₱{{ number_format($customer->total_spent, 2) }}</span>
                </div>
                @empty
                <div class="list-group-item text-muted">No customer data yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Recent Orders</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Order</th><th>Customer</th><th>Room</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('coffeeshop.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->room_number ?? '—' }}</td>
                            <td>₱{{ number_format($order->total, 2) }}</td>
                            <td>{{ strtoupper($order->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No orders found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Tabs</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Tab</th><th>Total</th><th>Status</th><th>Opened</th></tr></thead>
                    <tbody>
                    @forelse($tabs as $tab)
                        <tr>
                            <td>{{ $tab->tab_name }}</td>
                            <td>₱{{ number_format($tab->total, 2) }}</td>
                            <td>{{ strtoupper($tab->status) }}</td>
                            <td>{{ optional($tab->opened_at)->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No tabs found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
