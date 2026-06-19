@extends('layouts.app')

@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard')
@section('pageSubtitle', 'Hotel operations overview')

@section('content')

<style>
    .room-dashboard {
        background: #f5f5f5;
        border-radius: 20px;
        padding: 20px;
    }

    .room-type-btn {
        width: 100%;
        border: none;
        background: #ffffff;
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-weight: 500;
        transition: .3s;
    }

    .room-type-btn.active {
        background: #0d6efd;
        color: white;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(75px, 1fr));
        gap: 18px;
    }

    .room-box {
        height: 75px;
        border-radius: 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 22px;
        cursor: pointer;
        transition: .3s;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
    }

    .room-box:hover {
        transform: translateY(-3px);
    }

    .available {
        background: #2ebd39;
        color: white;
    }

    .occupied {
        background: white;
        color: #7ea6ff;
    }

    .reserved {
        background: #ffc107;
        color: white;
    }

    .cleaning {
        background: #fd7e14;
        color: white;
    }

    .maintenance {
        background: #6c757d;
        color: white;
    }

    .room-number {
        position: absolute;
        bottom: 5px;
        font-size: 11px;
        font-weight: 600;
    }

    .room-wrapper {
        position: relative;
    }
</style>

<!-- KPI CARDS -->
<div class="row g-4 mb-4">

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Today's Arrivals</small>
                        <h2 class="fw-bold mt-2">{{ $todayArrivals }}</h2>
                    </div>
                    <i class="fa-solid fa-plane-arrival fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Today's Departures</small>
                        <h2 class="fw-bold mt-2">{{ $todayDepartures }}</h2>
                    </div>
                    <i class="fa-solid fa-plane-departure fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Occupied Rooms</small>
                        <h2 class="fw-bold mt-2">{{ $occupiedRooms }}</h2>
                    </div>
                    <i class="fa-solid fa-bed fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Available Rooms</small>
                        <h2 class="fw-bold mt-2">{{ $availableRooms }}</h2>
                    </div>
                    <i class="fa-solid fa-door-open fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- TODAY'S BOOKINGS -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="fw-bold mb-0">
            <i class="fa-solid fa-calendar-check text-primary"></i> Today's Check-In/Check-Out
        </h5>
    </div>
    <div class="card-body">
        @if($todayBookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayBookings as $booking)
                            @if($booking->folio && $booking->folio->guest)
                            <tr>
                                <td>
                                    <strong>{{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $booking->room->room_number }}</span>
                                </td>
                                <td>{{ $booking->room->room_type }}</td>
                                <td>
                                    @if($booking->status === 'RESERVED')
                                        <span class="badge bg-warning">RESERVED</span>
                                    @elseif($booking->status === 'CHECKED_IN')
                                        <span class="badge bg-info">CHECKED IN</span>
                                    @elseif($booking->status === 'CHECKED_OUT')
                                        <span class="badge bg-success">CHECKED OUT</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $booking->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $booking->arrival_date->format('M d') }} @ {{ $booking->arrival_time }}</td>
                                <td>{{ $booking->departure_date->format('M d') }} @ {{ $booking->departure_time }}</td>
                                <td>
                                    @if($booking->status === 'RESERVED')
                                        <button class="btn btn-sm btn-success check-in-btn" data-booking-id="{{ $booking->booking_id }}" title="Check in guest">
                                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Check In
                                        </button>
                                    @elseif($booking->status === 'CHECKED_IN')
                                        <button class="btn btn-sm btn-danger check-out-btn" data-booking-id="{{ $booking->booking_id }}" title="Check out guest">
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Check Out
                                        </button>
                                    @elseif($booking->status === 'CHECKED_OUT' && $booking->actual_check_out)
                                        <small class="text-muted">{{ $booking->actual_check_out->format('g:i A') }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-4">No bookings for today</p>
        @endif
    </div>
</div>

<!-- ROOM MONITORING -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="fw-bold mb-0">
            Hotel Room Monitoring
        </h5>
    </div>

    <div class="card-body room-dashboard">

        <!-- LEGEND -->
        <div class="d-flex flex-wrap gap-4 mb-4">

            <div>
                <span class="legend-dot bg-success"></span>
                Available Room
            </div>

            <div>
                <span class="legend-dot" style="background-color: #7ea6ff;"></span>
                Occupied Room
            </div>

            <div>
                <span class="legend-dot bg-warning"></span>
                Reserved Room
            </div>

            <div>
                <span class="legend-dot" style="background-color: #fd7e14;"></span>
                Needs Cleaning ({{ $needsCleaningRooms }})
            </div>

            <div>
                <span class="legend-dot bg-secondary"></span>
                Under Maintenance ({{ $maintenanceRooms }})
            </div>

        </div>

        <div class="row">

            <!-- LEFT MENU -->
            <div class="col-lg-2 mb-3">
                @php
                    $roomTypes = array_keys($roomsByType);
                @endphp

                @forelse($roomTypes as $index => $type)
                    <button class="room-type-btn room-filter-btn {{ $index === 0 ? 'active' : '' }}" data-room-type="{{ $type }}">
                        {{ $type }}
                    </button>
                @empty
                    <p class="text-muted">No room types available</p>
                @endforelse
            </div>

            <!-- ROOM GRID -->
            <div class="col-lg-10">

                <div class="room-grid" id="roomGrid">
                    <p class="text-muted">Loading rooms...</p>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Room Action Modal -->
<div class="modal fade" id="roomActionModal" tabindex="-1" aria-labelledby="roomActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="roomActionModalLabel">Room <span id="modalRoomNumber"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-1" id="modalRoomType"></p>
                <p class="mb-3"><span class="badge" id="modalRoomStatus"></span></p>
                <div id="modalRoomActions"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const roomsData = @json($roomsByType);
    let roomActionModal = null;
    let selectedRoom = null;

    document.addEventListener('DOMContentLoaded', function() {
        roomActionModal = new bootstrap.Modal(document.getElementById('roomActionModal'));

        const firstActiveBtn = document.querySelector('.room-filter-btn.active');
        if (firstActiveBtn) {
            renderRoomGrid(roomsData[firstActiveBtn.getAttribute('data-room-type')] || []);
        }

        document.querySelectorAll('.room-filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const roomType = this.getAttribute('data-room-type');

                document.querySelectorAll('.room-filter-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                renderRoomGrid(roomsData[roomType] || []);
            });
        });

        document.querySelectorAll('.check-in-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                checkInGuest(this.getAttribute('data-booking-id'));
            });
        });

        document.querySelectorAll('.check-out-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                checkOutGuest(this.getAttribute('data-booking-id'));
            });
        });
    });

    function getRoomStatusClass(status) {
        const normalized = status.toUpperCase();
        if (normalized === 'AVAILABLE') return 'available';
        if (normalized === 'OCCUPIED') return 'occupied';
        if (normalized === 'CLEANING') return 'cleaning';
        if (normalized === 'MAINTENANCE') return 'maintenance';
        return 'reserved';
    }

    function getRoomIcon(status) {
        const normalized = status.toUpperCase();
        if (normalized === 'AVAILABLE') return 'fa-bed';
        if (normalized === 'OCCUPIED') return 'fa-user';
        if (normalized === 'CLEANING') return 'fa-broom';
        if (normalized === 'MAINTENANCE') return 'fa-wrench';
        return 'fa-lock';
    }

    function getRoomStatusLabel(status) {
        const normalized = status.toUpperCase();
        if (normalized === 'CLEANING') return 'NEEDS CLEANING';
        if (normalized === 'MAINTENANCE') return 'UNDER MAINTENANCE';
        return normalized;
    }

    function getRoomStatusBadgeClass(status) {
        const normalized = status.toUpperCase();
        if (normalized === 'AVAILABLE') return 'bg-success';
        if (normalized === 'OCCUPIED') return 'bg-primary';
        if (normalized === 'CLEANING') return 'bg-warning text-dark';
        if (normalized === 'MAINTENANCE') return 'bg-secondary';
        return 'bg-warning text-dark';
    }

    function renderRoomGrid(rooms) {
        const roomGrid = document.getElementById('roomGrid');
        roomGrid.innerHTML = '';

        if (rooms.length === 0) {
            roomGrid.innerHTML = '<p class="text-muted">No rooms available for this type</p>';
            return;
        }

        rooms.forEach(room => {
            const statusClass = getRoomStatusClass(room.status);
            const iconClass = getRoomIcon(room.status);

            const roomBox = document.createElement('div');
            roomBox.className = 'room-wrapper';
            roomBox.innerHTML = `
                <div class="room-box ${statusClass}" title="Room ${room.room_number} - ${getRoomStatusLabel(room.status)}" style="cursor: pointer;">
                    <i class="fa-solid ${iconClass}"></i>
                </div>
                <div class="room-number">${room.room_number}</div>
            `;

            roomBox.querySelector('.room-box').addEventListener('click', function() {
                openRoomModal(room);
            });

            roomGrid.appendChild(roomBox);
        });
    }

    function postJson(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(payload)
        }).then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            return data;
        });
    }

    function checkInGuest(bookingId) {
        if (!confirm('Proceed with check-in?')) {
            return;
        }

        postJson('{{ route("frontdesk.booking.check-in") }}', { booking_id: bookingId })
            .then(data => {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => showAlert('error', error.message));
    }

    function checkOutGuest(bookingId) {
        if (!confirm('Proceed with check-out? The room will be marked as needing cleaning.')) {
            return;
        }

        postJson('{{ route("frontdesk.booking.check-out") }}', { booking_id: bookingId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => showAlert('error', error.message));
    }

    function markRoomCleaned(roomId) {
        postJson('{{ route("frontdesk.room.mark-cleaned") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => showAlert('error', error.message));
    }

    function markRoomForCleaning(roomId) {
        postJson('{{ route("frontdesk.room.mark-for-cleaning") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => showAlert('error', error.message));
    }

    function markRoomForMaintenance(roomId) {
        postJson('{{ route("frontdesk.room.mark-maintenance") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => showAlert('error', error.message));
    }

    function markMaintenanceComplete(roomId) {
        postJson('{{ route("frontdesk.room.maintenance-complete") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => showAlert('error', error.message));
    }

    function openRoomModal(room) {
        selectedRoom = room;

        document.getElementById('modalRoomNumber').textContent = room.room_number;
        document.getElementById('modalRoomType').textContent = room.room_type;

        const statusBadge = document.getElementById('modalRoomStatus');
        const status = room.status.toUpperCase();
        statusBadge.textContent = getRoomStatusLabel(status);
        statusBadge.className = 'badge ' + getRoomStatusBadgeClass(status);

        const actions = document.getElementById('modalRoomActions');
        actions.innerHTML = '';

        if (status === 'OCCUPIED' && room.active_booking) {
            actions.innerHTML = `
                <div class="alert alert-light border mb-3">
                    <i class="fa-solid fa-user text-primary"></i>
                    <strong>Guest:</strong> ${room.active_booking.guest_name || 'Unknown'}
                </div>
                <button class="btn btn-danger w-100" id="modalCheckOutBtn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Check Out Guest
                </button>
            `;
            document.getElementById('modalCheckOutBtn').addEventListener('click', function() {
                checkOutGuest(room.active_booking.booking_id);
            });
        } else if (status === 'OCCUPIED') {
            actions.innerHTML = `
                <div class="alert alert-warning-subtle border border-warning-subtle mb-0">
                    Room is occupied but no active booking was found. Use the Check-In/Check-Out table above.
                </div>
            `;
        } else if (status === 'CLEANING') {
            actions.innerHTML = `
                <div class="alert alert-warning-subtle border border-warning-subtle mb-3">
                    <i class="fa-solid fa-broom"></i> Housekeeping needs to clean this room before the next guest.
                </div>
                <button class="btn btn-success w-100" id="modalMarkCleanedBtn">
                    <i class="fa-solid fa-check"></i> Mark as Cleaned (Available)
                </button>
            `;
            document.getElementById('modalMarkCleanedBtn').addEventListener('click', function() {
                markRoomCleaned(room.room_id);
            });
        } else if (status === 'MAINTENANCE') {
            actions.innerHTML = `
                <div class="alert alert-secondary-subtle border border-secondary-subtle mb-3">
                    <i class="fa-solid fa-wrench"></i> This room is out of order for repairs. Not available for guests.
                </div>
                <button class="btn btn-success w-100" id="modalMaintenanceCompleteBtn">
                    <i class="fa-solid fa-check"></i> Maintenance Complete (Available)
                </button>
            `;
            document.getElementById('modalMaintenanceCompleteBtn').addEventListener('click', function() {
                markMaintenanceComplete(room.room_id);
            });
        } else if (status === 'AVAILABLE') {
            actions.innerHTML = `
                <p class="text-muted small mb-3">This room is ready for guests.</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-warning" id="modalMarkCleaningBtn">
                        <i class="fa-solid fa-broom"></i> Send for Cleaning
                    </button>
                    <button class="btn btn-outline-secondary" id="modalMarkMaintenanceBtn">
                        <i class="fa-solid fa-wrench"></i> Put Under Maintenance
                    </button>
                </div>
            `;
            document.getElementById('modalMarkCleaningBtn').addEventListener('click', function() {
                markRoomForCleaning(room.room_id);
            });
            document.getElementById('modalMarkMaintenanceBtn').addEventListener('click', function() {
                markRoomForMaintenance(room.room_id);
            });
        } else if (status === 'RESERVED') {
            actions.innerHTML = `
                <div class="alert alert-warning-subtle border border-warning-subtle mb-0">
                    <i class="fa-solid fa-lock"></i> This room is reserved for an upcoming arrival. Use the Check-In/Check-Out table above to check in the guest.
                </div>
            `;
        } else {
            actions.innerHTML = `<p class="text-muted mb-0">No actions available for this room.</p>`;
        }

        roomActionModal.show();
    }

    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }
</script>
@endpush