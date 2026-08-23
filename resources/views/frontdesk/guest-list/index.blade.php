@extends('layouts.app')

@section('title', 'Guest List - EVSU Hotel')
@section('pageTitle', 'Guest List')
@section('pageSubtitle', 'Search and view all registered hotel guests.')

@section('content')

<div class="container-fluid d-print-none">

    {{-- Search and Filter Bar --}}
    <div class="card border-1 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('frontdesk.guest-list') }}" id="searchForm" class="d-flex align-items-center gap-2 flex-wrap justify-content-end m-0">
                
                <!-- SEARCH -->
                <div style="width: 320px; max-width: 100%;">
                    <div class="input-group shadow-sm" style="border: 1px solid #c2a889; border-radius: 0.5rem; overflow: hidden; height: 45px; background-color: #ffffff;">
                        <span class="input-group-text bg-white border-0 px-3">
                            <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-0 shadow-none py-2"
                            style="font-size: 1rem;"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search by name..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <!-- FILTER DROPDOWN -->
                <div class="dropdown">
                    <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                            type="button"
                            data-bs-toggle="dropdown"
                            style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem;">
                        <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                        <span class="fw-semibold">Filter</span>
                        @if($status !== '' && $status !== null)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                         onclick="event.stopPropagation()"
                         style="min-width: 280px; border-radius: 0.75rem;">

                        <!-- Status -->
                        <label class="form-label small mb-1 fw-semibold text-muted" for="status">Status</label>
                        <select class="form-select mb-3 shadow-none" id="status" name="status" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                            <option value="" {{ $status === '' || $status === null ? 'selected' : '' }}>All Statuses</option>
                            <option value="checked_in" {{ $status === 'checked_in' ? 'selected' : '' }}>Checked In (In-House)</option>
                            <option value="checked_out" {{ $status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        </select>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                            <a href="{{ route('frontdesk.guest-list') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                        </div>
                    </div>
                </div>

                <!-- PRINT BUTTON -->
                @php
                    $printText = 'Print Guest List';
                @endphp
                <button type="button" class="btn px-3 py-2 fw-semibold shadow-sm" style="height: 45px; border: 1px solid #c2a889; background-color: #f3ede4; color: #3d332a; font-size: 1rem; border-radius: 0.5rem;" onclick="window.print()">
                    <i class="fa-solid fa-print me-1" style="color: #334c42;"></i> {{ $printText }}
                </button>

            </form>
        </div>
    </div>

    {{-- Results Summary --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="mb-0" style="color: #3d332a;">
            @if($search)
                Showing <strong>{{ $guests->total() }}</strong> result(s) for
                "<strong>{{ $search }}</strong>"
            @else
                Showing all <strong>{{ $guests->total() }}</strong> registered guest(s)
            @endif
        </p>
        <span class="badge px-3 py-2 fw-semibold shadow-sm" style="border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;">
            Page {{ $guests->currentPage() }} of {{ $guests->lastPage() }}
        </span>
    </div>

    {{-- Guest Table --}}
    <div class="card border-1 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="guestTable">
                    <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                        <tr class="small fw-bold">
                            <th class="px-4 py-3" style="color: #2c241d;">#</th>
                            <th class="py-3" style="color: #2c241d;">GUEST NAME</th>
                            <th class="py-3" style="color: #2c241d;">MOBILE NUMBER</th>
                            <th class="py-3" style="color: #2c241d;">ADDRESS</th>
                            <th class="py-3 text-center" style="color: #2c241d;">TOTAL STAYS</th>
                            <th class="py-3 text-center" style="color: #2c241d;">LAST ROOM</th>
                            <th class="py-3 text-center" style="color: #2c241d;">LAST STAY</th>
                            <th class="py-3 text-center" style="color: #2c241d;">STATUS</th>
                            <th class="py-3 text-center" style="color: #2c241d;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $index => $guest)
                            @php
                                $totalStays     = $guest->folios->count();
                                $lastFolio      = $guest->folios->sortByDesc('folio_id')->first();
                                $lastBooking    = $lastFolio?->bookings->sortByDesc('booking_id')->first();
                                $lastRoom       = $lastBooking?->room;
                                $isCurrentlyIn  = $lastBooking && $lastBooking->status === 'CHECKED_IN';
                            @endphp
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td class="px-4 fw-medium" style="color: #1a1a1a;">
                                    {{ ($guests->currentPage() - 1) * $guests->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #1a1a1a;">
                                        {{ $guest->last_name }}, {{ $guest->first_name }}
                                    </div>
                                    <div class="small" style="color: #6b7280;">
                                        Registered {{ $guest->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td style="color: #262626;">
                                    {{ $guest->contact_number ?: '—' }}
                                </td>
                                <td class="small" style="color: #262626;">
                                    {{ $guest->address_line1 ?: '—' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge px-2.5 py-1 fw-semibold" style="border: 1px solid #c2a889; color: #262626; background-color: #f3ede4; border-radius: 0.375rem;">
                                        {{ $totalStays }} {{ Str::plural('stay', $totalStays) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($lastRoom)
                                        <span class="fw-bold" style="color: #1a1a1a;">{{ $lastRoom->room_number }}</span>
                                        <div class="small" style="color: #6b7280;">{{ $lastRoom->room_type }}</div>
                                    @else
                                        <span style="color: #6b7280;">—</span>
                                    @endif
                                </td>
                                <td class="text-center small" style="color: #262626;">
                                    @if($lastBooking)
                                        {{ $lastBooking->arrival_date->format('M d, Y') }}
                                        <div style="color: #6b7280;">→ {{ $lastBooking->departure_date ? $lastBooking->departure_date->format('M d, Y') : 'Open Stay' }}</div>
                                    @else
                                        <span style="color: #6b7280;">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($isCurrentlyIn)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle mb-1 fw-semibold" style="border-radius: 0.375rem;">
                                            <i class="fa-solid fa-circle-dot me-1"></i> In-House
                                        </span>
                                        @if($lastFolio)
                                            <div class="small" style="color: #4a3e35;">Folio: <strong>{{ $lastFolio->folio_number }}</strong></div>
                                        @endif
                                    @elseif($lastBooking?->status === 'CHECKED_OUT')
                                        <span class="badge px-2.5 py-1 fw-semibold" style="border: 1px solid #827567; color: #4a3e35; background-color: #eee9e0; border-radius: 0.375rem;">
                                            Checked Out
                                        </span>
                                    @else
                                        <span class="badge px-2.5 py-1 fw-semibold" style="border: 1px solid #827567; color: #4a3e35; background-color: #eee9e0; border-radius: 0.375rem;">No Stay</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-sm d-inline-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 36px; height: 36px; border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#guestModal{{ $guest->guest_id }}"
                                        title="View booking history"
                                    >
                                        <i class="fa-solid fa-eye fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-users-slash fa-2x mb-3 d-block"></i>
                                    @if($search)
                                        No guests found matching "<strong>{{ $search }}</strong>".
                                    @else
                                        No guests registered yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($guests->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $guests->links() }}
            </div>
        @endif
    </div>

</div>

<!-- High-fidelity printable guest list view -->
<div class="d-none d-print-block print-only-guest-list" style="font-family: Arial, sans-serif; color: #000000; background: #ffffff; padding: 20px; line-height: 1.4; width: 100%;">
    
    {{-- Header block --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 15px;">
        <tr>
            <!-- Left Logo -->
            <td style="width: 20%; vertical-align: top;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 75px; height: auto; object-fit: contain;">
            </td>
            <!-- Center Hotel Info -->
            <td style="width: 60%; text-align: center; vertical-align: top;">
                <h3 style="font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">EVSU Hotel</h3>
                <div style="font-size: 10px; margin-top: 3px; line-height: 1.3;">
                    Bonifacio Street, Ormoc City<br>
                    Tel. Nos. 255-3580 &bull; Fax No. 561-9620<br>
                    Email: hdfelipe@yahoo.com
                </div>
                <h4 style="font-size: 13px; font-weight: bold; margin: 12px 0 0 0; text-transform: uppercase; letter-spacing: 1px;">
                    @if($status === 'checked_in')
                        In-House Guest List
                    @elseif($status === 'checked_out')
                        Checked-Out Guest List
                    @else
                        Registered Guest List
                    @endif
                </h4>
            </td>
            <!-- Right Info -->
            <td style="width: 20%; text-align: right; font-size: 10px; font-weight: bold; line-height: 1.4; vertical-align: top; padding-top: 5px;">
                <div>DATE: {{ now()->format('m/d/Y') }}</div>
                <div>TIME: {{ now()->format('h:i A') }}</div>
            </td>
        </tr>
    </table>

    {{-- Metadata grid --}}
    <table style="width: 100%; font-size: 11px; margin-bottom: 20px; line-height: 1.5; border-collapse: collapse;">
        <tr>
            <td style="width: 15%; font-weight: bold; vertical-align: top;">REPORT TYPE</td>
            <td style="width: 35%; vertical-align: top;">: 
                @if($status === 'checked_in')
                    IN-HOUSE GUESTS ONLY
                @elseif($status === 'checked_out')
                    CHECKED-OUT GUESTS ONLY
                @else
                    ALL REGISTERED GUESTS
                @endif
            </td>
            <td style="width: 15%; font-weight: bold; vertical-align: top;">TOTAL RECORDS</td>
            <td style="width: 35%; vertical-align: top;">: {{ count($guests) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top;">GENERATED BY</td>
            <td style="vertical-align: top;">: {{ strtoupper(auth()->user()?->full_name ?? auth()->user()?->username ?? 'SYSTEM') }}</td>
            <td style="font-weight: bold; vertical-align: top;">SEARCH QUERY</td>
            <td style="vertical-align: top;">: {{ $search ? strtoupper($search) : 'NONE' }}</td>
        </tr>
    </table>

    {{-- Guests table --}}
    <table style="width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">
                <th style="padding: 5px 0; text-align: left; width: 5%;">#</th>
                <th style="padding: 5px 0; text-align: left; width: 25%;">GUEST NAME</th>
                <th style="padding: 5px 0; text-align: left; width: 15%;">MOBILE NO.</th>
                <th style="padding: 5px 0; text-align: left; width: 20%;">ADDRESS</th>
                <th style="padding: 5px 0; text-align: center; width: 10%;">STATUS</th>
                <th style="padding: 5px 0; text-align: center; width: 10%;">ROOM</th>
                <th style="padding: 5px 0; text-align: center; width: 15%;">FOLIO NO.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guests as $index => $guest)
                @php
                    $lastFolio      = $guest->folios->sortByDesc('folio_id')->first();
                    $lastBooking    = $lastFolio?->bookings->sortByDesc('booking_id')->first();
                    $lastRoom       = $lastBooking?->room;
                    $isCurrentlyIn  = $lastBooking && $lastBooking->status === 'CHECKED_IN';
                @endphp
                <tr>
                    <td style="padding: 6px 0; vertical-align: top;">{{ $index + 1 }}</td>
                    <td style="padding: 6px 0; vertical-align: top; font-weight: bold;">
                        {{ strtoupper($guest->last_name) }}, {{ strtoupper($guest->first_name) }}
                    </td>
                    <td style="padding: 6px 0; vertical-align: top;">{{ $guest->contact_number ?: '—' }}</td>
                    <td style="padding: 6px 0; vertical-align: top;">{{ strtoupper($guest->address_line1 ?: '') }}</td>
                    <td style="padding: 6px 0; vertical-align: top; text-align: center;">
                        @if($isCurrentlyIn)
                            IN-HOUSE
                        @elseif($lastBooking?->status === 'CHECKED_OUT')
                            CHECKED OUT
                        @else
                            NO STAY
                        @endif
                    </td>
                    <td style="padding: 6px 0; vertical-align: top; text-align: center;">
                        {{ $lastRoom?->room_number ?? '—' }}
                    </td>
                    <td style="padding: 6px 0; vertical-align: top; text-align: center;">
                        @if($isCurrentlyIn)
                            {{ $lastFolio->folio_number }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 20px 0; text-align: center; font-style: italic;">No guests found.</td>
                </tr>
            @endforelse
            <tr style="border-top: 1px solid #000; border-bottom: 3px double #000;">
                <td colspan="7" style="padding: 4px 0;"></td>
            </tr>
        </tbody>
    </table>

    {{-- Nothing follows centered --}}
    <div style="text-align: center; font-size: 10px; font-weight: bold; font-style: italic; margin-top: 15px;">
        *** Nothing follows ***
    </div>
</div>

{{-- Guest Detail Modals --}}
@foreach($guests as $guest)
    @php
        $sortedFolios = $guest->folios->sortByDesc('folio_id');
    @endphp
    <div class="modal fade" id="guestModal{{ $guest->guest_id }}" tabindex="-1" aria-labelledby="guestModalLabel{{ $guest->guest_id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">
                        {{ $guest->last_name }}, {{ $guest->first_name }}
                    </h5>

                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <h6 class="fw-semibold mb-3">Guest Information</h6>

                    <div class="row mb-4">

                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Mobile Number</small>
                            <div>{{ $guest->contact_number ?: '—' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Address</small>
                            <div>{{ $guest->address_line1 ?: '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">First Registered</small>
                            <div>{{ $guest->created_at->format('F d, Y') }}</div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Total Stays</small>
                            <div>{{ $guest->folios->count() }}</div>
                        </div>

                    </div>

                    <hr>

                    <h6 class="fw-semibold mb-3">Booking History</h6>

                    <div class="table-responsive">

                        <table class="table table-bordered table-sm align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>Folio</th>
                                    <th>Room</th>
                                    <th>Arrival</th>
                                    <th>Departure</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($sortedFolios as $folio)

                                    @foreach($folio->bookings->sortByDesc('booking_id') as $booking)

                                        <tr>

                                            <td>{{ $folio->folio_number }}</td>

                                            <td>
                                                {{ $booking->room?->room_number ?? '—' }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $booking->room?->room_type }}
                                                </small>
                                            </td>

                                            <td>{{ $booking->arrival_date->format('M d, Y') }}</td>

                                            <td>{{ $booking->departure_date ? $booking->departure_date->format('M d, Y') : 'Open Stay' }}</td>

                                            <td>{{ $folio->payment_method ?? '—' }}</td>

                                            <td>

                                                @if($booking->status=='CHECKED_IN')
                                                    <span class="badge bg-success">In-House</span>

                                                @elseif($booking->status=='CHECKED_OUT')
                                                    <span class="badge bg-secondary">Checked Out</span>

                                                @elseif($booking->status=='RESERVED')
                                                    <span class="badge bg-warning text-dark">Reserved</span>

                                                @else
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                @empty

                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No booking history found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
@endforeach

@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .main-content > .card, .container-fluid, .modal-backdrop, .modal, .d-print-none {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .print-only-guest-list {
            display: block !important;
            width: 100% !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('search');
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