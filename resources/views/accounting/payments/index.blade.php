@extends('layouts.app')

@section('title', 'Payments')
@section('pageTitle', 'Payments & Transactions')
@section('pageSubtitle', 'Track and manage all guest payments and settlements')

@section('content')

<!-- SUCCESS/ERROR MESSAGES -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Payments (Collections)</div>
                <div class="fw-bold fs-4 text-success">₱{{ number_format($totalPayments, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Cash Payments</div>
                <div class="fw-bold fs-4" style="color: #504538;">₱{{ number_format($cashPayments, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Card Payments</div>
                <div class="fw-bold fs-4" style="color: #504538;">₱{{ number_format($cardPayments, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Check / Other Payments</div>
                <div class="fw-bold fs-4" style="color: #827567;">₱{{ number_format($pendingPayments, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.payments') }}" method="GET" class="card border-1 shadow-sm rounded-4 mb-3" id="paymentsFilterForm">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold" style="color: #504538;">Payment Records</div>
            <small class="text-muted">All financial settlements from guests</small>
        </div>

        <div class="d-flex align-items-center gap-2">

            {{-- Search (live) --}}
            <div style="width: 320px;">
                <div class="input-group shadow-sm" style="border: 1px solid #c2a889; border-radius: 0.5rem; overflow: hidden; height: 45px; background-color: #ffffff;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="paymentsSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1rem;"
                        placeholder="Search reference no. or guest..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            {{-- Filter Dropdown --}}
            <div class="dropdown">
                <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem;">
                    <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                    <span class="fw-semibold">Filter</span>
                    @if($methodFilter !== 'All Methods')
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 260px; border-radius: 0.75rem; z-index: 1055;">

                    <label class="form-label small mb-1 fw-semibold text-muted">Payment Method</label>
                    <select name="method" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                        <option value="All Methods" {{ $methodFilter === 'All Methods' ? 'selected' : '' }}>All Methods</option>
                        <option value="CASH" {{ $methodFilter === 'CASH' ? 'selected' : '' }}>Cash</option>
                        <option value="CREDIT_CARD" {{ $methodFilter === 'CREDIT_CARD' ? 'selected' : '' }}>Card</option>
                        <option value="GCASH" {{ $methodFilter === 'GCASH' ? 'selected' : '' }}>GCash</option>
                        <option value="MAYA" {{ $methodFilter === 'MAYA' ? 'selected' : '' }}>Maya</option>
                        <option value="CHECK" {{ $methodFilter === 'CHECK' ? 'selected' : '' }}>Check</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                        <a href="{{ route('accounting.payments') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                    </div>
                </div>
            </div>

            <!-- ADD PAYMENT -->
            <button type="button" class="btn text-white rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal" style="height: 45px; background-color: #334c42; border: none; font-size: 1rem;">
                <i class="fa-solid fa-plus me-1"></i> Record Payment
            </button>

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-1 shadow-sm rounded-4 overflow-hidden">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                <tr class="small fw-bold" style="color: #1a1a1a;">
                    <th class="ps-3">REF NO</th>
                    <th>GUEST</th>
                    <th>METHOD</th>
                    <th>DATE</th>
                    <th>STATUS</th>
                    <th class="text-end">AMOUNT</th>
                    <th class="text-center pe-3">ACTION</th>
                </tr>
            </thead>

            <tbody>

                @forelse($payments as $p)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="ps-3 fw-bold" style="color: #1a1a1a; font-size: 0.95rem;">{{ $p->charge_number }}</td>
                        <td class="fw-semibold" style="color: #1a1a1a; font-size: 0.95rem;">
                            @if($p->folio && $p->folio->guest)
                                @if(Str::contains(strtolower($p->folio->guest->first_name . ' ' . $p->folio->guest->last_name), 'walk-in') || Str::contains(strtolower($p->folio->guest->first_name), 'pos'))
                                    <span class="badge me-1" style="border: 1px solid #c2a889; color: #262626; background-color: #f3ede4; border-radius: 0.375rem;"><i class="fa-solid fa-user-tag me-1"></i>Walk-in</span>
                                @endif
                                {{ $p->folio->guest->first_name }} {{ $p->folio->guest->last_name }}
                            @else
                                <span class="badge me-1" style="border: 1px solid #c2a889; color: #262626; background-color: #f3ede4; border-radius: 0.375rem;"><i class="fa-solid fa-user-tag me-1"></i>Walk-in</span>
                                <span style="color: #6b7280;">General Walk-in</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $m = strtolower($p->payment_method ?? '');
                                $pmClass = match($m) {
                                    'cash' => 'badge-payment-cash',
                                    'gcash' => 'badge-payment-gcash',
                                    'maya' => 'badge-payment-maya',
                                    'credit_card', 'card' => 'badge-payment-card',
                                    'account_charge', 'room_charge' => 'badge-payment-account',
                                    default => 'badge-payment-other',
                                };
                            @endphp
                            <span class="badge-payment {{ $pmClass }}">{{ str_replace('_', ' ', strtoupper($p->payment_method)) }}</span>
                        </td>
                        <td style="color: #262626; font-size: 0.90rem;">{{ $p->transaction_date->toDateString() }}</td>
                        <td><span class="badge bg-success-subtle text-success fw-semibold">Completed</span></td>
                        <td class="text-end fw-bold" style="color: #198754; font-size: 0.98rem;">₱{{ number_format($p->credit_amount, 2) }}</td>
                        <td class="text-center pe-3">
                            @if($p->folio_id)
                                <a href="{{ route('accounting.billing.show', $p->folio_id) }}" class="btn btn-sm px-3 fw-semibold shadow-sm" style="border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;">
                                    <i class="fa-solid fa-eye me-1"></i> View Invoice
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #6b7280;">No payments found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    @if($payments->hasPages())
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
        <small class="text-muted">
            Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }} records
        </small>
        <div>
            {{ $payments->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif

</div>

<!-- RECORD PAYMENT MODAL -->
@push('modals')
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form action="{{ route('accounting.payments.store') }}" method="POST" class="modal-content">
            @csrf

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="recordPaymentModalLabel">
                    <i class="fa-solid fa-money-bill-wave text-success me-2"></i>
                    Record Payment
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <div class="row g-4">

                    <!-- Folio -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Guest / Folio
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="folio_id"
                            class="form-select"
                            required>

                            <option value="" disabled selected>
                                Select guest folio
                            </option>

                            @foreach($openFolios as $f)
                                <option value="{{ $f->folio_id }}">
                                    Folio #{{ $f->folio_number }}
                                    —
                                    {{ $f->guest
                                        ? $f->guest->first_name.' '.$f->guest->last_name
                                        : 'Walk-in'
                                    }}
                                </option>
                            @endforeach

                        </select>

                        <div class="form-text">
                            Only open folios are available for payment.
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Payment Method
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="payment_method"
                            class="form-select"
                            required>

                            <option value="CASH">Cash</option>
                            <option value="CREDIT_CARD">Credit Card</option>
                            <option value="CHECK">Check</option>

                        </select>

                    </div>

                    <!-- Amount -->
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Amount
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                ₱
                            </span>

                            <input
                                type="number"
                                name="amount"
                                class="form-control text-end"
                                step="0.01"
                                min="0.01"
                                placeholder="0.00"
                                required>

                        </div>

                    </div>

                    <!-- Notes -->
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Reference Notes
                        </label>

                        <input
                            type="text"
                            name="reference_notes"
                            class="form-control"
                            placeholder="Receipt number, card reference, check number, etc.">

                        <div class="form-text">
                            Optional reference for future verification.
                        </div>

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa-solid fa-check me-1"></i>
                    Record Payment

                </button>

            </div>

        </form>

    </div>
</div>
@endpush

@push('scripts')
<script>
(function () {
    const input = document.getElementById('paymentsSearch');
    if (!input) return;
    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            input.closest('form').requestSubmit();
        }, 400);
    });
})();
</script>
@endpush

@endsection