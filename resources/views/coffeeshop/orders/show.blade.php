@extends('layouts.app')

@section('title', 'Order '.$order->order_number)
@section('pageTitle', 'Order '.$order->order_number)
@section('pageSubtitle', $order->customer_name)

@section('content')

@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Order detail</div>
                <div class="opacity-75 mt-1">Inspect each line item and the payment flow in a polished view.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden">
        <div class="row">
    <div class="col-12">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            {{-- ================= HEADER ================= --}}
        <div class="card-header bg-white p-4 border-bottom">

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

                <div>
                    <h4 class="fw-bold mb-1">
                        Order #{{ $order->order_number }}
                    </h4>
                    <div class="text-muted small">
                        {{ $order->customer_name }}
                    </div>
                </div>


            </div>

            <hr class="my-4">

            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <small class="text-muted d-block">Payment</small>
                    <div class="fw-semibold">
                        @if($order->payment_method)
                            @php
                                $paymentBadge = match($order->payment_method) {
                                    'room_charge' => 'bg-info',
                                    'account_charge' => 'bg-primary',
                                    'gcash' => 'bg-success',
                                    'card' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $paymentBadge }} text-white">
                                {{ str_replace('_',' ',strtoupper($order->payment_method)) }}
                            </span>
                        @else
                            N/A
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <small class="text-muted d-block">Room</small>
                    <div class="fw-semibold">
                        {{ $order->room_number ?? 'Walk-in' }}
                    </div>
                </div>

                <div class="col-md-2">
                    <small class="text-muted d-block">Cashier</small>
                    <div class="fw-semibold">
                        {{ $order->user?->full_name }}
                    </div>
                </div>

                {{-- CLOSED + REFUND BUTTON (MOVED HERE) --}}
                <div class="col-md-5">

                    <div class="d-flex justify-content-between align-items-end">

                        {{-- CLOSED --}}
                        <div>
                            <small class="text-muted d-block">Closed</small>
                            <div class="fw-semibold">
                                {{ optional($order->closed_at)->format('M d, Y H:i') ?? '—' }}
                            </div>
                        </div>

                        {{-- REFUND BUTTON (NO LOGIC CHANGED) --}}
                        <div>
                            @php
                                $pendingRefund = \App\Models\PosApprovalRequest::where('order_id', $order->order_id)
                                    ->where('request_type', 'refund')
                                    ->where('status', 'pending')
                                    ->first();
                            @endphp

                            @if($order->status === 'closed' && !$pendingRefund)
                                <button type="button" class="btn btn-outline-danger btn-sm" id="refund-btn">
                                    <i class="fa-solid fa-rotate-left me-1"></i>Refund
                                </button>
                            @elseif($pendingRefund)
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    Pending Refund
                                </span>
                            @elseif($order->status === 'refunded')
                                <span class="badge bg-danger px-3 py-2">
                                    Refunded
                                </span>
                            @endif
                        </div>

                    </div>

                </div>

            </div>

            {{-- ================= REFUND ALERTS (UNCHANGED FUNCTIONALITY) ================= --}}
            <div id="refund-actions-section" class="mt-3">

                <div id="refund-success-alert"
                    class="alert alert-danger text-center small mb-0 py-2 shadow-sm border-0 {{ $order->status === 'refunded' ? '' : 'd-none' }}">
                    <i class="fa-solid fa-rotate-left me-1"></i>
                    Order refunded and inventory restored.
                </div>

                <div id="refund-pending-alert"
                    class="alert alert-warning text-center small mb-0 py-2 shadow-sm border-0 {{ ($order->status === 'closed' && $pendingRefund) ? '' : 'd-none' }}">
                    <i class="fa-solid fa-clock me-1 text-warning"></i>
                    Refund pending Admin authorization.

                    <div id="refund-pending-reason" class="text-muted text-xs mt-1">
                        Reason: "{{ $pendingRefund?->reason }}"
                    </div>
                </div>

                {{-- FORM + MODAL (UNCHANGED) --}}
                <form action="{{ route('coffeeshop.orders.refund', $order) }}"
                    method="POST"
                    id="refund-form"
                    class="{{ ($order->status === 'closed' && !$pendingRefund) ? '' : 'd-none' }}">
                    @csrf
                    <input type="hidden" name="reason" id="refund-reason">

                </form>

                {{-- MODAL (UNCHANGED) --}}
                <div class="modal fade" id="refundOrderModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">

                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fa-solid fa-rotate-left me-2"></i>
                                    Refund Order
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4">
                                <p id="refund-warning-text" class="text-muted small mb-3">
                                    Are you sure you want to refund this order and restore inventory?
                                </p>

                                <div id="refund-reason-container" class="d-none">
                                    <label class="form-label small fw-semibold text-danger">
                                        Reason for Refund (Admin Override Required)
                                    </label>

                                    <textarea id="refund-reason-input"
                                        class="form-control"
                                        rows="3"
                                        placeholder="e.g. Returned items, wrong purchase, customer complained"></textarea>

                                    <div class="invalid-feedback">Refund reason is required.</div>
                                </div>
                            </div>

                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="confirm-refund-btn">
                                    Confirm Refund
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

            {{-- ================= TABLE ================= --}}
            <div class="table-responsive">

                <table class="table table-lg align-middle mb-0 coffeeshop-table">

                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-4">Item</th>
                            <th class="text-center py-3">Qty</th>
                            <th class="text-end py-3">Unit Price</th>
                            <th class="text-end pe-4 py-3">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="ps-4 py-3">

                                <div class="fw-semibold fs-6">
                                    {{ $item->product_name }}
                                </div>

                                <small class="text-muted d-block">
                                    {{ $item->product_description ?? 'No description' }}
                                </small>

                                {{-- ITEM NOTES DISPLAY --}}
                                @if(!empty($item->notes))
                                    <span class="badge bg-warning text-dark mt-1">
                                        <i class="fa-solid fa-note-sticky me-1"></i>
                                        {{ $item->notes }}
                                    </span>
                                @endif

                            </td>

                            <td class="text-center fw-semibold">{{ $item->quantity }}</td>

                            <td class="text-end">₱{{ number_format($item->unit_price,2) }}</td>

                            <td class="text-end pe-4 fw-bold text-primary">
                                ₱{{ number_format($item->line_total,2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-semibold py-3">Grand Total</td>
                            <td class="text-end pe-4 fw-bold text-primary fs-5">
                                ₱{{ number_format($order->total,2) }}
                            </td>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>

    </div>
</div>

@endsection
@push('scripts')
<script>
(function () {

    // prevent double initialization (Turbo-safe)
    if (window.__refundInitialized) return;
    window.__refundInitialized = true;

    const modalEl = document.getElementById('refundOrderModal');
    if (!modalEl) return;

    const refundModal = new bootstrap.Modal(modalEl);
    const refundReasonInput = document.getElementById('refund-reason-input');

    const refundBtn = document.getElementById('refund-btn');
    const confirmBtn = document.getElementById('confirm-refund-btn');

    if (refundBtn) {
        refundBtn.addEventListener('click', function () {

            const isAdmin = @json(auth()->user()?->role?->role_name === 'ADMIN');

            const reasonContainer = document.getElementById('refund-reason-container');
            const warningText = document.getElementById('refund-warning-text');

            refundReasonInput.value = '';
            refundReasonInput.classList.remove('is-invalid');

            if (isAdmin) {
                warningText.textContent = "Manager Override Refund Confirmation";
                reasonContainer.classList.add('d-none');
            } else {
                warningText.textContent = "Admin approval required. Please provide reason.";
                reasonContainer.classList.remove('d-none');
            }

            refundModal.show();
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {

            const isAdmin = @json(auth()->user()?->role?->role_name === 'ADMIN');

            if (!isAdmin) {
                const reason = refundReasonInput.value.trim();
                if (!reason) {
                    refundReasonInput.classList.add('is-invalid');
                    return;
                }
                document.getElementById('refund-reason').value = reason;
            } else {
                document.getElementById('refund-reason').value = 'Manager Override';
            }

            refundModal.hide();
            document.getElementById('refund-form').submit();
        });
    }

})();
</script>
@endpush