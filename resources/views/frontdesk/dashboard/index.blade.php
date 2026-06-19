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

    .vacant-room-badge {
        font-size: 0.95rem;
        padding: 0.5rem 0.85rem;
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

<!-- TODAY'S CHECK-IN & RESERVATIONS -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-plane-arrival text-primary"></i> Today's Check-In & Reservations
            </h5>
            @if($todayCheckIns->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" class="form-control" id="checkinSearch" placeholder="Search guest name...">
                    </div>
                    <select class="form-select form-select-sm" id="checkinSort" style="width: 180px;">
                        <option value="guest-asc">Guest (A-Z)</option>
                        <option value="guest-desc">Guest (Z-A)</option>
                        <option value="room-asc">Room (Low-High)</option>
                        <option value="room-desc">Room (High-Low)</option>
                        <option value="status-reserved">Reserved First</option>
                        <option value="status-checkedin">Checked In First</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($todayCheckIns->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="checkinTable">
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
                    <tbody id="checkinTableBody">
                        @foreach($todayCheckIns as $booking)
                            @if($booking->folio && $booking->folio->guest)
                            <tr
                                data-guest-name="{{ strtolower($booking->folio->guest->first_name . ' ' . $booking->folio->guest->last_name) }}"
                                data-room-number="{{ $booking->room->room_number }}"
                                data-status="{{ $booking->status }}"
                            >
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
                                    @elseif($booking->status === 'CHECKED_IN' && $booking->actual_check_in)
                                        <small class="text-muted">In at {{ $booking->actual_check_in->format('g:i A') }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mb-0 d-none" id="checkinNoResults">No guests match your search.</p>
        @else
            <p class="text-muted text-center py-4">No check-ins or reservations for today</p>
        @endif
    </div>
</div>

<!-- AVAILABLE ROOMS (NOT RESERVED OR IN USE) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-door-open text-success"></i> Available Rooms
                <span class="badge bg-success ms-2">{{ $vacantRooms->count() }}</span>
            </h5>
            @if($vacantRooms->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" class="form-control" id="vacantRoomSearch" placeholder="Search room or type...">
                    </div>
                    <select class="form-select form-select-sm" id="vacantRoomSort" style="width: 180px;">
                        <option value="room-asc">Room (Low-High)</option>
                        <option value="room-desc">Room (High-Low)</option>
                        <option value="type-asc">Type (A-Z)</option>
                        <option value="type-desc">Type (Z-A)</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($vacantRooms->count() > 0)
            <p class="text-muted small mb-3">Rooms that are ready and not reserved or currently in use.</p>
            <div class="d-flex flex-wrap gap-2" id="vacantRoomsList">
                @foreach($vacantRooms as $room)
                    <span
                        class="badge bg-success-subtle text-success border border-success-subtle vacant-room-badge"
                        data-room-number="{{ strtolower($room->room_number) }}"
                        data-room-type="{{ strtolower($room->room_type) }}"
                        data-room-sort="{{ $room->room_number }}"
                        data-type-sort="{{ strtolower($room->room_type) }}"
                    >
                        <i class="fa-solid fa-bed me-1"></i>
                        {{ $room->room_number }}
                        <small class="opacity-75">({{ $room->room_type }})</small>
                    </span>
                @endforeach
            </div>
            <p class="text-muted small mb-0 mt-3 d-none" id="vacantRoomsNoResults">No rooms match your search.</p>
        @else
            <p class="text-muted text-center py-4 mb-0">No vacant rooms available right now</p>
        @endif
    </div>
</div>

<!-- TODAY'S CHECK-OUT -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-plane-departure text-danger"></i> Today's Check-Out
            </h5>
            @if($todayCheckOuts->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" class="form-control" id="checkoutSearch" placeholder="Search guest name...">
                    </div>
                    <select class="form-select form-select-sm" id="checkoutSort" style="width: 180px;">
                        <option value="guest-asc">Guest (A-Z)</option>
                        <option value="guest-desc">Guest (Z-A)</option>
                        <option value="room-asc">Room (Low-High)</option>
                        <option value="room-desc">Room (High-Low)</option>
                        <option value="status-pending">Pending First</option>
                        <option value="status-done">Checked Out First</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($todayCheckOuts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="checkoutTable">
                    <thead class="table-light">
                        <tr>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Departure</th>
                            <th>Check-Out Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="checkoutTableBody">
                        @foreach($todayCheckOuts as $booking)
                            @if($booking->folio && $booking->folio->guest)
                            <tr
                                data-guest-name="{{ strtolower($booking->folio->guest->first_name . ' ' . $booking->folio->guest->last_name) }}"
                                data-room-number="{{ $booking->room->room_number }}"
                                data-status="{{ $booking->status }}"
                                data-checkout-time="{{ $booking->actual_check_out?->timestamp ?? 0 }}"
                            >
                                <td>
                                    <strong>{{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $booking->room->room_number }}</span>
                                </td>
                                <td>{{ $booking->room->room_type }}</td>
                                <td>
                                    @if($booking->status === 'CHECKED_IN')
                                        <span class="badge bg-info">PENDING CHECK-OUT</span>
                                    @elseif($booking->status === 'CHECKED_OUT')
                                        <span class="badge bg-success">CHECKED OUT</span>
                                    @endif
                                </td>
                                <td>{{ $booking->departure_date->format('M d') }} @ {{ $booking->departure_time }}</td>
                                <td>
                                    @if($booking->status === 'CHECKED_OUT' && $booking->actual_check_out)
                                        {{ $booking->actual_check_out->format('g:i A') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->status === 'CHECKED_IN')
                                        <button class="btn btn-sm btn-danger check-out-btn" data-booking-id="{{ $booking->booking_id }}" title="Check out guest">
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Check Out
                                        </button>
                                    @else
                                        <small class="text-success"><i class="fa-solid fa-check"></i> Done</small>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mb-0 d-none" id="checkoutNoResults">No guests match your search.</p>
        @else
            <p class="text-muted text-center py-4">No check-outs scheduled for today</p>
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

<!-- Check-Out Time Modal -->
<div class="modal fade" id="checkOutModal" tabindex="-1" aria-labelledby="checkOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="checkOutModalLabel">
                    <i class="fa-solid fa-arrow-right-from-bracket text-danger"></i> Check Out Guest
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted mb-3">Enter the check-out time. The room will be marked as needing cleaning.</p>
                <div class="row g-3">
                    <div class="col-7">
                        <label for="checkoutTimeInput" class="form-label fw-semibold">Time</label>
                        <input type="text" class="form-control" id="checkoutTimeInput" placeholder="e.g. 11:30" maxlength="5">
                        <div class="form-text">Use 12-hour format (1:00 – 12:59)</div>
                    </div>
                    <div class="col-5">
                        <label for="checkoutPeriodSelect" class="form-label fw-semibold">Period</label>
                        <select class="form-select" id="checkoutPeriodSelect">
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>
                <div class="invalid-feedback d-block d-none" id="checkoutTimeError"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCheckOutBtn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Confirm Check Out
                </button>
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
    let checkOutModal = null;
    let selectedRoom = null;
    let pendingCheckOutBookingId = null;

    document.addEventListener('DOMContentLoaded', function() {
        roomActionModal = new bootstrap.Modal(document.getElementById('roomActionModal'));
        checkOutModal = new bootstrap.Modal(document.getElementById('checkOutModal'));

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
                openCheckOutModal(this.getAttribute('data-booking-id'));
            });
        });

        document.getElementById('confirmCheckOutBtn')?.addEventListener('click', submitCheckOut);

        const checkoutSearch = document.getElementById('checkoutSearch');
        const checkoutSort = document.getElementById('checkoutSort');
        if (checkoutSearch) {
            checkoutSearch.addEventListener('input', filterAndSortCheckouts);
        }
        if (checkoutSort) {
            checkoutSort.addEventListener('change', filterAndSortCheckouts);
        }

        const checkinSearch = document.getElementById('checkinSearch');
        const checkinSort = document.getElementById('checkinSort');
        if (checkinSearch) {
            checkinSearch.addEventListener('input', filterAndSortCheckins);
        }
        if (checkinSort) {
            checkinSort.addEventListener('change', filterAndSortCheckins);
        }

        const vacantRoomSearch = document.getElementById('vacantRoomSearch');
        const vacantRoomSort = document.getElementById('vacantRoomSort');
        if (vacantRoomSearch) {
            vacantRoomSearch.addEventListener('input', filterAndSortVacantRooms);
        }
        if (vacantRoomSort) {
            vacantRoomSort.addEventListener('change', filterAndSortVacantRooms);
        }
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

    function openCheckOutModal(bookingId) {
        pendingCheckOutBookingId = bookingId;

        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const period = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;

        document.getElementById('checkoutTimeInput').value = `${hours}:${minutes}`;
        document.getElementById('checkoutPeriodSelect').value = period;
        document.getElementById('checkoutTimeError').classList.add('d-none');

        checkOutModal.show();
    }

    function validateCheckoutTime(time) {
        return /^(0?[1-9]|1[0-2]):[0-5][0-9]$/.test(time);
    }

    function submitCheckOut() {
        const timeInput = document.getElementById('checkoutTimeInput');
        const periodSelect = document.getElementById('checkoutPeriodSelect');
        const errorEl = document.getElementById('checkoutTimeError');
        const checkoutTime = timeInput.value.trim();

        if (!validateCheckoutTime(checkoutTime)) {
            errorEl.textContent = 'Please enter a valid time (e.g. 11:30).';
            errorEl.classList.remove('d-none');
            timeInput.classList.add('is-invalid');
            return;
        }

        timeInput.classList.remove('is-invalid');
        errorEl.classList.add('d-none');

        postJson('{{ route("frontdesk.booking.check-out") }}', {
            booking_id: pendingCheckOutBookingId,
            checkout_time: checkoutTime,
            checkout_period: periodSelect.value
        })
            .then(data => {
                checkOutModal.hide();
                roomActionModal?.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => {
                errorEl.textContent = error.message;
                errorEl.classList.remove('d-none');
            });
    }

    function filterAndSortCheckins() {
        const tbody = document.getElementById('checkinTableBody');
        if (!tbody) {
            return;
        }

        const searchTerm = (document.getElementById('checkinSearch')?.value || '').toLowerCase().trim();
        const sortBy = document.getElementById('checkinSort')?.value || 'guest-asc';
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let visibleCount = 0;

        rows.forEach(row => {
            const guestName = row.getAttribute('data-guest-name') || '';
            const matches = guestName.includes(searchTerm);
            row.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount++;
            }
        });

        const visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const guestA = a.getAttribute('data-guest-name') || '';
            const guestB = b.getAttribute('data-guest-name') || '';
            const roomA = parseInt(a.getAttribute('data-room-number'), 10) || 0;
            const roomB = parseInt(b.getAttribute('data-room-number'), 10) || 0;
            const statusA = a.getAttribute('data-status');
            const statusB = b.getAttribute('data-status');

            switch (sortBy) {
                case 'guest-desc':
                    return guestB.localeCompare(guestA);
                case 'room-asc':
                    return roomA - roomB;
                case 'room-desc':
                    return roomB - roomA;
                case 'status-reserved':
                    if (statusA === statusB) return guestA.localeCompare(guestB);
                    return statusA === 'RESERVED' ? -1 : 1;
                case 'status-checkedin':
                    if (statusA === statusB) return guestA.localeCompare(guestB);
                    return statusA === 'CHECKED_IN' ? -1 : 1;
                case 'guest-asc':
                default:
                    return guestA.localeCompare(guestB);
            }
        });

        visibleRows.forEach(row => tbody.appendChild(row));

        const noResults = document.getElementById('checkinNoResults');
        if (noResults) {
            noResults.classList.toggle('d-none', visibleCount > 0);
        }
    }

    function filterAndSortVacantRooms() {
        const list = document.getElementById('vacantRoomsList');
        if (!list) {
            return;
        }

        const searchTerm = (document.getElementById('vacantRoomSearch')?.value || '').toLowerCase().trim();
        const sortBy = document.getElementById('vacantRoomSort')?.value || 'room-asc';
        const items = Array.from(list.querySelectorAll('[data-room-number]'));
        let visibleCount = 0;

        items.forEach(item => {
            const roomNumber = item.getAttribute('data-room-number') || '';
            const roomType = item.getAttribute('data-room-type') || '';
            const matches = roomNumber.includes(searchTerm) || roomType.includes(searchTerm);
            item.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount++;
            }
        });

        const visibleItems = items.filter(item => item.style.display !== 'none');

        visibleItems.sort((a, b) => {
            const roomA = parseInt(a.getAttribute('data-room-sort'), 10) || 0;
            const roomB = parseInt(b.getAttribute('data-room-sort'), 10) || 0;
            const typeA = a.getAttribute('data-type-sort') || '';
            const typeB = b.getAttribute('data-type-sort') || '';

            switch (sortBy) {
                case 'room-desc':
                    return roomB - roomA;
                case 'type-asc':
                    return typeA.localeCompare(typeB) || roomA - roomB;
                case 'type-desc':
                    return typeB.localeCompare(typeA) || roomA - roomB;
                case 'room-asc':
                default:
                    return roomA - roomB;
            }
        });

        visibleItems.forEach(item => list.appendChild(item));

        const noResults = document.getElementById('vacantRoomsNoResults');
        if (noResults) {
            noResults.classList.toggle('d-none', visibleCount > 0);
        }
    }

    function filterAndSortCheckouts() {
        const tbody = document.getElementById('checkoutTableBody');
        if (!tbody) {
            return;
        }

        const searchTerm = (document.getElementById('checkoutSearch')?.value || '').toLowerCase().trim();
        const sortBy = document.getElementById('checkoutSort')?.value || 'guest-asc';
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let visibleCount = 0;

        rows.forEach(row => {
            const guestName = row.getAttribute('data-guest-name') || '';
            const matches = guestName.includes(searchTerm);
            row.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount++;
            }
        });

        const visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const guestA = a.getAttribute('data-guest-name') || '';
            const guestB = b.getAttribute('data-guest-name') || '';
            const roomA = parseInt(a.getAttribute('data-room-number'), 10) || 0;
            const roomB = parseInt(b.getAttribute('data-room-number'), 10) || 0;
            const statusA = a.getAttribute('data-status');
            const statusB = b.getAttribute('data-status');

            switch (sortBy) {
                case 'guest-desc':
                    return guestB.localeCompare(guestA);
                case 'room-asc':
                    return roomA - roomB;
                case 'room-desc':
                    return roomB - roomA;
                case 'status-pending':
                    if (statusA === statusB) return guestA.localeCompare(guestB);
                    return statusA === 'CHECKED_IN' ? -1 : 1;
                case 'status-done':
                    if (statusA === statusB) return guestA.localeCompare(guestB);
                    return statusA === 'CHECKED_OUT' ? -1 : 1;
                case 'guest-asc':
                default:
                    return guestA.localeCompare(guestB);
            }
        });

        visibleRows.forEach(row => tbody.appendChild(row));

        const noResults = document.getElementById('checkoutNoResults');
        if (noResults) {
            noResults.classList.toggle('d-none', visibleCount > 0);
        }
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
        openCheckOutModal(bookingId);
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