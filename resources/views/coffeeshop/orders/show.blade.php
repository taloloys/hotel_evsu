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
                <div class="mb-2"><strong>Status:</strong> 
                    <span id="order-status-badge" class="badge bg-{{ $order->status === 'refunded' ? 'danger' : ($order->status === 'closed' ? 'success' : 'secondary') }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
                <div class="mb-2"><strong>Payment:</strong> {{ $order->payment_method ? str_replace('_', ' ', strtoupper($order->payment_method)) : 'N/A' }}</div>
                <div class="mb-2"><strong>Room:</strong> {{ $order->room_number ?? 'Walk-in' }}</div>
                <div class="mb-2"><strong>Total:</strong> ₱{{ number_format($order->total, 2) }}</div>
                <div class="mb-2"><strong>Cashier:</strong> {{ $order->user?->full_name }}</div>
                <div class="mb-3"><strong>Closed:</strong> {{ optional($order->closed_at)->format('M d, Y H:i') ?? '—' }}</div>

                <div id="refund-actions-section">
                    @php
                        $pendingRefund = \App\Models\PosApprovalRequest::where('order_id', $order->order_id)
                            ->where('request_type', 'refund')
                            ->where('status', 'pending')
                            ->first();
                    @endphp

                    <!-- Refund Success Alert -->
                    <div id="refund-success-alert" class="alert alert-danger text-center small mb-0 py-2 shadow-sm border-0 {{ $order->status === 'refunded' ? '' : 'd-none' }}">
                        <i class="fa-solid fa-rotate-left me-1"></i>Order refunded and inventory restored.
                    </div>

                    <!-- Refund Pending Alert -->
                    <div id="refund-pending-alert" class="alert alert-warning text-center small mb-0 py-2 shadow-sm border-0 {{ ($order->status === 'closed' && $pendingRefund) ? '' : 'd-none' }}">
                        <i class="fa-solid fa-clock me-1 text-warning"></i>Refund pending Admin authorization.
                        <div id="refund-pending-reason" class="text-muted text-xs mt-1">Reason: "{{ $pendingRefund?->reason }}"</div>
                    </div>

                    <!-- Refund Action Form -->
                    <form action="{{ route('coffeeshop.orders.refund', $order) }}" method="POST" id="refund-form" class="{{ ($order->status === 'closed' && !$pendingRefund) ? '' : 'd-none' }}">
                        @csrf
                        <input type="hidden" name="reason" id="refund-reason">
                        <button type="button" class="btn btn-outline-danger w-100" id="refund-btn">
                            <i class="fa-solid fa-rotate-left me-1"></i>Refund Order
                        </button>
                    </form>

                    <!-- Refund Modal (always present so Bootstrap JS can access it) -->
                    <div class="modal fade" id="refundOrderModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title"><i class="fa-solid fa-rotate-left me-2"></i>Refund Order</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p id="refund-warning-text" class="text-muted small mb-3">Are you sure you want to refund this order and restore inventory?</p>
                                    <div id="refund-reason-container" class="d-none">
                                        <label for="refund-reason-input" class="form-label small fw-semibold text-danger">Reason for Refund (Admin Override Required)</label>
                                        <textarea id="refund-reason-input" class="form-control" rows="3" placeholder="e.g. Returned items, wrong purchase, customer complained, etc."></textarea>
                                        <div class="invalid-feedback">Refund reason is required.</div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="confirm-refund-btn">Confirm Refund</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @push('scripts')
                <script>
                    const refundModal = new bootstrap.Modal(document.getElementById('refundOrderModal'));
                    const refundReasonInput = document.getElementById('refund-reason-input');

                    document.getElementById('refund-btn')?.addEventListener('click', function(e) {
                        const isAdmin = @json(auth()->user()?->role?->role_name === 'ADMIN');
                        const reasonContainer = document.getElementById('refund-reason-container');
                        const warningText = document.getElementById('refund-warning-text');

                        refundReasonInput.classList.remove('is-invalid');
                        refundReasonInput.value = '';

                        if (isAdmin) {
                            warningText.textContent = "Are you sure you want to refund this order and restore inventory? (Manager Override)";
                            reasonContainer.classList.add('d-none');
                        } else {
                            warningText.textContent = "Refund of this order requires Admin Authorization. Please state the reason for this request below:";
                            reasonContainer.classList.remove('d-none');
                        }

                        refundModal.show();
                    });

                    document.getElementById('confirm-refund-btn')?.addEventListener('click', function() {
                        const isAdmin = @json(auth()->user()?->role?->role_name === 'ADMIN');
                        if (!isAdmin) {
                            const reason = refundReasonInput.value.trim();
                            if (!reason) {
                                refundReasonInput.classList.add('is-invalid');
                                return;
                            }
                            refundReasonInput.classList.remove('is-invalid');
                            document.getElementById('refund-reason').value = reason;
                        } else {
                            document.getElementById('refund-reason').value = 'Manager Override';
                        }
                        refundModal.hide();
                        document.getElementById('refund-form').submit();
                    });

                    // Real-time polling for order status
                    let lastStatus = @json($order->status);
                    let wasPending = @json(!!$pendingRefund);

                    async function checkOrderStatus() {
                        try {
                            const res = await fetch('{{ route('coffeeshop.orders.status-json', $order) }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            if (!res.ok) return;
                            const data = await res.json();

                            // Check if status changed
                            if (data.status !== lastStatus) {
                                lastStatus = data.status;
                                const statusBadge = document.getElementById('order-status-badge');
                                if (statusBadge) {
                                    statusBadge.textContent = data.status.toUpperCase();
                                    statusBadge.className = 'badge bg-' + (data.status === 'refunded' ? 'danger' : (data.status === 'closed' ? 'success' : 'secondary'));
                                }
                                
                                if (data.status === 'refunded') {
                                    document.getElementById('refund-success-alert')?.classList.remove('d-none');
                                    document.getElementById('refund-pending-alert')?.classList.add('d-none');
                                    document.getElementById('refund-form')?.classList.add('d-none');
                                }
                            }

                            // Check if pending refund status changed
                            const isPending = !!data.pending_refund;
                            if (isPending !== wasPending) {
                                wasPending = isPending;
                                if (isPending) {
                                    document.getElementById('refund-pending-alert')?.classList.remove('d-none');
                                    document.getElementById('refund-pending-reason').textContent = 'Reason: "' + data.pending_refund.reason + '"';
                                    document.getElementById('refund-form')?.classList.add('d-none');
                                } else {
                                    document.getElementById('refund-pending-alert')?.classList.add('d-none');
                                    if (lastStatus === 'closed') {
                                        document.getElementById('refund-form')?.classList.remove('d-none');
                                        
                                        const alertDiv = document.createElement('div');
                                        alertDiv.className = 'alert alert-danger text-center small mb-3 py-2 border-0 shadow-sm';
                                        alertDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i>Refund request was REJECTED/CANCELLED by the Admin.';
                                        const wrapper = document.getElementById('refund-actions-section');
                                        wrapper.insertBefore(alertDiv, wrapper.firstChild);
                                        setTimeout(() => alertDiv.remove(), 6000);
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('Error polling order status:', err);
                        }
                    }

                    if (lastStatus === 'closed' || wasPending) {
                        setInterval(checkOrderStatus, 5000);
                    }
                </script>
                @endpush
            </div>
        </div>
    </div>
</div>
@endsection
