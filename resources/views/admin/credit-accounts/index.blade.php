@extends('layouts.app')

@section('title', 'Credit Accounts')
@section('pageTitle', 'Credit Accounts')
@section('pageSubtitle', 'Manage corporate and VIP credit accounts')

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

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Credit Accounts</h5>
        <small class="text-muted">View and create credit accounts for billing</small>
    </div>
    <div class="d-flex justify-content-end align-items-center gap-2">
        <button id="add-account-btn"
                class="btn btn-primary d-flex align-items-center gap-2 px-3"
                style="height: 38px; border-radius: 6px;"
                data-bs-toggle="modal"
                data-bs-target="#addAccountModal">
            <i class="fa-solid fa-plus"></i>
            <span>Add Account</span>
        </button>
    </div>
</div>

<!-- ACCOUNTS TABLE -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Account Name</th>
                        <th>Contact</th>
                        <th>Limit</th>
                        <th>Outstanding Balance</th>
                        <th>Available Credit</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $account->account_name }}</td>
                            <td>
                                {{ $account->contact_name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $account->contact_number }}</small>
                            </td>
                            <td>₱{{ number_format($account->credit_limit, 2) }}</td>
                            <td>₱{{ number_format($account->outstanding_balance, 2) }}</td>
                            <td>
                                @if($account->available_credit <= 0)
                                    <span class="text-danger fw-bold">₱{{ number_format($account->available_credit, 2) }}</span>
                                @else
                                    <span class="text-success fw-bold">₱{{ number_format($account->available_credit, 2) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.credit-accounts.show', $account) }}" class="btn btn-sm btn-outline-primary px-2" title="View Ledger">
                                    <i class="fa-solid fa-eye"></i> View Ledger
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No credit accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD ACCOUNT MODAL -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addAccountModalLabel">
                    <i class="fa-solid fa-plus me-2 text-primary"></i>
                    Add New Credit Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.credit-accounts.store') }}">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label for="account_name" class="form-label fw-semibold">Account Name / Company</label>
                        <input type="text" id="account_name" name="account_name" class="form-control" value="{{ old('account_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_name" class="form-label fw-semibold">Contact Person</label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control" value="{{ old('contact_name') }}">
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" value="{{ old('contact_number') }}">
                    </div>
                    <div class="mb-3">
                        <label for="credit_limit" class="form-label fw-semibold">Credit Limit (₱)</label>
                        <input type="number" id="credit_limit" name="credit_limit" class="form-control" min="0" step="0.01" value="{{ old('credit_limit', '10000.00') }}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Account</button>
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
