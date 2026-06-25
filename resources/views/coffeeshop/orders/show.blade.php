@extends('layouts.app')

@section('title', 'Order '.$order->order_number)
@section('pageTitle', 'Order '.$order->order_number)
@section('pageSubtitle', $order->customer_name)

@section('content')
@include('coffeeshop.partials.alerts')

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Items Ordered</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                <small class="text-muted">{{ $item->product_description }}</small>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                            <td>₱{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-2"><strong>Status:</strong> {{ strtoupper($order->status) }}</div>
                <div class="mb-2"><strong>Payment:</strong> {{ $order->payment_method ? str_replace('_', ' ', strtoupper($order->payment_method)) : 'N/A' }}</div>
                <div class="mb-2"><strong>Room:</strong> {{ $order->room_number ?? 'Walk-in' }}</div>
                <div class="mb-2"><strong>Total:</strong> ₱{{ number_format($order->total, 2) }}</div>
                <div class="mb-2"><strong>Cashier:</strong> {{ $order->user?->full_name }}</div>
                <div class="mb-3"><strong>Closed:</strong> {{ optional($order->closed_at)->format('M d, Y H:i') ?? '—' }}</div>

                @if($order->status === 'closed')
                <form action="{{ route('coffeeshop.orders.refund', $order) }}" method="POST" onsubmit="return confirm('Refund this order and restore inventory?')">
                    @csrf
                    <button class="btn btn-outline-danger w-100">Refund Order</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
