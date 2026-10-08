@extends('layouts.app')

@section('title', 'Facility Reservations - Don Felipe Hotel')
@section('pageTitle', 'Facility Reservations')
@section('pageSubtitle', 'Review and manage public facility booking requests.')

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

    @if(session('error'))
        <div class="toast align-items-center text-white bg-danger border-0 shadow show" role="alert" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <form method="GET"
            action="{{ route('frontdesk.facility-reservations.index') }}"
            class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"
            id="facilityReservationFilterForm">

            <div>
                <h5 class="fw-bold mb-1" style="color: #504538; font-family: 'Franklin Gothic Medium', sans-serif;">Facility Reservation Entry</h5>
                <small class="text-muted">
                    Review and manage public facility booking requests.
                </small>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

                <!-- Search -->
                <div style="width: 320px; max-width: 100%;">
                    <div class="input-group shadow-sm" style="border: 1px solid #c2a889; border-radius: 0.5rem; overflow: hidden; height: 45px; background-color: #ffffff;">
                        <span class="input-group-text bg-white border-0 px-3">
                            <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control border-0 shadow-none py-2"
                            style="font-size: 1rem;"
                            id="filterSearch"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Booker, Ref #, or Event..."
                            autocomplete="off">
                    </div>
                </div>

                <!-- FILTER DROPDOWN -->
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                            type="button"
                            data-bs-toggle="dropdown"
                            style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; background-color: #f3ede4; color: #3d332a; font-size: 1rem;">
                        <i class="fa-solid fa-filter" style="color: #334c42;"></i>
                        <span class="fw-semibold">Filter</span>
                        @if($status !== 'all')
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                         onclick="event.stopPropagation()"
                         style="min-width: 280px; border-radius: 0.75rem;">

                        <!-- Status -->
                        <label class="form-label small mb-1 fw-semibold text-muted">Status</label>
                        <select
                            class="form-select mb-3 shadow-none"
                            id="filterStatus"
                            name="status"
                            style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                            <option value="all" @selected($status === 'all')>All</option>
                            <option value="pending" @selected($status === 'pending')>Pending</option>
                            <option value="approved" @selected($status === 'approved')>Approved</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="completed" @selected($status === 'completed')>Completed</option>
                            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                            <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                        </select>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none; border-radius: 0.375rem;">Apply</button>
                            <a href="{{ route('frontdesk.facility-reservations.index') }}" class="btn w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; background-color: #eee9e0; color: #4a3e35; border-radius: 0.375rem;">Reset</a>
                        </div>
                    </div>
                </div>

                <!-- NEW FACILITY RESERVATION BUTTON -->
                <a href="{{ route('frontdesk.facility-reservations.create') }}" class="btn text-white px-3.5 d-flex align-items-center gap-2 fw-semibold shadow-sm" style="height: 45px; background-color: #334c42; border: none; border-radius: 0.5rem; font-size: 1rem;">
                    <i class="fa-solid fa-plus"></i>
                    Book Facility
                </a>
            </div>

        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                    <tr class="small fw-bold">
                        <th class="ps-3" style="color: #2c241d;">REF #</th>
                        <th style="color: #2c241d;">BOOKER & EVENT</th>
                        <th style="color: #2c241d;">FACILITY</th>
                        <th style="color: #2c241d;">SCHEDULE</th>
                        <th style="color: #2c241d;">AMOUNT</th>
                        <th style="color: #2c241d;">STATUS</th>
                        <th class="text-center pe-3" style="color: #2c241d;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="ps-3 fw-bold" style="color: #1a1a1a;">{{ $res->reference_number }}</td>
                            <td>
                                <div class="fw-bold" style="color: #1a1a1a;">{{ $res->booker_name }}</div>
                                @if($res->event_name)
                                    <div class="text-primary small fw-semibold">
                                        <i class="fa-solid fa-tag me-1"></i>{{ $res->event_name }}
                                    </div>
                                @endif
                                <div class="text-muted small">{{ $res->booker_contact }}</div>
                            </td>
                            <td>
                                @if($res->isConsolidated())
                                    <div class="fw-bold" style="color: #1a1a1a;">
                                        {{ $res->facilitySet->name ?? $res->facility_name }}
                                        <span class="badge bg-info text-dark ms-1" style="font-size: 0.65rem;">Set</span>
                                    </div>
                                    <div class="text-muted small" title="{{ $res->all_facilities->pluck('name')->join(', ') }}">
                                        {{ $res->all_facilities->count() }} spaces &bull; {{ $res->duration_label }}
                                    </div>
                                @else
                                    <div class="fw-bold" style="color: #1a1a1a;">{{ $res->facility->name ?? 'Deleted Facility' }}</div>
                                    <div class="text-muted small">{{ $res->duration_label }}</div>
                                @endif
                            </td>
                            <td style="color: #262626;">
                                <div class="fw-semibold">
                                    {{ $res->reservation_date->format('M d, Y') }}
                                    @if($res->end_date && $res->end_date->gt($res->reservation_date))
                                        &ndash; {{ $res->end_date->format('M d, Y') }}
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($res->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #334c42;">
                                    ₱{{ number_format($res->effective_amount, 2) }}
                                </div>
                                @if($res->agreed_rate !== null)
                                    <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.65rem;">
                                        Agreed: ₱{{ number_format($res->agreed_rate, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @switch($res->status)
                                    @case('pending')
                                        <span class="badge-status badge-status-cleaning">Pending</span>
                                        @break
                                    @case('approved')
                                        <span class="badge-status badge-status-reserved">Approved</span>
                                        @break
                                    @case('active')
                                        <span class="badge-status badge-status-active">Active</span>
                                        @break
                                    @case('completed')
                                        <span class="badge-status badge-status-closed">Completed</span>
                                        @break
                                    @case('cancelled')
                                    @case('rejected')
                                        <span class="badge-status badge-status-maintenance">{{ ucfirst($res->status) }}</span>
                                        @break
                                    @default
                                        <span class="badge-status badge-status-maintenance">{{ ucfirst($res->status) }}</span>
                                @endswitch
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1.5">
                                    @if($res->status === 'approved')
                                        <form action="{{ route('frontdesk.facility-reservations.check-in', $res) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Start this booking now and mark facility as Active/In-use?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; border: 1px solid #10b981; color: #047857; background-color: #d1fae5; border-radius: 0.375rem;" title="Move In / Check-In">
                                                <i class="fa-solid fa-play fs-6"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('frontdesk.facility-reservations.show', $res) }}"
                                        class="btn btn-sm d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 36px; height: 36px; border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;"
                                        title="Manage reservation">
                                        <i class="fa-solid fa-arrow-right fs-6"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted d-flex flex-column align-items-center justify-content-center">
                                    <i class="fa-regular fa-calendar-xmark fs-1 mb-3" style="color: #c2a889;"></i>
                                    <p class="mb-0 fw-semibold">No facility reservations found.</p>
                                    <p class="small text-muted mb-3">Adjust your filters or book a new facility.</p>
                                    <a href="{{ route('frontdesk.facility-reservations.create') }}" class="btn btn-sm text-white px-3 shadow-sm" style="background-color: #334c42; border-radius: 0.375rem;">Book Facility</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-color: #ede4d8 !important;">
                <span class="text-muted small">
                    Showing <strong>{{ $reservations->firstItem() ?? 0 }}</strong>&mdash;<strong>{{ $reservations->lastItem() ?? 0 }}</strong> of <strong>{{ $reservations->total() }}</strong> reservation(s)
                </span>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="badge px-3 py-2 fw-semibold shadow-sm" style="border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;">
                        Page {{ $reservations->currentPage() }} of {{ $reservations->lastPage() }}
                    </span>
                    <div>
                        {{ $reservations->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
