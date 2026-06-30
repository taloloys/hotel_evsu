@extends('layouts.app')

@section('title', 'Credit Account: ' . $account->account_name)
@section('pageTitle', 'Credit Account: ' . $account->account_name)
@section('pageSubtitle', 'Ledger details and payments')

@section('content')

{{-- TOAST CONTAINER --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div id="validationToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Error:</strong> {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>

<!-- HEADER / BACK -->
<div class="mb-3">
    <a href="{{ route('admin.credit-accounts') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Accounts
    </a>
</div>

<!-- ACCOUNT DETAILS & KPI -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Account Details</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Account Name</span>
                        <span class="fw-semibold">{{ $account->account_name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Contact Person</span>
                        <span>{{ $account->contact_name ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Contact Number</span>
                        <span>{{ $account->contact_number ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Credit Limit</span>
                        <span class="fw-bold">₱{{ number_format($account->credit_limit, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="mb-4">
                    <h6 class="text-muted mb-1">Outstanding Balance</h6>
                    <h2 class="fw-bold {{ $account->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                        ₱{{ number_format($account->outstanding_balance, 2) }}
                    </h2>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Available Credit</h6>
                    <h4 class="fw-bold {{ $account->available_credit <= 0 ? 'text-danger' : 'text-success' }}">
                        ₱{{ number_format($account->available_credit, 2) }}
                    </h4>
                </div>
                <div class="mt-4">
                    <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fa-solid fa-money-bill-wave me-2"></i> Record Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LEDGER TABLE -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0">Ledger History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Notes</th>
                        <th>Processed By</th>
                        <th class="text-end pe-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($account->ledgers as $ledger)
                        <tr>
                            <td class="ps-3">{{ $ledger->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($ledger->type === 'charge')
                                    <span class="badge bg-danger">Charge</span>
                                @else
                                    <span class="badge bg-success">Payment</span>
                                @endif
                            </td>
                            <td>
                                @if($ledger->reference_type && $ledger->reference_id)
                                    {{ $ledger->reference_type }} #{{ $ledger->reference_id }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $ledger->notes ?? '—' }}</td>
                            <td>{{ $ledger->processedBy?->full_name ?? 'System' }}</td>
                            <td class="text-end pe-3 fw-bold {{ $ledger->type === 'charge' ? 'text-danger' : 'text-success' }}">
                                {{ $ledger->type === 'charge' ? '+' : '-' }}₱{{ number_format($ledger->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No ledger records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- RECORD PAYMENT MODAL -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="paymentModalLabel">
                    <i class="fa-solid fa-money-bill-wave me-2 text-success"></i>
                    Record Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.credit-accounts.payment', $account) }}">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="alert alert-info mb-4">
                        Current Outstanding Balance: <strong>₱{{ number_format($account->outstanding_balance, 2) }}</strong>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">Payment Amount (₱)</label>
                        <input type="number" id="amount" name="amount" class="form-control form-control-lg" min="0.01" step="0.01" value="{{ old('amount') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes / Reference</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="E.g., Check #123456, Bank Transfer">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });
    })();
</script>
@endpush
