@extends('layouts.app')

@section('title', 'Guest List - Don Felipe Hotel')
@section('pageTitle', 'Guest List')
@section('pageSubtitle', 'Search and view all registered hotel guests.')

@section('content')

<div class="container-fluid">

    {{-- Search Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('frontdesk.guest-list') }}" id="searchForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="search">Search Guest</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search by first name, last name..."
                                autocomplete="off"
                            >
                            @if($search)
                                <a href="{{ route('frontdesk.guest-list') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-search me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="window.print()">
                            <i class="fa-solid fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Summary --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            @if($search)
                Showing <strong>{{ $guests->total() }}</strong> result(s) for
                "<strong>{{ $search }}</strong>"
            @else
                Showing all <strong>{{ $guests->total() }}</strong> registered guest(s)
            @endif
        </p>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
            Page {{ $guests->currentPage() }} of {{ $guests->lastPage() }}
        </span>
    </div>

    {{-- Guest Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="guestTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="py-3">Guest Name</th>
                            <th class="py-3">Mobile Number</th>
                            <th class="py-3">Address</th>
                            <th class="py-3 text-center">Total Stays</th>
                            <th class="py-3 text-center">Last Room</th>
                            <th class="py-3 text-center">Last Stay</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Action</th>
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
                            <tr>
                                <td class="px-4 text-muted small">
                                    {{ ($guests->currentPage() - 1) * $guests->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $guest->last_name }}, {{ $guest->first_name }}
                                    </div>
                                    <div class="text-muted small">
                                        Registered {{ $guest->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    {{ $guest->contact_number ?: '—' }}
                                </td>
                                <td class="text-muted small">
                                    {{ $guest->address_line1 ?: '—' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2">
                                        {{ $totalStays }} {{ Str::plural('stay', $totalStays) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($lastRoom)
                                        <span class="fw-semibold">{{ $lastRoom->room_number }}</span>
                                        <div class="text-muted small">{{ $lastRoom->room_type }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    @if($lastBooking)
                                        {{ $lastBooking->arrival_date->format('M d, Y') }}
                                        <div class="text-muted">→ {{ $lastBooking->departure_date->format('M d, Y') }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($isCurrentlyIn)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="fa-solid fa-circle-dot me-1"></i> In-House
                                        </span>
                                    @elseif($lastBooking?->status === 'CHECKED_OUT')
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            Checked Out
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">No Stay</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#guestModal{{ $guest->guest_id }}"
                                        title="View booking history"
                                    >
                                        <i class="fa-solid fa-eye"></i>
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

{{-- Guest Detail Modals --}}
@foreach($guests as $guest)
    @php
        $sortedFolios = $guest->folios->sortByDesc('folio_id');
    @endphp
    <div class="modal fade" id="guestModal{{ $guest->guest_id }}" tabindex="-1" aria-labelledby="guestModalLabel{{ $guest->guest_id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="guestModalLabel{{ $guest->guest_id }}">
                        <i class="fa-solid fa-user me-2 text-primary"></i>
                        {{ $guest->last_name }}, {{ $guest->first_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Guest Info --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small fw-semibold">MOBILE NUMBER</p>
                            <p class="mb-0">{{ $guest->contact_number ?: '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small fw-semibold">ADDRESS</p>
                            <p class="mb-0">{{ $guest->address_line1 ?: '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small fw-semibold">FIRST REGISTERED</p>
                            <p class="mb-0">{{ $guest->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small fw-semibold">TOTAL STAYS</p>
                            <p class="mb-0">
                                <span class="badge bg-primary">{{ $guest->folios->count() }}</span>
                            </p>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold mb-3">Booking History</h6>

                    @forelse($sortedFolios as $folio)
                        @foreach($folio->bookings->sortByDesc('booking_id') as $booking)
                            <div class="card border mb-3">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <p class="mb-1 text-muted small fw-semibold">FOLIO</p>
                                            <p class="mb-0 fw-semibold">{{ $folio->folio_number }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1 text-muted small fw-semibold">ROOM</p>
                                            <p class="mb-0 fw-semibold">
                                                {{ $booking->room?->room_number ?? '—' }}
                                                <span class="text-muted small fw-normal">{{ $booking->room?->room_type }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1 text-muted small fw-semibold">CHECK-IN → CHECK-OUT</p>
                                            <p class="mb-0 small">
                                                {{ $booking->arrival_date->format('M d, Y') }}
                                                → {{ $booking->departure_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="col-md-3 text-md-end">
                                            @if($booking->status === 'CHECKED_IN')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">In-House</span>
                                            @elseif($booking->status === 'CHECKED_OUT')
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Checked Out</span>
                                            @elseif($booking->status === 'RESERVED')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Reserved</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelled</span>
                                            @endif
                                            @if($folio->payment_method)
                                                <div class="text-muted small mt-1">
                                                    {{ $folio->payment_method === 'Cash' ? '💵' : '💳' }} {{ $folio->payment_method }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <p class="text-muted">No bookings on record.</p>
                    @endforelse

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('styles')
<style>
    @media print {
        .btn, form, nav, aside, .modal, [data-bs-toggle], .card-footer {
            display: none !important;
        }
        #guestTable thead {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endpush