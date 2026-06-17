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

    .cleaning {
        background: #ff4d4f;
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
                        <h2 class="fw-bold mt-2">12</h2>
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
                        <h2 class="fw-bold mt-2">8</h2>
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
                        <h2 class="fw-bold mt-2">86</h2>
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
                        <h2 class="fw-bold mt-2">34</h2>
                    </div>
                    <i class="fa-solid fa-door-open fa-2x text-warning"></i>
                </div>
            </div>
        </div>
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
                <span class="legend-dot bg-secondary"></span>
                Occupied Room
            </div>

            <div>
                <span class="legend-dot bg-success"></span>
                Available Room
            </div>

            <div>
                <span class="legend-dot bg-danger"></span>
                Cleaning Room
            </div>

        </div>

        <div class="row">

            <!-- LEFT MENU -->
            <div class="col-lg-2 mb-3">

                <button class="room-type-btn">
                    Single Room
                </button>

                <button class="room-type-btn">
                    Twin Room
                </button>

                <button class="room-type-btn">
                    Studio Room
                </button>

                <button class="room-type-btn">
                    Deluxe Room
                </button>

                <button class="room-type-btn active">
                    Suite
                </button>

                <button class="room-type-btn">
                    President Suite
                </button>

                <button class="room-type-btn">
                    Connecting Room
                </button>

            </div>

            <!-- ROOM GRID -->
            <div class="col-lg-10">

                <div class="room-grid">

                    <!-- Available -->
                    <div class="room-wrapper">
                        <div class="room-box available">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                    </div>

                    <!-- Occupied -->
                    <div class="room-wrapper">
                        <div class="room-box occupied">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>

                    <!-- Cleaning -->
                    <div class="room-wrapper">
                        <div class="room-box cleaning">
                            <i class="fa-solid fa-broom"></i>
                        </div>
                    </div>

                    <div class="room-box available">
                        <i class="fa-solid fa-bed"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box available">
                        <i class="fa-solid fa-bed"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box available">
                        <i class="fa-solid fa-bed"></i>
                    </div>

                    <div class="room-box cleaning">
                        <i class="fa-solid fa-broom"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box available">
                        <i class="fa-solid fa-bed"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="room-box cleaning">
                        <i class="fa-solid fa-broom"></i>
                    </div>

                    <div class="room-box available">
                        <i class="fa-solid fa-bed"></i>
                    </div>

                    <div class="room-box occupied">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

@endsection