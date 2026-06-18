@extends('layouts.app')

@section('title', 'Expenses')
@section('pageTitle', 'Operating Expenses')
@section('pageSubtitle', 'Track hotel operational costs and departmental spending')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Expenses</div>
                <div class="fw-bold fs-3 text-danger">₱34,120</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Utilities</div>
                <div class="fw-bold fs-3">₱12,500</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Salaries</div>
                <div class="fw-bold fs-3">₱13,420</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Supplies</div>
                <div class="fw-bold fs-3">₱8,200</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<div class="card border-0 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Expense Records</div>
            <small class="text-muted">Operational spending by department</small>
        </div>

        <div class="d-flex gap-2">

            <!-- SEARCH -->
            <input type="text"
                   class="form-control form-control-sm"
                   style="width: 220px;"
                   placeholder="Search expense / department">

            <!-- FILTER -->
            <select class="form-select form-select-sm" style="width: 160px;">
                <option>All Departments</option>
                <option>Housekeeping</option>
                <option>Front Office</option>
                <option>Kitchen</option>
                <option>Maintenance</option>
            </select>

            <!-- ADD EXPENSE -->
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>
                Add Expense
            </button>

        </div>

    </div>

</div>


<!-- TABLE -->
<div class="card border-0 shadow-sm">

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

                <tr>
                    <td>2026-06-18</td>
                    <td>Housekeeping</td>
                    <td>Cleaning Supplies</td>
                    <td>Supplies</td>
                    <td><span class="badge bg-success">Approved</span></td>
                    <td class="text-end fw-bold">₱2,500</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>2026-06-18</td>
                    <td>Maintenance</td>
                    <td>Aircon Repair</td>
                    <td>Repairs</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td class="text-end fw-bold text-warning">₱4,800</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>2026-06-17</td>
                    <td>HR</td>
                    <td>Staff Salaries</td>
                    <td>Payroll</td>
                    <td><span class="badge bg-success">Approved</span></td>
                    <td class="text-end fw-bold">₱13,420</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection