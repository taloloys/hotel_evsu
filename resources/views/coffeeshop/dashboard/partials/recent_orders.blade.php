@forelse($recentOrders as $order)
    @php
        $status = strtolower($order->status);
        $statusBadge = match($status) {
            'paid', 'closed' => 'bg-success',
            'pending', 'active', 'open' => 'bg-warning text-dark',
            'cancelled' => 'bg-danger',
            'refunded' => 'bg-info text-dark',
            default => 'bg-secondary'
        };

        $payment = strtolower($order->payment_method ?? '');
        $paymentBadge = match($payment) {
            'cash' => 'border border-success text-success bg-success-subtle px-2 py-1 rounded-pill',
            'room_charge' => 'border border-primary text-primary bg-primary-subtle px-2 py-1 rounded-pill',
            default => 'border border-secondary text-secondary bg-secondary-subtle px-2 py-1 rounded-pill'
        };

        $itemsString = $order->items->map(function ($item) {
            return $item->product_name . ($item->quantity > 1 ? " ({$item->quantity})" : "");
        })->implode(', ');
    @endphp

    <tr>
        <td>
            <a href="{{ route('coffeeshop.orders.show', $order) }}" class="fw-bold text-decoration-none text-dark hover-opacity">
                {{ $order->order_number }}
            </a>
        </td>
        <td>
            <span class="fw-semibold">{{ $order->customer_name }}</span>
            @if($order->room_number)
                <span class="badge bg-secondary text-white ms-1" style="font-size: 0.7rem;">Room {{ $order->room_number }}</span>
            @endif
        </td>
        <td>
            <div class="small text-muted text-wrap" style="max-width: 320px;">
                {{ $itemsString ?: 'No items' }}
            </div>
        </td>
        <td class="fw-bold text-brown">₱{{ number_format($order->total, 2) }}</td>
        <td>
            @if($order->payment_method)
                <span class="small font-monospace {{ $paymentBadge }}" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                    {{ str_replace('_', ' ', $order->payment_method) }}
                </span>
            @else
                <span class="text-muted small">N/A</span>
            @endif
        </td>
        <td>
            <span class="dashboard-pill {{ $statusBadge }}">{{ strtoupper($order->status) }}</span>
        </td>
        <td>
            <div class="small text-dark fw-medium">{{ $order->created_at->format('h:i A') }}</div>
            <div class="text-muted" style="font-size: 0.7rem;">{{ $order->created_at->diffForHumans() }}</div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">No recent orders placed.</td>
    </tr>
@endforelse
