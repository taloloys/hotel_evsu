@extends('layouts.app')

@section('title', 'Rooms Management')
@section('pageTitle', 'Rooms Management')
@section('pageSubtitle', 'Manage hotel rooms, status, and availability')

@section('content')

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h5 class="fw-bold mb-0">Hotel Rooms</h5>
        <small class="text-muted">Monitor room status and manage availability</small>
    </div>

</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Rooms</div>
                    <h4 class="mb-0">120</h4>
                </div>
                <i class="fa-solid fa-door-open fs-3 text-primary"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Occupied</div>
                    <h4 class="mb-0">85</h4>
                </div>
                <i class="fa-solid fa-bed fs-3 text-danger"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Available</div>
                    <h4 class="mb-0">30</h4>
                </div>
                <i class="fa-solid fa-door-open fs-3 text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Maintenance</div>
                    <h4 class="mb-0">5</h4>
                </div>
                <i class="fa-solid fa-screwdriver-wrench fs-3 text-warning"></i>
            </div>
        </div>
    </div>

</div>

<!-- ROOMS TABLE -->
<div class="card border-0 shadow-sm">

    <!-- TOP RIGHT BUTTON -->
    <div class="card-header bg-white border-0 d-flex justify-content-end align-items-center py-3">

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="fa-solid fa-plus me-1"></i>
            Add Room
        </button>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Status</th>
                        <th>Rate</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- ROOM 1 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:42px;height:42px;">
                                    <i class="fa-solid fa-bed text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Room 101</div>
                                    <small class="text-muted">Standard Room</small>
                                </div>
                            </div>
                        </td>

                        <td>Single</td>
                        <td>1st Floor</td>

                        <td><span class="badge bg-success">Available</span></td>

                        <td>₱2,500</td>

                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- ROOM 2 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:42px;height:42px;">
                                    <i class="fa-solid fa-bed text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Room 205</div>
                                    <small class="text-muted">Deluxe Room</small>
                                </div>
                            </div>
                        </td>

                        <td>Double</td>
                        <td>2nd Floor</td>

                        <td><span class="badge bg-danger">Occupied</span></td>

                        <td>₱3,800</td>

                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-outline-warning"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- ROOM 3 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:42px;height:42px;">
                                    <i class="fa-solid fa-bed text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Room 310</div>
                                    <small class="text-muted">Suite</small>
                                </div>
                            </div>
                        </td>

                        <td>Suite</td>
                        <td>3rd Floor</td>

                        <td><span class="badge bg-warning text-dark">Maintenance</span></td>

                        <td>₱6,500</td>

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

<!-- ADD ROOM MODAL -->
<div class="modal fade" id="addRoomModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" class="form-control" placeholder="e.g. 101">
                </div>

                <div class="mb-3">
                    <label class="form-label">Room Type</label>
                    <select class="form-select">
                        <option>Standard</option>
                        <option>Deluxe</option>
                        <option>Suite</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Floor</label>
                    <input type="text" class="form-control" placeholder="e.g. 1st Floor">
                </div>

                <div class="mb-3">
                    <label class="form-label">Rate (PHP)</label>
                    <input type="number" class="form-control" placeholder="2500">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option>Available</option>
                        <option>Occupied</option>
                        <option>Maintenance</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i>
                    Save Room
                </button>
            </div>

        </div>

    </div>

</div>

@endsection