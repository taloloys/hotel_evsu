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
<form action="{{ route('accounting.expenses') }}" method="GET" class="card border-1 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Expense Records</div>
            <small class="text-muted">Operational spending by department</small>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">

            <!-- FILTER -->
            <div style="width: 220px; border: 1px solid #000000; border-radius: .375rem;">
                <select
                    name="department"
                    class="form-select border-0"
                    onchange="this.form.submit()">

                    <option value="All Departments"
                        {{ $departmentFilter === 'All Departments' ? 'selected' : '' }}>
                        All Departments
                    </option>

                    @foreach($departments as $dept)
                        <option value="{{ $dept }}"
                            {{ $departmentFilter === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- SEARCH -->
            <div style="width: 340px; border: 1px solid #000000; border-radius: .375rem;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">
                        <i class="fa-solid fa-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        name="search"
                        class="form-control border-0"
                        placeholder="Search expense or category..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            <!-- SEARCH BUTTON -->
            <button type="submit" class="btn btn-primary px-3">
                <i class="fa-solid fa-search me-1"></i>
                Search
            </button>

            <!-- ADD EXPENSE -->
            <button
                type="button"
                class="btn btn-primary px-3"
                data-bs-toggle="modal"
                data-bs-target="#addExpenseModal">

                <i class="fa-solid fa-plus me-1"></i>
                Add Expense

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
                    <th>Date</th>
                    <th>Department</th>
                    <th>Description</th>
                    <th>Category</th>
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
                        <td>{{ $exp->description }}</td>
                        <td>{{ $exp->category }}</td>
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
                        <td colspan="7" class="text-center text-muted py-4">No operational expenses found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

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
                        <label class="form-label small text-muted">Department *</label>
                        <input type="text" name="department" class="form-control" placeholder="Housekeeping, Maintenance, HR..." required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Description *</label>
                    <input type="text" name="description" class="form-control" placeholder="Office Stationery, AC Repair, Electricity Bill..." required>
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

@endsection