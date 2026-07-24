@extends('layouts.app')

@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard')
@section('pageSubtitle', 'Hotel operations overview')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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

    .legend-item {
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        border: 1px solid #eaeaea;
        transition: all 0.3s ease;
    }

    .legend-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }

    .legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
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
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 20px;
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
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
        line-height: 1;
    }

    .vacant-room-badge {
        font-size: 0.95rem;
        padding: 0.5rem 0.85rem;
    }

    .room-toolbar-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .room-toolbar-search {
        width: 360px;
        height: 45px;
        border: 1px solid #000;
        border-radius: 0.375rem;
        overflow: hidden;
        background: #fff;
    }

    .room-toolbar-search .input-group-text,
    .room-toolbar-search .form-control {
        height: 100%;
        border: 0;
        box-shadow: none;
    }

    .room-toolbar-search .form-control {
        background: #fff;
    }

    .room-toolbar-select {
        width: 200px;
        height: 45px;
        border: 1px solid #000 !important;
        border-radius: 8px;
        box-shadow: none !important;
        outline: none;
    }
</style>

@if($errors->has('shift'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('shift') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($overdueGuests->count() > 0)
<div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
    <div class="d-flex align-items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation fs-4 mt-1 flex-shrink-0"></i>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-2">
                {{ $overdueGuests->count() }} Overdue {{ Str::plural('Guest', $overdueGuests->count()) }} — Past Departure Date
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0 align-middle">
                    <tbody>
                        @foreach($overdueGuests as $b)
                        <tr>
                            <td class="ps-0 fw-semibold">
                                {{ $b->folio?->guest?->first_name }} {{ $b->folio?->guest?->last_name }}
                            </td>
                            <td>
                                <span class="badge bg-dark">Room {{ $b->room?->room_number }}</span>
                            </td>
                            <td class="text-danger fw-semibold">
                                Due out: {{ $b->departure_date->format('M d, Y') }}
                            </td>
                            <td>
                                @if($b->folio)
                                    <a href="{{ route('frontdesk.guest-folio.show', $b->folio->folio_id) }}"
                                       class="btn btn-sm btn-outline-light">
                                        <i class="fa-solid fa-clock me-1"></i> Extend Stay
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- SHIFT MANAGEMENT BAR -->
<div class="card border-0 shadow-sm mb-4 rounded-4 {{ $activeShift ? 'border-start border-4 border-success' : 'border-start border-4 border-warning' }}">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ $activeShift ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="width: 48px; height: 48px;">
                    <i class="fa-solid fa-cash-register fs-4"></i>
                </div>
                <div>
                    @if($activeShift)
                        <h6 class="fw-bold mb-1">
                            Active Shift Session: <span class="text-success">{{ $activeShift->schedule ? $activeShift->schedule->shift_name : 'Unscheduled Shift' }}</span>
                        </h6>
                        <small class="text-muted">
                            Opened at {{ $activeShift->start_time->format('M d, g:i A') }} | Live Sales: 
                            <strong class="text-success">₱{{ number_format($shiftSales['payments'], 2) }}</strong> (Cash: ₱{{ number_format($shiftSales['cash'], 2) }}, Card: ₱{{ number_format($shiftSales['card'], 2) }}) | Charges posted: <strong class="text-danger">₱{{ number_format($shiftSales['charges'], 2) }}</strong>
                        </small>
                    @else
                        <h6 class="fw-bold mb-1 text-warning">
                            ⚠️ No Active Shift Session
                        </h6>
                        <small class="text-muted">
                            Please open a cashier shift drawer session to perform reservation check-ins, check-outs, and billing postings.
                        </small>
                    @endif
                </div>
            </div>
            <div>
                @if($activeShift)
                    <button class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
                        <i class="fa-solid fa-power-off me-1"></i> Close Shift
                    </button>
                @else
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#openShiftModal">
                        <i class="fa-solid fa-play me-1"></i> Open Shift / Drawer
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- KPI CARDS -->
<div class="row g-4 mb-4">

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0" style="width: 52px; height: 52px;">
                    <i class="fa-solid fa-plane-arrival fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Today's Arrivals</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $todayArrivals }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger flex-shrink-0" style="width: 52px; height: 52px;">
                    <i class="fa-solid fa-plane-departure fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Today's Departures</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $todayDepartures }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info flex-shrink-0" style="width: 52px; height: 52px;">
                    <i class="fa-solid fa-bed fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Occupied Rooms</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $occupiedRooms }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success flex-shrink-0" style="width: 52px; height: 52px;">
                    <i class="fa-solid fa-door-open fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Available Rooms</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $availableRooms }}</h3>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- TODAY'S CHECK-IN & RESERVATIONS -->
<div class="card border-1 shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-plane-arrival text-primary"></i> Today's Check-In & Reservations
            </h5>
            @if($todayCheckIns->count() > 0)
                <div class="room-toolbar-group">
                    <div class="input-group room-toolbar-search">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-search"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            id="checkinSearch"
                            placeholder="Search guest name..."
                        >
                    </div>
                    <select
                        id="checkinSort"
                        class="form-select shadow-none room-toolbar-select"
                    >
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
                                        <span class="badge-status badge-status-reserved">RESERVED</span>
                                    @elseif($booking->status === 'CHECKED_IN')
                                        <span class="badge-status badge-status-checkedin">CHECKED IN</span>
                                    @else
                                        <span class="badge-status badge-status-maintenance">{{ $booking->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $booking->arrival_date->format('M d') }} @ {{ $booking->arrival_time }}</td>
                                <td>{{ $booking->departure_date?->format('M d') ?? 'Open' }} @ {{ $booking->departure_time ?? '—' }}</td>
                                <td>
                                    @if($booking->status === 'RESERVED')
                                        <button class="btn btn-sm btn-success check-in-btn" 
                                                data-booking-id="{{ $booking->booking_id }}" 
                                                data-guest-name="{{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}"
                                                data-room-number="{{ $booking->room->room_number }}"
                                                title="Check in guest">
                                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i>Check In
                                        </button>
                                    @elseif($booking->status === 'CHECKED_IN' && $booking->actual_check_in)
                                        <span class="badge-status badge-status-checkedin" title="Guest checked in">
                                            <i class="fa-solid fa-check-double me-1"></i>In at {{ $booking->actual_check_in->format('g:i A') }}
                                        </span>
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
            <div class="fd-empty-state">
                <i class="fa-solid fa-calendar-check d-block"></i>
                <div class="fw-semibold text-dark">No check-ins or reservations for today</div>
                <small class="text-muted">Guests scheduled for check-in today will appear here.</small>
            </div>
        @endif
    </div>
</div>

<!-- AVAILABLE ROOMS (NOT RESERVED OR IN USE) -->
<div class="card border-1 shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-door-open text-success"></i> Available Rooms
                <span class="badge bg-success ms-2">{{ $vacantRooms->count() }}</span>
            </h5>
            @if($vacantRooms->count() > 0)
                <div class="room-toolbar-group">

                    <!-- Search -->
                    <div class="input-group room-toolbar-search">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-search"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            id="vacantRoomSearch"
                            placeholder="Search room or type..."
                        >
                    </div>

                    <!-- Sort -->
                    <select
                        id="vacantRoomSort"
                        class="form-select shadow-none room-toolbar-select"
                    >
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
            <div id="vacantRoomsList">
                @foreach($vacantRooms->groupBy('room_type') as $type => $roomsInType)
                    <div class="vacant-room-group mb-3" data-room-type-group="{{ strtolower($type) }}">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-layer-group text-success small"></i>
                            <span class="fw-bold text-dark small">{{ $type }}</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem;">{{ $roomsInType->count() }} available</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 ps-2">
                            @foreach($roomsInType as $room)
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle vacant-room-badge"
                                    data-room-number="{{ strtolower($room->room_number) }}"
                                    data-room-type="{{ strtolower($room->room_type) }}"
                                    data-room-sort="{{ $room->room_number }}"
                                    data-type-sort="{{ strtolower($room->room_type) }}"
                                >
                                    <i class="fa-solid fa-bed me-1"></i>
                                    {{ $room->room_number }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-muted small mb-0 mt-3 d-none" id="vacantRoomsNoResults">No rooms match your search.</p>
        @else
            <div class="fd-empty-state">
                <i class="fa-solid fa-door-closed d-block"></i>
                <div class="fw-semibold text-dark">No vacant rooms available right now</div>
                <small class="text-muted">All rooms are currently occupied or under maintenance.</small>
            </div>
        @endif
    </div>
</div>

<!-- OCCUPIED ROOMS -->
<div class="card border-1 shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-bed text-primary"></i> Occupied Rooms
                <span class="badge bg-primary ms-2">{{ $occupiedRoomList->count() }}</span>
            </h5>

            @if($occupiedRoomList->count() > 0)
                <div class="room-toolbar-group">

                    <!-- Search -->
                    <div class="input-group room-toolbar-search">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-search"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            id="occupiedRoomSearch"
                            placeholder="Search room or guest..."
                        >
                    </div>

                    <!-- Sort -->
                    <select
                        id="occupiedRoomSort"
                        class="form-select shadow-none room-toolbar-select"
                    >
                        <option value="room-asc">Room (Low-High)</option>
                        <option value="room-desc">Room (High-Low)</option>
                        <option value="guest-asc">Guest (A-Z)</option>
                        <option value="guest-desc">Guest (Z-A)</option>
                    </select>

                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($occupiedRoomList->count() > 0)
            <p class="text-muted small mb-3">Rooms currently checked in, including walk-in registrations.</p>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="occupiedRoomsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Guest</th>
                            <th>Folio</th>
                            <th>Departure</th>
                        </tr>
                    </thead>
                    <tbody id="occupiedRoomsTableBody">
                        @foreach($occupiedRoomList as $room)
                            <tr
                                data-room-number="{{ strtolower($room['room_number']) }}"
                                data-room-type="{{ strtolower($room['room_type']) }}"
                                data-guest-name="{{ strtolower($room['guest_name'] ?? '') }}"
                                data-room-sort="{{ $room['room_number'] }}"
                            >
                                <td><span class="badge bg-primary">{{ $room['room_number'] }}</span></td>
                                <td>{{ $room['room_type'] }}</td>
                                <td><strong>{{ $room['guest_name'] ?: '—' }}</strong></td>
                                <td>{{ $room['folio_number'] ?: '—' }}</td>
                                <td>
                                    @if($room['is_overdue'])
                                        <span class="badge bg-danger me-1">OVERDUE</span>
                                    @endif
                                    {{ $room['departure_date']?->format('M d, Y') ?? 'Open Stay' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mb-0 mt-3 d-none" id="occupiedRoomsNoResults">No occupied rooms match your search.</p>
        @else
            <p class="text-muted text-center py-4 mb-0">No occupied rooms right now</p>
        @endif
    </div>
</div>

<!-- TODAY'S CHECK-OUT -->
<div class="card border-1 shadow-sm mb-4">
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
                                @php
                                    $isOverdueRow = $booking->status === 'CHECKED_IN'
                                        && $booking->departure_date
                                        && $booking->departure_date->lt(now()->startOfDay());
                                @endphp
                                <td>
                                    @if($isOverdueRow)
                                        <span class="badge bg-danger">OVERDUE</span>
                                    @elseif($booking->status === 'CHECKED_IN')
                                        <span class="badge bg-info">PENDING CHECK-OUT</span>
                                    @elseif($booking->status === 'CHECKED_OUT')
                                        <span class="badge bg-success">CHECKED OUT</span>
                                    @endif
                                </td>
                                <td>{{ $booking->departure_date?->format('M d') ?? 'Open' }} @ {{ $booking->departure_time ?? '—' }}</td>
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
<div class="card border-1 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="fw-bold mb-0">
            Hotel Room Monitoring
        </h5>
    </div>

    <div class="card-body room-dashboard">

        <!-- LEGEND -->
        <div class="d-flex flex-wrap gap-3 mb-4">

            <div class="legend-item">
                <span class="legend-dot bg-success"></span>
                Available Room
            </div>

            <div class="legend-item">
                <span class="legend-dot" style="background-color: #7ea6ff;"></span>
                Occupied Room
            </div>

            <div class="legend-item">
                <span class="legend-dot bg-warning"></span>
                Reserved Room
            </div>

            <div class="legend-item">
                <span class="legend-dot" style="background-color: #fd7e14;"></span>
                Needs Cleaning ({{ $needsCleaningRooms }})
            </div>

            <div class="legend-item">
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

<!-- Check-In Confirmation Modal -->
<div class="modal fade" id="checkInConfirmModal" tabindex="-1" aria-labelledby="checkInConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="checkInConfirmModalLabel">
                    <i class="fa-solid fa-plane-arrival text-success me-2"></i> Confirm Guest Check-In
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to check in <strong id="checkInConfirmGuestName"></strong> to Room <span class="badge bg-secondary px-2 py-1 fs-6" id="checkInConfirmRoomNumber"></span>?</p>
                <div class="mb-3">
                    <label for="checkInNetRate" class="form-label fw-semibold small">Agreed Room Rate (optional override)</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="checkInNetRate" min="0" step="0.01" placeholder="Leave blank for default rate">
                        <span class="input-group-text text-muted">/night</span>
                    </div>
                </div>
                <p class="text-muted small mb-0">This will change the room status to <strong>OCCUPIED</strong> and post room charges for the stay.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmCheckInBtn">
                    <i class="fa-solid fa-check me-1"></i> Proceed Check-In
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

<!-- Open Shift Modal -->
<div class="modal fade" id="openShiftModal" tabindex="-1" aria-labelledby="openShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="openShiftModalLabel">
                    <i class="fa-solid fa-play text-primary me-2"></i>Open Shift Drawer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('frontdesk.shift.open') }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Select your scheduled shift for today to open the session and activate your transactions drawer.</p>
                    
                    <div class="mb-3">
                        <label for="open_schedule_id" class="form-label fw-semibold">Today's Schedule</label>
                        <select id="open_schedule_id" name="schedule_id" class="form-select">
                            <option value="">No Schedule (Unscheduled Shift)</option>
                            @foreach($todaySchedules as $sched)
                                <option value="{{ $sched->id }}">
                                    {{ $sched->shift_name }} ({{ Carbon\Carbon::parse($sched->scheduled_start_time)->format('g:i A') }} - {{ Carbon\Carbon::parse($sched->scheduled_end_time)->format('g:i A') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Shift Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Close Shift Modal -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" aria-labelledby="closeShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="closeShiftModalLabel">
                    <i class="fa-solid fa-power-off text-danger me-2"></i>Close Shift Session
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('frontdesk.shift.close') }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Reconcile your drawer. Closing this shift session will deactivate your POS/billing transactions drawer.</p>
                    
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="fw-bold mb-2">Shift Sales Summary</h6>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Total Cash Payments:</span>
                            <span class="fw-bold text-success">₱{{ number_format($shiftSales['cash'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Total Card Payments:</span>
                            <span class="fw-bold text-primary">₱{{ number_format($shiftSales['card'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-danger">
                            <span>Total Cash Expenses:</span>
                            <span class="fw-bold">- ₱{{ number_format($shiftSales['expenses'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold">
                            <span>Total Sales Collected:</span>
                            <span>₱{{ number_format($shiftSales['payments'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1 mt-1 fw-bold text-success">
                            <span>Expected Cash in Drawer:</span>
                            <span>₱{{ number_format($shiftSales['cash'] - $shiftSales['expenses'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger mt-1 small">
                            <span>Charges Posted to Rooms:</span>
                            <span>₱{{ number_format($shiftSales['charges'], 2) }}</span>
                        </div>
                    </div>
                    
                    <p class="text-danger small fw-semibold mb-0">Are you sure you want to end your shift now?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Close Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function() {
        const roomsData = @json($roomsByType);
        let roomActionModal = null;
        let checkOutModal = null;
        let checkInConfirmModal = null;
        let selectedRoom = null;
        let pendingCheckOutBookingId = null;
        let pendingCheckInBookingId = null;
 
        roomActionModal = new bootstrap.Modal(document.getElementById('roomActionModal'));
        checkOutModal = new bootstrap.Modal(document.getElementById('checkOutModal'));
        checkInConfirmModal = new bootstrap.Modal(document.getElementById('checkInConfirmModal'));
 
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
                checkInGuest(
                    this.getAttribute('data-booking-id'),
                    this.getAttribute('data-guest-name'),
                    this.getAttribute('data-room-number')
                );
            });
        });
 
        document.querySelectorAll('.check-out-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                openCheckOutModal(this.getAttribute('data-booking-id'));
            });
        });
 
        document.getElementById('confirmCheckOutBtn')?.addEventListener('click', submitCheckOut);
        document.getElementById('confirmCheckInBtn')?.addEventListener('click', submitCheckIn);

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

        const occupiedRoomSearch = document.getElementById('occupiedRoomSearch');
        const occupiedRoomSort = document.getElementById('occupiedRoomSort');
        if (occupiedRoomSearch) {
            occupiedRoomSearch.addEventListener('input', filterAndSortOccupiedRooms);
        }
        if (occupiedRoomSort) {
            occupiedRoomSort.addEventListener('change', filterAndSortOccupiedRooms);
        }



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
                    <div class="room-number">${room.room_number}</div>
                </div>
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

        const now = window.currentServerTime || new Date();
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
        const confirmBtn = document.getElementById('confirmCheckOutBtn');
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

        window.setBtnLoading(confirmBtn, true, 'Checking Out...');

        postJson('{{ route("frontdesk.booking.check-out") }}', {
            booking_id: pendingCheckOutBookingId,
            checkout_time: checkoutTime,
            checkout_period: periodSelect.value
        })
            .then(data => {
                checkOutModal.hide();
                roomActionModal?.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                window.setBtnLoading(confirmBtn, false);
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

    function filterAndSortOccupiedRooms() {
        const tbody = document.getElementById('occupiedRoomsTableBody');
        if (!tbody) {
            return;
        }

        const searchTerm = (document.getElementById('occupiedRoomSearch')?.value || '').toLowerCase().trim();
        const sortBy = document.getElementById('occupiedRoomSort')?.value || 'room-asc';
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let visibleCount = 0;

        rows.forEach(row => {
            const roomNumber = row.getAttribute('data-room-number') || '';
            const roomType = row.getAttribute('data-room-type') || '';
            const guestName = row.getAttribute('data-guest-name') || '';
            const matches = roomNumber.includes(searchTerm)
                || roomType.includes(searchTerm)
                || guestName.includes(searchTerm);
            row.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount++;
            }
        });

        const visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const roomA = parseInt(a.getAttribute('data-room-sort'), 10) || 0;
            const roomB = parseInt(b.getAttribute('data-room-sort'), 10) || 0;
            const guestA = a.getAttribute('data-guest-name') || '';
            const guestB = b.getAttribute('data-guest-name') || '';

            switch (sortBy) {
                case 'room-desc':
                    return roomB - roomA;
                case 'guest-asc':
                    return guestA.localeCompare(guestB) || roomA - roomB;
                case 'guest-desc':
                    return guestB.localeCompare(guestA) || roomA - roomB;
                case 'room-asc':
                default:
                    return roomA - roomB;
            }
        });

        visibleRows.forEach(row => tbody.appendChild(row));

        const noResults = document.getElementById('occupiedRoomsNoResults');
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

    function checkInGuest(bookingId, guestName, roomNumber) {
        pendingCheckInBookingId = bookingId;
        document.getElementById('checkInConfirmGuestName').textContent = guestName || 'Guest';
        document.getElementById('checkInConfirmRoomNumber').textContent = roomNumber || '—';
        checkInConfirmModal.show();
    }

    function submitCheckIn() {
        if (!pendingCheckInBookingId) return;
        const confirmBtn = document.getElementById('confirmCheckInBtn');

        const payload = { booking_id: pendingCheckInBookingId };
        const netRateInput = document.getElementById('checkInNetRate');
        if (netRateInput && netRateInput.value) {
            payload.net_rate = netRateInput.value;
        }

        window.setBtnLoading(confirmBtn, true, 'Checking In...');

        postJson('{{ route("frontdesk.booking.check-in") }}', payload)
            .then(data => {
                checkInConfirmModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                window.setBtnLoading(confirmBtn, false);
                showAlert('error', error.message);
            });
    }

    function checkOutGuest(bookingId) {
        openCheckOutModal(bookingId);
    }

    function markRoomCleaned(roomId, btn) {
        window.setBtnLoading(btn, true, 'Updating...');
        postJson('{{ route("frontdesk.room.mark-cleaned") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                window.setBtnLoading(btn, false);
                showAlert('error', error.message);
            });
    }

    function markRoomForCleaning(roomId, btn) {
        window.setBtnLoading(btn, true, 'Updating...');
        postJson('{{ route("frontdesk.room.mark-for-cleaning") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                window.setBtnLoading(btn, false);
                showAlert('error', error.message);
            });
    }

    function markRoomForMaintenance(roomId, btn) {
        window.setBtnLoading(btn, true, 'Updating...');
        postJson('{{ route("frontdesk.room.mark-maintenance") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                window.setBtnLoading(btn, false);
                showAlert('error', error.message);
            });
    }

    function markMaintenanceComplete(roomId, btn) {
        window.setBtnLoading(btn, true, 'Updating...');
        postJson('{{ route("frontdesk.room.maintenance-complete") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                window.setBtnLoading(btn, false);
                showAlert('error', error.message);
            });
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
                markRoomCleaned(room.room_id, this);
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
                markMaintenanceComplete(room.room_id, this);
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
                markRoomForCleaning(room.room_id, this);
            });
            document.getElementById('modalMarkMaintenanceBtn').addEventListener('click', function() {
                markRoomForMaintenance(room.room_id, this);
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
    })();
</script>
@endpush