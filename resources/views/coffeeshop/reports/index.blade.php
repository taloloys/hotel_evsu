@extends('layouts.app')

@section('title', 'Reports')
@section('pageTitle', 'POS Reports')
@section('pageSubtitle', 'Sales reports and payment breakdown')

@section('content')
@include('coffeeshop.partials.alerts')

<form method="GET" class="row g-2 mb-4">
    <div class="col-auto"><input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control"></div>
    <div class="col-auto"><input type="date" name="date_to" value="{{ $dateTo }}" class="form-control"></div>
    <div class="col-auto">
        <select name="payment_method" class="form-select">
            <option value="all" @selected($paymentMethod === 'all')>All Payments</option>
            <option value="cash" @selected($paymentMethod === 'cash')>Cash</option>
            <option value="room_charge" @selected($paymentMethod === 'room_charge')>Room Charge</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Generate</button></div>
    <div class="col-auto"><a href="{{ route('coffeeshop.reports.export', request()->query()) }}" class="btn btn-success">Export CSV</a></div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total Sales</div><h4>₱{{ number_format($summary['total_sales'], 2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Orders</div><h4>{{ $summary['order_count'] }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Cash Sales</div><h4>₱{{ number_format($summary['cash_total'], 2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Room Charges</div><h4>₱{{ number_format($summary['room_total'], 2) }}</h4></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Customer</th><th>Room</th><th>Payment</th><th>Total</th><th>Closed At</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('coffeeshop.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->room_number ?? '—' }}</td>
                    <td>{{ str_replace('_', ' ', strtoupper($order->payment_method)) }}</td>
                    <td>₱{{ number_format($order->total, 2) }}</td>
                    <td>{{ optional($order->closed_at)->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No report data for selected filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
