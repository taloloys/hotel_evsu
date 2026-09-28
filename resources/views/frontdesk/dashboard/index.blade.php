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
        background: #f8f3ed;
        border: 1px solid rgba(130, 117, 103, 0.25);
        border-radius: 20px;
        padding: 20px;
    }

    .room-type-btn {
        width: 100%;
        border: 1px solid rgba(130, 117, 103, 0.3);
        background: #f5ebe0;
        color: #504538;
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-weight: 500;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        transition: .3s;
    }

    .room-type-btn:hover {
        background: #e6d6c4;
        color: #334c42;
    }

    .room-type-btn.active {
        background: #334c42 !important;
        color: white !important;
        border-color: #334c42 !important;
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
        border: 1px solid rgba(130, 117, 103, 0.25);
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        color: #504538;
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
        background: #627e71;
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
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    .vacant-room-badge {
        font-size: 0.95rem;
        padding: 0.5rem 0.85rem;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    .room-toolbar-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .room-toolbar-search {
        width: 360px;
        max-width: 100%;
        height: 45px;
        border: 1px solid #827567;
        border-radius: 0.375rem;
        overflow: hidden;
        background: #fff;
    }

    .room-toolbar-search .input-group-text,
    .room-toolbar-search .form-control {
        height: 100%;
        border: 0;
        box-shadow: none;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    .room-toolbar-search .form-control {
        background: #fff;
        color: #504538;
    }

    .room-toolbar-select {
        width: 200px;
        max-width: 100%;
        height: 45px;
        border: 1px solid #827567 !important;
        border-radius: 8px;
        box-shadow: none !important;
        outline: none;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        color: #504538;
    }

    .table thead th {
        font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;
        color: #212529;
        font-weight: 700;
    }

    .table tbody td {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        color: #212529 !important;
        font-weight: 400 !important;
    }

    .table-hover tbody tr:nth-of-type(even) {
        background-color: rgba(248, 243, 237, 0.6);
    }

    /* Calendar & Timeline Styles */
    .view-toggle-group .btn {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.45rem 1rem;
        border: 1px solid #827567;
        background: #ffffff;
        color: #504538;
        transition: all 0.2s ease;
    }
    .view-toggle-group .btn.active {
        background: #334c42 !important;
        color: #ffffff !important;
        border-color: #334c42 !important;
        box-shadow: 0 2px 6px rgba(51, 76, 66, 0.25);
    }
    .timeline-wrapper {
        background: #ffffff;
        border: 1px solid rgba(130, 117, 103, 0.25);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .timeline-controls-bar {
        background: #faf6f0;
        border-bottom: 1px solid #dfd6cb;
        padding: 12px 16px;
    }
    .timeline-scroll-container {
        overflow-x: auto;
        overflow-y: visible;
        max-height: 680px;
        position: relative;
    }
    .timeline-table {
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }
    .timeline-sticky-col {
        position: sticky;
        left: 0;
        z-index: 25;
        background: #faf6f0;
        min-width: 210px;
        max-width: 230px;
        width: 220px;
        border-right: 2px solid #dfd6cb;
        box-shadow: 3px 0 8px rgba(0, 0, 0, 0.04);
    }
    .timeline-header-corner {
        position: sticky;
        top: 0;
        left: 0;
        z-index: 35;
        background: #f0e7dc;
        border-bottom: 2px solid #dfd6cb;
        border-right: 2px solid #dfd6cb;
        padding: 10px 14px;
        font-weight: 700;
        font-size: 0.85rem;
        color: #334c42;
    }
    .timeline-header-date {
        position: sticky;
        top: 0;
        z-index: 30;
        background: #f8f3ed;
        border-bottom: 2px solid #dfd6cb;
        border-right: 1px solid #ede4d8;
        min-width: 68px;
        width: 68px;
        padding: 8px 4px;
        text-align: center;
        transition: background 0.15s;
    }
    .timeline-header-date.is-today {
        background: #334c42 !important;
        color: #ffffff !important;
    }
    .timeline-header-date.is-today .timeline-day-name,
    .timeline-header-date.is-today .timeline-day-num,
    .timeline-header-date.is-today .timeline-day-month {
        color: #ffffff !important;
    }
    .timeline-header-date.is-weekend {
        background: #f3ece3;
    }
    .timeline-day-name {
        font-size: 0.70rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #827567;
        line-height: 1.1;
    }
    .timeline-day-num {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1.2;
    }
    .timeline-day-month {
        font-size: 0.68rem;
        font-weight: 600;
        color: #627e71;
    }
    .timeline-type-group-header {
        background: #eee5d8;
        font-weight: 700;
        font-size: 0.80rem;
        color: #334c42;
        padding: 6px 14px;
        border-bottom: 1px solid #dfd6cb;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .timeline-room-row {
        height: 52px;
    }
    .timeline-room-row:hover .timeline-room-cell-info {
        background: #f3ede4;
    }
    .timeline-room-cell-info {
        padding: 6px 12px;
        border-bottom: 1px solid #ede4d8;
        background: #faf6f0;
        transition: background 0.15s;
        cursor: pointer;
    }
    .timeline-room-number {
        font-weight: 700;
        font-size: 0.90rem;
        color: #1a1a1a;
    }
    .timeline-room-floor {
        font-size: 0.72rem;
        color: #827567;
    }
    .timeline-cell {
        min-width: 68px;
        width: 68px;
        height: 52px;
        border-right: 1px solid #ede4d8;
        border-bottom: 1px solid #ede4d8;
        position: relative;
        padding: 3px;
        vertical-align: middle;
        background: #ffffff;
        transition: background 0.15s;
    }
    .timeline-cell.is-today {
        background: rgba(98, 126, 113, 0.05);
    }
    .timeline-cell.is-weekend {
        background: rgba(248, 243, 237, 0.5);
    }
    .timeline-cell.is-available:hover {
        background: rgba(98, 126, 113, 0.15) !important;
        cursor: pointer;
    }
    .timeline-cell.is-available:hover::after {
        content: '+ Book';
        position: absolute;
        inset: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.70rem;
        font-weight: 700;
        color: #334c42;
        background: rgba(255, 255, 255, 0.85);
        border: 1px dashed #627e71;
        border-radius: 6px;
    }
    .timeline-event-bar {
        position: absolute;
        top: 6px;
        bottom: 6px;
        border-radius: 8px;
        z-index: 10;
        padding: 4px 8px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        user-select: none;
    }
    .timeline-event-bar:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.20);
        z-index: 15;
        filter: brightness(1.05);
    }
    .timeline-event-reserved {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        border: 1px solid #b45309;
    }
    .timeline-event-occupied {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #ffffff;
        border: 1px solid #1e40af;
    }
    .timeline-event-cleaning {
        background: linear-gradient(135deg, #fd7e14, #d9480f);
        color: #ffffff;
        border: 1px solid #c92a2a;
    }
    .timeline-event-maintenance {
        background: repeating-linear-gradient(45deg, #6c757d, #6c757d 8px, #495057 8px, #495057 16px);
        color: #ffffff;
        border: 1px solid #343a40;
    }
    .timeline-status-pill {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }
</style>

@if($errors->has('shift'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('shift') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($overdueGuests->count() > 0)
<div class="alert border-0 shadow-sm mb-4 p-4" role="alert" style="background: #fff5f5; border: 1px solid #fecaca !important; border-left: 5px solid #dc2626 !important; border-radius: 0.75rem;">
    <div class="d-flex align-items-start gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: #fee2e2; color: #dc2626;">
            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-2 font-display" style="color: #991b1b; font-size: 1.05rem;">
                {{ $overdueGuests->count() }} Overdue {{ Str::plural('Guest', $overdueGuests->count()) }} — Past Departure Date
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0 align-middle">
                    <tbody>
                        @foreach($overdueGuests as $b)
                        <tr>
                            <td class="ps-0 fw-bold" style="color: #1a1a1a; font-size: 0.95rem;">
                                {{ $b->folio?->guest?->first_name }} {{ $b->folio?->guest?->last_name }}
                            </td>
                            <td>
                                <span class="badge px-2.5 py-1 fw-semibold" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; border-radius: 0.375rem;">Room {{ $b->room?->room_number }}</span>
                            </td>
                            <td class="fw-bold" style="color: #dc2626;">
                                Due out: {{ $b->departure_date->format('M d, Y') }}
                            </td>
                            <td>
                                @if($b->folio)
                                    <a href="{{ route('frontdesk.guest-folio.show', $b->folio->folio_id) }}"
                                       class="btn btn-sm text-white fw-semibold shadow-sm"
                                       style="background-color: #334c42; border: none; border-radius: 0.375rem; padding: 0.35rem 0.85rem;">
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
<div class="card border-0 shadow-sm mb-4 rounded-4" style="border-left: 5px solid {{ $activeShift ? '#627e71' : '#d97706' }} !important; background: {{ $activeShift ? '#ffffff' : '#fffbe6' }}; border: 1px solid {{ $activeShift ? '#c2a889' : '#ffe58f' }} !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: {{ $activeShift ? 'rgba(98,126,113,0.15)' : '#fff1b8' }}; color: {{ $activeShift ? '#627e71' : '#d48806' }};">
                    <i class="fa-solid fa-cash-register fs-4"></i>
                </div>
                <div>
                    @if($activeShift)
                        <h6 class="fw-bold mb-1 font-display" style="color: #1a1a1a;">
                            Active Shift Session: <span style="color: #334c42;">{{ $activeShift->schedule ? $activeShift->schedule->shift_name : 'Unscheduled Shift' }}</span>
                        </h6>
                        <small style="color: #3d332a; font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;">
                            Opened at {{ $activeShift->start_time->format('M d, g:i A') }} | Live Sales: 
                            <strong style="color: #334c42;">₱{{ number_format($shiftSales['payments'], 2) }}</strong> (Cash: ₱{{ number_format($shiftSales['cash'], 2) }}, Card: ₱{{ number_format($shiftSales['card'], 2) }}) | Charges posted: <strong class="text-danger">₱{{ number_format($shiftSales['charges'], 2) }}</strong>
                        </small>
                    @else
                        <h6 class="fw-bold mb-1 font-display" style="color: #78350f; font-size: 1.05rem;">
                            ⚠️ No Active Shift Session
                        </h6>
                        <small style="color: #4a3e35; font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; font-size: 0.90rem;">
                            Please open a cashier shift drawer session to perform reservation check-ins, check-outs, and billing postings.
                        </small>
                    @endif
                </div>
            </div>
            <div>
                @if($activeShift)
                    <button class="btn btn-sm px-3 fw-semibold font-body shadow-sm" style="background-color: #f3ede4; border: 1px solid #c2a889; color: #1a1a1a; border-radius: 0.5rem;" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
                        <i class="fa-solid fa-power-off me-1"></i> Close Shift
                    </button>
                @else
                    <button class="btn text-white btn-sm px-3.5 py-2 fw-semibold font-body shadow-sm" style="background-color: #334c42; border: none; border-radius: 0.5rem;" data-bs-toggle="modal" data-bs-target="#openShiftModal">
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
        <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff; border: 1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background: #e8f0ec; color: #334c42;">
                    <i class="fa-solid fa-plane-arrival fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold font-body" style="color: #1a1a1a;">Today's Arrivals</div>
                    <h3 class="fw-bold mb-0 mt-1 font-display" style="color: #1a1a1a; font-size: 1.85rem;">{{ $todayArrivals }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff; border: 1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background: #f3ede4; color: #504538;">
                    <i class="fa-solid fa-plane-departure fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold font-body" style="color: #1a1a1a;">Today's Departures</div>
                    <h3 class="fw-bold mb-0 mt-1 font-display" style="color: #1a1a1a; font-size: 1.85rem;">{{ $todayDepartures }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff; border: 1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background: #e8f0ec; color: #334c42;">
                    <i class="fa-solid fa-bed fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold font-body" style="color: #1a1a1a;">Occupied Rooms</div>
                    <h3 class="fw-bold mb-0 mt-1 font-display" style="color: #1a1a1a; font-size: 1.85rem;">{{ $occupiedRooms }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff; border: 1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background: #e8f0ec; color: #334c42;">
                    <i class="fa-solid fa-door-open fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold font-body" style="color: #1a1a1a;">Available Rooms</div>
                    <h3 class="fw-bold mb-0 mt-1 font-display" style="color: #1a1a1a; font-size: 1.85rem;">{{ $availableRooms }}</h3>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- TODAY'S CHECK-IN & RESERVATIONS -->
<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #c2a889 !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h5 class="fw-bold mb-0 font-display" style="color: #1a1a1a;">
                <i class="fa-solid fa-plane-arrival me-2" style="color: #334c42;"></i> Today's Check-In & Reservations
            </h5>
            @if($todayCheckIns->count() > 0)
                <div class="room-toolbar-group">
                    <div class="input-group room-toolbar-search" style="width: 280px; height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem;">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-search" style="color: #627e71;"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            id="checkinSearch"
                            placeholder="Search guest, room, or folio..."
                            style="font-size: 0.95rem;"
                        >
                    </div>
                    <select
                        id="checkinSort"
                        class="form-select shadow-none room-toolbar-select"
                        style="height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem; font-size: 0.95rem; color: #1a1a1a;"
                    >
                        <option value="all">All Check-Ins</option>
                        <option value="status-reserved">Filter: Reserved Only</option>
                        <option value="status-checkedin">Filter: Checked In Only</option>
                        <option value="guest-asc">Sort: Guest (A-Z)</option>
                        <option value="guest-desc">Sort: Guest (Z-A)</option>
                        <option value="room-asc">Sort: Room (Low to High)</option>
                        <option value="room-desc">Sort: Room (High to Low)</option>
                    </select>
                </div>
            @endif
        </div>

        @if($todayCheckIns->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="checkinTable">
                    <thead style="background-color: transparent; border-bottom: 2px solid #c2a889;">
                        <tr class="small fw-bold" style="color: #2c241d;">
                            <th class="ps-3">ROOM NO.</th>
                            <th>TYPE</th>
                            <th>GUEST NAME</th>
                            <th>FOLIO NO.</th>
                            <th>ARRIVAL</th>
                            <th>DEPARTURE</th>
                            <th>STATUS</th>
                            <th class="pe-3">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="checkinTableBody">
                        @foreach($todayCheckIns as $booking)
                            @if($booking->folio && $booking->folio->guest)
                            <tr
                                style="border-bottom: 1px solid #f0f0f0;"
                                data-guest-name="{{ strtolower($booking->folio->guest->first_name . ' ' . $booking->folio->guest->last_name) }}"
                                data-room-number="{{ $booking->room->room_number }}"
                                data-folio-number="{{ strtolower($booking->folio->folio_number) }}"
                                data-status="{{ $booking->status }}"
                            >
                                <td class="ps-3 fw-bold" style="color: #1a1a1a;">
                                    {{ $booking->room->room_number }}
                                </td>
                                <td style="color: #4a4a4a; font-weight: 400;">
                                    {{ $booking->room->room_type }}
                                </td>
                                <td class="fw-bold" style="color: #1a1a1a;">
                                    {{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}
                                </td>
                                <td class="fw-bold" style="color: #1a1a1a;">
                                    {{ $booking->folio->folio_number }}
                                </td>
                                <td style="color: #262626;">
                                    {{ $booking->arrival_date->format('m/d/Y') }}
                                    @if($booking->arrival_time)
                                        <small class="d-block" style="color: #71717a;">{{ \Carbon\Carbon::parse($booking->arrival_time)->format('g:i A') }}</small>
                                    @endif
                                </td>
                                <td style="color: #262626;">
                                    {{ $booking->departure_date ? $booking->departure_date->format('m/d/Y') : '—' }}
                                    @if($booking->departure_time)
                                        <small class="d-block" style="color: #71717a;">{{ \Carbon\Carbon::parse($booking->departure_time)->format('g:i A') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->status === 'RESERVED')
                                        <span class="badge-status badge-status-reserved">RESERVED</span>
                                    @elseif($booking->status === 'CHECKED_IN')
                                        <span class="badge-status badge-status-checkedin">CHECKED IN</span>
                                    @else
                                        <span class="badge-status badge-status-maintenance">{{ $booking->status }}</span>
                                    @endif
                                </td>
                                <td class="pe-3">
                                    @if($booking->status === 'RESERVED')
                                        <div class="d-flex align-items-center gap-1.5">
                                            <button type="button"
                                                    class="btn btn-sm text-white check-in-btn d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                    style="width: 34px; height: 34px; background-color: #334c42; border: none; border-radius: 0.375rem;"
                                                    data-booking-id="{{ $booking->booking_id }}" 
                                                    data-guest-name="{{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}"
                                                    data-room-number="{{ $booking->room->room_number }}"
                                                    title="Check in guest">
                                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm extend-departure-btn d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                    style="width: 34px; height: 34px; background-color: #f8f3ed; border: 1px solid #c2a889; color: #334c42; border-radius: 0.375rem;"
                                                    data-booking-id="{{ $booking->booking_id }}" 
                                                    data-guest-name="{{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}"
                                                    data-folio-number="{{ $booking->folio->folio_number }}"
                                                    data-room-number="{{ $booking->room->room_number }}"
                                                    data-room-type="{{ $booking->room->room_type }}"
                                                    data-status="{{ $booking->status }}"
                                                    data-arrival-date="{{ $booking->arrival_date->format('Y-m-d') }}"
                                                    data-arrival-display="{{ $booking->arrival_date->format('m/d/Y') }}{{ $booking->arrival_time ? ' ' . \Carbon\Carbon::parse($booking->arrival_time)->format('g:i A') : '' }}"
                                                    data-departure-date="{{ $booking->departure_date ? $booking->departure_date->format('Y-m-d') : '' }}"
                                                    data-departure-time="{{ $booking->departure_time ? \Carbon\Carbon::parse($booking->departure_time)->format('H:i') : '12:00' }}"
                                                    data-departure-display="{{ $booking->departure_date ? $booking->departure_date->format('m/d/Y') : 'Open Stay' }}{{ $booking->departure_time ? ' ' . \Carbon\Carbon::parse($booking->departure_time)->format('g:i A') : '' }}"
                                                    data-net-rate="{{ $booking->folio->net_rate ?? $booking->room->base_rate }}"
                                                    title="Move reservation">
                                                <i class="fa-solid fa-calendar-plus" style="color: #627e71;"></i>
                                            </button>
                                            <form method="POST" action="{{ route('frontdesk.reservation.cancel', $booking->booking_id) }}" class="d-inline m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" 
                                                        class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                        style="width: 34px; height: 34px; border: 1px solid #f87171; color: #991b1b; background-color: #fee2e2; border-radius: 0.375rem;" 
                                                        title="Cancel reservation"
                                                        onclick="swalConfirmCancelDashboardReservation(this)">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            </form>
                                        </div>
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
            <div class="fd-empty-state text-center py-4">
                <i class="fa-solid fa-calendar-check d-block fs-2 mb-2" style="color: #827567;"></i>
                <div class="fw-semibold font-display" style="color: #504538;">No check-ins or reservations for today</div>
                <small style="color: #827567; font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;">Guests scheduled for check-in today will appear here.</small>
            </div>
        @endif
    </div>
</div>

<!-- AVAILABLE ROOMS (NOT RESERVED OR IN USE) -->
<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #c2a889 !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h5 class="fw-bold mb-0 font-display" style="color: #1a1a1a;">
                <i class="fa-solid fa-door-open me-2" style="color: #334c42;"></i> Available Rooms
                <span class="badge ms-2" style="background: #334c42; color: #ffffff; border-radius: 0.375rem; font-size: 0.85rem;">{{ $vacantRooms->count() }}</span>
            </h5>
            @if($vacantRooms->count() > 0)
                <div class="room-toolbar-group">

                    <!-- Search -->
                    <div class="input-group room-toolbar-search" style="width: 280px; height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem;">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-search" style="color: #627e71;"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            id="vacantRoomSearch"
                            placeholder="Search room or type..."
                            style="font-size: 0.95rem;"
                        >
                    </div>

                    <!-- Sort -->
                    <select
                        id="vacantRoomSort"
                        class="form-select shadow-none room-toolbar-select"
                        style="height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem; font-size: 0.95rem; color: #1a1a1a;"
                    >
                        <option value="room-asc">Sort: Room (Low to High)</option>
                        <option value="room-desc">Sort: Room (High to Low)</option>
                        <option value="type-asc">Sort: Room Type (A-Z)</option>
                        <option value="type-desc">Sort: Room Type (Z-A)</option>
                    </select>

                </div>
            @endif
        </div>
        @if($vacantRooms->count() > 0)
            <p class="small mb-3 font-body" style="color: #4a3e35; font-size: 0.95rem;">Rooms that are ready and not reserved or currently in use.</p>
            <div id="vacantRoomsList" class="row g-3">
                @foreach($vacantRooms->groupBy('room_type') as $type => $roomsInType)
                    <div class="col-lg-6 vacant-room-group" data-room-type-group="{{ strtolower($type) }}">
                        <div class="p-3 rounded-3 h-100" style="background: #f8f3ed; border: 1px solid #c2a889;">
                            <div class="d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom" style="border-color: #c2a889 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-layer-group small" style="color: #334c42;"></i>
                                    <span class="fw-bold font-display" style="color: #1a1a1a; font-size: 0.95rem;">{{ $type }}</span>
                                </div>
                                <span class="badge rounded-pill fw-semibold" style="font-size: 0.75rem; background: #e8f0ec; color: #1e332b; border: 1px solid #627e71;">{{ $roomsInType->count() }} available</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                @foreach($roomsInType as $room)
                                    <span
                                        class="badge vacant-room-badge shadow-sm"
                                        style="background-color: #334c42 !important; color: #ffffff !important; border: none !important; font-weight: 600; font-size: 0.9rem; border-radius: 0.375rem; padding: 0.4rem 0.75rem;"
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
                    </div>
                @endforeach
            </div>
            <p class="text-muted small mb-0 mt-3 d-none" id="vacantRoomsNoResults">No rooms match your search.</p>
        @else
            <div class="fd-empty-state text-center py-4">
                <i class="fa-solid fa-door-closed d-block fs-2 mb-2" style="color: #827567;"></i>
                <div class="fw-bold font-display" style="color: #1a1a1a;">No vacant rooms available right now</div>
                <small style="color: #6b7280; font-weight: 500;">All rooms are currently occupied or under maintenance.</small>
            </div>
        @endif
    </div>
</div>

<!-- OCCUPIED ROOMS -->
<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #c2a889 !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h5 class="fw-bold mb-0 font-display" style="color: #1a1a1a;">
                <i class="fa-solid fa-bed me-2" style="color: #334c42;"></i> Occupied Rooms
                <span class="badge ms-2" style="background: #334c42; color: #ffffff; border-radius: 0.375rem; font-size: 0.85rem;">{{ $occupiedRoomList->count() }}</span>
            </h5>

            @if($occupiedRoomList->count() > 0)
                <div class="room-toolbar-group">

                    <!-- Search -->
                    <div class="input-group room-toolbar-search" style="width: 280px; height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem;">
                        <span class="input-group-text bg-white border-0">
                            <i class="fa-solid fa-search" style="color: #627e71;"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            id="occupiedRoomSearch"
                            placeholder="Search room, guest, or folio..."
                            style="font-size: 0.95rem;"
                        >
                    </div>

                    <!-- Sort / Filter -->
                    <select
                        id="occupiedRoomSort"
                        class="form-select shadow-none room-toolbar-select"
                        style="height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem; font-size: 0.95rem; color: #1a1a1a;"
                    >
                        <option value="all">All Occupied Rooms</option>
                        <option value="overdue-only">Filter: Overdue Only</option>
                        <option value="open-stay-only">Filter: Open Stays Only</option>
                        <option value="room-asc">Sort: Room (Low to High)</option>
                        <option value="room-desc">Sort: Room (High to Low)</option>
                        <option value="guest-asc">Sort: Guest (A-Z)</option>
                        <option value="guest-desc">Sort: Guest (Z-A)</option>
                        <option value="type-asc">Sort: Room Type (A-Z)</option>
                    </select>

                </div>
            @endif
        </div>
        @if($occupiedRoomList->count() > 0)
            <p class="small mb-3 font-body" style="color: #4a3e35; font-size: 0.95rem;">Rooms currently checked in, including walk-in registrations.</p>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="occupiedRoomsTable">
                    <thead style="background-color: transparent; border-bottom: 2px solid #c2a889;">
                        <tr class="small fw-bold" style="color: #1a1a1a;">
                            <th class="ps-4">Room</th>
                            <th>Type</th>
                            <th>Guest</th>
                            <th>Folio</th>
                            <th class="pe-4">Departure</th>
                        </tr>
                    </thead>
                    <tbody id="occupiedRoomsTableBody">
                        @foreach($occupiedRoomList as $room)
                            <tr
                                style="border-bottom: 1px solid #f0f0f0;"
                                data-room-number="{{ strtolower($room['room_number']) }}"
                                data-room-type="{{ strtolower($room['room_type']) }}"
                                data-guest-name="{{ strtolower($room['guest_name'] ?? '') }}"
                                data-folio-number="{{ strtolower($room['folio_number'] ?? '') }}"
                                data-is-overdue="{{ $room['is_overdue'] ? '1' : '0' }}"
                                data-is-open-stay="{{ $room['departure_date'] ? '0' : '1' }}"
                                data-room-sort="{{ $room['room_number'] }}"
                            >
                                <td class="ps-4"><span class="badge px-2.5 py-1.5 fw-bold" style="font-size: 0.88rem; background-color: #334c42 !important; color: #ffffff !important; border-radius: 0.375rem;">{{ $room['room_number'] }}</span></td>
                                <td style="color: #4a4a4a; font-weight: 400;">{{ $room['room_type'] }}</td>
                                <td style="color: #262626; font-weight: 400;">{{ $room['guest_name'] ?: '—' }}</td>
                                <td style="color: #262626; font-weight: 400;">{{ $room['folio_number'] ?: '—' }}</td>
                                <td class="pe-4" style="color: #262626; font-weight: 400;">
                                    @if($room['is_overdue'])
                                        <span class="badge fw-bold me-1" style="background-color: #dc2626; color: #ffffff; border-radius: 0.375rem; font-size: 0.75rem; padding: 0.3rem 0.5rem;">OVERDUE</span>
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
            <p class="text-center py-4 mb-0 fw-semibold font-body" style="color: #4a3e35;">No occupied rooms right now</p>
        @endif
    </div>
</div>

<!-- TODAY'S CHECK-OUT -->
<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #c2a889 !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h5 class="fw-bold mb-0 font-display" style="color: #1a1a1a;">
                <i class="fa-solid fa-plane-departure me-2" style="color: #334c42;"></i> Today's Check-Out
            </h5>
            @if($todayCheckOuts->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    <div class="input-group" style="width: 240px; height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem;">
                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-search" style="color: #627e71;"></i></span>
                        <input type="text" class="form-control border-0 shadow-none" id="checkoutSearch" placeholder="Search guest, room, or folio..." style="font-size: 0.95rem;">
                    </div>
                    <select class="form-select shadow-none" id="checkoutSort" style="width: 220px; height: 42px; border: 1px solid #c2a889; border-radius: 0.5rem; font-size: 0.95rem; color: #1a1a1a;">
                        <option value="all">All Check-Outs</option>
                        <option value="overdue-only">Filter: Overdue Check-Outs</option>
                        <option value="status-pending">Filter: Pending Check-Out</option>
                        <option value="status-done">Filter: Checked Out</option>
                        <option value="guest-asc">Sort: Guest (A-Z)</option>
                        <option value="guest-desc">Sort: Guest (Z-A)</option>
                        <option value="room-asc">Sort: Room (Low to High)</option>
                        <option value="room-desc">Sort: Room (High to Low)</option>
                    </select>
                </div>
            @endif
        </div>

        @if($todayCheckOuts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="checkoutTable">
                    <thead style="background-color: transparent; border-bottom: 2px solid #c2a889;">
                        <tr class="small fw-bold" style="color: #1a1a1a;">
                            <th class="ps-4">Room</th>
                            <th>Type</th>
                            <th>Guest</th>
                            <th>Status</th>
                            <th>Departure</th>
                            <th>Check-Out Time</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="checkoutTableBody">
                        @foreach($todayCheckOuts as $booking)
                            @if($booking->folio && $booking->folio->guest)
                            @php
                                $isOverdueRow = $booking->status === 'CHECKED_IN'
                                    && $booking->departure_date
                                    && $booking->departure_date->lt(now()->startOfDay());
                            @endphp
                            <tr
                                style="border-bottom: 1px solid #f0f0f0;"
                                data-guest-name="{{ strtolower($booking->folio->guest->first_name . ' ' . $booking->folio->guest->last_name) }}"
                                data-room-number="{{ strtolower($booking->room->room_number) }}"
                                data-folio-number="{{ strtolower($booking->folio->folio_number) }}"
                                data-status="{{ $booking->status }}"
                                data-is-overdue="{{ $isOverdueRow ? '1' : '0' }}"
                                data-checkout-time="{{ $booking->actual_check_out?->timestamp ?? 0 }}"
                            >
                                <td class="ps-4">
                                    <span class="badge px-2.5 py-1.5 fw-bold" style="font-size: 0.88rem; background-color: #334c42 !important; color: #ffffff !important; border-radius: 0.375rem;">{{ $booking->room->room_number }}</span>
                                </td>
                                <td style="color: #4a4a4a; font-weight: 400;">{{ $booking->room->room_type }}</td>
                                <td style="color: #262626; font-weight: 400;">{{ $booking->folio->guest->first_name }} {{ $booking->folio->guest->last_name }}</td>
                                <td>
                                    @if($isOverdueRow)
                                        <span class="badge fw-bold" style="background-color: #dc2626; color: #ffffff; border-radius: 0.375rem;">OVERDUE</span>
                                    @elseif($booking->status === 'CHECKED_IN')
                                        <span class="badge-status badge-status-open">PENDING CHECK-OUT</span>
                                    @elseif($booking->status === 'CHECKED_OUT')
                                        <span class="badge-status badge-status-closed">CHECKED OUT</span>
                                    @endif
                                </td>
                                <td style="color: #262626;">{{ $booking->departure_date?->format('M d') ?? 'Open' }} @ {{ $booking->departure_time ?? '—' }}</td>
                                <td>
                                    @if($booking->status === 'CHECKED_OUT' && $booking->actual_check_out)
                                        {{ $booking->actual_check_out->format('g:i A') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="pe-4">
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
            <p class="text-muted small mb-0 mt-3 d-none px-4 pb-3" id="checkoutNoResults">No guests match your search.</p>
        @else
            <p class="text-muted text-center py-4 font-body">No check-outs scheduled for today</p>
        @endif
    </div>
</div>

<!-- ROOM MONITORING & CALENDAR -->
<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #c2a889 !important;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(51, 76, 66, 0.1); color: #334c42;">
                <i class="fa-solid fa-calendar-days fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 font-display" style="color: #1a1a1a;">
                    Hotel Room Monitoring & Calendar
                </h5>
                <small class="text-muted">Live room status, occupancy schedule, and multi-day reservation timeline</small>
            </div>
        </div>
        
        <!-- VIEW MODE SWITCHER -->
        <div class="btn-group view-toggle-group shadow-sm" role="group" aria-label="View switch">
            <button type="button" class="btn active" id="btnTimelineView" onclick="window.switchRoomView('timeline')">
                <i class="fa-solid fa-calendar-week me-1"></i> Calendar Timeline
            </button>
            <button type="button" class="btn" id="btnGridView" onclick="window.switchRoomView('grid')">
                <i class="fa-solid fa-grip me-1"></i> Room Grid
            </button>
        </div>
    </div>

    <div class="card-body p-4 pt-2">

        <!-- LEGEND & LIVE COUNTERS -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 rounded-3" style="background: #faf6f0; border: 1px solid rgba(130, 117, 103, 0.2);">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #627e71;"></span>
                    <span>Available</span>
                    <span class="badge ms-1 px-2 py-0.5" id="legendCountAvailable" style="background: #e8f0ec; color: #334c42; font-size: 0.75rem;">{{ $availableRooms }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #3b82f6;"></span>
                    <span>Occupied</span>
                    <span class="badge ms-1 px-2 py-0.5" id="legendCountOccupied" style="background: #dbeafe; color: #1e40af; font-size: 0.75rem;">{{ $occupiedRooms }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #f59e0b;"></span>
                    <span>Reserved</span>
                    <span class="badge ms-1 px-2 py-0.5" id="legendCountReserved" style="background: #fef3c7; color: #92400e; font-size: 0.75rem;">{{ $todayArrivals }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #fd7e14;"></span>
                    <span>Needs Cleaning</span>
                    <span class="badge ms-1 px-2 py-0.5" id="legendCountCleaning" style="background: #ffedd5; color: #9a3412; font-size: 0.75rem;">{{ $needsCleaningRooms }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #6c757d;"></span>
                    <span>Under Maintenance</span>
                    <span class="badge ms-1 px-2 py-0.5" id="legendCountMaintenance" style="background: #f1f5f9; color: #475569; font-size: 0.75rem;">{{ $maintenanceRooms }}</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-muted" id="timelineRangeDisplay">Loading range...</span>
            </div>
        </div>

        <!-- 1. CALENDAR TIMELINE VIEW -->
        <div id="calendarTimelineViewContainer">
            <!-- TIMELINE TOOLBAR -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <!-- Date Navigation -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="btn-group shadow-sm">
                        <button class="btn btn-sm btn-light border px-2.5 fw-bold" id="btnTimelinePrev" title="Previous range">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-light border px-3 fw-bold" id="btnTimelineToday" title="Jump to Today">
                            <i class="fa-solid fa-calendar-day text-success me-1"></i> Today
                        </button>
                        <button class="btn btn-sm btn-light border px-2.5 fw-bold" id="btnTimelineNext" title="Next range">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <div class="input-group input-group-sm shadow-sm" style="width: 170px;">
                        <span class="input-group-text bg-white border-end-0" style="border-color: #c2a889;"><i class="fa-solid fa-calendar text-muted"></i></span>
                        <input type="date" class="form-control border-start-0 ps-0" id="timelineDatePicker" style="border-color: #c2a889;">
                    </div>

                    <!-- Duration Selector -->
                    <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Duration">
                        <button type="button" class="btn btn-light border timeline-duration-btn" data-days="7">7 Days</button>
                        <button type="button" class="btn btn-light border timeline-duration-btn active fw-bold" data-days="14" style="background: #e8f0ec; color: #334c42; border-color: #627e71 !important;">14 Days</button>
                        <button type="button" class="btn btn-light border timeline-duration-btn" data-days="30">30 Days</button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm shadow-sm" id="timelineRoomTypeFilter" style="width: 180px; border-color: #c2a889;">
                        <option value="ALL">All Room Types</option>
                    </select>

                    <button class="btn btn-sm btn-light border shadow-sm px-2.5" id="btnRefreshTimeline" title="Refresh Timeline">
                        <i class="fa-solid fa-rotate"></i>
                    </button>
                </div>
            </div>

            <!-- TIMELINE SCROLL MATRIX -->
            <div class="timeline-wrapper shadow-sm">
                <div class="timeline-scroll-container" id="timelineScrollContainer">
                    <div id="timelineContent">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                            Loading reservation timeline and room monitoring calendar...
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-2 px-1 small text-muted">
                <div>
                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> 
                    <strong>Tip:</strong> Click any <strong>Reserved</strong> or <strong>Occupied</strong> bar for quick check-in, check-out, or stay details. Click any <strong>Available</strong> date cell to quickly book or change room status.
                </div>
                <div>
                    <span class="badge bg-light text-dark border">Scroll horizontally ➔</span>
                </div>
            </div>
        </div>

        <!-- 2. ROOM GRID VIEW (Original) -->
        <div id="roomGridViewContainer" class="d-none">
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
</div>

<!-- Check-Out Time Modal -->
<div class="modal fade" id="checkOutModal" tabindex="-1" aria-labelledby="checkOutModalLabel" aria-hidden="true" style="z-index: 1065;">
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

<!-- Move Departure Modal -->
<div class="modal fade" id="extendDepartureModal" tabindex="-1" aria-labelledby="extendDepartureModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="extendDepartureModalLabel" style="color: #1a1a1a;">
                    <i class="fa-solid fa-calendar-plus me-2" style="color: #334c42;"></i> Move Guest Departure
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Guest & Stay Summary -->
                <div class="p-3 rounded-3 mb-3" style="background: #f8f3ed; border: 1px solid #c2a889;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-muted small d-block">Guest Name</span>
                            <span class="fw-bold fs-6" id="extendModalGuestName" style="color: #1a1a1a;">—</span>
                        </div>
                        <div class="text-end">
                            <span class="badge" id="extendModalRoomBadge" style="background-color: #334c42; color: #ffffff; font-size: 0.85rem;">Room —</span>
                            <small class="d-block text-muted" id="extendModalRoomType">—</small>
                        </div>
                    </div>
                    <div class="row g-2 pt-2 border-top" style="border-color: #e2d3be !important;">
                        <div class="col-6">
                            <span class="text-muted small d-block">Folio Number:</span>
                            <span class="fw-semibold small" id="extendModalFolioNumber" style="color: #1a1a1a;">—</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted small d-block">Booking Status:</span>
                            <span class="badge-status" id="extendModalStatus">—</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Arrival:</span>
                            <span class="small fw-semibold" id="extendModalArrival" style="color: #262626;">—</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted small d-block">Current Departure:</span>
                            <span class="small fw-semibold text-danger" id="extendModalCurrentDeparture">—</span>
                        </div>
                    </div>
                </div>

                <form id="extendDepartureForm">
                    <input type="hidden" id="extendBookingId">

                    <!-- New Departure Date -->
                    <div class="mb-3">
                        <label for="extendDepartureDate" class="form-label fw-semibold small" style="color: #1a1a1a;">
                            New Departure Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="extendDepartureDate" required style="border: 1px solid #c2a889;">
                        <div class="form-text" id="extendDateHint">Select a new date after the current departure date.</div>
                    </div>

                    <!-- New Departure Time -->
                    <div class="mb-3">
                        <label for="extendDepartureTime" class="form-label fw-semibold small" style="color: #1a1a1a;">
                            New Departure Time
                        </label>
                        <input type="time" class="form-control" id="extendDepartureTime" value="12:00" style="border: 1px solid #c2a889;">
                        <div class="form-text">Standard checkout is 12:00 PM.</div>
                    </div>

                    <!-- Net Rate Override (Optional) -->
                    <div class="mb-2">
                        <label for="extendNetRate" class="form-label fw-semibold small" style="color: #1a1a1a;">
                            Agreed Room Rate (Optional Override)
                        </label>
                        <div class="input-group" style="border: 1px solid #c2a889; border-radius: 0.375rem;">
                            <span class="input-group-text bg-white border-0">₱</span>
                            <input type="number" class="form-control border-0 shadow-none" id="extendNetRate" min="0" step="0.01" placeholder="Leave blank to keep current rate">
                            <span class="input-group-text bg-white border-0 text-muted">/night</span>
                        </div>
                    </div>

                    <div class="alert alert-danger d-none mt-3 mb-0" id="extendErrorAlert"></div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white fw-semibold" id="confirmExtendDepartureBtn" style="background-color: #334c42;">
                    <i class="fa-solid fa-check me-1"></i> Update Departure
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

<!-- CALENDAR BOOKING DETAILS MODAL -->
<div class="modal fade" id="calendarBookingModal" tabindex="-1" aria-labelledby="calendarBookingModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold font-display mb-0" id="calendarBookingModalLabel" style="color: #1a1a1a;">
                        Reservation & Stay Details
                    </h5>
                    <small class="text-muted" id="calModalBookingSubtitle">Booking # —</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Status Banner -->
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3" id="calModalStatusBanner" style="background: #f8f3ed; border: 1px solid #c2a889;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-hotel fs-5" id="calModalStatusIcon" style="color: #334c42;"></i>
                        <div>
                            <div class="fw-bold fs-6" id="calModalRoomText" style="color: #1a1a1a;">Room —</div>
                            <small class="text-muted" id="calModalRoomType">Type —</small>
                        </div>
                    </div>
                    <span class="badge px-2.5 py-1.5 fw-semibold" id="calModalStatusBadge">STATUS</span>
                </div>

                <!-- Overdue Alert if any -->
                <div class="alert alert-danger d-none mb-3 py-2 px-3 small border-0" id="calModalOverdueAlert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Overdue Guest!</strong> Departure date has passed.
                </div>

                <!-- Guest & Stay Info -->
                <div class="card border-0 mb-3 rounded-3" style="background: #faf6f0; border: 1px solid #e2d3be !important;">
                    <div class="card-body p-3">
                        <div class="mb-2 pb-2 border-bottom" style="border-color: #ede4d8 !important;">
                            <span class="text-muted small d-block">Primary Guest</span>
                            <div class="fw-bold fs-6" id="calModalGuestName" style="color: #1a1a1a;">—</div>
                            <div class="small text-muted" id="calModalGuestContact">—</div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-muted small d-block">Arrival Date</span>
                                <span class="fw-semibold small" id="calModalArrivalDate" style="color: #1a1a1a;">—</span>
                                <small class="d-block text-muted" id="calModalArrivalTime">14:00</small>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small d-block">Departure Date</span>
                                <span class="fw-semibold small" id="calModalDepartureDate" style="color: #1a1a1a;">—</span>
                                <small class="d-block text-muted" id="calModalDepartureTime">12:00</small>
                            </div>
                            <div class="col-6 pt-2">
                                <span class="text-muted small d-block">Folio Number</span>
                                <span class="fw-semibold small" id="calModalFolioNumber" style="color: #334c42;">—</span>
                            </div>
                            <div class="col-6 pt-2">
                                <span class="text-muted small d-block">Room Rate</span>
                                <span class="fw-semibold small" id="calModalNetRate" style="color: #1a1a1a;">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Action Buttons -->
                <div class="d-flex flex-column gap-2" id="calModalActionButtons">
                    <!-- Injected via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CALENDAR QUICK RESERVE / ROOM ACTION MODAL -->
<div class="modal fade" id="calendarQuickReserveModal" tabindex="-1" aria-labelledby="calendarQuickReserveModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold font-display mb-0" id="calendarQuickReserveModalLabel" style="color: #1a1a1a;">
                        Room Actions & Booking
                    </h5>
                    <small class="text-muted" id="calQuickModalSubtitle">Room —</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background: #f8f3ed; border: 1px solid #c2a889;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold fs-6" id="calQuickModalRoomTitle" style="color: #1a1a1a;">Room —</div>
                            <small class="text-muted" id="calQuickModalRoomType">Type —</small>
                        </div>
                        <div class="text-end">
                            <span class="badge" id="calQuickModalStatusBadge" style="background-color: #627e71; color: #ffffff;">AVAILABLE</span>
                            <small class="d-block text-muted" id="calQuickModalRate">₱0.00 / night</small>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top text-muted small" style="border-color: #e2d3be !important;">
                        <i class="fa-solid fa-calendar-day me-1 text-primary"></i> Target Date: <strong id="calQuickModalSelectedDate" style="color: #1a1a1a;">—</strong>
                    </div>
                </div>

                <h6 class="fw-bold small mb-2 text-uppercase" style="color: #827567; letter-spacing: 0.5px;">Reservation & Check-In</h6>
                <div class="d-grid gap-2 mb-3">
                    <a href="#" id="calQuickBtnNewReservation" class="btn text-white fw-semibold py-2 shadow-sm text-start d-flex align-items-center justify-content-between" style="background: #334c42;">
                        <span><i class="fa-solid fa-calendar-plus me-2"></i> Create Advance Reservation</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="#" id="calQuickBtnWalkInCheckIn" class="btn btn-outline-success fw-semibold py-2 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-door-open me-2"></i> Direct Walk-In Registration / Check-In</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <h6 class="fw-bold small mb-2 text-uppercase" style="color: #827567; letter-spacing: 0.5px;">Housekeeping & Maintenance</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-warning w-50 fw-semibold btn-sm py-2" id="calQuickBtnMarkCleaning">
                        <i class="fa-solid fa-broom me-1"></i> Send for Cleaning
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-50 fw-semibold btn-sm py-2" id="calQuickBtnMarkMaintenance">
                        <i class="fa-solid fa-wrench me-1"></i> Out of Order / Repair
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-outline-secondary w-100 fw-semibold" data-bs-dismiss="modal">Cancel</button>
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
        let extendDepartureModal = null;
        let selectedRoom = null;
        let pendingCheckOutBookingId = null;
        let pendingCheckInBookingId = null;
        let pendingExtendBookingId = null;
 
        roomActionModal = new bootstrap.Modal(document.getElementById('roomActionModal'));
        checkOutModal = new bootstrap.Modal(document.getElementById('checkOutModal'));
        checkInConfirmModal = new bootstrap.Modal(document.getElementById('checkInConfirmModal'));
        const extendDepartureModalEl = document.getElementById('extendDepartureModal');
        if (extendDepartureModalEl) {
            extendDepartureModal = new bootstrap.Modal(extendDepartureModalEl);
        }

        let calendarBookingModal = null;
        let calendarQuickReserveModal = null;
        const calBookingModalEl = document.getElementById('calendarBookingModal');
        if (calBookingModalEl) {
            calendarBookingModal = new bootstrap.Modal(calBookingModalEl);
        }
        const calQuickModalEl = document.getElementById('calendarQuickReserveModal');
        if (calQuickModalEl) {
            calendarQuickReserveModal = new bootstrap.Modal(calQuickModalEl);
        }

        // Timeline State
        function getTodayDateStr() {
            const d = new Date();
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        function addDaysToDateStr(dateStr, days) {
            const parts = dateStr.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            d.setDate(d.getDate() + days);
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        let timelineStartDate = getTodayDateStr();
        let timelineDays = 14;
        let timelineRoomType = 'ALL';
        let timelineDataCache = null;

        window.switchRoomView = function(view) {
            const btnTimeline = document.getElementById('btnTimelineView');
            const btnGrid = document.getElementById('btnGridView');
            const containerTimeline = document.getElementById('calendarTimelineViewContainer');
            const containerGrid = document.getElementById('roomGridViewContainer');

            if (view === 'grid') {
                btnGrid?.classList.add('active');
                btnTimeline?.classList.remove('active');
                containerGrid?.classList.remove('d-none');
                containerTimeline?.classList.add('d-none');
                localStorage.setItem('hotel_room_view_mode', 'grid');
            } else {
                btnTimeline?.classList.add('active');
                btnGrid?.classList.remove('active');
                containerTimeline?.classList.remove('d-none');
                containerGrid?.classList.add('d-none');
                localStorage.setItem('hotel_room_view_mode', 'timeline');
                if (!timelineDataCache) {
                    fetchTimelineData();
                }
            }
        };

        function fetchTimelineData() {
            const content = document.getElementById('timelineContent');
            if (content) {
                content.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        Updating reservation timeline and room monitoring calendar...
                    </div>
                `;
            }

            const url = `{{ route('frontdesk.calendar.timeline-data') }}?start_date=${encodeURIComponent(timelineStartDate)}&days=${timelineDays}&room_type=${encodeURIComponent(timelineRoomType)}`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load timeline data');
                return res.json();
            })
            .then(data => {
                timelineDataCache = data;
                renderTimelineTable(data);
                updateTimelineControls(data);
            })
            .catch(err => {
                if (content) {
                    content.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="fa-solid fa-triangle-exclamation fs-4 mb-2 d-block"></i>
                            Failed to load calendar data: ${err.message}.
                            <button class="btn btn-sm btn-outline-secondary mt-2 d-block mx-auto" onclick="fetchTimelineData()">Retry</button>
                        </div>
                    `;
                }
            });
        }

        function updateTimelineControls(data) {
            const rangeDisplay = document.getElementById('timelineRangeDisplay');
            if (rangeDisplay && data.range) {
                rangeDisplay.textContent = data.range.display;
            }

            const datePicker = document.getElementById('timelineDatePicker');
            if (datePicker && data.range) {
                datePicker.value = data.range.start_date;
            }

            if (data.summary) {
                const countAvail = document.getElementById('legendCountAvailable');
                if (countAvail) countAvail.textContent = data.summary.available;
                const countOcc = document.getElementById('legendCountOccupied');
                if (countOcc) countOcc.textContent = data.summary.occupied;
                const countRes = document.getElementById('legendCountReserved');
                if (countRes) countRes.textContent = data.summary.reserved;
                const countClean = document.getElementById('legendCountCleaning');
                if (countClean) countClean.textContent = data.summary.cleaning;
                const countMaint = document.getElementById('legendCountMaintenance');
                if (countMaint) countMaint.textContent = data.summary.maintenance;
            }

            // Populate room type filter
            const typeSelect = document.getElementById('timelineRoomTypeFilter');
            if (typeSelect && data.room_types && typeSelect.options.length <= 1) {
                typeSelect.innerHTML = '<option value="ALL">All Room Types</option>';
                data.room_types.forEach(type => {
                    const opt = document.createElement('option');
                    opt.value = type;
                    opt.textContent = type;
                    if (type === timelineRoomType) opt.selected = true;
                    typeSelect.appendChild(opt);
                });
            }
        }

        function getStatusColor(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return '#627e71';
            if (s === 'OCCUPIED') return '#3b82f6';
            if (s === 'RESERVED') return '#f59e0b';
            if (s === 'CLEANING') return '#fd7e14';
            if (s === 'MAINTENANCE') return '#6c757d';
            return '#627e71';
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatShortDate(dateStr) {
            if (!dateStr) return '—';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = months[parseInt(parts[1], 10) - 1] || parts[1];
            const day = parseInt(parts[2], 10);
            return `${month} ${day}`;
        }

        function renderTimelineTable(data) {
            const container = document.getElementById('timelineContent');
            if (!container) return;

            if (!data.rooms || data.rooms.length === 0) {
                container.innerHTML = `<p class="text-muted text-center py-5">No rooms available for the selected criteria.</p>`;
                return;
            }

            const dates = data.dates || [];
            const todayStr = getTodayDateStr();

            // Group rooms by room_type
            const grouped = {};
            data.rooms.forEach(r => {
                if (!grouped[r.room_type]) grouped[r.room_type] = [];
                grouped[r.room_type].push(r);
            });

            let html = `<table class="timeline-table"><thead><tr>`;
            html += `<th class="timeline-sticky-col timeline-header-corner">Rooms / Type</th>`;

            dates.forEach(d => {
                const isTodayClass = d.is_today ? 'is-today' : '';
                const isWeekendClass = d.is_weekend ? 'is-weekend' : '';
                html += `
                    <th class="timeline-header-date ${isTodayClass} ${isWeekendClass}" title="${d.full_formatted}">
                        <div class="timeline-day-name">${d.day_name}</div>
                        <div class="timeline-day-num">${d.day_number}</div>
                        <div class="timeline-day-month">${d.month_name}</div>
                    </th>
                `;
            });
            html += `</tr></thead><tbody>`;

            Object.keys(grouped).forEach(type => {
                const roomsInType = grouped[type];
                html += `
                    <tr class="timeline-type-group-row">
                        <td colspan="${dates.length + 1}" class="timeline-type-group-header">
                            <i class="fa-solid fa-layer-group me-2"></i> ${escapeHtml(type)} (${roomsInType.length} ${roomsInType.length === 1 ? 'room' : 'rooms'})
                        </td>
                    </tr>
                `;

                roomsInType.forEach(room => {
                    const statusColor = getStatusColor(room.current_status);
                    const statusBadgeClass = getRoomStatusBadgeClass(room.current_status);
                    const statusLabel = getRoomStatusLabel(room.current_status);
                    const bookings = room.bookings || [];
                    const currentActiveBooking = bookings.find(b => b.arrival_date <= todayStr && b.departure_date >= todayStr) || (bookings.length ? bookings[0] : null);

                    let roomScheduleHint = '';
                    if (currentActiveBooking) {
                        const isRes = currentActiveBooking.status === 'RESERVED';
                        const labelPrefix = isRes ? 'Reserved' : 'Occupied';
                        const hintColor = isRes ? '#b45309' : '#1d4ed8';
                        roomScheduleHint = `<div class="fw-semibold text-truncate" style="font-size: 0.70rem; color: ${hintColor};" title="${labelPrefix}: ${currentActiveBooking.arrival_date} until ${currentActiveBooking.departure_date}">
                            <i class="fa-regular fa-calendar-check me-0.5"></i> ${labelPrefix} until ${formatShortDate(currentActiveBooking.departure_date)}
                        </div>`;
                    }

                    html += `<tr class="timeline-room-row" data-room-id="${room.room_id}">`;
                    
                    // Sticky Room info cell
                    html += `
                        <td class="timeline-sticky-col timeline-room-cell-info" onclick="window.timelineOpenRoomModal(${room.room_id})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="overflow-hidden pe-1" style="max-width: 135px;">
                                    <div class="timeline-room-number text-truncate">
                                        <span class="timeline-status-pill" style="background-color: ${statusColor};"></span>
                                        Room ${escapeHtml(room.room_number)}
                                    </div>
                                    <div class="timeline-room-floor">${escapeHtml(room.floor)} • ₱${parseFloat(room.base_rate).toFixed(2)}</div>
                                    ${roomScheduleHint}
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge ${statusBadgeClass} py-1 px-1.5" style="font-size: 0.65rem;">
                                        ${statusLabel}
                                    </span>
                                </div>
                            </div>
                        </td>
                    `;

                    // Track which date indexes are covered by multi-day bookings
                    const coveredDateIndexes = new Set();

                    dates.forEach((d, dIdx) => {
                        const dateStr = d.date;
                        const isToday = d.is_today;
                        const isWeekend = d.is_weekend;

                        if (coveredDateIndexes.has(dIdx)) {
                            // Covered by a booking that started earlier
                            html += `<td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}"></td>`;
                            return;
                        }

                        // Find if any booking starts on or covers this date
                        const activeBooking = bookings.find(b => b.arrival_date <= dateStr && b.departure_date >= dateStr);

                        if (activeBooking) {
                            // Calculate span from current cell
                            let endIdx = dIdx;
                            for (let i = dIdx; i < dates.length; i++) {
                                if (activeBooking.departure_date >= dates[i].date) {
                                    endIdx = i;
                                    coveredDateIndexes.add(i);
                                } else {
                                    break;
                                }
                            }

                            const span = Math.max(1, endIdx - dIdx + 1);
                            const isReserved = activeBooking.status === 'RESERVED';
                            const eventClass = isReserved ? 'timeline-event-reserved' : 'timeline-event-occupied';
                            const iconClass = isReserved ? 'fa-calendar-check' : 'fa-bed';
                            const spanWidthPercent = span * 100;
                            const spanWidthCalc = `calc(${spanWidthPercent}% + ${(span - 1)}px - 8px)`;

                            const checkInFormatted = formatShortDate(activeBooking.arrival_date);
                            const checkOutFormatted = formatShortDate(activeBooking.departure_date);

                            let scheduleText = '';
                            if (span >= 2) {
                                scheduleText = `
                                    <span class="badge bg-black bg-opacity-25 text-white fw-semibold ms-auto flex-shrink-0" style="font-size: 0.68rem; padding: 2px 6px;">
                                        In: ${checkInFormatted} → Until: ${checkOutFormatted}
                                    </span>
                                `;
                            } else {
                                scheduleText = `
                                    <span class="badge bg-black bg-opacity-25 text-white fw-normal ms-1 flex-shrink-0" style="font-size: 0.65rem; padding: 1px 4px;">
                                        Until: ${checkOutFormatted}
                                    </span>
                                `;
                            }

                            html += `
                                <td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}" style="overflow: visible;">
                                    <div class="timeline-event-bar ${eventClass} d-flex align-items-center justify-content-between" 
                                         style="width: ${spanWidthCalc}; left: 4px;"
                                         title="${escapeHtml(activeBooking.guest_name)} - ${activeBooking.status} (Check-in: ${activeBooking.arrival_date} ${activeBooking.arrival_time || ''} → Until: ${activeBooking.departure_date} ${activeBooking.departure_time || ''})"
                                         onclick="event.stopPropagation(); window.openCalendarBookingModal(${activeBooking.booking_id}, ${room.room_id})">
                                        <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                            <i class="fa-solid ${iconClass} flex-shrink-0"></i>
                                            <span class="text-truncate fw-bold">${escapeHtml(activeBooking.guest_name)}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            ${scheduleText}
                                            ${activeBooking.is_overdue ? '<span class="badge bg-danger ms-1" style="font-size:0.6rem;">OVERDUE</span>' : ''}
                                        </div>
                                    </div>
                                </td>
                            `;
                        } else if (dateStr === todayStr && room.current_status === 'CLEANING') {
                            html += `
                                <td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}" style="overflow: visible;">
                                    <div class="timeline-event-bar timeline-event-cleaning d-flex justify-content-center align-items-center" 
                                         style="width: calc(100% - 8px); left: 4px;"
                                         title="Room ${escapeHtml(room.room_number)} - Needs Cleaning (Click to manage)"
                                         onclick="event.stopPropagation(); window.timelineOpenRoomModal(${room.room_id})">
                                        <i class="fa-solid fa-broom fs-6"></i>
                                    </div>
                                </td>
                            `;
                        } else if (dateStr === todayStr && room.current_status === 'MAINTENANCE') {
                            html += `
                                <td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}" style="overflow: visible;">
                                    <div class="timeline-event-bar timeline-event-maintenance d-flex justify-content-center align-items-center" 
                                         style="width: calc(100% - 8px); left: 4px;"
                                         title="Room ${escapeHtml(room.room_number)} - Under Maintenance (Click to manage)"
                                         onclick="event.stopPropagation(); window.timelineOpenRoomModal(${room.room_id})">
                                        <i class="fa-solid fa-wrench fs-6"></i>
                                    </div>
                                </td>
                            `;
                        } else {
                            // Available cell
                            html += `
                                <td class="timeline-cell is-available ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}" 
                                    title="Click to reserve or manage Room ${escapeHtml(room.room_number)} for ${dateStr}"
                                    onclick="window.openQuickReserveModal(${room.room_id}, '${dateStr}')">
                                </td>
                            `;
                        }
                    });

                    html += `</tr>`;
                });
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        }

        window.timelineOpenRoomModal = function(roomId) {
            if (!timelineDataCache) return;
            const room = timelineDataCache.rooms.find(r => r.room_id === roomId);
            if (room) {
                openRoomModal({
                    room_id: room.room_id,
                    room_number: room.room_number,
                    room_type: room.room_type,
                    status: room.current_status,
                    active_booking: room.bookings && room.bookings.length ? room.bookings[0] : null
                });
            }
        };

        window.openCalendarBookingModal = function(bookingId, roomId) {
            if (!timelineDataCache) return;
            const room = timelineDataCache.rooms.find(r => r.room_id === roomId);
            if (!room) return;
            let booking = null;
            if (bookingId !== null && bookingId !== undefined) {
                booking = (room.bookings || []).find(b => b.booking_id === bookingId);
            }
            if (!booking && room.bookings && room.bookings.length) {
                booking = room.bookings[0];
            }
            if (!booking) {
                window.timelineOpenRoomModal(roomId);
                return;
            }

            document.getElementById('calModalBookingSubtitle').textContent = booking.booking_id ? `Booking #${booking.booking_id} • Room ${room.room_number}` : `Room ${room.room_number} • In-House Stay`;
            document.getElementById('calModalRoomText').textContent = `Room ${room.room_number}`;
            document.getElementById('calModalRoomType').textContent = `${room.room_type} (${room.floor})`;
            document.getElementById('calModalGuestName').textContent = booking.guest_name || 'Guest';

            const contactParts = [];
            if (booking.guest_phone) contactParts.push(booking.guest_phone);
            if (booking.guest_email) contactParts.push(booking.guest_email);
            document.getElementById('calModalGuestContact').textContent = contactParts.length ? contactParts.join(' • ') : 'No contact info provided';

            document.getElementById('calModalArrivalDate').textContent = booking.arrival_date || '—';
            document.getElementById('calModalArrivalTime').textContent = booking.arrival_time ? `${booking.arrival_time}` : '14:00';
            document.getElementById('calModalDepartureDate').textContent = booking.departure_date || '—';
            document.getElementById('calModalDepartureTime').textContent = booking.departure_time ? `${booking.departure_time}` : '12:00';
            document.getElementById('calModalFolioNumber').textContent = booking.folio_number || ('FOL-' + booking.folio_id);
            document.getElementById('calModalNetRate').textContent = booking.net_rate ? `₱${parseFloat(booking.net_rate).toFixed(2)}/night` : `₱${parseFloat(room.base_rate).toFixed(2)}/night`;

            const overdueAlert = document.getElementById('calModalOverdueAlert');
            if (overdueAlert) {
                overdueAlert.classList.toggle('d-none', !booking.is_overdue);
            }

            const badge = document.getElementById('calModalStatusBadge');
            const icon = document.getElementById('calModalStatusIcon');
            const actionContainer = document.getElementById('calModalActionButtons');

            if (booking.status === 'RESERVED') {
                badge.className = 'badge bg-warning text-dark px-2.5 py-1.5 fw-semibold';
                badge.textContent = 'RESERVED';
                icon.className = 'fa-solid fa-calendar-check fs-5 text-warning';

                actionContainer.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between gap-2 w-100">
                        <button type="button" class="btn btn-success fw-semibold py-2 px-3 flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm text-truncate" id="calBtnCheckInNow">
                            <i class="fa-solid fa-plane-arrival flex-shrink-0"></i>
                            <span class="text-truncate">Check In Guest Now</span>
                        </button>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <button type="button" class="btn btn-outline-dark fw-semibold d-inline-flex align-items-center justify-content-center" id="calBtnExtendDeparture" title="Move Departure Date" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger fw-semibold d-inline-flex align-items-center justify-content-center" id="calBtnCancelReservation" title="Cancel Reservation" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                            <a href="/frontdesk/guest-folio/${booking.folio_id}" class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center shadow-sm" title="View Folio" style="background: #334c42; width: 42px; height: 42px;">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                        </div>
                    </div>
                `;

                document.getElementById('calBtnCheckInNow')?.addEventListener('click', function() {
                    calendarBookingModal?.hide();
                    checkInGuest(booking.booking_id, booking.guest_name, room.room_number);
                });

                document.getElementById('calBtnExtendDeparture')?.addEventListener('click', function() {
                    calendarBookingModal?.hide();
                    openCalendarExtendModal(booking, room);
                });

                document.getElementById('calBtnCancelReservation')?.addEventListener('click', function() {
                    calendarBookingModal?.hide();
                    cancelReservationFromCalendar(booking.booking_id);
                });
            } else if (booking.status === 'CHECKED_IN') {
                badge.className = 'badge bg-primary text-white px-2.5 py-1.5 fw-semibold';
                badge.textContent = 'CHECKED IN (OCCUPIED)';
                icon.className = 'fa-solid fa-bed fs-5 text-primary';

                const folioUrl = booking.folio_id ? `/frontdesk/guest-folio/${booking.folio_id}` : '/frontdesk/guest-folio';
                const hasUnpaidBalance = Boolean(booking.has_unpaid_balance);
                const checkOutLabel = hasUnpaidBalance
                    ? 'Check Out (Settle Balance)'
                    : 'Check Out';

                actionContainer.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between gap-2 w-100">
                        <a href="${folioUrl}" class="btn btn-danger fw-semibold py-2 px-3 flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm text-truncate" id="calBtnCheckOutNow">
                            <i class="fa-solid fa-file-invoice-dollar fs-6 flex-shrink-0"></i>
                            <span class="text-truncate">${checkOutLabel}</span>
                        </a>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <button type="button" class="btn btn-outline-dark fw-semibold d-inline-flex align-items-center justify-content-center" id="calBtnExtendStay" title="Extend Stay" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-clock"></i>
                            </button>
                            <a href="${folioUrl}" class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center shadow-sm" title="View Folio" style="background: #334c42; width: 42px; height: 42px;">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                        </div>
                    </div>
                `;

                document.getElementById('calBtnExtendStay')?.addEventListener('click', function() {
                    calendarBookingModal?.hide();
                    openCalendarExtendModal(booking, room);
                });
            } else {
                badge.className = 'badge bg-secondary text-white px-2.5 py-1.5 fw-semibold';
                badge.textContent = booking.status;
                icon.className = 'fa-solid fa-circle-check fs-5 text-secondary';

                actionContainer.innerHTML = `
                    <a href="/frontdesk/guest-folio/${booking.folio_id}" class="btn text-white w-100 fw-semibold py-2" style="background: #334c42;">
                        <i class="fa-solid fa-file-invoice me-1"></i> View Folio Details
                    </a>
                `;
            }

            calendarBookingModal?.show();
        };

        function openCalendarExtendModal(booking, room) {
            const fakeBtn = document.createElement('button');
            fakeBtn.setAttribute('data-booking-id', booking.booking_id);
            fakeBtn.setAttribute('data-guest-name', booking.guest_name);
            fakeBtn.setAttribute('data-folio-number', booking.folio_number);
            fakeBtn.setAttribute('data-room-number', room.room_number);
            fakeBtn.setAttribute('data-room-type', room.room_type);
            fakeBtn.setAttribute('data-status', booking.status);
            fakeBtn.setAttribute('data-arrival-date', booking.arrival_date);
            fakeBtn.setAttribute('data-arrival-display', booking.arrival_date);
            fakeBtn.setAttribute('data-departure-date', booking.departure_date);
            fakeBtn.setAttribute('data-departure-time', booking.departure_time || '12:00');
            fakeBtn.setAttribute('data-departure-display', booking.departure_date);
            fakeBtn.setAttribute('data-net-rate', booking.net_rate || room.base_rate);
            openExtendDepartureModal(fakeBtn);
        }

        function cancelReservationFromCalendar(bookingId) {
            if (!bookingId) return;

            const executeCancel = function () {
                fetch(`/frontdesk/reservation/${bookingId}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to cancel reservation.');
                    }
                    showAlert('success', data.message || 'Reservation cancelled successfully.');
                    setTimeout(refreshDashboardInPlace, 500);
                })
                .catch(error => {
                    showAlert('error', error.message);
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cancel Reservation?',
                    text: 'This action cannot be undone. The reservation will be marked as cancelled.',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-ban me-1"></i> Yes, Cancel It',
                    cancelButtonText: 'Keep Reservation',
                    confirmButtonColor: '#dc3545',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        executeCancel();
                    }
                });
            } else if (confirm('Cancel this reservation? This action cannot be undone.')) {
                executeCancel();
            }
        }

        window.openQuickReserveModal = function(roomId, dateStr) {
            if (!timelineDataCache) return;
            const room = timelineDataCache.rooms.find(r => r.room_id === roomId);
            if (!room) return;

            document.getElementById('calQuickModalSubtitle').textContent = `Room ${room.room_number} • ${room.room_type}`;
            document.getElementById('calQuickModalRoomTitle').textContent = `Room ${room.room_number}`;
            document.getElementById('calQuickModalRoomType').textContent = `${room.room_type} (${room.floor})`;
            document.getElementById('calQuickModalRate').textContent = `₱${parseFloat(room.base_rate).toFixed(2)} / night`;
            document.getElementById('calQuickModalSelectedDate').textContent = dateStr;

            const resBtn = document.getElementById('calQuickBtnNewReservation');
            if (resBtn) {
                resBtn.href = `{{ route('frontdesk.reservation') }}?room_id=${room.room_id}&arrival_date=${dateStr}`;
            }

            const walkInBtn = document.getElementById('calQuickBtnWalkInCheckIn');
            if (walkInBtn) {
                walkInBtn.href = `{{ route('frontdesk.checkin') }}?room_id=${room.room_id}`;
            }

            const cleanBtn = document.getElementById('calQuickBtnMarkCleaning');
            if (cleanBtn) {
                cleanBtn.onclick = function() {
                    calendarQuickReserveModal?.hide();
                    markRoomForCleaning(room.room_id, this);
                };
            }

            const maintBtn = document.getElementById('calQuickBtnMarkMaintenance');
            if (maintBtn) {
                maintBtn.onclick = function() {
                    calendarQuickReserveModal?.hide();
                    markRoomForMaintenance(room.room_id, this);
                };
            }

            calendarQuickReserveModal?.show();
        };

        // Timeline Toolbar Event Handlers
        document.getElementById('btnTimelinePrev')?.addEventListener('click', function() {
            timelineStartDate = addDaysToDateStr(timelineStartDate, -timelineDays);
            fetchTimelineData();
        });

        document.getElementById('btnTimelineToday')?.addEventListener('click', function() {
            timelineStartDate = getTodayDateStr();
            fetchTimelineData();
        });

        document.getElementById('btnTimelineNext')?.addEventListener('click', function() {
            timelineStartDate = addDaysToDateStr(timelineStartDate, timelineDays);
            fetchTimelineData();
        });

        document.getElementById('timelineDatePicker')?.addEventListener('change', function() {
            if (this.value) {
                timelineStartDate = this.value;
                fetchTimelineData();
            }
        });

        document.querySelectorAll('.timeline-duration-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.timeline-duration-btn').forEach(b => {
                    b.classList.remove('active', 'fw-bold');
                    b.style.background = '';
                    b.style.color = '';
                    b.style.borderColor = '';
                });
                this.classList.add('active', 'fw-bold');
                this.style.background = '#e8f0ec';
                this.style.color = '#334c42';
                this.style.borderColor = '#627e71';

                timelineDays = parseInt(this.getAttribute('data-days'), 10) || 14;
                fetchTimelineData();
            });
        });

        document.getElementById('timelineRoomTypeFilter')?.addEventListener('change', function() {
            timelineRoomType = this.value;
            fetchTimelineData();
        });

        document.getElementById('btnRefreshTimeline')?.addEventListener('click', function() {
            fetchTimelineData();
        });

        // Initialize default view mode (Timeline by default)
        const initialView = localStorage.getItem('hotel_room_view_mode') || 'timeline';
        window.switchRoomView(initialView);
 
        // Auto-open room modal if room query parameter is present in URL
        const urlParams = new URLSearchParams(window.location.search);
        const targetRoomParam = urlParams.get('room') || urlParams.get('room_number');
        if (targetRoomParam) {
            // Clean query params from browser URL so subsequent page reloads don't re-trigger the modal
            window.history.replaceState({}, document.title, window.location.pathname);

            const allRooms = Object.values(roomsData).flat();
            const targetRoom = allRooms.find(r => String(r.room_number) === String(targetRoomParam) || String(r.room_id) === String(targetRoomParam));
            if (targetRoom && targetRoom.status.toUpperCase() !== 'AVAILABLE') {
                const targetBtn = Array.from(document.querySelectorAll('.room-filter-btn'))
                    .find(btn => btn.getAttribute('data-room-type') === targetRoom.room_type);
                if (targetBtn) {
                    document.querySelectorAll('.room-filter-btn').forEach(btn => btn.classList.remove('active'));
                    targetBtn.classList.add('active');
                    renderRoomGrid(roomsData[targetRoom.room_type] || []);
                }
                setTimeout(() => {
                    openRoomModal(targetRoom);
                }, 300);
            }
        }
 
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

        document.querySelectorAll('.extend-departure-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                openExtendDepartureModal(this);
            });
        });

        document.getElementById('confirmCheckOutBtn')?.addEventListener('click', submitCheckOut);
        document.getElementById('confirmCheckInBtn')?.addEventListener('click', submitCheckIn);
        document.getElementById('confirmExtendDepartureBtn')?.addEventListener('click', submitExtendDeparture);

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

        const roomActionEl = document.getElementById('roomActionModal');
        if (roomActionEl && roomActionEl.classList.contains('show')) {
            const onHidden = function () {
                roomActionEl.removeEventListener('hidden.bs.modal', onHidden);
                checkOutModal.show();
            };
            roomActionEl.addEventListener('hidden.bs.modal', onHidden);
            if (roomActionModal) {
                roomActionModal.hide();
            } else {
                bootstrap.Modal.getInstance(roomActionEl)?.hide();
            }
        } else {
            checkOutModal.show();
        }
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
                setTimeout(refreshDashboardInPlace, 500);
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
            const guestName = (row.getAttribute('data-guest-name') || '').toLowerCase();
            const roomNumber = (row.getAttribute('data-room-number') || '').toLowerCase();
            const folioNumber = (row.getAttribute('data-folio-number') || '').toLowerCase();
            const matches = guestName.includes(searchTerm)
                || roomNumber.includes(searchTerm)
                || folioNumber.includes(searchTerm);
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
        const groups = Array.from(list.querySelectorAll('.vacant-room-group'));
        let totalVisible = 0;

        groups.forEach(group => {
            const badgeContainer = group.querySelector('.d-flex.flex-wrap');
            const countBadge = group.querySelector('.badge.rounded-pill');
            const items = Array.from(group.querySelectorAll('[data-room-number]'));
            let groupVisibleCount = 0;

            items.forEach(item => {
                const roomNumber = item.getAttribute('data-room-number') || '';
                const roomType = item.getAttribute('data-room-type') || '';
                const matches = roomNumber.includes(searchTerm) || roomType.includes(searchTerm);
                item.style.display = matches ? '' : 'none';
                if (matches) {
                    groupVisibleCount++;
                }
            });

            const visibleItems = items.filter(item => item.style.display !== 'none');
            visibleItems.sort((a, b) => {
                const roomA = parseInt(a.getAttribute('data-room-sort'), 10) || 0;
                const roomB = parseInt(b.getAttribute('data-room-sort'), 10) || 0;
                return sortBy === 'room-desc' ? roomB - roomA : roomA - roomB;
            });

            if (badgeContainer) {
                visibleItems.forEach(item => badgeContainer.appendChild(item));
            }

            if (groupVisibleCount > 0) {
                group.style.display = '';
                if (countBadge) {
                    countBadge.textContent = `${groupVisibleCount} available`;
                }
                totalVisible += groupVisibleCount;
            } else {
                group.style.display = 'none';
            }
        });

        if (sortBy === 'type-asc' || sortBy === 'type-desc') {
            groups.sort((a, b) => {
                const typeA = a.getAttribute('data-room-type-group') || '';
                const typeB = b.getAttribute('data-room-type-group') || '';
                return sortBy === 'type-desc' ? typeB.localeCompare(typeA) : typeA.localeCompare(typeB);
            });
        } else {
            groups.sort((a, b) => {
                const typeA = a.getAttribute('data-room-type-group') || '';
                const typeB = b.getAttribute('data-room-type-group') || '';
                return typeA.localeCompare(typeB);
            });
        }

        groups.forEach(group => list.appendChild(group));

        const noResults = document.getElementById('vacantRoomsNoResults');
        if (noResults) {
            noResults.classList.toggle('d-none', totalVisible > 0);
        }
    }

    function filterAndSortOccupiedRooms() {
        const tbody = document.getElementById('occupiedRoomsTableBody');
        if (!tbody) {
            return;
        }

        const searchTerm = (document.getElementById('occupiedRoomSearch')?.value || '').toLowerCase().trim();
        const sortBy = document.getElementById('occupiedRoomSort')?.value || 'all';
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let visibleCount = 0;

        rows.forEach(row => {
            const roomNumber = (row.getAttribute('data-room-number') || '').toLowerCase();
            const roomType = (row.getAttribute('data-room-type') || '').toLowerCase();
            const guestName = (row.getAttribute('data-guest-name') || '').toLowerCase();
            const folioNumber = (row.getAttribute('data-folio-number') || '').toLowerCase();
            const isOverdue = row.getAttribute('data-is-overdue') === '1';
            const isOpenStay = row.getAttribute('data-is-open-stay') === '1';

            let matchesSearch = roomNumber.includes(searchTerm)
                || roomType.includes(searchTerm)
                || guestName.includes(searchTerm)
                || folioNumber.includes(searchTerm);

            let matchesFilter = true;
            if (sortBy === 'overdue-only') {
                matchesFilter = isOverdue;
            } else if (sortBy === 'open-stay-only') {
                matchesFilter = isOpenStay;
            }

            const visible = matchesSearch && matchesFilter;
            row.style.display = visible ? '' : 'none';
            if (visible) {
                visibleCount++;
            }
        });

        const visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const roomA = parseInt(a.getAttribute('data-room-sort'), 10) || 0;
            const roomB = parseInt(b.getAttribute('data-room-sort'), 10) || 0;
            const guestA = a.getAttribute('data-guest-name') || '';
            const guestB = b.getAttribute('data-guest-name') || '';
            const typeA = a.getAttribute('data-room-type') || '';
            const typeB = b.getAttribute('data-room-type') || '';

            switch (sortBy) {
                case 'room-desc':
                    return roomB - roomA;
                case 'guest-asc':
                    return guestA.localeCompare(guestB) || roomA - roomB;
                case 'guest-desc':
                    return guestB.localeCompare(guestA) || roomA - roomB;
                case 'type-asc':
                    return typeA.localeCompare(typeB) || roomA - roomB;
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
        const sortBy = document.getElementById('checkoutSort')?.value || 'all';
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let visibleCount = 0;

        rows.forEach(row => {
            const guestName = (row.getAttribute('data-guest-name') || '').toLowerCase();
            const roomNumber = (row.getAttribute('data-room-number') || '').toLowerCase();
            const folioNumber = (row.getAttribute('data-folio-number') || '').toLowerCase();
            const isOverdue = row.getAttribute('data-is-overdue') === '1';
            const status = row.getAttribute('data-status');

            let matchesSearch = guestName.includes(searchTerm)
                || roomNumber.includes(searchTerm)
                || folioNumber.includes(searchTerm);

            let matchesFilter = true;
            if (sortBy === 'overdue-only') {
                matchesFilter = isOverdue;
            } else if (sortBy === 'status-pending') {
                matchesFilter = (status === 'CHECKED_IN');
            } else if (sortBy === 'status-done') {
                matchesFilter = (status === 'CHECKED_OUT');
            }

            const visible = matchesSearch && matchesFilter;
            row.style.display = visible ? '' : 'none';
            if (visible) {
                visibleCount++;
            }
        });

        const visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const guestA = a.getAttribute('data-guest-name') || '';
            const guestB = b.getAttribute('data-guest-name') || '';
            const roomA = parseInt(a.getAttribute('data-room-number'), 10) || 0;
            const roomB = parseInt(b.getAttribute('data-room-number'), 10) || 0;

            switch (sortBy) {
                case 'guest-desc':
                    return guestB.localeCompare(guestA);
                case 'room-asc':
                    return roomA - roomB;
                case 'room-desc':
                    return roomB - roomA;
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

    function refreshDashboardInPlace() {
        if (typeof window.fetchLayoutData === 'function') {
            window.fetchLayoutData();
        }
        if (window.Turbo) {
            window.Turbo.visit(window.location.href, { action: 'replace' });
        } else {
            location.reload();
        }
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
                setTimeout(refreshDashboardInPlace, 500);
            })
            .catch(error => {
                window.setBtnLoading(confirmBtn, false);
                showAlert('error', error.message);
            });
    }

    function openExtendDepartureModal(button) {
        const bookingId = button.getAttribute('data-booking-id');
        const guestName = button.getAttribute('data-guest-name') || 'Guest';
        const folioNumber = button.getAttribute('data-folio-number') || '—';
        const roomNumber = button.getAttribute('data-room-number') || '—';
        const roomType = button.getAttribute('data-room-type') || '';
        const status = button.getAttribute('data-status') || '';
        const arrivalDate = button.getAttribute('data-arrival-date');
        const arrivalDisplay = button.getAttribute('data-arrival-display') || arrivalDate || '—';
        const departureDate = button.getAttribute('data-departure-date');
        const departureTime = button.getAttribute('data-departure-time') || '12:00';
        const departureDisplay = button.getAttribute('data-departure-display') || '—';
        const netRate = button.getAttribute('data-net-rate') || '';

        pendingExtendBookingId = bookingId;
        document.getElementById('extendBookingId').value = bookingId;
        document.getElementById('extendModalGuestName').textContent = guestName;
        document.getElementById('extendModalRoomBadge').textContent = 'Room ' + roomNumber;
        document.getElementById('extendModalRoomType').textContent = roomType;
        document.getElementById('extendModalFolioNumber').textContent = folioNumber;

        const statusBadge = document.getElementById('extendModalStatus');
        if (statusBadge) {
            statusBadge.textContent = status === 'CHECKED_IN' ? 'CHECKED IN' : (status === 'RESERVED' ? 'RESERVED' : status);
            statusBadge.className = 'badge-status ' + (status === 'CHECKED_IN' ? 'badge-status-checkedin' : 'badge-status-reserved');
        }

        document.getElementById('extendModalArrival').textContent = arrivalDisplay;
        document.getElementById('extendModalCurrentDeparture').textContent = departureDisplay;

        const dateInput = document.getElementById('extendDepartureDate');
        const timeInput = document.getElementById('extendDepartureTime');
        const rateInput = document.getElementById('extendNetRate');
        const errorAlert = document.getElementById('extendErrorAlert');
        const hintEl = document.getElementById('extendDateHint');

        errorAlert.classList.add('d-none');
        errorAlert.textContent = '';
        dateInput.classList.remove('is-invalid');
        rateInput.value = '';
        rateInput.placeholder = netRate ? 'Current: ₱' + parseFloat(netRate).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : 'Leave blank to keep current rate';

        // Calculate minimum extension date safely using date components
        const now = window.currentServerTime || new Date();
        const pad = n => String(n).padStart(2, '0');
        const toIsoDate = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        // Base minimum: tomorrow
        const tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
        let minDate = new Date(tomorrow);

        // Must be after arrival date
        if (arrivalDate) {
            const arrParts = arrivalDate.split('-');
            if (arrParts.length === 3) {
                const dayAfterArr = new Date(parseInt(arrParts[0], 10), parseInt(arrParts[1], 10) - 1, parseInt(arrParts[2], 10) + 1);
                if (dayAfterArr > minDate) {
                    minDate = dayAfterArr;
                }
            }
        }

        // If current departure date is set and in the future, new departure must be strictly after it
        if (departureDate) {
            const depParts = departureDate.split('-');
            if (depParts.length === 3) {
                const dayAfterDep = new Date(parseInt(depParts[0], 10), parseInt(depParts[1], 10) - 1, parseInt(depParts[2], 10) + 1);
                if (dayAfterDep > minDate) {
                    minDate = dayAfterDep;
                }
            }
        }

        const minDateStr = toIsoDate(minDate);
        dateInput.min = minDateStr;
        dateInput.value = minDateStr;
        timeInput.value = departureTime;

        if (hintEl) {
            hintEl.textContent = 'Earliest extension date: ' + minDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '.';
        }

        extendDepartureModal?.show();
    }

    function submitExtendDeparture() {
        if (!pendingExtendBookingId) return;

        const confirmBtn = document.getElementById('confirmExtendDepartureBtn');
        const dateInput = document.getElementById('extendDepartureDate');
        const timeInput = document.getElementById('extendDepartureTime');
        const rateInput = document.getElementById('extendNetRate');
        const errorAlert = document.getElementById('extendErrorAlert');

        const newDate = dateInput.value;
        if (!newDate) {
            errorAlert.textContent = 'Please select a new departure date.';
            errorAlert.classList.remove('d-none');
            dateInput.classList.add('is-invalid');
            return;
        }

        if (dateInput.min && newDate < dateInput.min) {
            errorAlert.textContent = 'New departure date must be on or after ' + dateInput.min + '.';
            errorAlert.classList.remove('d-none');
            dateInput.classList.add('is-invalid');
            return;
        }

        dateInput.classList.remove('is-invalid');
        errorAlert.classList.add('d-none');

        window.setBtnLoading(confirmBtn, true, 'Updating...');

        const payload = {
            booking_id: pendingExtendBookingId,
            departure_date: newDate,
            departure_time: timeInput.value || '12:00'
        };

        if (rateInput.value && !isNaN(parseFloat(rateInput.value))) {
            payload.net_rate = rateInput.value;
        }

        postJson('{{ route("frontdesk.booking.extend") }}', payload)
            .then(data => {
                extendDepartureModal?.hide();
                showAlert('success', data.message);
                setTimeout(refreshDashboardInPlace, 500);
            })
            .catch(error => {
                window.setBtnLoading(confirmBtn, false);
                errorAlert.textContent = error.message;
                errorAlert.classList.remove('d-none');
            });
    }

    window.swalConfirmCancelDashboardReservation = function(btn) {
        var form = btn.closest('form');
        if (typeof Swal === 'undefined') {
            if (confirm('Cancel this reservation? This action cannot be undone.')) {
                if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
            }
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Cancel Reservation?',
            text: 'This action cannot be undone. The reservation will be marked as cancelled.',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-ban me-1"></i> Yes, Cancel It',
            cancelButtonText: 'Keep Reservation',
            confirmButtonColor: '#dc3545',
            reverseButtons: true,
        }).then(function(result) {
            if (result.isConfirmed && form) {
                if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
            }
        });
    };

    function checkOutGuest(bookingId) {
        openCheckOutModal(bookingId);
    }

    function markRoomCleaned(roomId, btn) {
        window.setBtnLoading(btn, true, 'Updating...');
        postJson('{{ route("frontdesk.room.mark-cleaned") }}', { room_id: roomId })
            .then(data => {
                roomActionModal.hide();
                showAlert('success', data.message);
                setTimeout(refreshDashboardInPlace, 500);
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
                setTimeout(refreshDashboardInPlace, 500);
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
                setTimeout(refreshDashboardInPlace, 500);
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
                setTimeout(refreshDashboardInPlace, 500);
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
            const folioUrl = room.active_booking.folio_id ? `/frontdesk/guest-folio/${room.active_booking.folio_id}` : '/frontdesk/guest-folio';
            const hasUnpaidBalance = Boolean(room.active_booking.has_unpaid_balance);
            const checkOutLabel = hasUnpaidBalance
                ? 'Check Out (Settle Balance)'
                : 'Check Out';

            actions.innerHTML = `
                <div class="alert alert-light border mb-3">
                    <i class="fa-solid fa-user text-primary"></i>
                    <strong>Guest:</strong> ${room.active_booking.guest_name || 'Unknown'}
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 w-100">
                    <a href="${folioUrl}" class="btn btn-danger fw-semibold py-2 px-3 flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm text-truncate">
                        <i class="fa-solid fa-file-invoice-dollar fs-6 flex-shrink-0"></i>
                        <span class="text-truncate">${checkOutLabel}</span>
                    </a>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <a href="${folioUrl}" class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center shadow-sm" title="View Folio" style="background: #334c42; width: 42px; height: 42px;">
                            <i class="fa-solid fa-file-invoice"></i>
                        </a>
                    </div>
                </div>
            `;
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