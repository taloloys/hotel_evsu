@extends('layouts.app')

@section('title', 'Billing Management')
@section('pageTitle', 'Billing & Invoices')
@section('pageSubtitle', 'Manage guest bills, invoices, and payment status')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Invoices</div>
                <div class="fw-bold fs-3">248</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Paid</div>
                <div class="fw-bold fs-3 text-success">192</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Pending</div>
                <div class="fw-bold fs-3 text-warning">38</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Unpaid Balance</div>
                <div class="fw-bold fs-3 text-danger">₱56,200</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<div class="card border-0 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Invoices</div>
            <small class="text-muted">All guest billing records</small>
        </div>

        <div class="d-flex gap-2">

            <input type="text" class="form-control form-control-sm" style="width: 220px;"
                   placeholder="Search invoice / guest">

            <select class="form-select form-select-sm" style="width: 160px;">
                <option>All Status</option>
                <option>Paid</option>
                <option>Pending</option>
                <option>Unpaid</option>
            </select>

            <button class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>
                New Invoice
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
                    <th>Invoice No</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>INV-1001</td>
                    <td>Juan Dela Cruz</td>
                    <td>Room 101</td>
                    <td>2026-06-18</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td class="text-end fw-bold">₱2,500</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>INV-1002</td>
                    <td>Maria Santos</td>
                    <td>Room 205</td>
                    <td>2026-06-18</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td class="text-end fw-bold">₱3,800</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>INV-1003</td>
                    <td>John Smith</td>
                    <td>Room 310</td>
                    <td>2026-06-17</td>
                    <td><span class="badge bg-danger">Unpaid</span></td>
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