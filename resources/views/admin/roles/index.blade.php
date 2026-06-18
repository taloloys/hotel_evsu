@extends('layouts.app')

@section('title', 'Roles Management')
@section('pageTitle', 'Roles Management')
@section('pageSubtitle', 'Manage hotel staff roles and access levels')

@section('content')

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h5 class="fw-bold mb-0">Hotel Roles</h5>
        <small class="text-muted">Control staff access across departments</small>
    </div>

    <button class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i>
        Add Role
    </button>

</div>

<!-- ROLE LIST -->
<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <!-- ADMIN -->
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

            <div class="d-flex align-items-center">

                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                     style="width:40px;height:40px;">
                    <i class="fa-solid fa-user-shield text-dark"></i>
                </div>

                <div>
                    <div class="fw-semibold">Administrator</div>
                    <small class="text-muted">Full system access</small>
                </div>

            </div>

            <span class="badge bg-dark">System</span>

        </div>

        <!-- FRONT DESK -->
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

            <div class="d-flex align-items-center">

                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                     style="width:40px;height:40px;">
                    <i class="fa-solid fa-bell-concierge text-primary"></i>
                </div>

                <div>
                    <div class="fw-semibold">Front Desk</div>
                    <small class="text-muted">Reservations & guest handling</small>
                </div>

            </div>

            <span class="badge bg-primary">Operations</span>

        </div>

        <!-- ACCOUNTING -->
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

            <div class="d-flex align-items-center">

                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                     style="width:40px;height:40px;">
                    <i class="fa-solid fa-receipt text-info"></i>
                </div>

                <div>
                    <div class="fw-semibold">Accounting</div>
                    <small class="text-muted">Billing & payments</small>
                </div>

            </div>

            <span class="badge bg-info text-dark">Finance</span>

        </div>

        <!-- COFFEE SHOP -->
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

            <div class="d-flex align-items-center">

                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                     style="width:40px;height:40px;">
                    <i class="fa-solid fa-mug-hot text-success"></i>
                </div>

                <div>
                    <div class="fw-semibold">Coffee Shop</div>
                    <small class="text-muted">POS & orders management</small>
                </div>

            </div>

            <span class="badge bg-success">POS</span>

        </div>

        <!-- HOUSEKEEPING -->
        <div class="d-flex justify-content-between align-items-center p-3">

            <div class="d-flex align-items-center">

                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                     style="width:40px;height:40px;">
                    <i class="fa-solid fa-broom text-warning"></i>
                </div>

                <div>
                    <div class="fw-semibold">Housekeeping</div>
                    <small class="text-muted">Room cleaning & maintenance</small>
                </div>

            </div>

            <span class="badge bg-warning text-dark">Service</span>

        </div>

    </div>

</div>

<!-- FOOTER NOTE -->
<div class="mt-3 text-muted small">
    Roles define what each hotel staff member can access in the system.
</div>

@endsection