@extends('layouts.app')

@section('title', 'Expenses')
@section('pageTitle', 'Operating Expenses')
@section('pageSubtitle', 'Track hotel operational costs and departmental spending')

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
                <div class="text-muted large">Total Expenses (Approved)</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($totalExpenses, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Utilities</div>
                <div class="fw-bold fs-3">₱{{ number_format($utilities, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Salaries & Wages</div>
                <div class="fw-bold fs-3">₱{{ number_format($salaries, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Supplies & Stationery</div>
                <div class="fw-bold fs-3">₱{{ number_format($supplies, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.expenses') }}" method="GET" class="card border-1 shadow-sm mb-3" id="expensesFilterForm">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Expense Records</div>
            <small class="text-muted">Operational spending by department</small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            <!-- SEARCH (live) -->
            <div style="width: 320px; border: 1px solid #000000; border-radius: .375rem; height: 45px;">
                <div class="input-group" style="height: 100%;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="expensesSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1.05rem;"
                        placeholder="Search expense or category..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="dropdown">
                <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 position-relative"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                    <i class="fa-solid fa-filter fs-5"></i>
                    <span>Filter</span>
                    @if($departmentFilter !== 'All Departments' || (in_array(auth()->user()?->role?->role_name, ['ADMIN', 'MANAGER', 'ACCOUNTING']) && $periodFilter !== 'Today'))
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                    @if(in_array(auth()->user()?->role?->role_name, ['ADMIN', 'MANAGER', 'ACCOUNTING']))
                    <label class="form-label small mb-1 fw-semibold text-muted">Period</label>
                    <select name="period" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="Today" {{ $periodFilter === 'Today' ? 'selected' : '' }}>Today</option>
                        <option value="This Week" {{ $periodFilter === 'This Week' ? 'selected' : '' }}>This Week</option>
                        <option value="Monthly" {{ $periodFilter === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                    @endif

                    <label class="form-label small mb-1 fw-semibold text-muted">Department</label>
                    <select name="department" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="All Departments" {{ $departmentFilter === 'All Departments' ? 'selected' : '' }}>All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $departmentFilter === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                        <a href="{{ route('accounting.expenses') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                    </div>
                </div>
            </div>

            @if(in_array(auth()->user()?->role?->role_name, ['ADMIN', 'MANAGER', 'ACCOUNTING', 'FRONT_DESK', 'CAFETERIA']))
            <!-- ADD EXPENSE -->
            <button
                type="button"
                class="btn btn-primary px-3 d-flex align-items-center justify-content-center"
                data-bs-toggle="modal"
                data-bs-target="#addExpenseModal"
                style="height: 45px; font-size: 1.05rem;">

                <i class="fa-solid fa-plus me-1"></i>
                Add Expense

            </button>
            @endif

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-1 shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th class="ps-4">Date</th>
                    <th>Department</th>
                    <th>Purpose / Description</th>
                    <th>Category</th>
                    <th>Funded By</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($expenses as $exp)
                    <tr>
                        <td>{{ $exp->expense_date->toDateString() }}</td>
                        <td>{{ $exp->department }}</td>
                        <td>{{ $exp->purpose }}</td>
                        <td>{{ $exp->category }}</td>
                        <td>
                            @if($exp->funding_source === 'FRONT DESK')
                                <span class="badge bg-primary text-white">Front Desk</span>
                            @else
                                <span class="badge bg-secondary text-white">Cafeteria</span>
                            @endif
                        </td>
                        <td>
                            @if($exp->status === 'APPROVED')
                                <span class="badge bg-success">Approved</span>
                            @elseif($exp->status === 'PENDING')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $exp->status === 'APPROVED' ? 'text-danger' : 'text-muted' }}">
                            ₱{{ number_format($exp->amount, 2) }}
                        </td>
                        <td class="text-center">
                            @if($exp->status === 'PENDING')
                                <form action="{{ route('accounting.expenses.approve', $exp->expense_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2 small">
                                        <i class="fa-solid fa-check me-1"></i> Approve
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">None</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No operational expenses found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-end mt-3">
    {{ $expenses->links('pagination::bootstrap-5') }}
</div>

<!-- ADD EXPENSE MODAL -->
@push('modals')
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('accounting.expenses.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="addExpenseModalLabel">Record Operating Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Target Department *</label>
                        <select name="department" class="form-select" required>
                            <option value="">Select Target...</option>
                            <option value="Front Office">Front Office</option>
                            <option value="Housekeeping">Housekeeping</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Purchasing">Purchasing</option>
                            <option value="Food & Beverage">Food & Beverage</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Funding Source *</label>
                        <select name="funding_source" class="form-select" required>
                            <option value="FRONT DESK">Front Desk</option>
                            <option value="CAFETERIA">Cafeteria</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Requested By *</label>
                        <input type="text" name="requested_by" class="form-control" placeholder="Name or Office In-Charge..." required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Purpose *</label>
                    <input type="text" name="purpose" class="form-control" placeholder="Rush need for supplies..." required>
                </div>

                <div class="row mb-0">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Category *</label>
                        <select name="category" class="form-select" required>
                            <option value="Supplies">Supplies & Stationery</option>
                            <option value="Repairs">Repairs & Maintenance</option>
                            <option value="Payroll">Salaries & Wages</option>
                            <option value="Utilities">Utilities (Power, Water, Net)</option>
                            <option value="Other">Other Expenses</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Amount (PHP) *</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm px-4">Save Expense</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
(function () {
    const input = document.getElementById('expensesSearch');
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