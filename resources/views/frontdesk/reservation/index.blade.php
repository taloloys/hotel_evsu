@extends('layouts.app')

@section('title', 'Reservation - Don Felipe Hotel')
@section('pageTitle', 'Reservation')
@section('pageSubtitle', 'Manage room reservations and booking schedules.')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h5 class="fw-bold mb-1">Room Reservation Entry</h5>
                <small class="text-muted">
                    View, create, edit and manage room reservations.
                </small>
            </div>

            <!-- NEW RESERVATION BUTTON (OPENS MODAL) -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newReservationModal">
                <i class="fa-solid fa-plus me-2"></i>
                New Reservation
            </button>

        </div>

        <!-- FILTERS -->
        <div class="card bg-light border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Date From</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Date To</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Room Status</label>
                        <select class="form-select">
                            <option>All Rooms</option>
                            <option>Reserved</option>
                            <option>Occupied</option>
                            <option>Available</option>
                            <option>Out of Order</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Search Guest</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Guest Name / Ref No">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Guest Name</th>
                        <th>Reference No.</th>
                        <th>Arrival</th>
                        <th>Departure</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>206</td>
                        <td>Economy</td>
                        <td>Gerald Tulin</td>
                        <td>RSV-2026001</td>
                        <td>06/16/2026</td>
                        <td>06/17/2026</td>
                        <td><span class="badge bg-success">Reserved</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>305</td>
                        <td>Deluxe</td>
                        <td>Maria Santos</td>
                        <td>RSV-2026002</td>
                        <td>06/18/2026</td>
                        <td>06/20/2026</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>501</td>
                        <td>Suite</td>
                        <td>John Ramirez</td>
                        <td>RSV-2026003</td>
                        <td>06/20/2026</td>
                        <td>06/24/2026</td>
                        <td><span class="badge bg-info">Checked In</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="newReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <form>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Guest Name</label>
                            <input type="text" class="form-control" placeholder="Enter guest name">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reference No.</label>
                            <input type="text" class="form-control" placeholder="RSV-0001">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Room No.</label>
                            <input type="number" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Arrival Date</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Departure Date</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Room Type</label>
                            <select class="form-select">
                                <option>Economy</option>
                                <option>Deluxe</option>
                                <option>Suite</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option>Reserved</option>
                                <option>Pending</option>
                                <option>Checked In</option>
                            </select>
                        </div>

                    </div>

                </form>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-primary">
                    Save Reservation
                </button>
            </div>

        </div>

    </div>
</div>

@endsection