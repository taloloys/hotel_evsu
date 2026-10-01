@extends('layouts.app')

@section('title', 'Book a Facility')
@section('pageTitle', 'New Facility Reservation')
@section('pageSubtitle', 'Book a facility on behalf of a guest, student group, or external client')

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please fix the errors below:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('frontdesk.facility-reservations.store') }}" method="POST">
            @csrf

            <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(51, 76, 66, 0.1); color: #334c42;">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <h5 class="fw-bold mb-0 font-display" style="color: #1a1a1a;">Reservation Details</h5>
                        </div>
                        <a href="{{ route('frontdesk.facility-reservations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Reservations
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- FACILITY SELECTION --}}
                    {{-- FACILITY SELECTION --}}
                    <div class="mb-4">
                        <label for="facility_id" class="form-label fw-bold">Select Facility <span class="text-danger">*</span></label>
                        <select name="facility_id" id="facility_id" class="form-select @error('facility_id') is-invalid @enderror" required>
                            <option value="">-- Choose a Facility --</option>
                            @foreach($facilities as $fac)
                                <option value="{{ $fac->facility_id }}"
                                        data-rate="{{ $fac->rate }}"
                                        data-rate-type="{{ $fac->rate_type }}"
                                        data-hourly-rate="{{ $fac->effective_hourly_rate ?? $fac->rate }}"
                                        data-daily-rate="{{ $fac->effective_daily_rate ?? $fac->rate }}"
                                        data-has-hourly="{{ $fac->effective_hourly_rate !== null ? '1' : '0' }}"
                                        data-has-daily="{{ $fac->effective_daily_rate !== null ? '1' : '0' }}"
                                        data-capacity="{{ $fac->capacity }}"
                                        {{ old('facility_id', $selectedFacilityId) == $fac->facility_id ? 'selected' : '' }}>
                                    {{ $fac->name }} &bull;
                                    @if($fac->hourly_rate && $fac->daily_rate)
                                        ₱{{ number_format($fac->hourly_rate, 2) }}/hr &amp; ₱{{ number_format($fac->daily_rate, 2) }}/day
                                    @else
                                        ₱{{ number_format($fac->rate, 2) }} / {{ $fac->rate_type }}
                                    @endif
                                    @if($fac->capacity) (up to {{ $fac->capacity }} pax) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('facility_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- BILLING MODE (Hourly vs Daily) --}}
                    <div class="mb-4 p-3 bg-light rounded-3 border" id="billingTypeSection">
                        <label class="form-label fw-bold text-dark mb-2">Billing Mode <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_type" id="billing_hourly" value="hourly"
                                       {{ old('billing_type', 'hourly') === 'hourly' ? 'checked' : '' }} required>
                                <label class="form-check-label fw-semibold" for="billing_hourly" id="labelBillingHourly">
                                    Hourly Billing
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_type" id="billing_daily" value="daily"
                                       {{ old('billing_type') === 'daily' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="billing_daily" id="labelBillingDaily">
                                    Daily Billing (Flat Rate)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- EVENT INFORMATION --}}
                    <h6 class="fw-bold border-bottom pb-2 mb-3 font-display" style="color: #1a1a1a;">
                        <i class="fa-solid fa-calendar-check me-1" style="color: #334c42;"></i> Event Information
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="event_name" class="form-label fw-bold">Event Name</label>
                            <input type="text"
                                   name="event_name"
                                   id="event_name"
                                   class="form-control @error('event_name') is-invalid @enderror"
                                   placeholder="e.g. Annual Department Conference"
                                   value="{{ old('event_name') }}">
                            @error('event_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="event_details" class="form-label fw-bold">Event Details / Special Setup</label>
                            <input type="text"
                                   name="event_details"
                                   id="event_details"
                                   class="form-control @error('event_details') is-invalid @enderror"
                                   placeholder="e.g. Sound system, 80 attendees, stage setup"
                                   value="{{ old('event_details') }}">
                            @error('event_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- DATE & TIME (MULTI-DAY SUPPORT) --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="reservation_date" class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="reservation_date"
                                   id="reservation_date"
                                   class="form-control @error('reservation_date') is-invalid @enderror"
                                   value="{{ old('reservation_date', now()->toDateString()) }}"
                                   min="{{ now()->toDateString() }}"
                                   required>
                            @error('reservation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-bold">End Date <span class="text-muted small">(Multi-day)</span></label>
                            <input type="date"
                                   name="end_date"
                                   id="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', now()->toDateString()) }}"
                                   min="{{ now()->toDateString() }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="start_time" class="form-label fw-bold">Daily Start Time <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="start_time"
                                   id="start_time"
                                   class="form-control @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', '08:00') }}"
                                   required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="end_time" class="form-label fw-bold">Daily End Time <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="end_time"
                                   id="end_time"
                                   class="form-control @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', '12:00') }}"
                                   required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- RATE & AGREED PRICING OVERRIDE (MATCHING REFERENCE IMAGE) --}}
                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6 border-end">
                                <label class="form-label fw-bold text-muted small text-uppercase mb-1">Base Facility Rate</label>
                                <div class="fs-5 fw-bold text-dark" id="displayBaseRate">₱0.00 / Hour</div>
                                <div class="text-muted small">Standard published rate for selected mode.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="agreed_rate" class="form-label fw-bold text-dark">Agreed Facility Rate (Override)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">₱</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="agreed_rate"
                                           id="agreed_rate"
                                           class="form-control @error('agreed_rate') is-invalid @enderror"
                                           placeholder="0.00"
                                           value="{{ old('agreed_rate') }}">
                                    <span class="input-group-text bg-white text-muted" id="agreedRateUnit">/ Hour</span>
                                </div>
                                <div class="form-text text-muted small">
                                    Leave blank to automatically use the published facility rate.
                                </div>
                                @error('agreed_rate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- PRICE ESTIMATION BOX --}}
                    <div class="p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center" style="background: #faf7f2; border: 1px solid #c2a889;">
                        <div>
                            <span class="text-muted small d-block">Estimated Amount:</span>
                            <span class="fs-4 fw-bold text-success" id="calcEstimatedAmount">₱0.00</span>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill px-3 py-1.5" id="calcDurationBadge" style="background: #e8f0ec; color: #334c42; font-weight: 600;">
                                Select facility
                            </span>
                        </div>
                    </div>

                    {{-- BOOKER DETAILS --}}
                    <h6 class="fw-bold border-bottom pb-2 mb-3 font-display" style="color: #1a1a1a;">
                        <i class="fa-solid fa-user me-1" style="color: #334c42;"></i> Booker Information
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="booker_name" class="form-label fw-bold">Full Name / Organization <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="booker_name"
                                   id="booker_name"
                                   class="form-control @error('booker_name') is-invalid @enderror"
                                   placeholder="e.g. John Doe / Math Dept"
                                   value="{{ old('booker_name') }}"
                                   required>
                            @error('booker_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="booker_email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email"
                                   name="booker_email"
                                   id="booker_email"
                                   class="form-control @error('booker_email') is-invalid @enderror"
                                   placeholder="e.g. guest@example.com"
                                   value="{{ old('booker_email') }}"
                                   required>
                            @error('booker_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="booker_contact" class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="booker_contact"
                                   id="booker_contact"
                                   class="form-control @error('booker_contact') is-invalid @enderror"
                                   placeholder="e.g. 09123456789"
                                   value="{{ old('booker_contact') }}"
                                   required>
                            @error('booker_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Initial Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Approved (Confirm Now)</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending (Needs Review)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="admin_notes" class="form-label fw-bold">Booking Notes / Purpose</label>
                            <textarea name="admin_notes"
                                      id="admin_notes"
                                      rows="3"
                                      class="form-control @error('admin_notes') is-invalid @enderror"
                                      placeholder="Optional notes or purpose of reservation...">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- SUBMIT BUTTONS --}}
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('frontdesk.facility-reservations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancel
                        </a>
                        <button type="submit" class="btn text-white rounded-pill px-4 shadow-sm fw-semibold" style="background: #334c42;">
                            <i class="fa-solid fa-calendar-check me-1"></i> Confirm &amp; Save Reservation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const facSelect = document.getElementById('facility_id');
        const billingHourlyRadio = document.getElementById('billing_hourly');
        const billingDailyRadio = document.getElementById('billing_daily');
        const labelHourly = document.getElementById('labelBillingHourly');
        const labelDaily = document.getElementById('labelBillingDaily');
        const startDateInput = document.getElementById('reservation_date');
        const endDateInput = document.getElementById('end_date');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const agreedRateInput = document.getElementById('agreed_rate');
        const displayBaseRateEl = document.getElementById('displayBaseRate');
        const agreedRateUnitEl = document.getElementById('agreedRateUnit');
        const estAmountEl = document.getElementById('calcEstimatedAmount');
        const durationBadge = document.getElementById('calcDurationBadge');

        function getSelectedBillingType() {
            if (billingDailyRadio && billingDailyRadio.checked) return 'daily';
            return 'hourly';
        }

        function syncFacilityRates() {
            const selectedOpt = facSelect.options[facSelect.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) {
                displayBaseRateEl.textContent = '₱0.00 / Hour';
                agreedRateUnitEl.textContent = '/ Hour';
                return;
            }

            const hourlyRate = parseFloat(selectedOpt.getAttribute('data-hourly-rate')) || 0;
            const dailyRate = parseFloat(selectedOpt.getAttribute('data-daily-rate')) || 0;
            const hasHourly = selectedOpt.getAttribute('data-has-hourly') === '1';
            const hasDaily = selectedOpt.getAttribute('data-has-daily') === '1';

            // Update radio labels
            if (labelHourly) {
                labelHourly.textContent = `Hourly Billing (₱${hourlyRate.toLocaleString('en-US', { minimumFractionDigits: 2 })}/hr)`;
                billingHourlyRadio.disabled = !hasHourly && hasDaily;
            }
            if (labelDaily) {
                labelDaily.textContent = `Daily Billing (₱${dailyRate.toLocaleString('en-US', { minimumFractionDigits: 2 })}/day)`;
                billingDailyRadio.disabled = !hasDaily && hasHourly;
            }

            // Auto-switch radio if currently selected mode is unavailable for this facility
            if (!hasHourly && hasDaily && billingHourlyRadio.checked) {
                billingDailyRadio.checked = true;
            } else if (!hasDaily && hasHourly && billingDailyRadio.checked) {
                billingHourlyRadio.checked = true;
            }

            updateCalculation();
        }

        function updateCalculation() {
            const selectedOpt = facSelect.options[facSelect.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) {
                estAmountEl.textContent = '₱0.00';
                durationBadge.textContent = 'Select facility';
                displayBaseRateEl.textContent = '₱0.00 / Hour';
                agreedRateUnitEl.textContent = '/ Hour';
                return;
            }

            const billingType = getSelectedBillingType();
            const hourlyRate = parseFloat(selectedOpt.getAttribute('data-hourly-rate')) || 0;
            const dailyRate = parseFloat(selectedOpt.getAttribute('data-daily-rate')) || 0;
            const baseRate = billingType === 'daily' ? dailyRate : hourlyRate;
            const unitLabel = billingType === 'daily' ? '/ Day' : '/ Hour';

            displayBaseRateEl.textContent = '₱' + baseRate.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + unitLabel;
            agreedRateUnitEl.textContent = unitLabel;

            // Resolve effective rate (agreed override if provided)
            const agreedVal = agreedRateInput.value.trim();
            const hasAgreed = agreedVal !== '' && !isNaN(agreedVal) && parseFloat(agreedVal) >= 0;
            const effectiveRate = hasAgreed ? parseFloat(agreedVal) : baseRate;

            // Date calculations (multi-day)
            const startDate = startDateInput.value;
            const endDate = endDateInput.value || startDate;
            let days = 1;
            if (startDate && endDate && endDate >= startDate) {
                const s = new Date(startDate);
                const e = new Date(endDate);
                days = Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1;
            }

            if (billingType === 'daily') {
                const total = days * effectiveRate;
                estAmountEl.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const dayLabel = days === 1 ? '1 Day (Daily Rate)' : `${days} Days (Daily Rate)`;
                durationBadge.textContent = hasAgreed ? `${dayLabel} • Agreed: ₱${effectiveRate.toFixed(2)}/day` : dayLabel;
                return;
            }

            // Hourly calculation
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            if (!startTime || !endTime) {
                estAmountEl.textContent = '₱0.00';
                durationBadge.textContent = 'Select start and end time';
                return;
            }

            const [startH, startM] = startTime.split(':').map(Number);
            const [endH, endM] = endTime.split(':').map(Number);
            const diffMinutes = (endH * 60 + endM) - (startH * 60 + startM);

            if (diffMinutes <= 0) {
                estAmountEl.textContent = '₱0.00';
                durationBadge.textContent = 'End time must be after start time';
                return;
            }

            const hoursPerDay = Math.round((diffMinutes / 60) * 100) / 100;
            const totalHours = hoursPerDay * days;
            const total = totalHours * effectiveRate;

            estAmountEl.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            let badgeText = days > 1
                ? `${days} Days × ${hoursPerDay}h/day (${totalHours}h total)`
                : `${hoursPerDay} ` + (hoursPerDay === 1 ? 'hour' : 'hours');

            if (hasAgreed) {
                badgeText += ` • Agreed: ₱${effectiveRate.toFixed(2)}/hr`;
            }

            durationBadge.textContent = badgeText;
        }

        facSelect.addEventListener('change', syncFacilityRates);
        if (billingHourlyRadio) billingHourlyRadio.addEventListener('change', updateCalculation);
        if (billingDailyRadio) billingDailyRadio.addEventListener('change', updateCalculation);
        startDateInput.addEventListener('change', () => {
            if (endDateInput.value < startDateInput.value) {
                endDateInput.value = startDateInput.value;
            }
            endDateInput.min = startDateInput.value;
            updateCalculation();
        });
        endDateInput.addEventListener('change', updateCalculation);
        startTimeInput.addEventListener('input', updateCalculation);
        endTimeInput.addEventListener('input', updateCalculation);
        agreedRateInput.addEventListener('input', updateCalculation);

        syncFacilityRates();
    })();
</script>
@endpush

@endsection
