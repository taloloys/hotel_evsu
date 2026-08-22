@forelse($recentOrders as $order)
    @php
        $status = strtolower($order->status);
        $statusBadge = match($status) {
            'paid', 'closed', 'completed' => 'badge-status-closed',
            'cancelled' => 'bg-danger text-white',
            'refunded' => 'bg-info text-white',
            default => 'badge-status-open'
        };

        $payment = strtolower($order->payment_method ?? '');
        $paymentBadge = match($payment) {
            'cash' => 'badge-payment-cash',
            'gcash' => 'badge-payment-gcash',
            'maya' => 'badge-payment-maya',
            'card', 'credit_card', 'debit_card' => 'badge-payment-card',
            'room_charge', 'account_charge' => 'badge-payment-account',
            default => 'badge-payment-other',
        };

        $itemsString = $order->items->map(function ($item) {
            return $item->product_name . ($item->quantity > 1 ? " ({$item->quantity})" : "");
        })->implode(', ');
    @endphp

    <tr>
        <td class="text-nowrap" style="padding: 1rem 0.75rem;">
            <a href="{{ route('coffeeshop.orders.show', $order) }}" class="text-decoration-none hover-opacity" style="font-size: 0.98rem; color: #2c241d; font-weight: 600; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                {{ $order->order_number }}
            </a>
        </td>
        <td style="padding: 1rem 0.75rem;">
            <span style="font-size: 0.98rem; color: #2c241d; font-weight: 600; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $order->customer_name }}</span>
            @if($order->room_number)
                <span class="badge bg-secondary text-white ms-1 fw-semibold" style="font-size: 0.78rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">Room {{ $order->room_number }}</span>
            @endif
        </td>
        <td style="padding: 1rem 0.75rem;">
            <div class="text-wrap" style="max-width: 320px; font-size: 0.94rem; color: #554d46; font-weight: 500; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                {{ $itemsString ?: 'No items' }}
            </div>
        </td>
        <td style="padding: 1rem 0.75rem; font-size: 1.02rem; color: #2c241d; font-weight: 600; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">₱{{ number_format($order->total, 2) }}</td>
        <td style="padding: 1rem 0.75rem;">
            @if($order->payment_method)
                <span class="badge-payment {{ $paymentBadge }}">
                    {{ str_replace('_', ' ', $order->payment_method) }}
                </span>
            @else
                <span class="text-muted small" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">N/A</span>
            @endif
        </td>
        <td style="padding: 1rem 0.75rem;">
            <span class="coffeeshop-pill fw-semibold {{ $statusBadge }}" style="font-size: 0.85rem; padding: 0.35rem 0.85rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ strtoupper($order->status) }}</span>
        </td>
        <td style="padding: 1rem 0.75rem;">
            <div style="font-size: 0.92rem; color: #2c241d; font-weight: 600; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $order->created_at->format('h:i A') }}</div>
            <div style="font-size: 0.82rem; color: #6c757d; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $order->created_at->diffForHumans() }}</div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">No recent orders placed.</td>
    </tr>
@endforelse
