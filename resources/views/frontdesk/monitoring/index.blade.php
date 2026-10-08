@extends('layouts.app')

@section('title', 'Monitoring & Calendar')
@section('pageTitle', 'Monitoring & Calendar')
@section('pageSubtitle', 'Live room & facility occupancy status, reservation timelines, and schedules')

@push('styles')
<meta name="turbo-cache-control" content="no-cache">
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
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
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 16px;
    }

    .room-box {
        height: 80px;
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

    /* Switchers & View Toggles */
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
        min-width: 220px;
        max-width: 240px;
        width: 230px;
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
        min-width: 72px;
        width: 72px;
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
        height: 56px;
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
        min-width: 72px;
        width: 72px;
        height: 56px;
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
    /* Facilities Event Bars */
    .timeline-event-fac-approved {
        background: linear-gradient(135deg, #334c42, #476659);
        color: #ffffff;
        border: 1px solid #283d35;
    }
    .timeline-event-fac-active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        border: 1px solid #1e40af;
    }
    .timeline-event-fac-pending {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        border: 1px solid #b45309;
    }
    .timeline-status-pill {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }

    /* Facility Cards Grid */
    .facility-grid-card {
        border: 1px solid #e2d3be;
        background: #ffffff;
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .facility-grid-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(80, 69, 56, 0.1);
    }
</style>

<!-- MAIN MONITORING & CALENDAR CARD -->
<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" id="monitoringAppContainer" data-turbo-cache="false" style="background: #ffffff; border: 1px solid #c2a889 !important;">
    <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: rgba(51, 76, 66, 0.1); color: #334c42;">
                <i class="fa-solid fa-bed fs-5" id="monitoringHeaderIcon"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 font-display" id="monitoringMainTitle" style="color: #1a1a1a;">
                    Hotel Room Monitoring & Calendar
                </h5>
                <small class="text-muted" id="monitoringSubTitle">Live room status, occupancy schedule, and multi-day reservation timeline</small>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- PANEL SWITCHER: Rooms / Facilities --}}
            <div class="btn-group shadow-sm" id="monitoringPanelSwitcher" role="group" aria-label="Entity Switcher">
                <button type="button" class="btn btn-sm active" id="btnRoomsPanel"
                        onclick="window.switchMonitoringPanel('rooms')"
                        style="background: #e8f0ec; color: #334c42; border-color: #627e71; font-size: 0.88rem; font-weight: 600;">
                    <i class="fa-solid fa-bed me-1"></i> Rooms
                </button>
                <button type="button" class="btn btn-sm btn-light border" id="btnFacilitiesPanel"
                        onclick="window.switchMonitoringPanel('facilities')"
                        style="font-size: 0.88rem; font-weight: 600;">
                    <i class="fa-solid fa-building me-1"></i> Facilities
                </button>
            </div>

            {{-- VIEW MODE SWITCHER: Calendar Timeline / Grid --}}
            <div class="btn-group view-toggle-group shadow-sm" id="monitoringViewSwitcher" role="group" aria-label="View Switcher">
                <button type="button" class="btn active" id="btnTimelineView" onclick="window.switchViewMode('timeline')">
                    <i class="fa-solid fa-calendar-week me-1"></i> Calendar Timeline
                </button>
                <button type="button" class="btn" id="btnGridView" onclick="window.switchViewMode('grid')">
                    <i class="fa-solid fa-grip me-1"></i> <span id="btnGridViewLabel">Room Grid</span>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-4 pt-2">

        <!-- 1. ROOMS LEGEND & LIVE COUNTERS -->
        <div id="roomsLegendContainer" class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 rounded-3" style="background: #faf6f0; border: 1px solid rgba(130, 117, 103, 0.2);">
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
                <span class="small fw-semibold text-muted" id="roomsTimelineRangeDisplay">Loading range...</span>
            </div>
        </div>

        <!-- 2. FACILITIES LEGEND & LIVE COUNTERS (shown when Facilities is selected) -->
        <div id="facilitiesLegendContainer" class="d-none d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 rounded-3" style="background: #faf6f0; border: 1px solid rgba(130, 117, 103, 0.2);">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #627e71;"></span>
                    <span>Available</span>
                    <span class="badge ms-1 px-2 py-0.5" id="facLegendAvailable" style="background: #e8f0ec; color: #334c42; font-size: 0.75rem;">{{ max(0, $totalFacilities - $bookedFacilitiesToday) }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #2563eb;"></span>
                    <span>In Use / Booked Today</span>
                    <span class="badge ms-1 px-2 py-0.5" id="facLegendBooked" style="background: #dbeafe; color: #1e40af; font-size: 0.75rem;">{{ $bookedFacilitiesToday }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #334c42;"></span>
                    <span>Reserved (Approved)</span>
                    <span class="badge ms-1 px-2 py-0.5" id="facLegendReserved" style="background: #e8f0ec; color: #334c42; font-size: 0.75rem;">0</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #f59e0b;"></span>
                    <span>Pending Approval</span>
                    <span class="badge ms-1 px-2 py-0.5" id="facLegendPending" style="background: #fef3c7; color: #92400e; font-size: 0.75rem;">{{ $pendingFacilityCount }}</span>
                </div>

                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #6c757d;"></span>
                    <span>Inactive / Maintenance</span>
                    <span class="badge ms-1 px-2 py-0.5" id="facLegendInactive" style="background: #f1f5f9; color: #475569; font-size: 0.75rem;">0</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-muted me-2" id="facilitiesTimelineRangeDisplay">Loading range...</span>
                @can('manage-reservations')
                <a href="{{ route('frontdesk.facility-reservations.create') }}?return_to=monitoring" class="btn btn-sm text-white rounded-pill px-3 shadow-xs" style="background: #334c42;">
                    <i class="fa-solid fa-plus me-1"></i> Book Facility
                </a>
                @endcan
            </div>
        </div>

        <!-- TIMELINE TOOLBAR (shown in Timeline View Mode) -->
        <div id="monitoringTimelineToolbar" class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
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
                <div class="d-flex align-items-center gap-1" id="timelineDurationContainer">
                    {{-- Room Durations (shown when on Rooms) --}}
                    <div class="btn-group btn-group-sm shadow-sm" id="roomDurationGroup" role="group" aria-label="Room Duration">
                        <button type="button" class="btn btn-light border timeline-duration-btn" data-days="7">7 Days</button>
                        <button type="button" class="btn btn-light border timeline-duration-btn active fw-bold" data-days="14" style="background: #e8f0ec; color: #334c42; border-color: #627e71 !important;">14 Days</button>
                        <button type="button" class="btn btn-light border timeline-duration-btn" data-days="30">30 Days</button>
                    </div>

                    {{-- Facility Durations & Hourly Toggle (shown when on Facilities) --}}
                    <div class="btn-group btn-group-sm shadow-sm d-none" id="facilityDurationGroup" role="group" aria-label="Facility Duration">
                        <button type="button" class="btn btn-light border timeline-fac-mode-btn" data-mode="hourly" title="Hourly time-slot schedule for selected day">
                            <i class="fa-solid fa-clock me-1 text-primary"></i> Hourly (Day)
                        </button>
                        <button type="button" class="btn btn-light border timeline-fac-mode-btn" data-mode="7">7 Days</button>
                        <button type="button" class="btn btn-light border timeline-fac-mode-btn active fw-bold" data-mode="14" style="background: #e8f0ec; color: #334c42; border-color: #627e71 !important;">14 Days</button>
                        <button type="button" class="btn btn-light border timeline-fac-mode-btn" data-mode="30">30 Days</button>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="d-flex align-items-center gap-2">
                {{-- Room Types Filter (shown when on Rooms) --}}
                <select class="form-select form-select-sm shadow-sm" id="timelineRoomTypeFilter" style="width: 180px; border-color: #c2a889;">
                    <option value="ALL">All Room Types</option>
                </select>

                {{-- Facility Filter (shown when on Facilities) --}}
                <select class="form-select form-select-sm shadow-sm d-none" id="timelineFacilityFilter" style="width: 200px; border-color: #c2a889;">
                    <option value="ALL">All Facilities</option>
                </select>

                <button class="btn btn-sm btn-light border shadow-sm px-2.5" id="btnRefreshTimeline" title="Refresh Timeline">
                    <i class="fa-solid fa-rotate"></i>
                </button>
            </div>
        </div>

        <!-- VIEW 1: ROOMS CALENDAR TIMELINE -->
        <div id="roomsTimelineViewContainer">
            <div class="timeline-wrapper shadow-sm">
                <div class="timeline-scroll-container" id="roomsTimelineScrollContainer">
                    <div id="roomsTimelineContent">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                            Loading room reservation timeline and occupancy calendar...
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-2 px-1 small text-muted">
                <div>
                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> 
                    <strong>Tip:</strong> Click any <strong>Reserved</strong> or <strong>Occupied</strong> bar for quick check-in, check-out, or folio details. Click any <strong>Available</strong> date cell to quickly book or change room status.
                </div>
                <div>
                    <span class="badge bg-light text-dark border">Scroll horizontally ➔</span>
                </div>
            </div>
        </div>

        <!-- VIEW 2: ROOM GRID VIEW -->
        <div id="roomsGridViewContainer" class="d-none">
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

        <!-- VIEW 3: FACILITIES CALENDAR TIMELINE -->
        <div id="facilitiesTimelineViewContainer" class="d-none">
            {{-- Hourly View Helper Notice --}}
            <div id="facilityHourlyHelperBar" class="d-none alert alert-light border py-2 px-3 mb-2 rounded-3 d-flex flex-wrap justify-content-between align-items-center shadow-xs" style="background: #faf6f0; border-color: #c2a889 !important;">
                <div>
                    <i class="fa-solid fa-clock me-2" style="color: #334c42;"></i>
                    <strong>Facility Hourly Schedule:</strong> Viewing 24-hour time slots for <span id="facilityHourlyDateLabel" class="fw-bold" style="color: #334c42;">—</span>.
                    <span class="text-muted ms-1 small">Click an open time slot to book, or click any reservation to inspect/manage.</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs" onclick="window.switchToFacilityMultiDayView()">
                    <i class="fa-solid fa-calendar-days me-1"></i> Multi-Day View (14 Days)
                </button>
            </div>

            <div class="timeline-wrapper shadow-sm">
                <div class="timeline-scroll-container" id="facilitiesTimelineScrollContainer">
                    <div id="facilitiesTimelineContent">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                            Loading facility reservation timeline and booking calendar...
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-2 px-1 small text-muted">
                <div>
                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> 
                    <strong>Tip:</strong> Click any <strong>Facility Reservation bar</strong> to review details, check-in, or manage the booking. Click any <strong>Available</strong> date cell to quickly book the facility for that date.
                </div>
                <div>
                    <span class="badge bg-light text-dark border">Scroll horizontally ➔</span>
                </div>
            </div>
        </div>

        <!-- VIEW 4: FACILITY GRID VIEW -->
        <div id="facilitiesGridViewContainer" class="d-none">
            @if(isset($facilitiesList) && $facilitiesList->count() > 0)
                <div class="row g-3">
                    @foreach($facilitiesList as $fac)
                        @php
                            $facBookedToday = $todayFacilityReservations->filter(function($r) use ($fac) {
                                return ($r->facility_id === $fac->facility_id || $r->reservedFacilities->contains('facility_id', $fac->facility_id))
                                    && ($r->status === 'approved' || $r->status === 'active');
                            })->isNotEmpty();

                            $facPendingToday = $todayFacilityReservations->filter(function($r) use ($fac) {
                                return ($r->facility_id === $fac->facility_id || $r->reservedFacilities->contains('facility_id', $fac->facility_id))
                                    && $r->status === 'pending';
                            })->isNotEmpty();

                            $facStatusColor = $facBookedToday ? '#2563eb' : ($facPendingToday ? '#f59e0b' : '#627e71');
                            $facStatusBg = $facBookedToday ? '#dbeafe' : ($facPendingToday ? '#fef3c7' : '#e8f0ec');
                            $facStatusText = $facBookedToday ? '#1e40af' : ($facPendingToday ? '#92400e' : '#334c42');
                            $facStatusLabel = $facBookedToday ? 'Booked Today' : ($facPendingToday ? 'Pending' : 'Available');
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="facility-grid-card h-100 shadow-sm position-relative"
                                 style="cursor: pointer;"
                                 onclick="window.openFacilityQuickInfo({{ $fac->facility_id }})">
                                {{-- Top status accent stripe --}}
                                <div style="height: 5px; background: {{ $facStatusColor }};"></div>
                                <div class="p-3">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        @if(!empty($fac->images) && count($fac->images) > 0)
                                            <img src="{{ \App\Models\Facility::imageUrl($fac->images[0]) }}"
                                                 alt="{{ $fac->name }}"
                                                 class="rounded-3 flex-shrink-0"
                                                 style="width: 52px; height: 52px; object-fit: cover;">
                                        @else
                                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                                 style="width: 52px; height: 52px; background: {{ $facStatusBg }}; color: {{ $facStatusText }};">
                                                <i class="fa-solid fa-building fs-4"></i>
                                            </div>
                                        @endif
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-truncate font-display" style="color: #1a1a1a; font-size: 0.95rem;" title="{{ $fac->name }}">
                                                {{ $fac->name }}
                                            </div>
                                            <div class="text-muted small">
                                                ₱{{ number_format($fac->rate, 2) }}/{{ $fac->rate_type === 'hourly' ? 'hr' : 'day' }}
                                                @if($fac->capacity) &bull; {{ $fac->capacity }} pax @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                                        <span class="badge rounded-pill px-2.5 py-1"
                                              style="background: {{ $facStatusBg }}; color: {{ $facStatusText }}; font-size: 0.75rem; font-weight: 700;">
                                            <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background: {{ $facStatusColor }}; margin-right:5px;"></span>
                                            {{ $facStatusLabel }}
                                        </span>
                                        <div class="d-flex align-items-center gap-1">
                                            @can('manage-reservations')
                                            <a href="{{ route('frontdesk.facility-reservations.create') }}?facility={{ $fac->facility_id }}&return_to=monitoring"
                                               class="btn btn-xs rounded-pill px-2.5 py-1 text-white"
                                               style="background: #334c42; font-size: 0.75rem;"
                                               onclick="event.stopPropagation()"
                                               title="Book this facility">
                                                <i class="fa-solid fa-plus me-1"></i> Book
                                            </a>
                                            <a href="{{ route('frontdesk.facility-reservations.index') }}?facility_id={{ $fac->facility_id }}"
                                               class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1"
                                               style="font-size: 0.75rem;"
                                               onclick="event.stopPropagation()">
                                                Bookings
                                            </a>
                                            @endcan
                                            @can('manage-facilities')
                                            <form method="POST" action="{{ route('frontdesk.facilities.toggle', $fac->facility_id) }}?return_to=monitoring" class="d-inline" onclick="event.stopPropagation()">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.75rem;" title="{{ $fac->is_active ? 'Mark Out of Order / Maintenance' : 'Mark Active' }}">
                                                    <i class="fa-solid fa-power-off {{ $fac->is_active ? 'text-success' : 'text-danger' }}"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                    {{-- Today's bookings for this facility --}}
                                    @php
                                        $facTodayRes = $todayFacilityReservations->first(function($r) use ($fac) {
                                            return $r->facility_id === $fac->facility_id || $r->reservedFacilities->contains('facility_id', $fac->facility_id);
                                        });
                                    @endphp
                                    @if($facTodayRes)
                                        <div class="mt-2 pt-2 border-top small">
                                            <div class="fw-semibold text-truncate" style="color: #1a1a1a;">{{ $facTodayRes->booker_name }}</div>
                                            @if($facTodayRes->start_time && $facTodayRes->end_time)
                                                <div class="text-muted">
                                                    {{ \Carbon\Carbon::parse($facTodayRes->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($facTodayRes->end_time)->format('g:i A') }}
                                                </div>
                                            @else
                                                <div class="text-muted">Full Day</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TODAY'S FACILITY SCHEDULE TABLE --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="fw-bold mb-3 font-display" style="color: #1a1a1a; font-size: 1rem;">
                        <i class="fa-solid fa-clock me-2" style="color: #334c42;"></i>Today's Facility Schedule
                        <span class="badge ms-1" style="background: #e8f0ec; color: #334c42; font-size: 0.8rem;">{{ $todayFacilityReservations->count() }}</span>
                        @if($pendingFacilityCount > 0)
                            <span class="badge bg-danger ms-1" style="font-size: 0.8rem;">{{ $pendingFacilityCount }} pending approval</span>
                        @endif
                    </div>

                    @if($todayFacilityReservations->count() > 0)
                        <div class="table-responsive mb-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: transparent; border-bottom: 2px solid #c2a889;">
                                    <tr class="small fw-bold" style="color: #1a1a1a;">
                                        <th class="ps-3">Ref #</th>
                                        <th>Facility</th>
                                        <th>Booker</th>
                                        <th>Time / Duration</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayFacilityReservations as $res)
                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                            <td class="ps-3"><span class="fw-semibold" style="color: #334c42; font-size: 0.85rem;">{{ $res->reference_number }}</span></td>
                                            <td>
                                                <div class="fw-semibold" style="color: #1a1a1a;">{{ $res->facility?->name ?? '—' }}</div>
                                                <small class="text-muted text-capitalize">{{ $res->facility?->rate_type }} rate</small>
                                            </td>
                                            <td>
                                                <div style="color: #1a1a1a; font-weight: 500;">{{ $res->booker_name }}</div>
                                                @if($res->booker_contact)
                                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $res->booker_contact }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($res->start_time && $res->end_time)
                                                    <span class="fw-semibold" style="color: #262626;">
                                                        {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }} &ndash; {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}
                                                    </span>
                                                    <small class="text-muted d-block">({{ $res->duration_label }})</small>
                                                @else
                                                    <span class="fw-semibold" style="color: #262626;">Full Day</span>
                                                @endif
                                            </td>
                                            <td><span class="fw-bold text-success">₱{{ number_format($res->estimated_amount, 2) }}</span></td>
                                            <td>
                                                @if($res->status === 'approved')
                                                    <span class="badge bg-success rounded-pill px-2 py-1">Approved</span>
                                                @elseif($res->status === 'pending')
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Pending</span>
                                                @elseif($res->status === 'rejected')
                                                    <span class="badge bg-danger rounded-pill px-2 py-1">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary rounded-pill px-2 py-1">{{ ucfirst($res->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-1">
                                                    @can('manage-reservations')
                                                    @if($res->status === 'approved')
                                                    <form method="POST" action="{{ route('frontdesk.facility-reservations.check-in', $res) }}?return_to=monitoring" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Check-In Guest">
                                                            <i class="fa-solid fa-play me-1"></i> Check In
                                                        </button>
                                                    </form>
                                                    @elseif($res->status === 'active')
                                                    <form method="POST" action="{{ route('frontdesk.facility-reservations.time-out', $res) }}?return_to=monitoring" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Time Out (Complete)">
                                                            <i class="fa-solid fa-stop me-1"></i> Time Out
                                                        </button>
                                                    </form>
                                                    @elseif($res->status === 'pending')
                                                    <form method="POST" action="{{ route('frontdesk.facility-reservations.approve', $res) }}?return_to=monitoring" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Approve Booking">
                                                            <i class="fa-solid fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                    @endif
                                                    <a href="{{ route('frontdesk.facility-reservations.show', $res) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                                        <i class="fa-solid fa-eye me-1"></i> Review
                                                    </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 rounded-3" style="background: #faf7f2; border: 1px dashed #c2a889;">
                            <i class="fa-solid fa-calendar-check d-block fs-3 mb-2" style="color: #c2a889;"></i>
                            <div class="fw-bold font-display" style="color: #1a1a1a;">No facility bookings today</div>
                            <small class="text-muted">All active facilities are currently available.</small>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-building d-block fs-1 mb-3 text-muted"></i>
                    <h6 class="fw-bold">No active facilities found</h6>
                    <p class="text-muted small">Add facilities through the Admin Panel to monitor them here.</p>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. ROOM ACTION MODAL (From Grid View) -->
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

<!-- 2. CALENDAR ROOM BOOKING DETAILS MODAL -->
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

<!-- 3. CALENDAR QUICK RESERVE / ROOM ACTION MODAL -->
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

<!-- 4. FACILITY RESERVATION DETAILS MODAL (Timeline Bar Click) -->
<div class="modal fade" id="facilityReservationModal" tabindex="-1" aria-labelledby="facilityReservationModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold font-display mb-0" id="facilityReservationModalLabel" style="color: #1a1a1a;">
                        Facility Booking Details
                    </h5>
                    <small class="text-muted" id="facModalRefSubtitle">Reference # —</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Facility & Status Banner -->
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3" style="background: #f8f3ed; border: 1px solid #c2a889;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-building fs-5" style="color: #334c42;"></i>
                        <div>
                            <div class="fw-bold fs-6" id="facModalFacilityName" style="color: #1a1a1a;">Facility Name</div>
                            <small class="text-muted" id="facModalBillingInfo">Rate Type</small>
                        </div>
                    </div>
                    <span class="badge px-2.5 py-1.5 fw-semibold" id="facModalStatusBadge">STATUS</span>
                </div>

                <!-- Event & Booker Info -->
                <div class="card border-0 mb-3 rounded-3" style="background: #faf6f0; border: 1px solid #e2d3be !important;">
                    <div class="card-body p-3">
                        <div class="mb-2 pb-2 border-bottom" style="border-color: #ede4d8 !important;">
                            <span class="text-muted small d-block">Event Name</span>
                            <div class="fw-bold fs-6" id="facModalEventName" style="color: #1a1a1a;">—</div>
                            <small class="text-muted d-block mt-0.5" id="facModalEventDetails"></small>
                        </div>

                        <div class="mb-2 pb-2 border-bottom" style="border-color: #ede4d8 !important;">
                            <span class="text-muted small d-block">Booker Information</span>
                            <div class="fw-bold" id="facModalBookerName" style="color: #1a1a1a;">—</div>
                            <div class="small text-muted" id="facModalBookerContact">—</div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-muted small d-block">Date(s)</span>
                                <span class="fw-semibold small" id="facModalDates" style="color: #1a1a1a;">—</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small d-block">Time Schedule</span>
                                <span class="fw-semibold small" id="facModalTime" style="color: #1a1a1a;">—</span>
                            </div>
                            <div class="col-6 pt-2">
                                <span class="text-muted small d-block">Estimated Amount</span>
                                <span class="fw-bold text-success" id="facModalAmount">₱0.00</span>
                            </div>
                            <div class="col-6 pt-2">
                                <span class="text-muted small d-block">Reservation Type</span>
                                <span class="small fw-semibold" id="facModalConsolidated">Direct Facility</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-column gap-2" id="facModalActionButtons">
                    <!-- Injected via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5. FACILITY QUICK INFO MODAL (Click on Facility name or card) -->
<div class="modal fade" id="facilityQuickInfoModal" tabindex="-1" aria-labelledby="facilityQuickInfoModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold font-display mb-0" id="facQuickInfoName">Facility Details</h5>
                    <small class="text-muted" id="facQuickInfoRate">Rate info</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background: #f8f3ed; border: 1px solid #c2a889;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Current Live Status:</span>
                        <span class="badge" id="facQuickInfoStatusBadge">AVAILABLE</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Capacity:</span>
                        <span class="fw-bold" id="facQuickInfoCapacity">—</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="#" id="facQuickBtnBook" class="btn text-white fw-semibold py-2 shadow-sm" style="background: #334c42;">
                        <i class="fa-solid fa-calendar-plus me-1"></i> Book This Facility
                    </a>
                    <a href="#" id="facQuickBtnViewReservations" class="btn btn-outline-secondary fw-semibold py-2">
                        <i class="fa-solid fa-list me-1"></i> View All Bookings
                    </a>
                    @can('manage-facilities')
                    <form id="facQuickToggleForm" method="POST" action="" class="d-none">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="return_to" value="monitoring">
                        <button type="submit" id="facQuickBtnToggle" class="btn btn-outline-warning fw-semibold py-2 w-100">
                            <i class="fa-solid fa-power-off me-1"></i> Mark Out of Order / Maintenance
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5B. FACILITY EXTEND MODAL -->
<div class="modal fade" id="facilityExtendModal" tabindex="-1" aria-labelledby="facilityExtendModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="facilityExtendForm" action="" class="modal-content border-0 shadow rounded-4 overflow-hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="return_to" value="monitoring">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold font-display" id="facilityExtendModalLabel" style="color: #1a1a1a;">
                    <i class="fa-solid fa-clock me-2" style="color: #334c42;"></i> Extend Facility Reservation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background: #f8f3ed; border: 1px solid #c2a889;">
                    <span class="text-muted small d-block">Reservation Reference</span>
                    <strong id="facExtendRefNum" class="fs-6" style="color: #334c42;">—</strong>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="facExtendEndDate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">New End Time <span class="text-danger">*</span></label>
                    <input type="time" name="end_time" id="facExtendEndTime" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #334c42;">Confirm Extension</button>
            </div>
        </form>
    </div>
</div>

<!-- 5C. FACILITY CANCEL / REJECT MODAL -->
<div class="modal fade" id="facilityCancelModal" tabindex="-1" aria-labelledby="facilityCancelModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="facilityCancelForm" action="" class="modal-content border-0 shadow rounded-4 overflow-hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="return_to" value="monitoring">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-danger font-display" id="facilityCancelModalLabel">
                    <i class="fa-solid fa-ban me-2"></i> Cancel Facility Reservation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Are you sure you want to cancel or reject reservation <strong id="facCancelRefNum" class="text-dark"></strong>?</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Cancellation / Rejection Reason <span class="text-danger">*</span></label>
                    <textarea name="admin_notes" id="facCancelReason" class="form-control" rows="3" placeholder="State reason for staff and client records..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Keep Booking</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4" id="facCancelSubmitBtn">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MOVE RESERVATION MODAL -->
<div class="modal fade" id="extendDepartureModal" tabindex="-1" aria-labelledby="extendDepartureModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="extendDepartureModalLabel" style="color: #1a1a1a;">
                    <i class="fa-solid fa-calendar-plus me-2" style="color: #334c42;"></i> Move Reservation Dates
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                            <span class="text-muted small d-block">Current Arrival:</span>
                            <span class="small fw-semibold text-primary" id="extendModalArrival" style="color: #262626;">—</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted small d-block">Current Departure:</span>
                            <span class="small fw-semibold text-danger" id="extendModalCurrentDeparture">—</span>
                        </div>
                    </div>
                </div>

                <form id="extendDepartureForm">
                    <input type="hidden" id="extendBookingId">
                    <div class="row g-2 mb-3" id="extendArrivalContainer">
                        <div class="col-6">
                            <label for="extendArrivalDate" class="form-label fw-semibold small" style="color: #1a1a1a;">
                                New Arrival Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="extendArrivalDate" required style="border: 1px solid #c2a889;">
                        </div>
                        <div class="col-6">
                            <label for="extendArrivalTime" class="form-label fw-semibold small" style="color: #1a1a1a;">
                                New Arrival Time
                            </label>
                            <input type="time" class="form-control" id="extendArrivalTime" value="14:00" style="border: 1px solid #c2a889;">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="extendDepartureDate" class="form-label fw-semibold small" style="color: #1a1a1a;">
                                New Departure Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="extendDepartureDate" required style="border: 1px solid #c2a889;">
                        </div>
                        <div class="col-6">
                            <label for="extendDepartureTime" class="form-label fw-semibold small" style="color: #1a1a1a;">
                                New Departure Time
                            </label>
                            <input type="time" class="form-control" id="extendDepartureTime" value="12:00" style="border: 1px solid #c2a889;">
                        </div>
                    </div>
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
                    <i class="fa-solid fa-check me-1"></i> Update Dates
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 7. CHECK-IN CONFIRMATION MODAL -->
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

@endsection

@push('scripts')
<script>
    (function() {
        const roomsByType = @json($roomsByType);
        const serverInitialPanel = @json($initialPanel ?? null);

        // State variables
        let currentPanel = serverInitialPanel || localStorage.getItem('hotel_monitoring_panel') || 'rooms';
        let currentViewMode = localStorage.getItem('hotel_monitoring_view_mode') || 'timeline';
        let timelineFacilityMode = 'multiday'; // 'hourly' or 'multiday'

        function getTodayDateStr() {
            const d = new Date();
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        let timelineStartDate = getTodayDateStr();
        let timelineDays = 14;
        let timelineRoomType = 'ALL';
        let timelineFacilityId = 'ALL';

        let roomTimelineCache = null;
        let facilityTimelineCache = null;
        let monitoringPageGeneration = 0;
        let timelineRequestControllers = new Set();

        // Modals instances
        let roomActionModal = null;
        let calendarBookingModal = null;
        let calendarQuickReserveModal = null;
        let facilityReservationModal = null;
        let facilityQuickInfoModal = null;
        let facilityExtendModal = null;
        let facilityCancelModal = null;
        let extendDepartureModal = null;
        let checkInConfirmModal = null;

        function initMonitoringModals() {
            const ramEl = document.getElementById('roomActionModal');
            if (ramEl && window.bootstrap) roomActionModal = bootstrap.Modal.getOrCreateInstance(ramEl);

            const cbmEl = document.getElementById('calendarBookingModal');
            if (cbmEl && window.bootstrap) calendarBookingModal = bootstrap.Modal.getOrCreateInstance(cbmEl);

            const cqrmEl = document.getElementById('calendarQuickReserveModal');
            if (cqrmEl && window.bootstrap) calendarQuickReserveModal = bootstrap.Modal.getOrCreateInstance(cqrmEl);

            const frmEl = document.getElementById('facilityReservationModal');
            if (frmEl && window.bootstrap) facilityReservationModal = bootstrap.Modal.getOrCreateInstance(frmEl);

            const fqiEl = document.getElementById('facilityQuickInfoModal');
            if (fqiEl && window.bootstrap) facilityQuickInfoModal = bootstrap.Modal.getOrCreateInstance(fqiEl);

            const femEl = document.getElementById('facilityExtendModal');
            if (femEl && window.bootstrap) facilityExtendModal = bootstrap.Modal.getOrCreateInstance(femEl);

            const fcmEl = document.getElementById('facilityCancelModal');
            if (fcmEl && window.bootstrap) facilityCancelModal = bootstrap.Modal.getOrCreateInstance(fcmEl);

            const edmEl = document.getElementById('extendDepartureModal');
            if (edmEl && window.bootstrap) extendDepartureModal = bootstrap.Modal.getOrCreateInstance(edmEl);

            const cicmEl = document.getElementById('checkInConfirmModal');
            if (cicmEl && window.bootstrap) checkInConfirmModal = bootstrap.Modal.getOrCreateInstance(cicmEl);
        }

        function closeAllOpenModals() {
            [roomActionModal, calendarBookingModal, calendarQuickReserveModal, facilityReservationModal, facilityQuickInfoModal, facilityExtendModal, facilityCancelModal, extendDepartureModal, checkInConfirmModal].forEach(m => {
                if (m) {
                    try { m.hide(); } catch(e) {}
                }
            });
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        }

        function cleanupMonitoring() {
            monitoringPageGeneration++;
            timelineRequestControllers.forEach(controller => controller.abort());
            timelineRequestControllers.clear();
            closeAllOpenModals();
        }

        function initMonitoringPage() {
            const appContainer = document.getElementById('monitoringAppContainer');
            if (!appContainer) return;
            if (document.documentElement.hasAttribute('data-turbo-preview')) return;
            if (appContainer.dataset.initialized === 'true') return;
            appContainer.dataset.initialized = 'true';

            monitoringPageGeneration++;
            timelineRequestControllers.forEach(controller => controller.abort());
            timelineRequestControllers.clear();

            // Invalidate cache on each view init to guarantee live state
            roomTimelineCache = null;
            facilityTimelineCache = null;

            initMonitoringModals();
            initToolbarControls();

            // Initial render of room grid
            const initialType = Object.keys(roomsByType)[0];
            if (initialType) renderRoomGrid(initialType);

            // Bind room filter buttons
            document.querySelectorAll('.room-filter-btn').forEach(btn => {
                btn.onclick = function() {
                    document.querySelectorAll('.room-filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    renderRoomGrid(this.getAttribute('data-room-type'));
                };
            });

            // Initial load based on active panel
            window.switchMonitoringPanel(currentPanel);
        }

        // Prevent listener stacking: clean up old handlers on document
        if (window._monitoringTurboLoadHandler) {
            document.removeEventListener('turbo:load', window._monitoringTurboLoadHandler);
        }
        if (window._monitoringTurboBeforeVisitHandler) {
            document.removeEventListener('turbo:before-visit', window._monitoringTurboBeforeVisitHandler);
        }
        if (window._monitoringTurboBeforeRenderHandler) {
            document.removeEventListener('turbo:before-render', window._monitoringTurboBeforeRenderHandler);
        }

        window._monitoringTurboLoadHandler = function() {
            const appContainer = document.getElementById('monitoringAppContainer');
            if (!appContainer) {
                if (window._monitoringTurboLoadHandler) {
                    document.removeEventListener('turbo:load', window._monitoringTurboLoadHandler);
                    window._monitoringTurboLoadHandler = null;
                }
                return;
            }
            initMonitoringPage();
        };

        window._monitoringTurboBeforeVisitHandler = function() {
            cleanupMonitoring();
        };

        window._monitoringTurboBeforeRenderHandler = function(e) {
            cleanupMonitoring();
            const nextContainer = e?.detail?.newBody?.querySelector('#monitoringAppContainer');
            if (!nextContainer) {
                if (window._monitoringTurboLoadHandler) {
                    document.removeEventListener('turbo:load', window._monitoringTurboLoadHandler);
                    window._monitoringTurboLoadHandler = null;
                }
                if (window._monitoringTurboBeforeVisitHandler) {
                    document.removeEventListener('turbo:before-visit', window._monitoringTurboBeforeVisitHandler);
                    window._monitoringTurboBeforeVisitHandler = null;
                }
                if (window._monitoringTurboBeforeRenderHandler) {
                    document.removeEventListener('turbo:before-render', window._monitoringTurboBeforeRenderHandler);
                    window._monitoringTurboBeforeRenderHandler = null;
                }
            }
        };

        document.addEventListener('turbo:load', window._monitoringTurboLoadHandler);
        document.addEventListener('turbo:before-visit', window._monitoringTurboBeforeVisitHandler);
        document.addEventListener('turbo:before-render', window._monitoringTurboBeforeRenderHandler);

        // Panel Switcher (Rooms vs Facilities)
        window.switchMonitoringPanel = function(panel) {
            currentPanel = panel;
            localStorage.setItem('hotel_monitoring_panel', panel);

            const btnRooms = document.getElementById('btnRoomsPanel');
            const btnFacilities = document.getElementById('btnFacilitiesPanel');
            const headerIcon = document.getElementById('monitoringHeaderIcon');
            const mainTitle = document.getElementById('monitoringMainTitle');
            const subTitle = document.getElementById('monitoringSubTitle');
            const btnGridViewLabel = document.getElementById('btnGridViewLabel');

            const roomsLegend = document.getElementById('roomsLegendContainer');
            const facilitiesLegend = document.getElementById('facilitiesLegendContainer');
            const roomTypeFilter = document.getElementById('timelineRoomTypeFilter');
            const facilityFilter = document.getElementById('timelineFacilityFilter');
            const roomDurationGroup = document.getElementById('roomDurationGroup');
            const facilityDurationGroup = document.getElementById('facilityDurationGroup');

            if (panel === 'facilities') {
                btnFacilities?.classList.add('active');
                if (btnFacilities) {
                    btnFacilities.style.background = '#e8f0ec';
                    btnFacilities.style.color = '#334c42';
                    btnFacilities.style.borderColor = '#627e71';
                }
                btnRooms?.classList.remove('active');
                if (btnRooms) {
                    btnRooms.style.background = '#f8f9fa';
                    btnRooms.style.color = '#212529';
                    btnRooms.style.borderColor = '#dee2e6';
                }

                if (headerIcon) headerIcon.className = 'fa-solid fa-building fs-5';
                if (mainTitle) mainTitle.textContent = 'Hotel Facility Monitoring & Calendar';
                if (subTitle) subTitle.textContent = 'Live facility status, reservation schedule, and multi-day booking timeline';
                if (btnGridViewLabel) btnGridViewLabel.textContent = 'Facility Grid';

                roomsLegend?.classList.add('d-none');
                facilitiesLegend?.classList.remove('d-none');

                roomTypeFilter?.classList.add('d-none');
                facilityFilter?.classList.remove('d-none');

                roomDurationGroup?.classList.add('d-none');
                facilityDurationGroup?.classList.remove('d-none');
            } else {
                btnRooms?.classList.add('active');
                if (btnRooms) {
                    btnRooms.style.background = '#e8f0ec';
                    btnRooms.style.color = '#334c42';
                    btnRooms.style.borderColor = '#627e71';
                }
                btnFacilities?.classList.remove('active');
                if (btnFacilities) {
                    btnFacilities.style.background = '#f8f9fa';
                    btnFacilities.style.color = '#212529';
                    btnFacilities.style.borderColor = '#dee2e6';
                }

                if (headerIcon) headerIcon.className = 'fa-solid fa-bed fs-5';
                if (mainTitle) mainTitle.textContent = 'Hotel Room Monitoring & Calendar';
                if (subTitle) subTitle.textContent = 'Live room status, occupancy schedule, and multi-day reservation timeline';
                if (btnGridViewLabel) btnGridViewLabel.textContent = 'Room Grid';

                facilitiesLegend?.classList.add('d-none');
                roomsLegend?.classList.remove('d-none');

                facilityFilter?.classList.add('d-none');
                roomTypeFilter?.classList.remove('d-none');

                facilityDurationGroup?.classList.add('d-none');
                roomDurationGroup?.classList.remove('d-none');
            }

            // Sync active view based on current view mode
            window.switchViewMode(currentViewMode);
        };

        // View Mode Switcher (Timeline vs Grid)
        window.switchViewMode = function(mode) {
            currentViewMode = mode;
            localStorage.setItem('hotel_monitoring_view_mode', mode);

            const btnTimeline = document.getElementById('btnTimelineView');
            const btnGrid = document.getElementById('btnGridView');
            const toolbar = document.getElementById('monitoringTimelineToolbar');

            const roomsTimeline = document.getElementById('roomsTimelineViewContainer');
            const roomsGrid = document.getElementById('roomsGridViewContainer');
            const facilitiesTimeline = document.getElementById('facilitiesTimelineViewContainer');
            const facilitiesGrid = document.getElementById('facilitiesGridViewContainer');

            // Hide all 4 view containers first
            roomsTimeline?.classList.add('d-none');
            roomsGrid?.classList.add('d-none');
            facilitiesTimeline?.classList.add('d-none');
            facilitiesGrid?.classList.add('d-none');

            if (mode === 'grid') {
                btnGrid?.classList.add('active');
                btnTimeline?.classList.remove('active');
                toolbar?.classList.add('d-none');

                if (currentPanel === 'facilities') {
                    facilitiesGrid?.classList.remove('d-none');
                } else {
                    roomsGrid?.classList.remove('d-none');
                }
            } else {
                btnTimeline?.classList.add('active');
                btnGrid?.classList.remove('active');
                toolbar?.classList.remove('d-none');

                if (currentPanel === 'facilities') {
                    facilitiesTimeline?.classList.remove('d-none');
                    if (facilityTimelineCache) {
                        if (timelineFacilityMode === 'hourly') {
                            renderFacilityHourlyTimelineTable(facilityTimelineCache);
                        } else {
                            renderFacilityTimelineTable(facilityTimelineCache);
                        }
                        updateFacilityTimelineControls(facilityTimelineCache);
                    } else {
                        fetchFacilityTimelineData();
                    }
                } else {
                    roomsTimeline?.classList.remove('d-none');
                    if (roomTimelineCache) {
                        renderRoomTimelineTable(roomTimelineCache);
                        updateRoomTimelineControls(roomTimelineCache);
                    } else {
                        fetchRoomTimelineData();
                    }
                }
            }
        };

        // Toolbar initialization
        function initToolbarControls() {
            const datePicker = document.getElementById('timelineDatePicker');
            if (datePicker) {
                datePicker.value = timelineStartDate;
                datePicker.onchange = function() {
                    timelineStartDate = this.value;
                    refreshActiveTimeline();
                };
            }

            const prevBtn = document.getElementById('btnTimelinePrev');
            if (prevBtn) {
                prevBtn.onclick = function() {
                    if (currentPanel === 'facilities' && timelineFacilityMode === 'hourly') {
                        timelineStartDate = addDaysToDateStr(timelineStartDate, -1);
                    } else {
                        timelineStartDate = addDaysToDateStr(timelineStartDate, -timelineDays);
                    }
                    refreshActiveTimeline();
                };
            }

            const nextBtn = document.getElementById('btnTimelineNext');
            if (nextBtn) {
                nextBtn.onclick = function() {
                    if (currentPanel === 'facilities' && timelineFacilityMode === 'hourly') {
                        timelineStartDate = addDaysToDateStr(timelineStartDate, 1);
                    } else {
                        timelineStartDate = addDaysToDateStr(timelineStartDate, timelineDays);
                    }
                    refreshActiveTimeline();
                };
            }

            const todayBtn = document.getElementById('btnTimelineToday');
            if (todayBtn) {
                todayBtn.onclick = function() {
                    timelineStartDate = getTodayDateStr();
                    refreshActiveTimeline();
                };
            }

            // Room duration buttons (7, 14, 30 days)
            document.querySelectorAll('#roomDurationGroup .timeline-duration-btn').forEach(btn => {
                btn.onclick = function() {
                    document.querySelectorAll('#roomDurationGroup .timeline-duration-btn').forEach(b => {
                        b.classList.remove('active', 'fw-bold');
                        b.style.background = '#ffffff';
                        b.style.color = '#504538';
                        b.style.borderColor = '#dee2e6';
                    });
                    this.classList.add('active', 'fw-bold');
                    this.style.background = '#e8f0ec';
                    this.style.color = '#334c42';
                    this.style.borderColor = '#627e71';
                    timelineDays = parseInt(this.getAttribute('data-days'), 10) || 14;
                    fetchRoomTimelineData();
                };
            });

            // Facility duration buttons (Hourly, 7, 14, 30 days)
            document.querySelectorAll('#facilityDurationGroup .timeline-fac-mode-btn').forEach(btn => {
                btn.onclick = function() {
                    const mode = this.getAttribute('data-mode');
                    if (mode === 'hourly') {
                        window.switchToFacilityHourlyView();
                    } else {
                        const days = parseInt(mode, 10) || 14;
                        window.switchToFacilityMultiDayView(days);
                    }
                };
            });

            const roomTypeFilter = document.getElementById('timelineRoomTypeFilter');
            if (roomTypeFilter) {
                roomTypeFilter.onchange = function() {
                    timelineRoomType = this.value;
                    fetchRoomTimelineData();
                };
            }

            const facilityFilter = document.getElementById('timelineFacilityFilter');
            if (facilityFilter) {
                facilityFilter.onchange = function() {
                    timelineFacilityId = this.value;
                    fetchFacilityTimelineData();
                };
            }

            const refreshBtn = document.getElementById('btnRefreshTimeline');
            if (refreshBtn) {
                refreshBtn.onclick = function() {
                    if (currentPanel === 'facilities') {
                        fetchFacilityTimelineData();
                    } else {
                        fetchRoomTimelineData();
                    }
                };
            }
        }

        function refreshActiveTimeline() {
            const datePicker = document.getElementById('timelineDatePicker');
            if (datePicker) datePicker.value = timelineStartDate;

            if (currentPanel === 'facilities') {
                if (timelineFacilityMode === 'hourly') {
                    const dateLabel = document.getElementById('facilityHourlyDateLabel');
                    if (dateLabel) dateLabel.textContent = formatFullDate(timelineStartDate);
                }
                fetchFacilityTimelineData();
            } else {
                fetchRoomTimelineData();
            }
        }

        function addDaysToDateStr(dateStr, days) {
            const parts = dateStr.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            d.setDate(d.getDate() + days);
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
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

        function formatFullDate(dateStr) {
            if (!dateStr) return '—';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${days[d.getDay()]}, ${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
        }

        function formatHourLabel(h) {
            if (h === 0) return { primary: '12 AM', sub: '00:00' };
            if (h < 12) return { primary: `${h} AM`, sub: `${String(h).padStart(2, '0')}:00` };
            if (h === 12) return { primary: '12 PM', sub: '12:00' };
            return { primary: `${h - 12} PM`, sub: `${String(h).padStart(2, '0')}:00` };
        }

        // ================= ROOMS TIMELINE LOGIC =================

        window.fetchRoomTimelineData = function fetchRoomTimelineData() {
            const requestGeneration = monitoringPageGeneration;
            const controller = new AbortController();
            timelineRequestControllers.add(controller);
            const content = document.getElementById('roomsTimelineContent');
            if (content) {
                content.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        Updating room reservation timeline...
                    </div>
                `;
            }

            const url = `{{ route('frontdesk.monitoring.room-timeline-data') }}?start_date=${encodeURIComponent(timelineStartDate)}&days=${timelineDays}&room_type=${encodeURIComponent(timelineRoomType)}`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                signal: controller.signal
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load room timeline data');
                return res.json();
            })
            .then(data => {
                if (requestGeneration !== monitoringPageGeneration || !document.getElementById('monitoringAppContainer')) return;
                roomTimelineCache = data;
                renderRoomTimelineTable(data);
                updateRoomTimelineControls(data);
            })
            .catch(err => {
                if (err.name === 'AbortError' || requestGeneration !== monitoringPageGeneration) return;
                if (content) {
                    content.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="fa-solid fa-triangle-exclamation fs-4 mb-2 d-block"></i>
                            Failed to load room data: ${err.message}.
                            <button class="btn btn-sm btn-outline-secondary mt-2 d-block mx-auto" onclick="window.fetchRoomTimelineData()">Retry</button>
                        </div>
                    `;
                }
            })
            .finally(() => {
                timelineRequestControllers.delete(controller);
            });
        };

        function updateRoomTimelineControls(data) {
            const rangeDisplay = document.getElementById('roomsTimelineRangeDisplay');
            if (rangeDisplay && data.range) {
                rangeDisplay.textContent = data.range.display;
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

        function renderRoomTimelineTable(data) {
            const container = document.getElementById('roomsTimelineContent');
            if (!container) return;

            if (!data.rooms || data.rooms.length === 0) {
                container.innerHTML = `<p class="text-muted text-center py-5">No rooms available for the selected criteria.</p>`;
                return;
            }

            const dates = data.dates || [];
            const todayStr = getTodayDateStr();

            // Group by room_type
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
                    const statusColor = getRoomStatusColor(room.current_status);
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
                    
                    // Left sticky cell
                    html += `
                        <td class="timeline-sticky-col timeline-room-cell-info" onclick="window.timelineOpenRoomModal(${room.room_id})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="overflow-hidden pe-1" style="max-width: 140px;">
                                    <div class="timeline-room-number text-truncate">
                                        <span class="timeline-status-pill" style="background-color: ${statusColor};"></span>
                                        Room ${escapeHtml(room.room_number)}
                                    </div>
                                    <div class="timeline-room-floor">${escapeHtml(room.floor || 'Floor')} • ₱${parseFloat(room.base_rate).toFixed(2)}</div>
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

                    const coveredDateIndexes = new Set();

                    dates.forEach((d, dIdx) => {
                        const dateStr = d.date;
                        const isToday = d.is_today;
                        const isWeekend = d.is_weekend;

                        if (coveredDateIndexes.has(dIdx)) {
                            html += `<td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}"></td>`;
                            return;
                        }

                        const activeBooking = bookings.find(b => b.arrival_date <= dateStr && b.departure_date >= dateStr);

                        if (activeBooking) {
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
                                        In: ${checkInFormatted} → Out: ${checkOutFormatted}
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
                                         title="${escapeHtml(activeBooking.guest_name)} (${activeBooking.status}) - ${activeBooking.arrival_date} until ${activeBooking.departure_date}"
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
                                         title="Room ${escapeHtml(room.room_number)} - Needs Cleaning"
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
                                         title="Room ${escapeHtml(room.room_number)} - Under Maintenance"
                                         onclick="event.stopPropagation(); window.timelineOpenRoomModal(${room.room_id})">
                                        <i class="fa-solid fa-wrench fs-6"></i>
                                    </div>
                                </td>
                            `;
                        } else {
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

        function getRoomStatusColor(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return '#627e71';
            if (s === 'OCCUPIED') return '#3b82f6';
            if (s === 'RESERVED') return '#f59e0b';
            if (s === 'CLEANING') return '#fd7e14';
            if (s === 'MAINTENANCE') return '#6c757d';
            return '#627e71';
        }

        function getRoomStatusBadgeClass(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return 'bg-success';
            if (s === 'OCCUPIED') return 'bg-primary';
            if (s === 'RESERVED') return 'bg-warning text-dark';
            if (s === 'CLEANING') return 'bg-warning text-dark';
            if (s === 'MAINTENANCE') return 'bg-secondary';
            return 'bg-secondary';
        }

        function getRoomStatusLabel(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return 'AVAILABLE';
            if (s === 'OCCUPIED') return 'OCCUPIED';
            if (s === 'RESERVED') return 'RESERVED';
            if (s === 'CLEANING') return 'CLEANING';
            if (s === 'MAINTENANCE') return 'MAINTENANCE';
            return status || 'UNKNOWN';
        }

        // ================= FACILITIES TIMELINE & HOURLY LOGIC =================

        window.switchToFacilityHourlyView = function(dateStr) {
            window.closeFacilityReservationModal?.();
            timelineFacilityMode = 'hourly';
            timelineDays = 1;
            if (dateStr) {
                timelineStartDate = dateStr;
            }
            const dp = document.getElementById('timelineDatePicker');
            if (dp) dp.value = timelineStartDate;

            // Update facility duration button styling
            document.querySelectorAll('#facilityDurationGroup .timeline-fac-mode-btn').forEach(b => {
                const isHourly = b.getAttribute('data-mode') === 'hourly';
                b.classList.toggle('active', isHourly);
                b.classList.toggle('fw-bold', isHourly);
                b.style.background = isHourly ? '#e8f0ec' : '#ffffff';
                b.style.color = isHourly ? '#334c42' : '#504538';
                b.style.borderColor = isHourly ? '#627e71' : '#dee2e6';
            });

            // Show helper bar
            const helperBar = document.getElementById('facilityHourlyHelperBar');
            const dateLabel = document.getElementById('facilityHourlyDateLabel');
            if (helperBar) helperBar.classList.remove('d-none');
            if (dateLabel) dateLabel.textContent = formatFullDate(timelineStartDate);

            fetchFacilityTimelineData();
        };

        window.switchToFacilityMultiDayView = function(days) {
            timelineFacilityMode = 'multiday';
            timelineDays = days || 14;

            // Update facility duration button styling
            document.querySelectorAll('#facilityDurationGroup .timeline-fac-mode-btn').forEach(b => {
                const isTarget = b.getAttribute('data-mode') === String(timelineDays);
                b.classList.toggle('active', isTarget);
                b.classList.toggle('fw-bold', isTarget);
                b.style.background = isTarget ? '#e8f0ec' : '#ffffff';
                b.style.color = isTarget ? '#334c42' : '#504538';
                b.style.borderColor = isTarget ? '#627e71' : '#dee2e6';
            });

            // Hide helper bar
            const helperBar = document.getElementById('facilityHourlyHelperBar');
            if (helperBar) helperBar.classList.add('d-none');

            fetchFacilityTimelineData();
        };

        window.fetchFacilityTimelineData = function fetchFacilityTimelineData() {
            const requestGeneration = monitoringPageGeneration;
            const controller = new AbortController();
            timelineRequestControllers.add(controller);
            const content = document.getElementById('facilitiesTimelineContent');
            if (content) {
                content.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        Updating facility reservation timeline...
                    </div>
                `;
            }

            const queryDays = timelineFacilityMode === 'hourly' ? 1 : timelineDays;
            const url = `{{ route('frontdesk.monitoring.facility-timeline-data') }}?start_date=${encodeURIComponent(timelineStartDate)}&days=${queryDays}&facility_id=${encodeURIComponent(timelineFacilityId)}`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                signal: controller.signal
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load facility timeline data');
                return res.json();
            })
            .then(data => {
                if (requestGeneration !== monitoringPageGeneration || !document.getElementById('monitoringAppContainer')) return;
                facilityTimelineCache = data;
                if (timelineFacilityMode === 'hourly') {
                    renderFacilityHourlyTimelineTable(data);
                } else {
                    renderFacilityTimelineTable(data);
                }
                updateFacilityTimelineControls(data);
            })
            .catch(err => {
                if (err.name === 'AbortError' || requestGeneration !== monitoringPageGeneration) return;
                if (content) {
                    content.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="fa-solid fa-triangle-exclamation fs-4 mb-2 d-block"></i>
                            Failed to load facility data: ${err.message}.
                            <button class="btn btn-sm btn-outline-secondary mt-2 d-block mx-auto" onclick="window.fetchFacilityTimelineData()">Retry</button>
                        </div>
                    `;
                }
            })
            .finally(() => {
                timelineRequestControllers.delete(controller);
            });
        };

        function updateFacilityTimelineControls(data) {
            const rangeDisplay = document.getElementById('facilitiesTimelineRangeDisplay');
            if (rangeDisplay) {
                if (timelineFacilityMode === 'hourly') {
                    rangeDisplay.textContent = formatFullDate(timelineStartDate);
                } else if (data.range) {
                    rangeDisplay.textContent = data.range.display;
                }
            }

            if (data.summary) {
                const facAvail = document.getElementById('facLegendAvailable');
                if (facAvail) facAvail.textContent = data.summary.available;
                const facBooked = document.getElementById('facLegendBooked');
                if (facBooked) facBooked.textContent = data.summary.booked;
                const facRes = document.getElementById('facLegendReserved');
                if (facRes) facRes.textContent = data.summary.reserved;
                const facPending = document.getElementById('facLegendPending');
                if (facPending) facPending.textContent = data.summary.pending;
                const facInact = document.getElementById('facLegendInactive');
                if (facInact) facInact.textContent = data.summary.maintenance;
            }

            const facSelect = document.getElementById('timelineFacilityFilter');
            if (facSelect && data.facility_filters && facSelect.options.length <= 1) {
                facSelect.innerHTML = '<option value="ALL">All Facilities</option>';
                data.facility_filters.forEach(fac => {
                    const opt = document.createElement('option');
                    opt.value = fac.id;
                    opt.textContent = fac.name;
                    if (String(fac.id) === String(timelineFacilityId)) opt.selected = true;
                    facSelect.appendChild(opt);
                });
            }
        }

        // 1. Multi-Day Facility Timeline Table
        function renderFacilityTimelineTable(data) {
            const container = document.getElementById('facilitiesTimelineContent');
            if (!container) return;

            if (!data.facilities || data.facilities.length === 0) {
                container.innerHTML = `<p class="text-muted text-center py-5">No facilities found for the selected criteria.</p>`;
                return;
            }

            const dates = data.dates || [];

            let html = `<table class="timeline-table"><thead><tr>`;
            html += `<th class="timeline-sticky-col timeline-header-corner">Facilities / Rate</th>`;

            dates.forEach(d => {
                const isTodayClass = d.is_today ? 'is-today' : '';
                const isWeekendClass = d.is_weekend ? 'is-weekend' : '';
                html += `
                    <th class="timeline-header-date ${isTodayClass} ${isWeekendClass}" 
                        title="Click to view 24-hour schedule for ${d.full_formatted}"
                        onclick="window.switchToFacilityHourlyView('${d.date}')"
                        style="cursor: pointer;">
                        <div class="timeline-day-name">${d.day_name}</div>
                        <div class="timeline-day-num">${d.day_number}</div>
                        <div class="timeline-day-month">${d.month_name}</div>
                        <div class="small opacity-75 mt-0.5" style="font-size: 0.6rem;"><i class="fa-solid fa-clock"></i> Hourly</div>
                    </th>
                `;
            });
            html += `</tr></thead><tbody>`;

            data.facilities.forEach(fac => {
                const statusColor = getFacilityStatusColor(fac.current_status);
                const statusBadgeClass = getFacilityStatusBadgeClass(fac.current_status);
                const statusLabel = getFacilityStatusLabel(fac.current_status);
                const reservations = fac.reservations || [];

                html += `<tr class="timeline-room-row" data-facility-id="${fac.facility_id}">`;

                // Left sticky cell
                html += `
                    <td class="timeline-sticky-col timeline-room-cell-info" onclick="window.openFacilityQuickInfo(${fac.facility_id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden pe-1" style="max-width: 140px;">
                                <div class="timeline-room-number text-truncate">
                                    <span class="timeline-status-pill" style="background-color: ${statusColor};"></span>
                                    ${escapeHtml(fac.name)}
                                </div>
                                <div class="timeline-room-floor">
                                    ${fac.capacity ? fac.capacity + ' pax • ' : ''}₱${parseFloat(fac.rate).toFixed(2)}/${fac.rate_type === 'hourly' ? 'hr' : 'day'}
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge ${statusBadgeClass} py-1 px-1.5" style="font-size: 0.65rem;">
                                    ${statusLabel}
                                </span>
                            </div>
                        </div>
                    </td>
                `;

                const coveredDateIndexes = new Set();

                dates.forEach((d, dIdx) => {
                    const dateStr = d.date;
                    const isToday = d.is_today;
                    const isWeekend = d.is_weekend;

                    if (coveredDateIndexes.has(dIdx)) {
                        html += `<td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}"></td>`;
                        return;
                    }

                    // Find reservations covering this date
                    const matchingRes = reservations.find(r => r.start_date <= dateStr && r.end_date >= dateStr);

                    if (matchingRes) {
                        let endIdx = dIdx;
                        for (let i = dIdx; i < dates.length; i++) {
                            if (matchingRes.end_date >= dates[i].date) {
                                endIdx = i;
                                coveredDateIndexes.add(i);
                            } else {
                                break;
                            }
                        }

                        const span = Math.max(1, endIdx - dIdx + 1);
                        const isApproved = matchingRes.status === 'approved';
                        const isActive = matchingRes.status === 'active';
                        const isPending = matchingRes.status === 'pending';

                        let eventClass = 'timeline-event-fac-approved';
                        let iconClass = 'fa-calendar-check';
                        if (isActive) {
                            eventClass = 'timeline-event-fac-active';
                            iconClass = 'fa-clock';
                        } else if (isPending) {
                            eventClass = 'timeline-event-fac-pending';
                            iconClass = 'fa-hourglass-half';
                        }

                        const spanWidthPercent = span * 100;
                        const spanWidthCalc = `calc(${spanWidthPercent}% + ${(span - 1)}px - 8px)`;

                        html += `
                            <td class="timeline-cell ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}" style="overflow: visible;">
                                <div class="timeline-event-bar ${eventClass} d-flex align-items-center justify-content-between" 
                                     style="width: ${spanWidthCalc}; left: 4px;"
                                     title="${escapeHtml(matchingRes.booker_name)} - ${escapeHtml(matchingRes.event_name)} (${matchingRes.time_formatted})"
                                     onclick="event.stopPropagation(); window.openFacilityReservationModal(${matchingRes.reservation_id}, ${fac.facility_id})">
                                    <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                        <i class="fa-solid ${iconClass} flex-shrink-0"></i>
                                        <span class="text-truncate fw-bold">${escapeHtml(matchingRes.booker_name)}</span>
                                        <small class="text-truncate opacity-75 d-none d-md-inline">(${escapeHtml(matchingRes.event_name)})</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-black bg-opacity-25 text-white fw-semibold ms-auto flex-shrink-0" style="font-size: 0.65rem; padding: 2px 5px;">
                                            ${matchingRes.time_formatted}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        `;
                    } else {
                        html += `
                            <td class="timeline-cell is-available ${isToday ? 'is-today' : ''} ${isWeekend ? 'is-weekend' : ''}" 
                                title="Click to book ${escapeHtml(fac.name)} on ${dateStr}"
                                onclick="window.location.href='{{ route('frontdesk.facility-reservations.create') }}?facility=${fac.facility_id}&date=${dateStr}&return_to=monitoring'">
                            </td>
                        `;
                    }
                });

                html += `</tr>`;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        }

        // 2. 24-Hour Facility Hourly Matrix Table
        function renderFacilityHourlyTimelineTable(data) {
            const container = document.getElementById('facilitiesTimelineContent');
            if (!container) return;

            if (!data.facilities || data.facilities.length === 0) {
                container.innerHTML = `<p class="text-muted text-center py-5">No facilities found for the selected criteria.</p>`;
                return;
            }

            const targetDateStr = timelineStartDate;
            const isToday = (targetDateStr === getTodayDateStr());
            const currentHour = isToday ? new Date().getHours() : -1;

            let html = `<table class="timeline-table"><thead><tr>`;
            html += `<th class="timeline-sticky-col timeline-header-corner">Facility / Rate</th>`;

            for (let h = 0; h < 24; h++) {
                const isCurrent = (h === currentHour);
                const lbl = formatHourLabel(h);
                html += `
                    <th class="timeline-header-date ${isCurrent ? 'is-today' : ''}" style="min-width: 68px; width: 68px;">
                        <div class="timeline-day-name">${lbl.sub}</div>
                        <div class="timeline-day-num" style="font-size: 0.88rem;">${lbl.primary}</div>
                        ${isCurrent ? '<span class="badge bg-white text-dark mt-0.5" style="font-size: 0.58rem; padding: 1px 4px;">NOW</span>' : ''}
                    </th>
                `;
            }
            html += `</tr></thead><tbody>`;

            data.facilities.forEach(fac => {
                const statusColor = getFacilityStatusColor(fac.current_status);
                const statusBadgeClass = getFacilityStatusBadgeClass(fac.current_status);
                const statusLabel = getFacilityStatusLabel(fac.current_status);
                const reservations = fac.reservations || [];

                html += `<tr class="timeline-room-row" data-facility-id="${fac.facility_id}">`;

                // Left sticky cell
                html += `
                    <td class="timeline-sticky-col timeline-room-cell-info" onclick="window.openFacilityQuickInfo(${fac.facility_id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="overflow-hidden pe-1" style="max-width: 140px;">
                                <div class="timeline-room-number text-truncate">
                                    <span class="timeline-status-pill" style="background-color: ${statusColor};"></span>
                                    ${escapeHtml(fac.name)}
                                </div>
                                <div class="timeline-room-floor">
                                    ${fac.capacity ? fac.capacity + ' pax • ' : ''}₱${parseFloat(fac.rate).toFixed(2)}/${fac.rate_type === 'hourly' ? 'hr' : 'day'}
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="badge ${statusBadgeClass} py-1 px-1.5" style="font-size: 0.65rem;">
                                    ${statusLabel}
                                </span>
                            </div>
                        </div>
                    </td>
                `;

                // Calculate reservation spans across the 24 hours of targetDateStr
                const hourlyReservations = [];
                reservations.forEach(r => {
                    if (r.start_date <= targetDateStr && r.end_date >= targetDateStr) {
                        let startH = 0;
                        if (r.start_date === targetDateStr && r.start_time) {
                            startH = parseInt(r.start_time.split(':')[0], 10);
                        }
                        let endH = 24;
                        if (r.end_date === targetDateStr && r.end_time) {
                            const parts = r.end_time.split(':');
                            const hr = parseInt(parts[0], 10);
                            const min = parseInt(parts[1] || '0', 10);
                            endH = min > 0 ? Math.min(24, hr + 1) : Math.min(24, Math.max(startH + 1, hr));
                        }
                        if (endH > startH) {
                            hourlyReservations.push({
                                res: r,
                                startH: startH,
                                endH: endH,
                            });
                        }
                    }
                });

                const coveredHours = new Set();

                for (let h = 0; h < 24; h++) {
                    const isCurrent = (h === currentHour);

                    if (coveredHours.has(h)) {
                        html += `<td class="timeline-cell ${isCurrent ? 'is-today' : ''}"></td>`;
                        continue;
                    }

                    const matching = hourlyReservations.find(hr => hr.startH <= h && hr.endH > h);

                    if (matching) {
                        const span = Math.max(1, matching.endH - h);
                        for (let k = h; k < h + span; k++) {
                            coveredHours.add(k);
                        }

                        const r = matching.res;
                        const isApproved = r.status === 'approved';
                        const isActive = r.status === 'active';
                        const isPending = r.status === 'pending';

                        let eventClass = 'timeline-event-fac-approved';
                        let iconClass = 'fa-calendar-check';
                        if (isActive) {
                            eventClass = 'timeline-event-fac-active';
                            iconClass = 'fa-clock';
                        } else if (isPending) {
                            eventClass = 'timeline-event-fac-pending';
                            iconClass = 'fa-hourglass-half';
                        }

                        const spanWidthPercent = span * 100;
                        const spanWidthCalc = `calc(${spanWidthPercent}% + ${(span - 1)}px - 8px)`;

                        html += `
                            <td class="timeline-cell ${isCurrent ? 'is-today' : ''}" style="overflow: visible;">
                                <div class="timeline-event-bar ${eventClass} d-flex align-items-center justify-content-between" 
                                     style="width: ${spanWidthCalc}; left: 4px;"
                                     title="${escapeHtml(r.booker_name)} - ${escapeHtml(r.event_name)} (${r.time_formatted})"
                                     onclick="event.stopPropagation(); window.openFacilityReservationModal(${r.reservation_id}, ${fac.facility_id})">
                                    <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                        <i class="fa-solid ${iconClass} flex-shrink-0"></i>
                                        <span class="text-truncate fw-bold">${escapeHtml(r.booker_name)}</span>
                                        <small class="text-truncate opacity-75 d-none d-lg-inline">(${escapeHtml(r.event_name)})</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-black bg-opacity-25 text-white fw-semibold ms-auto flex-shrink-0" style="font-size: 0.65rem; padding: 2px 5px;">
                                            ${r.time_formatted}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        `;
                    } else if (fac.current_status === 'MAINTENANCE') {
                        html += `
                            <td class="timeline-cell ${isCurrent ? 'is-today' : ''}" style="overflow: visible;">
                                <div class="timeline-event-bar timeline-event-maintenance d-flex justify-content-center align-items-center" 
                                     style="width: calc(100% - 8px); left: 4px;"
                                     title="${escapeHtml(fac.name)} - Out of Order / Maintenance">
                                    <i class="fa-solid fa-wrench fs-6"></i>
                                </div>
                            </td>
                        `;
                    } else {
                        const hourFormatted = String(h).padStart(2, '0') + ':00';
                        const hourLabel = formatHourLabel(h).primary;
                        html += `
                            <td class="timeline-cell is-available ${isCurrent ? 'is-today' : ''}" 
                                title="Click to book ${escapeHtml(fac.name)} at ${hourLabel} on ${targetDateStr}"
                                onclick="window.location.href='{{ route('frontdesk.facility-reservations.create') }}?facility=${fac.facility_id}&reservation_date=${targetDateStr}&start_time=${hourFormatted}&return_to=monitoring'">
                            </td>
                        `;
                    }
                }

                html += `</tr>`;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        }

        function getFacilityStatusColor(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return '#627e71';
            if (s === 'IN_USE') return '#2563eb';
            if (s === 'BOOKED') return '#334c42';
            if (s === 'PENDING') return '#f59e0b';
            if (s === 'MAINTENANCE') return '#6c757d';
            return '#627e71';
        }

        function getFacilityStatusBadgeClass(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return 'bg-success';
            if (s === 'IN_USE') return 'bg-primary';
            if (s === 'BOOKED') return 'bg-info text-white';
            if (s === 'PENDING') return 'bg-warning text-dark';
            if (s === 'MAINTENANCE') return 'bg-secondary';
            return 'bg-secondary';
        }

        function getFacilityStatusLabel(status) {
            const s = (status || '').toUpperCase();
            if (s === 'AVAILABLE') return 'AVAILABLE';
            if (s === 'IN_USE') return 'IN USE';
            if (s === 'BOOKED') return 'BOOKED';
            if (s === 'PENDING') return 'PENDING';
            if (s === 'MAINTENANCE') return 'INACTIVE';
            return status || 'AVAILABLE';
        }

        // ================= ROOM GRID VIEW =================

        function renderRoomGrid(roomType) {
            const grid = document.getElementById('roomGrid');
            if (!grid) return;

            const rooms = roomsByType[roomType] || [];
            if (rooms.length === 0) {
                grid.innerHTML = '<p class="text-muted">No rooms in this category</p>';
                return;
            }

            let html = '';
            rooms.forEach(room => {
                const s = (room.status || '').toLowerCase();
                let iconClass = 'fa-solid fa-bed';
                if (s === 'cleaning') iconClass = 'fa-solid fa-broom';
                if (s === 'maintenance') iconClass = 'fa-solid fa-wrench';

                html += `
                    <div class="room-box ${s}" onclick='window.openRoomModal(${JSON.stringify(room)})'>
                        <i class="${iconClass}"></i>
                        <span class="room-number">${room.room_number}</span>
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        window.openRoomModal = function(room) {
            document.getElementById('modalRoomNumber').textContent = room.room_number;
            document.getElementById('modalRoomType').textContent = room.room_type;
            const statusBadge = document.getElementById('modalRoomStatus');
            statusBadge.textContent = room.status;
            statusBadge.className = 'badge ' + getRoomStatusBadgeClass(room.status);

            const actionsDiv = document.getElementById('modalRoomActions');
            let actionsHtml = '';

            if (room.status === 'AVAILABLE') {
                actionsHtml = `
                    <div class="d-grid gap-2">
                        <a href="/frontdesk/registration?room_id=${room.room_id}" class="btn btn-success">
                            <i class="fa-solid fa-key me-1"></i> Walk-In Registration / Check-In
                        </a>
                        <a href="/frontdesk/reservation?room_id=${room.room_id}" class="btn text-white" style="background:#334c42;">
                            <i class="fa-solid fa-calendar-plus me-1"></i> Create Reservation
                        </a>
                        <button type="button" class="btn btn-outline-warning" onclick="changeRoomStatus(${room.room_id}, 'mark-for-cleaning')">
                            <i class="fa-solid fa-broom me-1"></i> Send for Cleaning
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="changeRoomStatus(${room.room_id}, 'mark-maintenance')">
                            <i class="fa-solid fa-wrench me-1"></i> Out of Order / Maintenance
                        </button>
                    </div>
                `;
            } else if (room.status === 'CLEANING') {
                actionsHtml = `
                    <button type="button" class="btn btn-success w-100" onclick="changeRoomStatus(${room.room_id}, 'mark-cleaned')">
                        <i class="fa-solid fa-check me-1"></i> Mark as Cleaned & Available
                    </button>
                `;
            } else if (room.status === 'MAINTENANCE') {
                actionsHtml = `
                    <button type="button" class="btn btn-success w-100" onclick="changeRoomStatus(${room.room_id}, 'maintenance-complete')">
                        <i class="fa-solid fa-check me-1"></i> Complete Maintenance & Available
                    </button>
                `;
            } else if (room.status === 'OCCUPIED' && room.active_booking) {
                const folioUrl = `/frontdesk/guest-folio/${room.active_booking.folio_id}`;
                actionsHtml = `
                    <p class="mb-2"><strong>Guest:</strong> ${escapeHtml(room.active_booking.guest_name)}</p>
                    <p class="mb-3"><strong>Balance:</strong> ₱${parseFloat(room.active_booking.balance || 0).toFixed(2)}</p>
                    <div class="d-grid gap-2">
                        <a href="${folioUrl}" class="btn btn-primary">
                            <i class="fa-solid fa-file-invoice me-1"></i> View Folio & Billing
                        </a>
                        <button type="button" class="btn btn-outline-dark" onclick="openExtendStay(${room.active_booking.booking_id})">
                            <i class="fa-solid fa-clock me-1"></i> Extend Stay
                        </button>
                    </div>
                `;
            }

            actionsDiv.innerHTML = actionsHtml;
            roomActionModal?.show();
        };

        window.timelineOpenRoomModal = function(roomId) {
            if (!roomTimelineCache) return;
            const room = roomTimelineCache.rooms.find(r => r.room_id === roomId);
            if (room) {
                window.openRoomModal({
                    room_id: room.room_id,
                    room_number: room.room_number,
                    room_type: room.room_type,
                    status: room.current_status,
                    active_booking: room.bookings && room.bookings.length ? room.bookings[0] : null
                });
            }
        };

        window.openQuickReserveModal = function(roomId, dateStr) {
            if (!roomTimelineCache) return;
            const room = roomTimelineCache.rooms.find(r => r.room_id === roomId);
            if (!room) return;

            document.getElementById('calQuickModalSubtitle').textContent = `Room ${room.room_number} • ${room.room_type}`;
            document.getElementById('calQuickModalRoomTitle').textContent = `Room ${room.room_number}`;
            document.getElementById('calQuickModalRoomType').textContent = `${room.room_type} (${room.floor || 'Floor'})`;
            document.getElementById('calQuickModalRate').textContent = `₱${parseFloat(room.base_rate).toFixed(2)} / night`;
            document.getElementById('calQuickModalSelectedDate').textContent = dateStr;

            const resLink = document.getElementById('calQuickBtnNewReservation');
            if (resLink) resLink.href = `/frontdesk/reservation?room_id=${room.room_id}&arrival_date=${dateStr}&return_to=monitoring&open_modal=1`;

            const walkInLink = document.getElementById('calQuickBtnWalkInCheckIn');
            if (walkInLink) walkInLink.href = `/frontdesk/registration?room_id=${room.room_id}&arrival_date=${dateStr}&return_to=monitoring`;

            document.getElementById('calQuickBtnMarkCleaning').onclick = function() {
                calendarQuickReserveModal?.hide();
                window.changeRoomStatus(room.room_id, 'mark-for-cleaning');
            };

            document.getElementById('calQuickBtnMarkMaintenance').onclick = function() {
                calendarQuickReserveModal?.hide();
                window.changeRoomStatus(room.room_id, 'mark-maintenance');
            };

            calendarQuickReserveModal?.show();
        };

        window.openCalendarBookingModal = function(bookingId, roomId) {
            if (!roomTimelineCache) return;
            const room = roomTimelineCache.rooms.find(r => r.room_id === roomId);
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
            document.getElementById('calModalRoomType').textContent = `${room.room_type} (${room.floor || 'Floor'})`;
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
            if (overdueAlert) overdueAlert.classList.toggle('d-none', !booking.is_overdue);

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
            } else if (booking.status === 'CHECKED_IN') {
                badge.className = 'badge bg-primary text-white px-2.5 py-1.5 fw-semibold';
                badge.textContent = 'CHECKED IN (OCCUPIED)';
                icon.className = 'fa-solid fa-bed fs-5 text-primary';

                const folioUrl = booking.folio_id ? `/frontdesk/guest-folio/${booking.folio_id}` : '/frontdesk/guest-folio';
                actionContainer.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between gap-2 w-100">
                        <a href="${folioUrl}" class="btn btn-danger fw-semibold py-2 px-3 flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm text-truncate">
                            <i class="fa-solid fa-file-invoice-dollar fs-6 flex-shrink-0"></i>
                            <span class="text-truncate">Check Out / Folio</span>
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
                actionContainer.innerHTML = `
                    <a href="/frontdesk/guest-folio/${booking.folio_id}" class="btn text-white w-100 fw-semibold py-2" style="background: #334c42;">
                        <i class="fa-solid fa-file-invoice me-1"></i> View Folio Details
                    </a>
                `;
            }

            calendarBookingModal?.show();
        };

        // Facility Reservation Modal (Timeline bar click)
        window.openFacilityReservationModal = function(reservationId, facilityId) {
            if (!facilityTimelineCache) return;
            const facility = facilityTimelineCache.facilities.find(f => f.facility_id === facilityId);
            if (!facility) return;

            const res = (facility.reservations || []).find(r => r.reservation_id === reservationId);
            if (!res) return;

            const safeRef = (res.reference_number || '').replace(/'/g, "\\'");
            const displayRef = res.reference_number ? (res.reference_number.startsWith('#') ? res.reference_number : `#${res.reference_number}`) : '—';
            document.getElementById('facModalRefSubtitle').textContent = `Reference ${displayRef}`;
            document.getElementById('facModalFacilityName').textContent = facility.name;
            document.getElementById('facModalBillingInfo').textContent = `${facility.rate_type === 'hourly' ? 'Hourly' : 'Daily'} Rate • ₱${parseFloat(facility.rate).toFixed(2)}`;

            const badge = document.getElementById('facModalStatusBadge');
            badge.textContent = (res.status || '').toUpperCase();
            badge.className = 'badge px-2.5 py-1.5 fw-semibold ' + getFacilityStatusBadgeClass(res.status);

            document.getElementById('facModalEventName').textContent = res.event_name || 'Facility Reservation';
            document.getElementById('facModalEventDetails').textContent = res.event_details || '';
            document.getElementById('facModalBookerName').textContent = res.booker_name || 'Booker';

            const contactParts = [];
            if (res.booker_contact) contactParts.push(res.booker_contact);
            if (res.booker_email) contactParts.push(res.booker_email);
            document.getElementById('facModalBookerContact').textContent = contactParts.length ? contactParts.join(' • ') : 'No contact info';

            document.getElementById('facModalDates').textContent = res.start_date === res.end_date ? res.start_date : `${res.start_date} → ${res.end_date}`;
            document.getElementById('facModalTime').textContent = res.time_formatted || 'Full Day';
            document.getElementById('facModalAmount').textContent = `₱${parseFloat(res.estimated_amount).toFixed(2)}`;
            document.getElementById('facModalConsolidated').textContent = res.is_consolidated ? (res.facility_set_name || 'Consolidated Set') : 'Direct Single Facility';

            const actionsDiv = document.getElementById('facModalActionButtons');
            const showUrl = `/frontdesk/facility-reservations/${res.reservation_id}?return_to=monitoring`;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            let actionsHtml = `
                <div class="d-flex flex-wrap align-items-center gap-2 w-100">
                    <a href="${showUrl}" class="btn btn-outline-secondary flex-grow-1 fw-semibold py-2">
                        <i class="fa-solid fa-eye me-1"></i> Review Full Details
                    </a>
            `;

            if (res.status === 'pending') {
                actionsHtml += `
                    <form method="POST" action="/frontdesk/facility-reservations/${res.reservation_id}/approve" class="d-inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="return_to" value="monitoring">
                        <button type="submit" class="btn btn-success fw-semibold py-2 px-3">
                            <i class="fa-solid fa-check me-1"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger fw-semibold py-2 px-3" onclick="window.rejectFacilityReservation(${res.reservation_id}, '${safeRef}')">
                        <i class="fa-solid fa-xmark me-1"></i> Reject
                    </button>
                `;
            } else if (res.status === 'approved') {
                actionsHtml += `
                    <form method="POST" action="/frontdesk/facility-reservations/${res.reservation_id}/check-in" class="d-inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="return_to" value="monitoring">
                        <button type="submit" class="btn btn-success fw-semibold py-2 px-3">
                            <i class="fa-solid fa-play me-1"></i> Check In
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-dark fw-semibold py-2 px-3" onclick="window.openFacilityExtendModal(${res.reservation_id}, '${safeRef}', '${res.end_date}', '${res.end_time || '17:00'}')">
                        <i class="fa-solid fa-clock me-1"></i> Extend
                    </button>
                    <button type="button" class="btn btn-outline-danger fw-semibold py-2 px-3" onclick="window.openFacilityCancelModal(${res.reservation_id}, '${safeRef}', 'cancel')">
                        <i class="fa-solid fa-ban me-1"></i> Cancel
                    </button>
                `;
            } else if (res.status === 'active') {
                actionsHtml += `
                    <form method="POST" action="/frontdesk/facility-reservations/${res.reservation_id}/time-out" class="d-inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="return_to" value="monitoring">
                        <button type="submit" class="btn btn-warning fw-semibold py-2 px-3">
                            <i class="fa-solid fa-stop me-1"></i> Time Out
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-dark fw-semibold py-2 px-3" onclick="window.openFacilityExtendModal(${res.reservation_id}, '${safeRef}', '${res.end_date}', '${res.end_time || '17:00'}')">
                        <i class="fa-solid fa-clock me-1"></i> Extend
                    </button>
                `;
            }

            actionsHtml += `
                <button type="button" class="btn btn-sm btn-light border w-100 mt-1 py-1.5" onclick="window.switchToFacilityHourlyView('${res.start_date}')">
                    <i class="fa-solid fa-calendar-day me-1 text-primary"></i> View Hourly Schedule for ${res.start_date}
                </button>
            </div>`;

            actionsDiv.innerHTML = actionsHtml;
            facilityReservationModal?.show();
        };

        // Facility Quick Info Modal (Card click or sticky name click)
        window.openFacilityQuickInfo = function(facilityId) {
            let fac = null;
            if (facilityTimelineCache) {
                fac = facilityTimelineCache.facilities.find(f => f.facility_id === facilityId);
            }

            if (!fac) {
                window.location.href = `/frontdesk/facility-reservations?facility_id=${facilityId}`;
                return;
            }

            document.getElementById('facQuickInfoName').textContent = fac.name;
            document.getElementById('facQuickInfoRate').textContent = `₱${parseFloat(fac.rate).toFixed(2)} / ${fac.rate_type === 'hourly' ? 'hour' : 'day'}`;

            const badge = document.getElementById('facQuickInfoStatusBadge');
            badge.textContent = getFacilityStatusLabel(fac.current_status);
            badge.className = 'badge ' + getFacilityStatusBadgeClass(fac.current_status);

            document.getElementById('facQuickInfoCapacity').textContent = fac.capacity ? `${fac.capacity} pax` : 'Not specified';

            const bookBtn = document.getElementById('facQuickBtnBook');
            if (bookBtn) bookBtn.href = `/frontdesk/facility-reservations/create?facility=${fac.facility_id}&return_to=monitoring`;

            const viewBtn = document.getElementById('facQuickBtnViewReservations');
            if (viewBtn) viewBtn.href = `/frontdesk/facility-reservations?facility_id=${fac.facility_id}`;

            // Maintenance toggle form
            const toggleForm = document.getElementById('facQuickToggleForm');
            const toggleBtn = document.getElementById('facQuickBtnToggle');
            if (toggleForm && toggleBtn) {
                toggleForm.action = `/frontdesk/facilities/${fac.facility_id}/toggle?return_to=monitoring`;
                toggleForm.classList.remove('d-none');
                if (fac.is_active) {
                    toggleBtn.className = 'btn btn-outline-warning fw-semibold py-2 w-100';
                    toggleBtn.innerHTML = '<i class="fa-solid fa-power-off me-1"></i> Mark Out of Order / Maintenance';
                } else {
                    toggleBtn.className = 'btn btn-outline-success fw-semibold py-2 w-100';
                    toggleBtn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Mark Active & Available';
                }
            }

            facilityQuickInfoModal?.show();
        };

        window.closeFacilityReservationModal = function() {
            if (facilityReservationModal) {
                facilityReservationModal.hide();
            } else {
                const el = document.getElementById('facilityReservationModal');
                if (el && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(el).hide();
                }
            }
        };

        // Facility Extend Modal
        window.openFacilityExtendModal = function(reservationId, refNum, currentEndDate, currentEndTime) {
            window.closeFacilityReservationModal();

            const form = document.getElementById('facilityExtendForm');
            if (form) form.action = `/frontdesk/facility-reservations/${reservationId}/extend`;

            const refEl = document.getElementById('facExtendRefNum');
            if (refEl) refEl.textContent = refNum || `ID #${reservationId}`;

            const endD = document.getElementById('facExtendEndDate');
            if (endD) endD.value = currentEndDate || getTodayDateStr();

            const endT = document.getElementById('facExtendEndTime');
            if (endT) endT.value = currentEndTime || '18:00';

            facilityExtendModal?.show();
        };

        // Facility Cancel / Reject Modal
        window.openFacilityCancelModal = function(reservationId, refNum, actionType) {
            window.closeFacilityReservationModal();

            const form = document.getElementById('facilityCancelForm');
            const label = document.getElementById('facilityCancelModalLabel');
            const submitBtn = document.getElementById('facCancelSubmitBtn');
            const refEl = document.getElementById('facCancelRefNum');

            if (refEl) refEl.textContent = refNum || `ID #${reservationId}`;

            if (actionType === 'reject') {
                if (form) form.action = `/frontdesk/facility-reservations/${reservationId}/reject`;
                if (label) label.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Reject Booking Request';
                if (submitBtn) submitBtn.textContent = 'Confirm Rejection';
            } else {
                if (form) form.action = `/frontdesk/facility-reservations/${reservationId}/cancel`;
                if (label) label.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Cancel Facility Reservation';
                if (submitBtn) submitBtn.textContent = 'Confirm Cancellation';
            }

            facilityCancelModal?.show();
        };

        window.rejectFacilityReservation = async function(reservationId, refNum) {
            window.closeFacilityReservationModal();

            const popupOptions = {
                title: `Reject reservation ${refNum || `#${reservationId}`}?`,
                input: 'textarea',
                inputLabel: 'Reason for rejection',
                inputPlaceholder: 'State the reason for rejecting this booking request...',
                inputAttributes: {
                    'aria-label': 'Reason for rejection'
                },
                showCancelButton: true,
                confirmButtonText: 'Reject Reservation',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Keep Booking',
                reverseButtons: true,
                inputValidator: value => {
                    if (!value || !value.trim()) {
                        return 'A rejection reason is required.';
                    }
                    return undefined;
                }
            };

            if (!window.Swal) {
                const reason = window.prompt('Reason for rejection:');
                if (!reason || !reason.trim()) return;

                submitFacilityReservationRejection(reservationId, reason.trim());
                return;
            }

            const result = await window.Swal.fire(popupOptions);
            if (result.isConfirmed) {
                submitFacilityReservationRejection(reservationId, result.value.trim());
            }
        };

        function submitFacilityReservationRejection(reservationId, reason) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/frontdesk/facility-reservations/${reservationId}/reject`;
            form.dataset.turbo = 'false';

            [
                ['_token', document.querySelector('meta[name="csrf-token"]')?.content || ''],
                ['_method', 'PATCH'],
                ['admin_notes', reason],
                ['return_to', 'monitoring']
            ].forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Helper operations: change room status, check in, extend
        window.changeRoomStatus = function(roomId, action) {
            fetch(`/frontdesk/room/${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ room_id: roomId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    roomActionModal?.hide();
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    roomTimelineCache = null;
                    fetchRoomTimelineData();
                } else {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Update Failed', text: data.message || 'Action failed.' });
                    } else {
                        alert(data.message || 'Action failed.');
                    }
                }
            })
            .catch(err => {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
                } else {
                    alert('Network error: ' + err.message);
                }
            });
        }

        function checkInGuest(bookingId, guestName, roomNumber) {
            document.getElementById('checkInConfirmGuestName').textContent = guestName;
            document.getElementById('checkInConfirmRoomNumber').textContent = roomNumber;
            const confirmBtn = document.getElementById('confirmCheckInBtn');
            if (confirmBtn) {
                confirmBtn.onclick = function() {
                    const netRate = document.getElementById('checkInNetRate')?.value;
                    const payload = { booking_id: bookingId };
                    if (netRate) payload.net_rate = netRate;

                    window.setBtnLoading(confirmBtn, true, 'Processing Check-In...');

                    fetch('{{ route("frontdesk.booking.check-in") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        window.setBtnLoading(confirmBtn, false);
                        if (data.success) {
                            checkInConfirmModal?.hide();
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Check-In Successful',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                            roomTimelineCache = null;
                            fetchRoomTimelineData();
                        } else {
                            if (window.Swal) {
                                Swal.fire({ icon: 'error', title: 'Check-In Failed', text: data.message || 'Unable to check in.' });
                            } else {
                                alert(data.message || 'Check-in failed.');
                            }
                        }
                    })
                    .catch(err => {
                        window.setBtnLoading(confirmBtn, false);
                        if (window.Swal) {
                            Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
                        } else {
                            alert('Network error: ' + err.message);
                        }
                    });
                };
            }
            checkInConfirmModal?.show();
        }

        function openCalendarExtendModal(booking, room) {
            document.getElementById('extendModalGuestName').textContent = booking.guest_name;
            document.getElementById('extendModalRoomBadge').textContent = `Room ${room.room_number}`;
            document.getElementById('extendModalRoomType').textContent = room.room_type;
            document.getElementById('extendModalFolioNumber').textContent = booking.folio_number;
            document.getElementById('extendModalStatus').textContent = booking.status;
            document.getElementById('extendModalArrival').textContent = booking.arrival_date;
            document.getElementById('extendModalCurrentDeparture').textContent = booking.departure_date;

            document.getElementById('extendBookingId').value = booking.booking_id;
            
            const arrContainer = document.getElementById('extendArrivalContainer');
            const arrDateInput = document.getElementById('extendArrivalDate');
            const arrTimeInput = document.getElementById('extendArrivalTime');
            
            if (booking.status === 'CHECKED_IN') {
                if (arrContainer) arrContainer.classList.add('d-none');
                if (arrDateInput) arrDateInput.removeAttribute('required');
            } else {
                if (arrContainer) arrContainer.classList.remove('d-none');
                if (arrDateInput) {
                    arrDateInput.setAttribute('required', 'required');
                    arrDateInput.value = booking.arrival_date;
                }
                if (arrTimeInput) arrTimeInput.value = booking.arrival_time || '14:00';
            }

            document.getElementById('extendDepartureDate').value = booking.departure_date;
            document.getElementById('extendDepartureTime').value = booking.departure_time || '12:00';
            document.getElementById('extendNetRate').value = booking.net_rate || '';

            document.getElementById('confirmExtendDepartureBtn').onclick = function() {
                const bId = document.getElementById('extendBookingId').value;
                const newArrDate = document.getElementById('extendArrivalDate') ? document.getElementById('extendArrivalDate').value : booking.arrival_date;
                const newArrTime = document.getElementById('extendArrivalTime') ? document.getElementById('extendArrivalTime').value : booking.arrival_time;
                const newDep = document.getElementById('extendDepartureDate').value;
                const newTime = document.getElementById('extendDepartureTime').value;
                const newRate = document.getElementById('extendNetRate').value;
                const btn = this;
                window.setBtnLoading(btn, true, 'Updating...');

                fetch('{{ route("frontdesk.booking.move-date") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        booking_id: bId,
                        arrival_date: newArrDate,
                        arrival_time: newArrTime,
                        departure_date: newDep,
                        departure_time: newTime,
                        net_rate: newRate
                    })
                })
                .then(r => r.json())
                .then(res => {
                    window.setBtnLoading(btn, false);
                    if (res.success) {
                        extendDepartureModal?.hide();
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dates Updated',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                        roomTimelineCache = null;
                        fetchRoomTimelineData();
                    } else {
                        const err = document.getElementById('extendErrorAlert');
                        if (err) {
                            err.textContent = res.message || 'Failed to update departure.';
                            err.classList.remove('d-none');
                        }
                    }
                })
                .catch(e => {
                    window.setBtnLoading(btn, false);
                    alert(e.message);
                });
            };

            extendDepartureModal?.show();
        }

        window.openExtendStay = function(bookingId) {
            if (!bookingId) return;
            window.location.href = `/frontdesk/guest-folio`;
        };

        // Run initialization after all public handlers have been assigned.
        initMonitoringPage();
    })();
</script>
@endpush
