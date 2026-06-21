@extends('layouts.app')

@section('title', 'Check In - Don Felipe Hotel')
@section('pageTitle', 'Existing Guest Check In')
@section('pageSubtitle', 'Check in existing guests and assign available rooms.')

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

<div class="container-fluid">

    <form method="POST" action="{{ route('frontdesk.checkin.store') }}" id="checkInForm">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Registration Information</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="folio_number">Folio Number</label>
                        <input type="text" class="form-control bg-light" id="folio_number" name="folio_number" value="{{ old('folio_number', $suggestedFolioNumber) }}" maxlength="20" readonly>
                        <div class="form-text">Auto-generated reference number.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="payment_method">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                            <option value="Cash" @selected(old('payment_method', 'Cash') === 'Cash')>
                                💵 Cash
                            </option>
                            <option value="Credit Card" @selected(old('payment_method') === 'Credit Card')>
                                💳 Credit Card
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Guest Selection</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label" for="guest_search">Search Existing Guest</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control" id="guest_search" placeholder="Type guest's first or last name to search...">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label" for="guest_id">Select Guest <span class="text-danger">*</span></label>
                        <select class="form-select @error('guest_id') is-invalid @enderror" id="guest_id" name="guest_id" required>
                            <option value="">Choose a guest...</option>
                            @foreach($guests as $guest)
                                <option
                                    value="{{ $guest->guest_id }}"
                                    data-contact="{{ $guest->contact_number ?? 'N/A' }}"
                                    data-address="{{ trim(($guest->address_line1 ?? '') . ' ' . ($guest->address_line2 ?? '')) ?: 'N/A' }}"
                                    @selected(old('guest_id') == $guest->guest_id)
                                >
                                    {{ $guest->last_name }}, {{ $guest->first_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('guest_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Guest Details Card (Read-only confirmation) -->
                <div class="mt-4 p-3 bg-light border rounded d-none" id="guest_info_card">
                    <h6 class="fw-bold mb-3 text-secondary text-uppercase small">Selected Guest Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <span class="text-muted d-block small">Full Name</span>
                            <strong id="display_guest_name">-</strong>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="text-muted d-block small">Mobile Number</span>
                            <strong id="display_guest_contact">-</strong>
                        </div>
                        <div class="col-md-12">
                            <span class="text-muted d-block small">Address</span>
                            <strong id="display_guest_address">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Stay Information</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="arrival_date">Arrival Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('arrival_date') is-invalid @enderror" id="arrival_date" name="arrival_date" value="{{ old('arrival_date', $defaults['arrival_date']) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="arrival_time">Arrival Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('arrival_time') is-invalid @enderror" id="arrival_time" name="arrival_time" value="{{ old('arrival_time', $defaults['arrival_time']) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="departure_date">Departure Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('departure_date') is-invalid @enderror" id="departure_date" name="departure_date" value="{{ old('departure_date') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="departure_time">Departure Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('departure_time') is-invalid @enderror" id="departure_time" name="departure_time" value="{{ old('departure_time', $defaults['departure_time']) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="num_pax">No. of Pax</label>
                        <input type="number" class="form-control @error('num_pax') is-invalid @enderror" id="num_pax" name="num_pax" value="{{ old('num_pax', $defaults['num_pax']) }}" min="1" max="20">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="has_joiner">Joiner</label>
                        <select class="form-select @error('has_joiner') is-invalid @enderror" id="has_joiner" name="has_joiner">
                            <option value="0" @selected(old('has_joiner', '0') == '0')>No</option>
                            <option value="1" @selected(old('has_joiner') == '1')>Yes</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="market_segment">Market Segment</label>
                        <select class="form-select @error('market_segment') is-invalid @enderror" id="market_segment" name="market_segment">
                            @foreach(['Walk-in', 'NONE', 'Corporate', 'Government', 'Travel Agency'] as $segment)
                                <option value="{{ $segment }}" @selected(old('market_segment', $defaults['market_segment']) === $segment)>{{ $segment }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Room Information</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="room_type_filter">Filter by Room Type</label>
                        <select class="form-select" id="room_type_filter">
                            <option value="">All Types</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label" for="room_id">Room <span class="text-danger">*</span></label>
                        <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                            <option value="">Select available room</option>
                            @foreach($assignableRooms as $room)
                                <option
                                    value="{{ $room->room_id }}"
                                    data-room-type="{{ $room->room_type }}"
                                    data-base-rate="{{ $room->base_rate }}"
                                    @selected(old('room_id') == $room->room_id)
                                >
                                    {{ $room->room_number }} — {{ $room->room_type }} (₱{{ number_format($room->base_rate, 2) }}/night)
                                </option>
                            @endforeach
                        </select>
                        @if($assignableRooms->isEmpty())
                            <div class="form-text text-danger">No available rooms right now. Check the dashboard for rooms under cleaning or maintenance.</div>
                        @else
                            <div class="form-text">Only rooms that are available and not reserved or occupied are listed.</div>
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Room Base Rate</label>
                        <input type="text" class="form-control bg-light" id="room_base_rate_display" value="Select a room to view rate" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Additional Information</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label" for="special_arrangements">Special Arrangements</label>
                        <textarea class="form-control @error('special_arrangements') is-invalid @enderror" id="special_arrangements" name="special_arrangements" rows="2" placeholder="e.g. extra pillows, early check-in request...">{{ old('special_arrangements') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <div class="alert alert-light border mb-0">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i>
                            Saving this check-in will immediately check the guest in and mark the selected room as <strong>OCCUPIED</strong> on the dashboard.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('frontdesk.checkin') }}" class="btn btn-secondary">Clear</a>
                    <button type="submit" class="btn btn-primary" @disabled($assignableRooms->isEmpty())>
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Check In
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const guestSearch = document.getElementById('guest_search');
        const guestSelect = document.getElementById('guest_id');
        const guestInfoCard = document.getElementById('guest_info_card');
        const displayGuestName = document.getElementById('display_guest_name');
        const displayGuestContact = document.getElementById('display_guest_contact');
        const displayGuestAddress = document.getElementById('display_guest_address');

        const roomTypeFilter = document.getElementById('room_type_filter');
        const roomSelect = document.getElementById('room_id');
        const rateDisplay = document.getElementById('room_base_rate_display');
        const arrivalDate = document.getElementById('arrival_date');
        const departureDate = document.getElementById('departure_date');

        // Search Guest Filtering
        if (guestSearch && guestSelect) {
            const allGuestOptions = Array.from(guestSelect.querySelectorAll('option[value]'));

            guestSearch.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                allGuestOptions.forEach(option => {
                    if (option.value === "") {
                        return;
                    }
                    const text = option.textContent.toLowerCase();
                    const matches = text.includes(query);
                    option.hidden = !matches;
                    option.disabled = !matches;
                });

                // If currently selected option is now hidden, reset selection
                const current = guestSelect.options[guestSelect.selectedIndex];
                if (current && current.disabled) {
                    guestSelect.value = '';
                    guestInfoCard.classList.add('d-none');
                }
            });
        }

        // Selected Guest details card updater
        if (guestSelect) {
            function updateGuestDetails() {
                const selected = guestSelect.options[guestSelect.selectedIndex];
                if (!selected || !selected.value) {
                    guestInfoCard.classList.add('d-none');
                    return;
                }

                displayGuestName.textContent = selected.textContent.trim();
                displayGuestContact.textContent = selected.getAttribute('data-contact') || 'N/A';
                displayGuestAddress.textContent = selected.getAttribute('data-address') || 'N/A';
                guestInfoCard.classList.remove('d-none');
            }

            guestSelect.addEventListener('change', updateGuestDetails);
            updateGuestDetails(); // Run on initial load to handle old inputs
        }

        // Room rate and filtering
        function updateRateDisplay() {
            const selected = roomSelect.options[roomSelect.selectedIndex];
            if (!selected || !selected.value) {
                rateDisplay.value = 'Select a room to view rate';
                return;
            }

            const rate = parseFloat(selected.getAttribute('data-base-rate') || '0');
            rateDisplay.value = '₱' + rate.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / night';
        }

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
                    updateRateDisplay();
                }
            });

            roomSelect.addEventListener('change', updateRateDisplay);
            updateRateDisplay();
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
    });
</script>
@endpush
