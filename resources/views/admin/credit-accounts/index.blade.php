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

<!-- KPI SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Total Accounts</div>
                    <div class="fs-4 fw-bold text-brown">{{ $accounts->count() }}</div>
                </div>
                <div class="rounded-circle bg-primary-subtle p-3 text-primary"><i class="fa-solid fa-building-user fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Total Credit Limit</div>
                    <div class="fs-4 fw-bold text-success">₱{{ number_format($accounts->sum('credit_limit'), 2) }}</div>
                </div>
                <div class="rounded-circle bg-success-subtle p-3 text-success"><i class="fa-solid fa-credit-card fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small">Outstanding Balance</div>
                    <div class="fs-4 fw-bold text-danger">₱{{ number_format($accounts->sum('current_balance'), 2) }}</div>
                </div>
                <div class="rounded-circle bg-danger-subtle p-3 text-danger"><i class="fa-solid fa-file-invoice-dollar fs-4"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- HEADER & TOOLBAR -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0">Credit Accounts</h5>
        <small class="text-muted">View and create corporate and VIP credit accounts for billing</small>
    </div>
    <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">

        <!-- SEARCH -->
        <div style="width: 320px;">
            <div class="input-group" style="border: 1px solid black; border-radius: 6px; height: 45px;">
                <span class="input-group-text bg-white border-0 px-3">
                    <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                </span>
                <input type="text"
                       id="creditSearchInput"
                       class="form-control border-0 shadow-none py-2"
                       placeholder="Search account or contact..."
                       autocomplete="off"
                       style="font-size: 1.05rem;">
            </div>
        </div>

        <!-- FILTER -->
        <div class="dropdown">
            <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3"
                    data-bs-toggle="dropdown"
                    style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                <i class="fa-solid fa-filter fs-5"></i>
                <span>Filter</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 style="min-width: 240px; border-radius: 8px;">
                <label class="form-label small mb-1 fw-semibold">Credit Status</label>
                <select id="filterCreditStatusSelect" class="form-select mb-3" style="height: 38px; border-radius: 6px;">
                    <option value="">All Accounts</option>
                    <option value="available">Has Available Credit</option>
                    <option value="maxed">Maxed Out / Over Limit</option>
                </select>
                <div class="d-flex gap-2">
                    <button id="creditFilterApplyBtn" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                    <button id="creditFilterResetBtn" class="btn btn-light w-50" style="height: 38px;">Reset</button>
                </div>
            </div>
        </div>

        <button id="add-account-btn"
                class="btn btn-primary d-flex align-items-center gap-2 px-3 text-nowrap"
                style="height: 45px; border-radius: 6px; font-size: 1.05rem;"
                data-bs-toggle="modal"
                data-bs-target="#addAccountModal">
            <i class="fa-solid fa-plus fs-5"></i>
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
                        <tr data-account-name="{{ strtolower($account->account_name) }}"
                            data-contact-name="{{ strtolower($account->contact_name ?? '') }}"
                            data-credit-status="{{ $account->available_credit <= 0 ? 'maxed' : 'available' }}">
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

        // ── Credit Accounts Search & Filter ──────────────────────────────────
        const searchInput     = document.getElementById('creditSearchInput');
        const statusSelect    = document.getElementById('filterCreditStatusSelect');
        const applyBtn        = document.getElementById('creditFilterApplyBtn');
        const resetBtn        = document.getElementById('creditFilterResetBtn');
        const tbody           = document.querySelector('.table tbody');
        const dropdownToggle  = document.querySelector('.dropdown [data-bs-toggle="dropdown"]');

        let activeStatus = '';

        function applyFilters() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const rows  = tbody ? tbody.querySelectorAll('tr[data-account-name]') : [];
            let visible = 0;

            rows.forEach(function (row) {
                const matchSearch = !query ||
                    row.dataset.accountName.includes(query) ||
                    row.dataset.contactName.includes(query);
                const matchStatus = !activeStatus || row.dataset.creditStatus === activeStatus;
                const show = matchSearch && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) { visible++; }
            });

            // Show empty-state row if nothing matches
            let emptyRow = document.getElementById('noFilterResultsRow');
            if (!emptyRow && rows.length > 0 && visible === 0) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'noFilterResultsRow';
                emptyRow.innerHTML = '<td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-magnifying-glass me-2"></i>No accounts match your search or filter.</td>';
                tbody.appendChild(emptyRow);
            } else if (emptyRow) {
                emptyRow.style.display = (rows.length > 0 && visible === 0) ? '' : 'none';
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        if (searchInput) {
            searchInput.addEventListener('input', debounce(applyFilters, 400));
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                activeStatus = statusSelect.value;
                applyFilters();
                if (dropdownToggle) {
                    const dd = bootstrap.Dropdown.getInstance(dropdownToggle);
                    if (dd) { dd.hide(); }
                }
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                if (searchInput) { searchInput.value = ''; }
                if (statusSelect) { statusSelect.value = ''; }
                activeStatus = '';
                applyFilters();
            });
        }
    })();
</script>
@endpush
