@extends('layouts.app')

@section('title', 'Orders')
@section('pageTitle', 'Order Management')
@section('pageSubtitle', 'Open orders, active tabs, closed, cancelled, and refunded orders')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="d-flex flex-wrap gap-2 mb-3">
    @foreach(['all' => 'All Orders', 'active_tabs' => 'Active Tabs', 'closed' => 'Closed', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded'] as $key => $label)
    <a href="{{ route('coffeeshop.orders', ['status' => $key]) }}" class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
    @endforeach
</div>

<form method="GET" class="row g-2 mb-3">
    <input type="hidden" name="status" value="{{ $status }}">
    <div class="col-md-4"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search customer, room, order #"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Search</button></div>
</form>

@if($status === 'active_tabs')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Customer</th><th>Room</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($activeTabs as $tab)
                <tr>
                    <td>{{ $tab->tab_name }}</td>
                    <td>{{ $tab->room?->room_number ?? '—' }}</td>
                    <td>{{ $tab->items->map(fn($i) => $i->product?->name.' x'.$i->quantity)->join(', ') }}</td>
                    <td>₱{{ number_format($tab->total, 2) }}</td>
                    <td><span class="badge bg-success">OPEN TAB</span></td>
                    <td><a href="{{ route('coffeeshop.pos') }}" class="btn btn-sm btn-primary">Manage</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No active tabs.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($activeTabs->hasPages())<div class="card-footer">{{ $activeTabs->links() }}</div>@endif
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Customer</th><th>Room</th><th>Payment</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->room_number ?? '—' }}</td>
                    <td>{{ $order->payment_method ? str_replace('_', ' ', strtoupper($order->payment_method)) : '—' }}</td>
                    <td>₱{{ number_format($order->total, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ strtoupper($order->status) }}</span></td>
                    <td><a href="{{ route('coffeeshop.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())<div class="card-footer">{{ $orders->links() }}</div>@endif
</div>
@endif
@endsection
