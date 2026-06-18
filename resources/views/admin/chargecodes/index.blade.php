@extends('layouts.app')

@section('title', 'Charge Codes')
@section('pageTitle', 'Charge Codes')
@section('pageSubtitle', 'Manage hotel billing charges like laundry, minibar, and services')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">


</div>

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Charges</div>
                    <div class="fw-bold fs-5">12</div>
                </div>
                <i class="fa-solid fa-receipt text-primary fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Active</div>
                    <div class="fw-bold fs-5">10</div>
                </div>
                <i class="fa-solid fa-circle-check text-success fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Inactive</div>
                    <div class="fw-bold fs-5">2</div>
                </div>
                <i class="fa-solid fa-circle-xmark text-danger fs-4"></i>
            </div>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end align-items-center mb-3 gap-2 flex-wrap">

    <!-- SEARCH (SMALL) -->
    <div style="width: 220px;">
        <input type="text"
               class="form-control form-control-sm"
               placeholder="Search charges...">
    </div>

    <!-- FILTER ICON ONLY -->
    <button class="btn btn-outline-secondary btn-sm"
            data-bs-toggle="collapse"
            data-bs-target="#filterBox">
        <i class="fa-solid fa-filter"></i>
    </button>

    <!-- ADD CHARGE -->
    <button class="btn btn-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#addChargeModal">
        <i class="fa-solid fa-plus me-1"></i>
        Add Charge
    </button>

</div>

<div class="collapse mb-3" id="filterBox">
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="row g-2">

                <div class="col-md-6">
                    <select class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option>Laundry</option>
                        <option>Room Service</option>
                        <option>Minibar</option>
                        <option>Housekeeping</option>
                        <option>Others</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <select class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Charge Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th width="120">Action</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td class="fw-semibold">Laundry Service</td>
                    <td>Laundry</td>
                    <td>₱150.00</td>
                    <td>
                        <span class="badge bg-success">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td class="fw-semibold">Room Cleaning Extra</td>
                    <td>Housekeeping</td>
                    <td>₱200.00</td>
                    <td>
                        <span class="badge bg-success">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td class="fw-semibold">Mini Bar Water</td>
                    <td>Minibar</td>
                    <td>₱50.00</td>
                    <td>
                        <span class="badge bg-success">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Active
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td class="fw-semibold">Late Checkout Fee</td>
                    <td>Others</td>
                    <td>₱500.00</td>
                    <td>
                        <span class="badge bg-secondary">
                            <i class="fa-solid fa-circle-xmark me-1"></i>
                            Inactive
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

<div class="modal fade" id="addChargeModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Charge Name</label>
                    <input type="text" class="form-control" placeholder="e.g. Laundry Service">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select">
                        <option>Laundry</option>
                        <option>Room Service</option>
                        <option>Minibar</option>
                        <option>Housekeeping</option>
                        <option>Others</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control" placeholder="0.00">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i>
                    Save Charge
                </button>
            </div>

        </div>

    </div>

</div>

@endsection