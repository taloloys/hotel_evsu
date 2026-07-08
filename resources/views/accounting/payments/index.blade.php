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
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Total Payments (Collections)</div>
                <div class="fw-bold fs-3 text-success">₱{{ number_format($totalPayments, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Cash Payments</div>
                <div class="fw-bold fs-3">₱{{ number_format($cashPayments, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Card Payments</div>
                <div class="fw-bold fs-3">₱{{ number_format($cardPayments, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Check / Other Payments</div>
                <div class="fw-bold fs-3 text-warning">₱{{ number_format($pendingPayments, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.payments') }}" method="GET" class="card border-1 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Payment Records</div>
            <small class="text-muted">All financial settlements from guests</small>
        </div>

        <div class="d-flex gap-2">

            <div class="d-flex align-items-center gap-3">

                {{-- Filter --}}
                <div style="width: 200px; border: 1px solid #000000; border-radius: .375rem;">
                    <select
                        name="method"
                        class="form-select"
                        style="height:40px;"
                        onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.submit()">

                        <option value="All Methods" {{ $methodFilter === 'All Methods' ? 'selected' : '' }}>
                            All Methods
                        </option>

                        <option value="CASH" {{ $methodFilter === 'CASH' ? 'selected' : '' }}>
                            Cash
                        </option>

                        <option value="CREDIT_CARD" {{ $methodFilter === 'CREDIT_CARD' ? 'selected' : '' }}>
                            Card
                        </option>

                        <option value="CHECK" {{ $methodFilter === 'CHECK' ? 'selected' : '' }}>
                            Check
                        </option>

                    </select>
                </div>

                {{-- Search --}}
                <div style="width: 300px; border: 1px solid #000000; border-radius: .375rem;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fa-solid fa-search text-muted"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0"
                            style="height:40px;"
                            placeholder="Search reference no. or guest..."
                            value="{{ $search }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Search Button --}}
                <button type="submit" class="btn btn-primary px-4" style="height:40px;">
                    <i class="fa-solid fa-search me-1"></i>
                    Search
                </button>

            </div>

            <!-- ADD PAYMENT -->
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="fa-solid fa-plus me-1"></i> Record Payment
            </button>

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-1 shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Ref No</th>
                    <th>Guest</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($payments as $p)
                    <tr>
                        <td>{{ $p->charge_number }}</td>
                        <td>
                            @if($p->folio && $p->folio->guest)
                                {{ $p->folio->guest->first_name }} {{ $p->folio->guest->last_name }}
                            @else
                                <span class="text-muted">Walk-in / General</span>
                            @endif
                        </td>
                        <td>
                            @if($p->payment_method === 'CASH')
                                <span class="badge bg-success">Cash</span>
                            @elseif($p->payment_method === 'CREDIT_CARD')
                                <span class="badge bg-primary">Card</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ $p->payment_method }}</span>
                            @endif
                        </td>
                        <td>{{ $p->transaction_date->toDateString() }}</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td class="text-end fw-bold text-success">₱{{ number_format($p->credit_amount, 2) }}</td>
                        <td class="text-center">
                            @if($p->folio_id)
                                <a href="{{ route('accounting.billing.show', $p->folio_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> View Invoice
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No payments found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

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

@endsection