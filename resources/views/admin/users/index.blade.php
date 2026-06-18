@extends('layouts.app')

@section('title', 'Users Management')
@section('pageTitle', 'Users Management')
@section('pageSubtitle', 'Manage hotel staff accounts and role assignments')

@section('content')

<!-- KPI CARDS -->
<div class="row g-3 mb-3">

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Users</div>
                    <h4 class="mb-0">3</h4>
                </div>
                <i class="fa-solid fa-users fa-2x text-primary"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Active Users</div>
                    <h4 class="mb-0 text-success">3</h4>
                </div>
                <i class="fa-solid fa-user-check fa-2x text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Inactive Users</div>
                    <h4 class="mb-0 text-danger">0</h4>
                </div>
                <i class="fa-solid fa-user-slash fa-2x text-danger"></i>
            </div>
        </div>
    </div>

</div>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h5 class="fw-bold mb-0">Hotel Staff Users</h5>
        <small class="text-muted">Create accounts and manage roles & access</small>
    </div>

    <!-- RIGHT ACTIONS (SEARCH + FILTER + ADD USER) -->
    <div class="d-flex gap-2 align-items-center">

        <!-- SEARCH -->
         <div style="width: 220px;">
            <input type="text"
                class="form-control form-control-sm"
                placeholder="Search charges...">
        </div>

        <!-- FILTER ICON DROPDOWN -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                <i class="fa-solid fa-filter"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 240px;">

                <label class="form-label small mb-1">Role</label>
                <select class="form-select form-select-sm mb-2">
                    <option>All Roles</option>
                    <option>Admin</option>
                    <option>Front Desk</option>
                    <option>Accounting</option>
                    <option>Coffee Shop</option>
                </select>

                <label class="form-label small mb-1">Status</label>
                <select class="form-select form-select-sm mb-3">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm w-50">Apply</button>
                    <button class="btn btn-light btn-sm w-50">Reset</button>
                </div>

            </div>
        </div>

        <!-- ADD USER (BIGGER BUTTON) -->
        <button class="btn btn-primary px-1 py-1 fw-semibold"
                data-bs-toggle="modal"
                data-bs-target="#addUserModal">
            <i class="fa-solid fa-user-plus me-2"></i>
            Add User
        </button>

    </div>

</div>

<!-- USERS TABLE -->
<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:42px;height:42px;">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Front Desk Officer</div>
                                    <small class="text-muted">ID: U-001</small>
                                </div>
                            </div>
                        </td>
                        <td>frontdesk@hotel.com</td>
                        <td><span class="badge bg-primary">Front Desk</span></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-user-gear"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-user-slash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:42px;height:42px;">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Cashier</div>
                                    <small class="text-muted">ID: U-002</small>
                                </div>
                            </div>
                        </td>
                        <td>cashier@hotel.com</td>
                        <td><span class="badge bg-info text-dark">Accounting</span></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-user-gear"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-user-slash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:42px;height:42px;">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">System Admin</div>
                                    <small class="text-muted">ID: U-003</small>
                                </div>
                            </div>
                        </td>
                        <td>admin@hotel.com</td>
                        <td><span class="badge bg-dark">Admin</span></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-user-gear"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-user-slash"></i></button>
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- NOTE -->
<div class="mt-3 text-muted small">
    Note: Users must have a role assigned before accessing hotel modules.
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select">
                        <option>Admin</option>
                        <option>Front Desk</option>
                        <option>Accounting</option>
                        <option>Coffee Shop</option>
                    </select>
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
                <button class="btn btn-primary">Save User</button>
            </div>

        </div>

    </div>

</div>

@endsection