@extends('layouts.app')

@section('title', 'Payments')
@section('pageTitle', 'Payments & Transactions')
@section('pageSubtitle', 'Track and manage all guest payments and settlements')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Payments</div>
                <div class="fw-bold fs-3">₱185,320</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Cash Payments</div>
                <div class="fw-bold fs-3">₱72,500</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Card Payments</div>
                <div class="fw-bold fs-3">₱96,820</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Pending Payments</div>
                <div class="fw-bold fs-3 text-warning">₱15,000</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<div class="card border-0 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Payment Records</div>
            <small class="text-muted">All financial settlements from guests</small>
        </div>

        <div class="d-flex gap-2">

            <!-- SEARCH -->
            <input type="text" class="form-control form-control-sm"
                   style="width: 220px;"
                   placeholder="Search reference / guest">

            <!-- FILTER -->
            <select class="form-select form-select-sm" style="width: 160px;">
                <option>All Methods</option>
                <option>Cash</option>
                <option>Card</option>
                <option>Room Charge</option>
            </select>

            <!-- ADD PAYMENT -->
            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>
                Record Payment
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

                <tr>
                    <td>PAY-1001</td>
                    <td>Juan Dela Cruz</td>
                    <td><span class="badge bg-success">Cash</span></td>
                    <td>2026-06-18</td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td class="text-end fw-bold">₱2,500</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>PAY-1002</td>
                    <td>Maria Santos</td>
                    <td><span class="badge bg-primary">Card</span></td>
                    <td>2026-06-18</td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td class="text-end fw-bold">₱3,800</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>PAY-1003</td>
                    <td>John Smith</td>
                    <td><span class="badge bg-warning text-dark">Room Charge</span></td>
                    <td>2026-06-17</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td class="text-end fw-bold text-danger">₱6,500</td>
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