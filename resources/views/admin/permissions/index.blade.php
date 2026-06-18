@extends('layouts.app')

@section('title', 'Permissions Management')
@section('pageTitle', 'Permissions')
@section('pageSubtitle', 'Control system access for hotel operations')

@section('content')

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h5 class="fw-bold mb-0">Permissions</h5>
        <small class="text-muted">Manage access rights for hotel staff roles</small>
    </div>

    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
        <i class="fa-solid fa-plus me-1"></i>
        Add Permission
    </button>

</div>

<!-- PERMISSION LIST -->
<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <div class="list-group list-group-flush">

            <!-- ITEM -->
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">

                <div class="d-flex align-items-center">

                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px;height:42px;">
                        <i class="fa-solid fa-user text-secondary"></i>
                    </div>

                    <div class="fw-semibold">manage-users</div>

                </div>

                <span class="badge bg-dark">System</span>

            </div>

            <!-- ITEM -->
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">

                <div class="d-flex align-items-center">

                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px;height:42px;">
                        <i class="fa-solid fa-bell-concierge text-primary"></i>
                    </div>

                    <div class="fw-semibold">manage-reservations</div>

                </div>

                <span class="badge bg-primary">Front Desk</span>

            </div>

            <!-- ITEM -->
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">

                <div class="d-flex align-items-center">

                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px;height:42px;">
                        <i class="fa-solid fa-receipt text-info"></i>
                    </div>

                    <div class="fw-semibold">view-folio</div>

                </div>

                <span class="badge bg-info text-dark">Accounting</span>

            </div>

            <!-- ITEM -->
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">

                <div class="d-flex align-items-center">

                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px;height:42px;">
                        <i class="fa-solid fa-bell-concierge text-primary"></i>
                    </div>

                    <div class="fw-semibold">process-checkout</div>

                </div>

                <span class="badge bg-primary">Front Desk</span>

            </div>

            <!-- ITEM -->
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">

                <div class="d-flex align-items-center">

                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:42px;height:42px;">
                        <i class="fa-solid fa-boxes-stacked text-success"></i>
                    </div>

                    <div class="fw-semibold">manage-inventory</div>

                </div>

                <span class="badge bg-success">Inventory</span>

            </div>

        </div>

    </div>

</div>

<!-- FOOTER -->
<div class="mt-3 text-muted small">
    Permissions define what actions hotel staff can perform in the system.
</div>

<!-- ADD PERMISSION MODAL -->
<div class="modal fade" id="addPermissionModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Permission Name</label>
                    <input type="text" class="form-control" placeholder="e.g. manage-users">
                </div>

                <div class="mb-3">
                    <label class="form-label">Module</label>
                    <select class="form-select">
                        <option>System</option>
                        <option>Front Desk</option>
                        <option>Accounting</option>
                        <option>Inventory</option>
                        <option>POS</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-warning">
                    <i class="fa-solid fa-save me-1"></i>
                    Save
                </button>
            </div>

        </div>

    </div>

</div>

@endsection