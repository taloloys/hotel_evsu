@extends('layouts.app')

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

        <form method="GET" action="{{ route('frontdesk.reservation') }}" class="card bg-light border-0 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold" for="filterDateFrom">Date From</label>
                        <input type="date" class="form-control" id="filterDateFrom" name="date_from" value="{{ $filters['date_from'] }}">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold" for="filterDateTo">Date To</label>
                        <input type="date" class="form-control" id="filterDateTo" name="date_to" value="{{ $filters['date_to'] }}">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold" for="filterStatus">Booking Status</label>
                        <select class="form-select" id="filterStatus" name="status">
                            <option value="all" @selected($filters['status'] === 'all')>All Statuses</option>
                            <option value="RESERVED" @selected($filters['status'] === 'RESERVED')>Reserved</option>
                            <option value="CHECKED_IN" @selected($filters['status'] === 'CHECKED_IN')>Checked In</option>
                            <option value="CHECKED_OUT" @selected($filters['status'] === 'CHECKED_OUT')>Checked Out</option>
                            <option value="CANCELLED" @selected($filters['status'] === 'CANCELLED')>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-semibold" for="filterSearch">Search Guest / Folio</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                            <input type="text" class="form-control" id="filterSearch" name="search" value="{{ $filters['search'] }}" placeholder="Guest name or folio no.">
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('frontdesk.reservation') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
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
                                {{ $reservation->departure_date->format('m/d/Y') }}
                                @if($reservation->departure_time)
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($reservation->departure_time)->format('g:i A') }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($reservation->status)
                                    @case('RESERVED')
                                        <span class="badge bg-warning text-dark">Reserved</span>
                                        @break
                                    @case('CHECKED_IN')
                                        <span class="badge bg-info">Checked In</span>
                                        @break
                                    @case('CHECKED_OUT')
                                        <span class="badge bg-secondary">Checked Out</span>
                                        @break
                                    @case('CANCELLED')
                                        <span class="badge bg-danger">Cancelled</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $reservation->status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary view-reservation-btn"
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
                                        data-departure="{{ $reservation->departure_date->format('M d, Y') }}"
                                        data-departure-time="{{ $reservation->departure_time ? \Carbon\Carbon::parse($reservation->departure_time)->format('g:i A') : '—' }}"
                                        data-status="{{ $reservation->status }}"
                                        data-pax="{{ $reservation->folio->num_pax }}"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    @if(in_array($reservation->status, ['RESERVED', 'CHECKED_IN']))
                                        <form method="POST" action="{{ route('frontdesk.reservation.cancel', $reservation) }}" class="d-inline" onsubmit="return confirm('Cancel this reservation?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger" title="Cancel reservation">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No reservations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control" id="guest_search" placeholder="Type guest name to search..." autocomplete="off">
                            </div>
                            <div id="guest_search_results" class="dropdown-menu w-100 shadow-sm mt-1" style="display: none; max-height: 250px; overflow-y: auto; z-index: 1050;"></div>
                            <div class="form-text">If there is no existing guest, simply type the guest details directly below.</div>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" maxlength="50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" maxlength="50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="contact_number">Contact Number</label>
                            <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="address_line1">Address Line 1</label>
                            <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" maxlength="100">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="address_line2">Address Line 2</label>
                            <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" name="address_line2" value="{{ old('address_line2') }}" maxlength="100">
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-file-invoice me-2"></i>Folio (folios table)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="folio_number">Folio Number</label>
                            <input type="text" class="form-control @error('folio_number') is-invalid @enderror" id="folio_number" name="folio_number" value="{{ old('folio_number', $suggestedFolioNumber) }}" maxlength="20" placeholder="Auto-generated if blank">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="registration_number">Registration Number</label>
                            <input type="text" class="form-control @error('registration_number') is-invalid @enderror" id="registration_number" name="registration_number" value="{{ old('registration_number') }}" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="account_number">Account Number</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number') }}" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="num_pax">Number of Guests (num_pax)</label>
                            <input type="number" class="form-control @error('num_pax') is-invalid @enderror" id="num_pax" name="num_pax" value="{{ old('num_pax', 1) }}" min="1" max="20">
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-bed me-2"></i>Booking (bookings table)</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="room_type_filter">Filter by Room Type</label>
                            <select class="form-select" id="room_type_filter">
                                <option value="">All Types</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="room_id">Room <span class="text-danger">*</span></label>
                            <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                <option value="">Select available room</option>
                                @foreach($assignableRooms as $room)
                                    <option
                                        value="{{ $room->room_id }}"
                                        data-room-type="{{ $room->room_type }}"
                                        @selected(old('room_id') == $room->room_id)
                                    >
                                        {{ $room->room_number }} — {{ $room->room_type }} (₱{{ number_format($room->base_rate, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @if($assignableRooms->isEmpty())
                                <div class="form-text text-danger">No available rooms right now.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="arrival_date">Arrival Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('arrival_date') is-invalid @enderror" id="arrival_date" name="arrival_date" value="{{ old('arrival_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="arrival_time">Arrival Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('arrival_time') is-invalid @enderror" id="arrival_time" name="arrival_time" value="{{ old('arrival_time', '12:00') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="departure_date">Departure Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('departure_date') is-invalid @enderror" id="departure_date" name="departure_date" value="{{ old('departure_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="departure_time">Departure Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('departure_time') is-invalid @enderror" id="departure_time" name="departure_time" value="{{ old('departure_time', '12:00') }}" required>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-light border mb-0">
                                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                                New reservations are saved with booking status <strong>RESERVED</strong> and will appear on the dashboard when arrival is today.
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Reservation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Guest</dt>
                    <dd class="col-sm-8" id="viewGuest"></dd>
                    <dt class="col-sm-4">Contact</dt>
                    <dd class="col-sm-8" id="viewContact"></dd>
                    <dt class="col-sm-4">Folio No.</dt>
                    <dd class="col-sm-8" id="viewFolio"></dd>
                    <dt class="col-sm-4">Registration</dt>
                    <dd class="col-sm-8" id="viewRegistration"></dd>
                    <dt class="col-sm-4">Room</dt>
                    <dd class="col-sm-8" id="viewRoom"></dd>
                    <dt class="col-sm-4">Arrival</dt>
                    <dd class="col-sm-8" id="viewArrival"></dd>
                    <dt class="col-sm-4">Departure</dt>
                    <dd class="col-sm-8" id="viewDeparture"></dd>
                    <dt class="col-sm-4">Guests (Pax)</dt>
                    <dd class="col-sm-8" id="viewPax"></dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8" id="viewStatus"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() || old('first_name'))
            const modal = new bootstrap.Modal(document.getElementById('newReservationModal'));
            modal.show();
        @endif

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

            document.addEventListener('click', function(e) {
                if (!guestSearchInput.contains(e.target) && !guestSearchResults.contains(e.target)) {
                    guestSearchResults.style.display = 'none';
                }
            });
        }

        const roomTypeFilter = document.getElementById('room_type_filter');
        const roomSelect = document.getElementById('room_id');

        if (roomTypeFilter && roomSelect) {
            const allOptions = Array.from(roomSelect.querySelectorAll('option[data-room-type]'));

            roomTypeFilter.addEventListener('change', function() {
                const selectedType = this.value;

                allOptions.forEach(option => {
                    const matches = !selectedType || option.getAttribute('data-room-type') === selectedType;
                    option.hidden = !matches;
                    option.disabled = !matches;
                });

                const current = roomSelect.options[roomSelect.selectedIndex];
                if (current && current.disabled) {
                    roomSelect.value = '';
                }
            });
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
    });
</script>
@endpush
