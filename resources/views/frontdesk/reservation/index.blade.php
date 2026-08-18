@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    /* Match the project's existing input height and border */
    #newReservationModal .ts-wrapper.single .ts-control,
    #newReservationModal .ts-wrapper .ts-control {
        min-height: 46px;
        border: 1px solid #000 !important;
        border-radius: .375rem;
        padding-top: 0;
        padding-bottom: 0;
        display: flex;
        align-items: center;
        box-shadow: none !important;
    }
    #newReservationModal .ts-wrapper.single.input-active .ts-control,
    #newReservationModal .ts-wrapper.single:focus-within .ts-control {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 .25rem rgba(13,110,253,.25) !important;
    }
    #newReservationModal .ts-dropdown {
        border: 1px solid #ced4da;
        border-radius: .375rem;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
        z-index: 1060;
    }
    #newReservationModal .ts-dropdown .option.selected {
        background-color: #0d6efd;
        color: #fff;
    }
    #newReservationModal .ts-dropdown .option:hover {
        background-color: #e9ecef;
        color: #212529;
    }
    #newReservationModal .ts-dropdown .option.selected:hover {
        background-color: #0b5ed7;
    }
</style>
@endpush

@section('title', 'Reservation - Don Felipe Hotel')
@section('pageTitle', 'Reservation')
@section('pageSubtitle', 'Manage room reservations and booking schedules.')

@section('content')

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    @if(session('success'))
        <div class="toast align-items-center text-white bg-success border-0 shadow show" role="alert" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="toast align-items-center text-white bg-danger border-0 shadow show" role="alert" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Room Reservation Entry</h5>
                <small class="text-muted">
                    View, create, edit and manage room reservations.
                </small>
            </div>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newReservationModal">
                <i class="fa-solid fa-plus me-2"></i>
                New Reservation
            </button>
        </div>

        <form method="GET"
            action="{{ route('frontdesk.reservation') }}"
            class="d-flex align-items-center gap-2 flex-wrap justify-content-end mb-4"
            id="reservationFilterForm">

            <!-- Search -->
            <div style="width: 320px;">
                <div class="input-group fd-search">
                    <span class="input-group-text bg-white border-0">
                        <i class="fa-solid fa-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        class="form-control border-0 shadow-none"
                        id="filterSearch"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Guest name or folio..."
                        autocomplete="off">
                </div>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3 position-relative fd-filter-btn"
                        type="button"
                        data-bs-toggle="dropdown">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                    @if($filters['status'] !== 'all' || !empty($filters['date_from']) || !empty($filters['date_to']))
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 280px; border-radius: 8px;">

                    <!-- Status -->
                    <label class="form-label small mb-1 fw-semibold">Status</label>
                    <select
                        class="form-select mb-3 shadow-none"
                        id="filterStatus"
                        name="status"
                        style="height:38px;border-radius:6px;">
                        <option value="all" @selected($filters['status'] === 'all')>All</option>
                        <option value="RESERVED" @selected($filters['status'] === 'RESERVED')>Reserved</option>
                        <option value="CHECKED_IN" @selected($filters['status'] === 'CHECKED_IN')>Checked In</option>
                        <option value="CHECKED_OUT" @selected($filters['status'] === 'CHECKED_OUT')>Checked Out</option>
                        <option value="CANCELLED" @selected($filters['status'] === 'CANCELLED')>Cancelled</option>
                    </select>

                    <!-- Date From -->
                    <label class="form-label small mb-1 fw-semibold">Date From</label>
                    <input
                        type="date"
                        class="form-control mb-3 shadow-none"
                        id="filterDateFrom"
                        name="date_from"
                        value="{{ $filters['date_from'] }}"
                        style="height:38px;border-radius:6px;">

                    <!-- Date To -->
                    <label class="form-label small mb-1 fw-semibold">Date To</label>
                    <input
                        type="date"
                        class="form-control mb-3 shadow-none"
                        id="filterDateTo"
                        name="date_to"
                        value="{{ $filters['date_to'] }}"
                        style="height:38px;border-radius:6px;">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                        <a href="{{ route('frontdesk.reservation') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                    </div>
                </div>
            </div>

        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Guest Name</th>
                        <th>Folio No.</th>
                        <th>Arrival</th>
                        <th>Departure</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->room->room_number }}</td>
                            <td>{{ $reservation->room->room_type }}</td>
                            <td>
                                {{ $reservation->folio->guest->first_name }}
                                {{ $reservation->folio->guest->last_name }}
                            </td>
                            <td>{{ $reservation->folio->folio_number }}</td>
                            <td>
                                {{ $reservation->arrival_date->format('m/d/Y') }}
                                @if($reservation->arrival_time)
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($reservation->arrival_time)->format('g:i A') }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $reservation->departure_date ? $reservation->departure_date->format('m/d/Y') : '—' }}
                                @if($reservation->departure_time)
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($reservation->departure_time)->format('g:i A') }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($reservation->status)
                                    @case('RESERVED')
                                        <span class="badge-status badge-status-reserved">Reserved</span>
                                        @break
                                    @case('CHECKED_IN')
                                        <span class="badge-status badge-status-checkedin">Checked In</span>
                                        @break
                                    @case('CHECKED_OUT')
                                        <span class="badge-status badge-status-closed">Checked Out</span>
                                        @break
                                    @case('CANCELLED')
                                        <span class="badge-status badge-status-maintenance">Cancelled</span>
                                        @break
                                    @default
                                        <span class="badge-status badge-status-maintenance">{{ $reservation->status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary view-reservation-btn"
                                        title="View reservation details"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewReservationModal"
                                        data-room="{{ $reservation->room->room_number }}"
                                        data-type="{{ $reservation->room->room_type }}"
                                        data-guest="{{ $reservation->folio->guest->first_name }} {{ $reservation->folio->guest->last_name }}"
                                        data-contact="{{ $reservation->folio->guest->contact_number }}"
                                        data-folio="{{ $reservation->folio->folio_number }}"
                                        data-registration="{{ $reservation->folio->registration_number }}"
                                        data-arrival="{{ $reservation->arrival_date->format('M d, Y') }}"
                                        data-arrival-time="{{ $reservation->arrival_time ? \Carbon\Carbon::parse($reservation->arrival_time)->format('g:i A') : '—' }}"
                                        data-departure="{{ $reservation->departure_date ? $reservation->departure_date->format('M d, Y') : 'Open Stay' }}"
                                        data-departure-time="{{ $reservation->departure_time ? \Carbon\Carbon::parse($reservation->departure_time)->format('g:i A') : '—' }}"
                                        data-status="{{ $reservation->status }}"
                                        data-pax="{{ $reservation->folio->num_pax }}"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    @if($reservation->status === 'RESERVED')
                                        <form method="POST" action="{{ route('frontdesk.reservation.cancel', $reservation) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn btn-outline-danger" title="Cancel this reservation"
                                                onclick="swalConfirmCancelReservation(this)">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="fd-empty-state">
                                    <i class="fa-solid fa-calendar-xmark d-block"></i>
                                    <div class="fw-semibold text-dark">No reservations found</div>
                                    <small class="text-muted">No reservations match the current search or filter criteria.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $reservations->links() }}
            </div>
        @endif

    </div>
</div>

@endsection

@push('modals')
<style>
    #newReservationModal .modal-dialog {
        max-height: calc(100vh - 2rem);
        margin: 1rem auto;
    }

    #newReservationModal .modal-content {
        max-height: calc(100vh - 2rem);
        overflow: hidden;
    }

    #newReservationModal .modal-body {
        max-height: calc(100vh - 12rem);
        overflow-y: auto;
    }
</style>

<!-- NEW RESERVATION MODAL -->
<div class="modal fade" id="newReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl">
        <form method="POST" action="{{ route('frontdesk.reservation.store') }}" id="newReservationForm" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user me-2"></i>Guest (guests table)</h6>
                    <div class="row g-3 mb-3 border-bottom pb-3">
                        <div class="col-md-12 position-relative">
                            <label class="form-label" for="guest_search">Search Existing Guest (Optional)</label>
                            <div class="input-group"
                                style="width:400px; border:1px solid #000000; border-radius:.5rem; overflow:hidden;">

                                <span class="input-group-text bg-white border-0 px-3">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                </span>

                                <input
                                    type="text"
                                    class="form-control border-0 shadow-none"
                                    id="guest_search"
                                    placeholder="Type guest name to search..."
                                    autocomplete="off"
                                    style="height:46px; font-size:15px;"
                                >

                            </div>
                            <div id="guest_search_results" class="dropdown-menu w-100 shadow-sm mt-1" style="display: none; max-height: 250px; overflow-y: auto; z-index: 1050;"></div>
                            <div class="form-text">If there is no existing guest, simply type the guest details directly below.</div>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">

                        <!-- First Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="first_name">
                                First Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('first_name') is-invalid @enderror"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                maxlength="50"
                                placeholder="Enter first name"
                                style="height:46px; border:1px solid #000000;"
                                required>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="last_name">
                                Last Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('last_name') is-invalid @enderror"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                maxlength="50"
                                placeholder="Enter last name"
                                style="height:46px; border:1px solid #000000;"
                                required>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="contact_number">
                                Contact Number
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('contact_number') is-invalid @enderror"
                                id="contact_number"
                                name="contact_number"
                                value="{{ old('contact_number') }}"
                                maxlength="20"
                                placeholder="09XXXXXXXXX"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="address_line1">
                                Address Line 1
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('address_line1') is-invalid @enderror"
                                id="address_line1"
                                name="address_line1"
                                value="{{ old('address_line1') }}"
                                maxlength="100"
                                placeholder="Street, Barangay"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" for="address_line2">
                                Address Line 2
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('address_line2') is-invalid @enderror"
                                id="address_line2"
                                name="address_line2"
                                value="{{ old('address_line2') }}"
                                maxlength="100"
                                placeholder="City, Province (Optional)"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-file-invoice me-2"></i>Folio (folios table)</h6>
                    <div class="row g-4 mb-4">

                        <!-- Folio Number -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="folio_number">
                                Folio Number
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('folio_number') is-invalid @enderror"
                                id="folio_number"
                                name="folio_number"
                                value="{{ old('folio_number', $suggestedFolioNumber) }}"
                                maxlength="20"
                                placeholder="Auto-generated if left blank"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                        <!-- Registration Number -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="registration_number">
                                Registration Number
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('registration_number') is-invalid @enderror"
                                id="registration_number"
                                name="registration_number"
                                value="{{ old('registration_number') }}"
                                maxlength="20"
                                placeholder="Enter registration number"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                        <!-- Account Number -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="account_number">
                                Account Number
                            </label>

                            <input
                                type="text"
                                class="form-control shadow-none @error('account_number') is-invalid @enderror"
                                id="account_number"
                                name="account_number"
                                value="{{ old('account_number') }}"
                                maxlength="20"
                                placeholder="Enter account number"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                        <!-- Number of Guests -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="num_pax">
                                Number of Guests
                            </label>

                            <input
                                type="number"
                                class="form-control shadow-none @error('num_pax') is-invalid @enderror"
                                id="num_pax"
                                name="num_pax"
                                value="{{ old('num_pax', 1) }}"
                                min="1"
                                max="20"
                                placeholder="1"
                                style="height:46px; border:1px solid #000000;">
                        </div>

                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-bed me-2"></i>Booking (bookings table)</h6>
                    <div class="row g-4">

                        <!-- Filter by Room Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="room_type_filter">
                                Filter by Room Type
                            </label>

                            <select
                                class="form-select shadow-none"
                                id="room_type_filter"
                                style="height:46px; border:1px solid #000000;">

                                <option value="">All Types</option>

                                @foreach($roomTypes as $type)
                                    <option value="{{ $type }}">
                                        {{ $type }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- Room -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="room_id">
                                Select Room
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select shadow-none @error('room_id') is-invalid @enderror"
                                id="room_id"
                                name="room_id"
                                style="height:46px; border:1px solid #000000;"
                                required>

                                <option value="">Select room for reservation</option>

                                @foreach($assignableRooms as $room)
                                    <option
                                        value="{{ $room->room_id }}"
                                        data-room-type="{{ $room->room_type }}"
                                        @selected(old('room_id') == $room->room_id)
                                    >
                                        {{ $room->room_number }}
                                        — {{ $room->room_type }}
                                        (₱{{ number_format($room->base_rate, 2) }})
                                        @if($room->status === 'CLEANING')
                                            [Needs Cleaning]
                                        @endif
                                    </option>
                                @endforeach

                            </select>

                            @if($assignableRooms->isEmpty())
                                <div class="form-text text-danger mt-2">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    No assignable rooms right now.
                                </div>
                            @endif
                        </div>

                        <!-- Arrival Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="arrival_date">
                                Arrival Date
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                class="form-control shadow-none @error('arrival_date') is-invalid @enderror"
                                id="arrival_date"
                                name="arrival_date"
                                value="{{ old('arrival_date', now()->toDateString()) }}"
                                style="height:46px; border:1px solid #000000;"
                                required>
                        </div>

                        <!-- Arrival Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="arrival_time">
                                Arrival Time
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="time"
                                class="form-control shadow-none @error('arrival_time') is-invalid @enderror"
                                id="arrival_time"
                                name="arrival_time"
                                value="{{ old('arrival_time', '12:00') }}"
                                style="height:46px; border:1px solid #000000;"
                                required>
                        </div>

                        <!-- Departure Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="departure_date">
                                Departure Date
                                <span class="text-danger reservation-departure-required">*</span>
                            </label>

                            <input
                                type="date"
                                class="form-control shadow-none @error('departure_date') is-invalid @enderror"
                                id="departure_date"
                                name="departure_date"
                                value="{{ old('departure_date') }}"
                                style="height:46px; border:1px solid #000000;"
                                required>
                        </div>

                        <!-- Departure Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="departure_time">
                                Departure Time
                                <span class="text-danger reservation-departure-required">*</span>
                            </label>

                            <input
                                type="time"
                                class="form-control shadow-none @error('departure_time') is-invalid @enderror"
                                id="departure_time"
                                name="departure_time"
                                value="{{ old('departure_time', '12:00') }}"
                                style="height:46px; border:1px solid #000000;"
                                required>
                        </div>

                        <!-- Open Stay -->
                        <div class="col-12">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="reservation_open_stay"
                                    name="open_stay"
                                    value="1"
                                    @checked(old('open_stay'))>
                                <label class="form-check-label fw-semibold" for="reservation_open_stay">
                                    Open Stay (no checkout date)
                                </label>
                            </div>
                        </div>

                        <!-- Information -->
                        <div class="col-12">
                            <div class="alert alert-primary border rounded-3 d-flex align-items-start mb-0">
                                <i class="fa-solid fa-circle-info fs-5 me-3 mt-1"></i>

                                <div>
                                    <strong>Reservation Status</strong><br>

                                    New reservations are automatically saved with the
                                    <strong>RESERVED</strong> status and will appear on the
                                    dashboard once the arrival date is today.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" @disabled($assignableRooms->isEmpty())>
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Reservation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW RESERVATION MODAL -->
<div class="modal fade" id="viewReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">

            <!-- Header -->
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-calendar-check text-primary me-2"></i>
                    Reservation Details
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">

                <table class="table table-hover align-middle mb-0">

                    <tbody>

                        <tr>
                            <th width="35%" class="text-muted fw-semibold">
                                Guest
                            </th>
                            <td id="viewGuest" class="fw-semibold"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Contact Number
                            </th>
                            <td id="viewContact"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Folio Number
                            </th>
                            <td id="viewFolio"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Registration Number
                            </th>
                            <td id="viewRegistration"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Room
                            </th>
                            <td id="viewRoom"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Arrival
                            </th>
                            <td id="viewArrival"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Departure
                            </th>
                            <td id="viewDeparture"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Number of Guests
                            </th>
                            <td id="viewPax"></td>
                        </tr>

                        <tr>
                            <th class="text-muted fw-semibold">
                                Status
                            </th>
                            <td>
                                <span id="viewStatus"></span>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-top">
                <button type="button"
                        class="btn btn-outline-secondary px-4"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    (function() {

        const guestSearchInput = document.getElementById('guest_search');
        const guestSearchResults = document.getElementById('guest_search_results');

        if (guestSearchInput && guestSearchResults) {
            let debounceTimer;

            guestSearchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    guestSearchResults.innerHTML = '';
                    guestSearchResults.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('frontdesk.guests.search') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(guests => {
                        guestSearchResults.innerHTML = '';
                        if (guests.length === 0) {
                            const item = document.createElement('div');
                            item.className = 'dropdown-item text-muted';
                            item.textContent = 'No matching guests found';
                            guestSearchResults.appendChild(item);
                        } else {
                            guests.forEach(guest => {
                                const item = document.createElement('a');
                                item.className = 'dropdown-item';
                                item.href = 'javascript:void(0);';
                                item.style.cursor = 'pointer';
                                
                                const folioNum = guest.folios && guest.folios.length > 0 
                                    ? guest.folios[0].folio_number 
                                    : '';

                                item.textContent = `${guest.last_name}, ${guest.first_name} ${guest.contact_number ? '('+guest.contact_number+')' : ''}`;
                                
                                item.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    
                                    document.getElementById('first_name').value = guest.first_name;
                                    document.getElementById('last_name').value = guest.last_name;
                                    document.getElementById('contact_number').value = guest.contact_number || '';
                                    document.getElementById('address_line1').value = guest.address_line1 || '';
                                    document.getElementById('address_line2').value = guest.address_line2 || '';
                                    
                                    document.getElementById('folio_number').value = "{{ $suggestedFolioNumber }}";

                                    guestSearchInput.value = `${guest.last_name}, ${guest.first_name}`;
                                    guestSearchResults.innerHTML = '';
                                    guestSearchResults.style.display = 'none';
                                });
                                guestSearchResults.appendChild(item);
                            });
                        }
                        guestSearchResults.style.display = 'block';
                    });
                }, 300);
            });

            const documentClickHandler = function(e) {
                // Self-clean if elements are no longer in the DOM
                if (!document.body.contains(guestSearchInput)) {
                    document.removeEventListener('click', documentClickHandler);
                    return;
                }
                if (!guestSearchInput.contains(e.target) && !guestSearchResults.contains(e.target)) {
                    guestSearchResults.style.display = 'none';
                }
            };
            document.addEventListener('click', documentClickHandler);
        }

        const roomTypeFilter = document.getElementById('room_type_filter');
        const roomSelect = document.getElementById('room_id');
        let roomTomSelect = null;

        // Tom Select must be initialised after the modal is visible so the
        // dropdown can calculate its position correctly.
        const reservationModal = document.getElementById('newReservationModal');
        if (reservationModal) {
            reservationModal.addEventListener('shown.bs.modal', function() {
                // Only initialise once
                if (!roomTomSelect && roomSelect) {
                    roomTomSelect = new TomSelect('#room_id', {
                        placeholder: 'Search or select a room…',
                        allowEmptyOption: true,
                        selectOnTab: true,
                        maxOptions: null,
                        dropdownParent: 'body',
                        render: {
                            option: function(data, escape) {
                                return `<div class="py-1">${escape(data.text)}</div>`;
                            },
                            item: function(data, escape) {
                                return `<div>${escape(data.text)}</div>`;
                            }
                        }
                    });

                    // Wire up Room Type filter to Tom Select
                    if (roomTypeFilter) {
                        roomTypeFilter.addEventListener('change', function() {
                            const selectedType = this.value;

                            Object.values(roomTomSelect.options).forEach(opt => {
                                const optEl = roomSelect.querySelector(`option[value="${opt.value}"]`);
                                const roomType = optEl ? optEl.getAttribute('data-room-type') : null;
                                const matches = !selectedType || roomType === selectedType;

                                if (matches) {
                                    roomTomSelect.removeOption(opt.value, true);
                                    roomTomSelect.addOption(opt, true);
                                } else {
                                    if (roomTomSelect.getValue() === opt.value) {
                                        roomTomSelect.clear(true);
                                    }
                                    roomTomSelect.removeOption(opt.value, true);
                                }
                            });
                            roomTomSelect.refreshOptions(false);
                        });
                    }
                }
            });

            // Re-open the modal automatically if there were validation errors
            @if($errors->any() || old('first_name'))
                const _modal = new bootstrap.Modal(reservationModal);
                _modal.show();
            @endif
        }

        document.querySelectorAll('.view-reservation-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('viewGuest').textContent = this.dataset.guest || '—';
                document.getElementById('viewContact').textContent = this.dataset.contact || '—';
                document.getElementById('viewFolio').textContent = this.dataset.folio || '—';
                document.getElementById('viewRegistration').textContent = this.dataset.registration || '—';
                document.getElementById('viewRoom').textContent = `${this.dataset.room} (${this.dataset.type})`;
                document.getElementById('viewArrival').textContent = `${this.dataset.arrival} @ ${this.dataset.arrivalTime}`;
                document.getElementById('viewDeparture').textContent = `${this.dataset.departure} @ ${this.dataset.departureTime}`;
                document.getElementById('viewPax').textContent = this.dataset.pax || '1';
                document.getElementById('viewStatus').textContent = this.dataset.status || '—';
            });
        });

        const arrivalDate = document.getElementById('arrival_date');
        const departureDate = document.getElementById('departure_date');
        const departureTime = document.getElementById('departure_time');
        const openStayCheckbox = document.getElementById('reservation_open_stay');

        function toggleReservationOpenStay() {
            const isOpenStay = openStayCheckbox && openStayCheckbox.checked;

            if (departureDate) {
                departureDate.disabled = isOpenStay;
                departureDate.required = !isOpenStay;
                if (isOpenStay) {
                    departureDate.value = '';
                }
            }

            if (departureTime) {
                departureTime.disabled = isOpenStay;
                departureTime.required = !isOpenStay;
                if (isOpenStay) {
                    departureTime.value = '';
                }
            }

            document.querySelectorAll('.reservation-departure-required').forEach(function(el) {
                el.classList.toggle('d-none', isOpenStay);
            });
        }

        if (openStayCheckbox) {
            openStayCheckbox.addEventListener('change', toggleReservationOpenStay);
            toggleReservationOpenStay();
        }

        if (arrivalDate && departureDate) {
            arrivalDate.addEventListener('change', function() {
                departureDate.min = this.value;
                if (departureDate.value && departureDate.value <= this.value) {
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    departureDate.value = nextDay.toISOString().split('T')[0];
                }
            });
        }
        // SweetAlert2 — Module 6: Cancel Reservation
        window.swalConfirmCancelReservation = function(btn) {
            var form = btn.closest('form');
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

        // Debounce auto-submit for server-side search input
        const searchInput = document.getElementById('filterSearch');
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
