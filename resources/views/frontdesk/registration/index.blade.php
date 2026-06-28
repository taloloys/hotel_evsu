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
                <h5 class="fw-bold mb-0">Guest Information</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" maxlength="50" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" maxlength="50" required>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label" for="address_line1">Address</label>
                        <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" maxlength="100">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="contact_number">Mobile Number</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" maxlength="20">
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

                    <div class="col-md-6">
                        <label class="form-label">Room Base Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" class="form-control bg-light" id="room_base_rate_display" value="—" readonly>
                            <span class="input-group-text text-muted">/night</span>
                        </div>
                        <div class="form-text">Standard published rate for selected room.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="net_rate">Agreed / Net Rate <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number"
                                class="form-control @error('net_rate') is-invalid @enderror"
                                id="net_rate"
                                name="net_rate"
                                value="{{ old('net_rate') }}"
                                min="0"
                                step="0.01"
                                placeholder="Enter agreed room price"
                            >
                            <span class="input-group-text text-muted">/night</span>
                        </div>
                        <div class="form-text text-primary">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Adjust for discounts. Leave blank to use the base rate.
                        </div>
                        @error('net_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('frontdesk.registration') }}" class="btn btn-secondary">Clear</a>
                    <button type="submit" class="btn btn-primary" @disabled($assignableRooms->isEmpty())>
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Registration
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
    })();
</script>
@endpush
