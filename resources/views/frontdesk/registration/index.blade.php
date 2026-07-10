@extends('layouts.app')

@section('title', 'Registration - Don Felipe Hotel')
@section('pageTitle', 'Guest Registration')
@section('pageSubtitle', 'Register walk-in guests and assign available rooms.')

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

    <form method="POST" action="{{ route('frontdesk.registration.store') }}" id="registrationForm">
        @csrf

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-file-invoice text-primary me-2"></i>
                    Registration Information
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <div class="row g-4">

                    <!-- Folio Number -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="folio_number">
                            Folio Number
                        </label>

                        <input
                            type="text"
                            class="form-control shadow-none bg-light"
                            id="folio_number"
                            name="folio_number"
                            value="{{ old('folio_number', $suggestedFolioNumber) }}"
                            maxlength="20"
                            readonly
                            style="height:46px; border:1px solid #ced4da;">

                        <small class="text-muted">
                            Auto-generated reference number.
                        </small>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="payment_method">
                            Payment Method
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            class="form-select shadow-none @error('payment_method') is-invalid @enderror"
                            id="payment_method"
                            name="payment_method"
                            style="height:46px; border:1px solid #ced4da;">

                            <option value="Cash"
                                @selected(old('payment_method', 'Cash') === 'Cash')>
                                Cash
                            </option>

                            <option value="Credit Card"
                                @selected(old('payment_method') === 'Credit Card')>
                                Credit Card
                            </option>

                        </select>

                        @error('payment_method')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-user text-primary me-2"></i>
                    Guest Information
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-12 position-relative">
                        <label class="form-label fw-semibold text-primary" for="guest_search">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Search Existing Guest (Optional)
                        </label>
                        <input type="text" class="form-control shadow-none border-primary" id="guest_search" placeholder="Search by name to autofill..." style="height:46px; background-color: #f8fbff;">
                        <ul class="list-group position-absolute w-100 mt-1 shadow d-none" id="guest_suggestions" style="z-index: 1050; max-height: 250px; overflow-y: auto;">
                        </ul>
                    </div>
                </div>
                <hr class="mb-4">

                <div class="row g-4">

                    <!-- First Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="first_name">
                            First Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control shadow-none @error('first_name') is-invalid @enderror"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            maxlength="50"
                            placeholder="Enter first name"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                        @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="last_name">
                            Last Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control shadow-none @error('last_name') is-invalid @enderror"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            maxlength="50"
                            placeholder="Enter last name"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                        @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="address_line1">
                            Address
                        </label>

                        <input
                            type="text"
                            class="form-control shadow-none @error('address_line1') is-invalid @enderror"
                            id="address_line1"
                            name="address_line1"
                            value="{{ old('address_line1') }}"
                            maxlength="100"
                            placeholder="Street, Barangay, City"
                            style="height:46px; border:1px solid #ced4da;">

                        @error('address_line1')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="contact_number">
                            Mobile Number
                        </label>

                        <input
                            type="text"
                            class="form-control shadow-none @error('contact_number') is-invalid @enderror"
                            id="contact_number"
                            name="contact_number"
                            value="{{ old('contact_number') }}"
                            maxlength="20"
                            placeholder="09XXXXXXXXX"
                            style="height:46px; border:1px solid #ced4da;">

                        @error('contact_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

            </div>

        </div>

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-bed text-primary me-2"></i>
                    Stay Information
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <div class="row g-4">

                    <!-- Arrival Date -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="arrival_date">
                            Arrival Date
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            class="form-control shadow-none @error('arrival_date') is-invalid @enderror"
                            id="arrival_date"
                            name="arrival_date"
                            value="{{ old('arrival_date', $defaults['arrival_date']) }}"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                        @error('arrival_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Arrival Time -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="arrival_time">
                            Arrival Time
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="time"
                            class="form-control shadow-none @error('arrival_time') is-invalid @enderror"
                            id="arrival_time"
                            name="arrival_time"
                            value="{{ old('arrival_time', $defaults['arrival_time']) }}"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                        @error('arrival_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Departure Date -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="departure_date">
                            Departure Date
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            class="form-control shadow-none @error('departure_date') is-invalid @enderror"
                            id="departure_date"
                            name="departure_date"
                            value="{{ old('departure_date') }}"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                        @error('departure_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Departure Time -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="departure_time">
                            Departure Time
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="time"
                            class="form-control shadow-none @error('departure_time') is-invalid @enderror"
                            id="departure_time"
                            name="departure_time"
                            value="{{ old('departure_time', $defaults['departure_time']) }}"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                        @error('departure_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Number of Guests -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="num_pax">
                            Number of Guests
                        </label>

                        <input
                            type="number"
                            class="form-control shadow-none @error('num_pax') is-invalid @enderror"
                            id="num_pax"
                            name="num_pax"
                            value="{{ old('num_pax', $defaults['num_pax']) }}"
                            min="1"
                            max="20"
                            style="height:46px; border:1px solid #ced4da;">

                        @error('num_pax')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Joiner -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="has_joiner">
                            Joiner
                        </label>

                        <select
                            class="form-select shadow-none @error('has_joiner') is-invalid @enderror"
                            id="has_joiner"
                            name="has_joiner"
                            style="height:46px; border:1px solid #ced4da;">

                            <option value="0" @selected(old('has_joiner', '0') == '0')>
                                No
                            </option>

                            <option value="1" @selected(old('has_joiner') == '1')>
                                Yes
                            </option>

                        </select>

                        @error('has_joiner')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Market Segment -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="market_segment">
                            Market Segment
                        </label>

                        <select
                            class="form-select shadow-none @error('market_segment') is-invalid @enderror"
                            id="market_segment"
                            name="market_segment"
                            style="height:46px; border:1px solid #ced4da;">

                            @foreach(['Walk-in', 'NONE', 'Corporate', 'Government', 'Travel Agency'] as $segment)
                                <option value="{{ $segment }}"
                                    @selected(old('market_segment', $defaults['market_segment']) === $segment)>
                                    {{ $segment }}
                                </option>
                            @endforeach

                        </select>

                        @error('market_segment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

            </div>

        </div>

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-bed text-primary me-2"></i>
                    Room Information
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <div class="row g-4">

                    <!-- Room Type -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="room_type_filter">
                            Room Type
                        </label>

                        <select
                            class="form-select shadow-none"
                            id="room_type_filter"
                            style="height:46px; border:1px solid #ced4da;">

                            <option value="">All Types</option>

                            @foreach($roomTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach

                        </select>

                        <small class="text-muted">
                            Filter available rooms by room type.
                        </small>
                    </div>

                    <!-- Room Selection -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="room_id">
                            Select Room
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            class="form-select shadow-none @error('room_id') is-invalid @enderror"
                            id="room_id"
                            name="room_id"
                            style="height:46px; border:1px solid #ced4da;"
                            required>

                            <option value="">Select available room</option>

                            @foreach($assignableRooms as $room)
                                <option
                                    value="{{ $room->room_id }}"
                                    data-room-type="{{ $room->room_type }}"
                                    data-base-rate="{{ $room->base_rate }}"
                                    @selected(old('room_id') == $room->room_id)
                                >
                                    {{ $room->room_number }} — {{ $room->room_type }}
                                    (₱{{ number_format($room->base_rate, 2) }}/night)
                                </option>
                            @endforeach

                        </select>

                        @error('room_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        @if($assignableRooms->isEmpty())
                            <small class="text-danger">
                                No available rooms right now. Check rooms under cleaning or maintenance.
                            </small>
                        @else
                            <small class="text-muted">
                                Only available rooms are displayed.
                            </small>
                        @endif
                    </div>

                    <!-- Base Rate -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Published Room Rate
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-light">
                                ₱
                            </span>

                            <input
                                type="text"
                                class="form-control bg-light shadow-none"
                                id="room_base_rate_display"
                                value="—"
                                readonly
                                style="height:46px; border:1px solid #ced4da;">

                            <span class="input-group-text bg-light">
                                /night
                            </span>

                        </div>

                        <small class="text-muted">
                            Standard rate of the selected room.
                        </small>
                    </div>

                    <!-- Net Rate -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="net_rate">
                            Agreed Room Rate
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                ₱
                            </span>

                            <input
                                type="number"
                                class="form-control shadow-none @error('net_rate') is-invalid @enderror"
                                id="net_rate"
                                name="net_rate"
                                value="{{ old('net_rate') }}"
                                min="0"
                                step="0.01"
                                placeholder="Enter room rate"
                                style="height:46px; border:1px solid #ced4da;">

                            <span class="input-group-text">
                                /night
                            </span>

                            @error('net_rate')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <small class="text-muted">
                            Leave blank to automatically use the published room rate.
                        </small>
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
                            Saving this registration will immediately check the guest in and mark the selected room as <strong>OCCUPIED</strong> on the dashboard.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-4">

            <small class="text-muted">
                <i class="fa-solid fa-circle-info me-1"></i>
                Review the information before saving the registration.
            </small>

            <div class="d-flex gap-2">

                <a href="{{ route('frontdesk.registration') }}"
                class="btn btn-outline-secondary px-4">
                    <i class="fa-solid fa-rotate-left me-2"></i>
                    Clear
                </a>

                <button
                    type="submit"
                    class="btn btn-primary px-4"
                    @disabled($assignableRooms->isEmpty())>

                    <i class="fa-solid fa-floppy-disk me-2"></i>
                    Save Registration

                </button>

            </div>

        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
    (function() {
        const roomTypeFilter = document.getElementById('room_type_filter');
        const roomSelect = document.getElementById('room_id');
        const rateDisplay = document.getElementById('room_base_rate_display');
        const netRateInput = document.getElementById('net_rate');
        const arrivalDate = document.getElementById('arrival_date');
        const departureDate = document.getElementById('departure_date');

        function updateRateDisplay() {
            const selected = roomSelect.options[roomSelect.selectedIndex];
            if (!selected || !selected.value) {
                rateDisplay.value = '—';
                return;
            }

            const rate = parseFloat(selected.getAttribute('data-base-rate') || '0');
            rateDisplay.value = rate.toFixed(2);

            // Auto-fill net_rate only if user hasn't typed something custom
            if (netRateInput && !netRateInput.dataset.userEdited) {
                netRateInput.value = rate.toFixed(2);
            }
        }

        // Track if user manually edited the net rate
        if (netRateInput) {
            netRateInput.addEventListener('input', function() {
                this.dataset.userEdited = 'true';
            });
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

            roomSelect.addEventListener('change', function() {
                if (netRateInput) {
                    netRateInput.dataset.userEdited = '';
                }
                updateRateDisplay();
            });
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

        // Guest Autocomplete
        const guestSearch = document.getElementById('guest_search');
        const guestSuggestions = document.getElementById('guest_suggestions');
        
        let searchTimeout;

        if (guestSearch && guestSuggestions) {
            guestSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    guestSuggestions.classList.add('d-none');
                    guestSuggestions.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('frontdesk.guests.search') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(guests => {
                            guestSuggestions.innerHTML = '';
                            
                            if (guests.length === 0) {
                                guestSuggestions.innerHTML = '<li class="list-group-item text-muted">No guests found.</li>';
                                guestSuggestions.classList.remove('d-none');
                                return;
                            }

                            guests.forEach(guest => {
                                const li = document.createElement('li');
                                li.className = 'list-group-item list-group-item-action cursor-pointer';
                                li.style.cursor = 'pointer';
                                
                                const nameDiv = document.createElement('div');
                                nameDiv.className = 'fw-bold';
                                nameDiv.textContent = `${guest.first_name} ${guest.last_name}`;
                                
                                const detailsDiv = document.createElement('div');
                                detailsDiv.className = 'small text-muted';
                                detailsDiv.textContent = [guest.contact_number, guest.address_line1].filter(Boolean).join(' • ');
                                
                                li.appendChild(nameDiv);
                                if (detailsDiv.textContent) li.appendChild(detailsDiv);

                                li.addEventListener('click', () => {
                                    document.getElementById('first_name').value = guest.first_name || '';
                                    document.getElementById('last_name').value = guest.last_name || '';
                                    document.getElementById('address_line1').value = guest.address_line1 || '';
                                    document.getElementById('contact_number').value = guest.contact_number || '';
                                    
                                    guestSearch.value = `${guest.first_name} ${guest.last_name}`;
                                    guestSuggestions.classList.add('d-none');
                                });

                                guestSuggestions.appendChild(li);
                            });
                            
                            guestSuggestions.classList.remove('d-none');
                        })
                        .catch(error => {
                            console.error('Error fetching guests:', error);
                        });
                }, 300); // Debounce delay
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!guestSearch.contains(e.target) && !guestSuggestions.contains(e.target)) {
                    guestSuggestions.classList.add('d-none');
                }
            });
        }
    })();
</script>
@endpush
