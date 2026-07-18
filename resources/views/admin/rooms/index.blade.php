@extends('layouts.app')

@section('title', 'Rooms Management')
@section('pageTitle', 'Rooms Management')
@section('pageSubtitle', 'Manage hotel rooms, status, and availability')

@section('content')

{{-- TOAST CONTAINER --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">

    @if(session('success'))
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->has('cannot_disable_occupied'))
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('cannot_disable_occupied') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->has('cannot_activate_status'))
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('cannot_activate_status') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->any() && !$errors->has('cannot_disable_occupied') && !$errors->has('cannot_activate_status'))
        <div id="validationToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Error:</strong> {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Total Rooms</div>
                    <h4 class="mb-0 fw-bold">{{ $totalCount }}</h4>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-door-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Occupied</div>
                    <h4 class="mb-0 fw-bold text-danger">{{ $occupiedCount }}</h4>
                </div>
                <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-bed fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Available</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $availableCount }}</h4>
                </div>
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-door-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Maintenance / Inactive</div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $maintenanceCount }} <span class="text-muted fs-6">/ {{ $inactiveCount }}</span></h4>
                </div>
                <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-screwdriver-wrench fs-4"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ROOMS TABLE CARD -->
<div class="card border-0 shadow-sm">

    <!-- HEADER / ACTIONS -->
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <div>
            <h5 class="fw-bold mb-0">Hotel Rooms</h5>
            <small class="text-muted">Monitor room status, floor assignments, and rates</small>
        </div>

        <!-- RIGHT ACTIONS WRAPPER -->
        <div class="d-flex justify-content-end align-items-center gap-2">

            <!-- FORM (FUNCTIONALITY UNCHANGED) -->
            <form action="{{ route('admin.rooms') }}"
                method="GET"
                class="d-flex align-items-center gap-2 m-0">

                <!-- SEARCH -->
                <div style="width: 340px;">
                    <div class="input-group"
                        style="border: 1px solid #000000; border-radius: 6px; overflow: hidden; height: 38px;">

                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>

                        <input type="text"
                            id="roomSearchInput"
                            name="search"
                            class="form-control border-0 shadow-none"
                            placeholder="Search room number..."
                            value="{{ $filters['search'] }}"
                            autocomplete="off">
                    </div>
                </div>

                <!-- FILTER -->
                <div class="dropdown">

                    <button type="button"
                            class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3 position-relative"
                            data-bs-toggle="dropdown"
                            style="height: 38px; border-radius: 6px;">

                        <i class="fa-solid fa-filter"></i>
                        <span>Filter</span>

                        @if($filters['room_type'] !== 'all' || $filters['status'] !== 'all' || $filters['is_active'] !== 'all')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        @endif

                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                        style="min-width: 300px; border-radius: 10px;">

                        <!-- ROOM TYPE -->
                        <label class="form-label small mb-1 fw-semibold">Room Type</label>
                        <select id="filterTypeSelect"
                                name="room_type"
                                class="form-select mb-3"
                                style="height: 38px; border-radius: 6px;">

                            <option value="all" @selected($filters['room_type'] === 'all')>All Types</option>

                            @foreach($roomTypes as $type)
                                <option value="{{ $type }}" @selected($filters['room_type'] === $type)>
                                    {{ $type }}
                                </option>
                            @endforeach

                        </select>

                        <!-- STATUS -->
                        <label class="form-label small mb-1 fw-semibold">Status</label>
                        <select id="filterStatusSelect"
                                name="status"
                                class="form-select mb-3"
                                style="height: 38px; border-radius: 6px;">

                            <option value="all" @selected($filters['status'] === 'all')>All Statuses</option>
                            <option value="AVAILABLE" @selected($filters['status'] === 'AVAILABLE')>Available</option>
                            <option value="OCCUPIED" @selected($filters['status'] === 'OCCUPIED')>Occupied</option>
                            <option value="RESERVED" @selected($filters['status'] === 'RESERVED')>Reserved</option>
                            <option value="CLEANING" @selected($filters['status'] === 'CLEANING')>Cleaning</option>
                            <option value="MAINTENANCE" @selected($filters['status'] === 'MAINTENANCE')>Maintenance</option>

                        </select>

                        <!-- ACTIVE STATUS -->
                        <label class="form-label small mb-1 fw-semibold">Active / Disabled</label>
                        <select id="filterActiveSelect"
                                name="is_active"
                                class="form-select mb-3"
                                style="height: 38px; border-radius: 6px;">

                            <option value="all" @selected($filters['is_active'] === 'all')>All Statuses</option>
                            <option value="active" @selected($filters['is_active'] === 'active')>Active</option>
                            <option value="disabled" @selected($filters['is_active'] === 'disabled')>Disabled</option>

                        </select>

                        <!-- BUTTONS -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-50" style="height: 38px; border-radius: 6px;">
                                Apply
                            </button>

                            <a href="{{ route('admin.rooms') }}"
                            class="btn btn-light w-50 d-flex align-items-center justify-content-center"
                            style="height: 38px; border-radius: 6px;">
                                Reset
                            </a>
                        </div>

                    </div>
                </div>

            </form>

            <!-- ADD ROOM BUTTON -->
            <button class="btn btn-primary d-flex align-items-center gap-2 px-3"
                    style="height: 38px; border-radius: 6px;"
                    data-bs-toggle="modal"
                    data-bs-target="#addRoomModal">

                <i class="fa-solid fa-plus"></i>
                <span>Add Room</span>

            </button>

        </div>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table id="roomsTable" class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Room</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Status</th>
                        <th>Rate</th>
                        <th>System Status</th>
                        <th class="text-center" style="width: 140px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rooms as $room)
                        @php
                            $statusBadge = match($room->status) {
                                'AVAILABLE' => 'bg-success',
                                'OCCUPIED' => 'bg-danger',
                                'RESERVED' => 'bg-primary',
                                'CLEANING' => 'bg-info text-dark',
                                'MAINTENANCE' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };

                            $statusLabel = match($room->status) {
                                'AVAILABLE' => 'Available',
                                'OCCUPIED' => 'Occupied',
                                'RESERVED' => 'Reserved',
                                'CLEANING' => 'Cleaning',
                                'MAINTENANCE' => 'Maintenance',
                                default => $room->status
                            };

                            $isOccupiedOrReserved = in_array($room->status, ['OCCUPIED', 'RESERVED'], true);
                        @endphp
                        <tr 
                            data-roomnumber="{{ strtolower($room->room_number) }}"
                            data-type="{{ strtolower($room->room_type) }}"
                            data-status="{{ $room->status }}"
                            data-active="{{ $room->is_active ? 'active' : 'disabled' }}"
                            @if(!$room->is_active) class="opacity-75 bg-light-subtle" @endif>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width:42px;height:42px;">
                                        <i class="fa-solid fa-bed text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Room {{ $room->room_number }}</div>
                                        <small class="text-muted">ID: R-{{ str_pad($room->room_id, 3, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $room->room_type }}</td>
                            <td>{{ $room->floor }}</td>

                            <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>

                            <td>₱{{ number_format($room->base_rate, 2) }}</td>

                            <td>
                                @if($room->is_active)
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Disabled</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- VIEW DETAILS --}}
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm px-2"
                                            title="View details"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewRoomModal"
                                            data-room-id="{{ $room->room_id }}"
                                            data-room-number="{{ $room->room_number }}"
                                            data-room-type="{{ $room->room_type }}"
                                            data-floor="{{ $room->floor }}"
                                            data-base-rate="{{ $room->base_rate }}"
                                            data-status-label="{{ $statusLabel }}"
                                            data-status-class="{{ $statusBadge }}"
                                            data-active="{{ $room->is_active ? 'Active' : 'Disabled' }}"
                                            data-active-class="{{ $room->is_active ? 'bg-success' : 'bg-danger' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    {{-- EDIT ROOM --}}
                                    <button type="button" 
                                            class="btn btn-outline-warning btn-sm px-2"
                                            title="Edit room"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editRoomModal"
                                            data-room-id="{{ $room->room_id }}"
                                            data-room-number="{{ $room->room_number }}"
                                            data-room-type="{{ $room->room_type }}"
                                            data-base-rate="{{ $room->base_rate }}"
                                            data-status="{{ $room->status }}"
                                            data-update-url="{{ route('admin.rooms.update', $room) }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    {{-- TOGGLE STATUS --}}
                                    @if($isOccupiedOrReserved && $room->is_active)
                                        <button class="btn btn-outline-secondary btn-sm px-2"
                                                title="Cannot disable an occupied or reserved room"
                                                data-bs-toggle="tooltip"
                                                disabled>
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            @if($room->is_active)
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-2" title="Disable room">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-outline-success btn-sm px-2" title="Enable room">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="noResultsRow">
                            <td colspan="7" class="text-center py-4 text-muted">
                                No rooms found in the system.
                            </td>
                        </tr>
                    @endforelse

                    {{-- Shown by JS when filters hide all visible rows --}}
                    <tr id="noFilterResultsRow" style="display:none;">
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-magnifying-glass me-2"></i>No rooms match your search or filter.
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-3 gap-2">
            <div class="small text-muted">
                Showing {{ $rooms->firstItem() ?? 0 }} to {{ $rooms->lastItem() ?? 0 }} of {{ $rooms->total() }} rooms
            </div>
            @if($rooms->hasPages())
                <div class="m-0">
                    {{ $rooms->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>

</div>

<!-- =========================================================
     ADD ROOM MODAL
     ========================================================= -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addRoomModalLabel">
                    <i class="fa-solid fa-plus me-2 text-primary"></i>
                    Add New Room
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- FORM (UNCHANGED ACTION + METHOD + CSRF) -->
            <form method="POST" action="{{ route('admin.rooms.store') }}">
                @csrf

                <div class="modal-body px-4 py-4">

                    <!-- ROOM NUMBER (NAME/ID UNCHANGED) -->
                    <div class="mb-3">
                        <label for="add_room_number" class="form-label fw-semibold">Room Number</label>
                        <input type="text"
                               id="add_room_number"
                               name="room_number"
                               class="form-control form-control-lg"
                               placeholder="e.g. 101"
                               required>

                        <small class="text-muted">
                            The floor is parsed dynamically from the room number prefix.
                        </small>
                    </div>

                    <!-- ROOM TYPE (UNCHANGED NAME) -->
                    <div class="mb-3">
                        <label for="add_room_type" class="form-label fw-semibold">Room Type</label>

                        <input type="text"
                               id="add_room_type"
                               name="room_type"
                               class="form-control form-control-lg"
                               list="roomTypesList"
                               placeholder="e.g. Single Room"
                               required>

                        <datalist id="roomTypesList">
                            @foreach($roomTypes as $type)
                                <option value="{{ $type }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- BASE RATE (UNCHANGED NAME) -->
                    <div class="mb-3">
                        <label for="add_base_rate" class="form-label fw-semibold">Base Rate (PHP)</label>

                        <input type="number"
                               id="add_base_rate"
                               name="base_rate"
                               class="form-control form-control-lg"
                               placeholder="2500"
                               step="0.01"
                               min="0"
                               required>
                    </div>

                    <!-- STATUS (UNCHANGED NAME + VALUES) -->
                    <div class="mb-3">
                        <label for="add_status" class="form-label fw-semibold">Initial Status</label>

                        <select id="add_status"
                                name="status"
                                class="form-select form-select-lg"
                                required>

                            <option value="AVAILABLE" selected>Available</option>
                            <option value="CLEANING">Cleaning</option>
                            <option value="MAINTENANCE">Maintenance</option>

                        </select>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1"></i>
                        Save Room
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- =========================================================
     VIEW DETAILS MODAL
     ========================================================= -->
<div class="modal fade" id="viewRoomModal" tabindex="-1" aria-labelledby="viewRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="viewRoomModalLabel">
                    <i class="fa-solid fa-bed me-2 text-primary"></i>Room Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-3">

                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:72px;height:72px;">
                        <i class="fa-solid fa-door-open fa-2x text-secondary"></i>
                    </div>
                    <h5 class="fw-bold mb-1" id="view-room-number">—</h5>
                    <span class="badge" id="view-status-badge">—</span>
                </div>

                <div class="list-group list-group-flush rounded">
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted small">Room Type</span>
                        <span class="fw-semibold small" id="view-room-type">—</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted small">Floor Assignment</span>
                        <span class="fw-semibold small" id="view-floor">—</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted small">Base Rate (Nightly)</span>
                        <span class="fw-semibold small text-primary" id="view-base-rate">—</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 border-0">
                        <span class="text-muted small">System Status</span>
                        <span id="view-active-badge" class="badge">—</span>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- =========================================================
     EDIT ROOM MODAL
     ========================================================= -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="editRoomModalLabel">
                    <i class="fa-solid fa-pen me-2 text-warning"></i>
                    Edit Room
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- FORM (UNCHANGED FUNCTIONALITY) -->
            <form method="POST" id="editRoomForm" action="">
                @csrf
                @method('PATCH')

                <div class="modal-body px-4 py-4">

                    <!-- ROOM NUMBER (UNCHANGED NAME/ID) -->
                    <div class="mb-3">
                        <label for="edit_room_number" class="form-label fw-semibold">Room Number</label>
                        <input type="text"
                               id="edit_room_number"
                               name="room_number"
                               class="form-control form-control-lg"
                               required>

                        <small class="text-muted">
                            The floor is parsed dynamically from the room number prefix.
                        </small>
                    </div>

                    <!-- ROOM TYPE -->
                    <div class="mb-3">
                        <label for="edit_room_type" class="form-label fw-semibold">Room Type</label>

                        <input type="text"
                               id="edit_room_type"
                               name="room_type"
                               class="form-control form-control-lg"
                               list="roomTypesList"
                               required>
                    </div>

                    <!-- BASE RATE -->
                    <div class="mb-3">
                        <label for="edit_base_rate" class="form-label fw-semibold">Base Rate (PHP)</label>

                        <input type="number"
                               id="edit_base_rate"
                               name="base_rate"
                               class="form-control form-control-lg"
                               step="0.01"
                               min="0"
                               required>
                    </div>

                    <!-- STATUS -->
                    <div class="mb-3">
                        <label for="edit_status" class="form-label fw-semibold">Status</label>

                        <select id="edit_status"
                                name="status"
                                class="form-select form-select-lg"
                                required>

                            <option value="AVAILABLE">Available</option>
                            <option value="OCCUPIED">Occupied</option>
                            <option value="RESERVED">Reserved</option>
                            <option value="CLEANING">Cleaning</option>
                            <option value="MAINTENANCE">Maintenance</option>

                        </select>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-warning text-white px-4">
                        <i class="fa-solid fa-save me-1"></i>
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        // ── Auto-show any pending toasts & initialize tooltips ────────────────
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // ── View Details modal population ─────────────────────────────────────
        const viewRoomModal = document.getElementById('viewRoomModal');
        if (viewRoomModal) {
            viewRoomModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('view-room-number').textContent = 'Room ' + btn.dataset.roomNumber;
                document.getElementById('view-room-type').textContent   = btn.dataset.roomType;
                document.getElementById('view-floor').textContent       = btn.dataset.floor;
                document.getElementById('view-base-rate').textContent   = '₱' + Number(btn.dataset.baseRate).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const statusBadge = document.getElementById('view-status-badge');
                statusBadge.textContent = btn.dataset.statusLabel;
                statusBadge.className   = 'badge ' + btn.dataset.statusClass;

                const activeBadge = document.getElementById('view-active-badge');
                activeBadge.textContent = btn.dataset.active;
                activeBadge.className   = 'badge ' + btn.dataset.activeClass;
            });
        }

        // ── Edit Room modal population ────────────────────────────────────────
        const editRoomModal = document.getElementById('editRoomModal');
        if (editRoomModal) {
            editRoomModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('edit_room_number').value = btn.dataset.roomNumber;
                document.getElementById('edit_room_type').value   = btn.dataset.roomType;
                document.getElementById('edit_base_rate').value   = btn.dataset.baseRate;
                document.getElementById('edit_status').value      = btn.dataset.status;
                document.getElementById('editRoomForm').action    = btn.dataset.updateUrl;
            });
        }

        // Debounce auto-submit for server-side search input
        const searchInput = document.getElementById('roomSearchInput');
        if (searchInput) {
            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const form = searchInput.closest('form');
            if (form) {
                searchInput.addEventListener('input', debounce(function () {
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }, 500));
            }
        }
    })();
</script>
@endpush