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
                            class="form-control bg-light shadow-none"
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

                        <small class="text-muted">
                            Select the guest's preferred payment method.
                        </small>
                    </div>

                </div>

            </div>

        </div>

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-user-check text-primary me-2"></i>
                    Guest Selection
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <div class="row g-4">

                    <div class="col-12 position-relative">

                        <label class="form-label fw-semibold" for="guest_search">
                            Search Existing Guest
                            <span class="text-danger">*</span>
                        </label>

                        <!-- Search -->
                        <div class="input-group"
                            style="width:100%; border:1px solid #ced4da; border-radius:.375rem; overflow:hidden;">

                            <span class="input-group-text bg-white border-0">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>

                            <input
                                type="text"
                                class="form-control border-0 shadow-none @error('guest_id') is-invalid @enderror"
                                id="guest_search"
                                placeholder="Search guest by first or last name..."
                                value="{{ old('guest_id') && isset($selectedGuest) ? $selectedGuest->last_name . ', ' . $selectedGuest->first_name : '' }}"
                                autocomplete="off"
                                style="height:46px;"
                                required>

                        </div>

                        @error('guest_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                        <div id="guest_search_results"
                            class="dropdown-menu w-100 shadow-sm mt-1"
                            style="display:none; max-height:250px; overflow-y:auto; z-index:1050;">
                        </div>

                        <input
                            type="hidden"
                            id="guest_id"
                            name="guest_id"
                            value="{{ old('guest_id', $selectedGuest->guest_id ?? '') }}"
                            required>

                        <small class="text-muted">
                            Search and select an existing guest before continuing.
                        </small>

                    </div>

                </div>

                <!-- Selected Guest -->
                <div class="border rounded-3 mt-4 d-none" id="guest_info_card">

                    <div class="border-bottom bg-light px-3 py-2">
                        <strong class="small text-uppercase text-secondary">
                            Selected Guest
                        </strong>
                    </div>

                    <div class="p-3">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="small text-muted mb-1">
                                    Full Name
                                </label>

                                <div class="fw-semibold" id="display_guest_name">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-muted mb-1">
                                    Mobile Number
                                </label>

                                <div class="fw-semibold" id="display_guest_contact">
                                    -
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="small text-muted mb-1">
                                    Address
                                </label>

                                <div class="fw-semibold" id="display_guest_address">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-calendar-days text-primary me-2"></i>
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
                            <span class="text-danger departure-required">*</span>
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
                            <span class="text-danger departure-required">*</span>
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

                    <!-- Open Stay -->
                    <div class="col-12">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="open_stay"
                                name="open_stay"
                                value="1"
                                @checked(old('open_stay'))>
                            <label class="form-check-label fw-semibold" for="open_stay">
                                Open Stay (no checkout date)
                            </label>
                            <div class="text-muted small">
                                Guest checkout date is unknown. Room charges will be posted daily at midnight.
                            </div>
                        </div>
                    </div>

                    <!-- Number of Pax -->
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

                        <small class="text-muted">
                            Total number of staying guests.
                        </small>
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

                            <option value="0" @selected(old('has_joiner','0') == '0')>
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
                                <option
                                    value="{{ $segment }}"
                                    @selected(old('market_segment', $defaults['market_segment']) === $segment)>
                                    {{ $segment }}
                                </option>
                            @endforeach

                        </select>

                        @error('market_segment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Select the guest's booking category.
                        </small>
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
                            Filter rooms by room type.
                        </small>
                    </div>

                    <!-- Room Selection -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="room_id">
                            Available Room
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
                                No available rooms right now. Check the dashboard for rooms under cleaning or maintenance.
                            </small>
                        @else
                            <small class="text-muted">
                                Only available rooms are listed.
                            </small>
                        @endif
                    </div>

                    <!-- Published Rate -->
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Published Room Rate
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-light border">
                                ₱
                            </span>

                            <input
                                type="text"
                                class="form-control bg-light shadow-none"
                                id="room_base_rate_display"
                                value="—"
                                readonly
                                style="height:46px; border:1px solid #ced4da;">

                            <span class="input-group-text bg-light border text-muted">
                                /night
                            </span>

                        </div>

                        <small class="text-muted">
                            Standard room rate based on the selected room.
                        </small>

                    </div>

                    <!-- Net Rate -->
                    <div class="col-md-6">

                        <label class="form-label fw-semibold" for="net_rate">
                            Agreed Room Rate
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text border">
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
                                placeholder="Enter agreed room rate"
                                style="height:46px; border:1px solid #ced4da;">

                            <span class="input-group-text border text-muted">
                                /night
                            </span>

                        </div>

                        @error('net_rate')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Leave blank to automatically use the published room rate.
                        </small>

                    </div>

                </div>

            </div>

        </div>

        <div class="card border mb-4">

            <!-- Card Header -->
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-clipboard-list text-primary me-2"></i>
                    Additional Information
                </h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <div class="row g-4">

                    <!-- Special Arrangements -->
                    <div class="col-12">

                        <label class="form-label fw-semibold" for="special_arrangements">
                            Special Arrangements
                        </label>

                        <textarea
                            class="form-control shadow-none @error('special_arrangements') is-invalid @enderror"
                            id="special_arrangements"
                            name="special_arrangements"
                            rows="4"
                            placeholder="Example: Extra pillows, early check-in request, late check-out, etc."
                            style="border:1px solid #ced4da; resize:none;">{{ old('special_arrangements') }}</textarea>

                        @error('special_arrangements')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Optional notes or special requests for this guest.
                        </small>

                    </div>

                    <!-- Information -->
                    <div class="col-12">

                        <div class="border rounded-3 p-3 bg-light">

                            <div class="d-flex align-items-start">

                                <i class="fa-solid fa-circle-info text-primary me-3 mt-1"></i>

                                <div>

                                    <div class="fw-semibold mb-1">
                                        Check-in Information
                                    </div>

                                    <div class="text-muted small">
                                        Saving this registration will immediately
                                        <strong>check in the guest</strong> and update the
                                        selected room's status to
                                        <strong>Occupied</strong>.
                                    </div>

                                </div>

                            </div>

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
    (function() {
        const guestSearchInput = document.getElementById('guest_search');
        const guestSearchResults = document.getElementById('guest_search_results');
        const guestIdInput = document.getElementById('guest_id');
        const guestInfoCard = document.getElementById('guest_info_card');
        const displayGuestName = document.getElementById('display_guest_name');
        const displayGuestContact = document.getElementById('display_guest_contact');
        const displayGuestAddress = document.getElementById('display_guest_address');
        const roomTypeFilter = document.getElementById('room_type_filter');
        const roomSelect = document.getElementById('room_id');
        const rateDisplay = document.getElementById('room_base_rate_display');
        const netRateInput = document.getElementById('net_rate');
        const arrivalDate = document.getElementById('arrival_date');
        const departureDate = document.getElementById('departure_date');
        const departureTime = document.getElementById('departure_time');
        const openStayCheckbox = document.getElementById('open_stay');

        function toggleOpenStay() {
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

            document.querySelectorAll('.departure-required').forEach(function(el) {
                el.classList.toggle('d-none', isOpenStay);
            });
        }

        if (openStayCheckbox) {
            openStayCheckbox.addEventListener('change', toggleOpenStay);
            toggleOpenStay();
        }

        function setGuestDetails(guest, folioNum) {
            displayGuestName.textContent = `${guest.last_name}, ${guest.first_name}`;
            displayGuestContact.textContent = guest.contact_number || 'N/A';
            displayGuestAddress.textContent = [guest.address_line1, guest.address_line2].filter(Boolean).join(' ') || 'N/A';
            guestInfoCard.classList.remove('d-none');

            document.getElementById('folio_number').value = "{{ $suggestedFolioNumber }}";
        }

        @if(isset($selectedGuest))
            setGuestDetails(
                {!! json_encode($selectedGuest) !!},
                ""
            );
        @endif

        if (guestSearchInput && guestSearchResults && guestIdInput) {
            let debounceTimer;

            guestSearchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length === 0) {
                    guestIdInput.value = '';
                    guestInfoCard.classList.add('d-none');
                    document.getElementById('folio_number').value = "{{ $suggestedFolioNumber }}";
                }

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
                                    
                                    guestIdInput.value = guest.guest_id;
                                    setGuestDetails(guest, folioNum);

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

        // Room rate and filtering
        function updateRateDisplay() {
            const selected = roomSelect.options[roomSelect.selectedIndex];
            if (!selected || !selected.value) {
                rateDisplay.value = '—';
                return;
            }

            const rate = parseFloat(selected.getAttribute('data-base-rate') || '0');
            rateDisplay.value = rate.toFixed(2);

            // Auto-fill net_rate if user hasn't typed something custom
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
                if (netRateInput) { netRateInput.dataset.userEdited = ''; }
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
    })();
</script>
@endpush
