@extends('layouts.app')

@section('title', 'Manage Facility Reservation')
@section('pageTitle', 'Facility Reservation Details')
@section('pageSubtitle', 'Review, operate controls, and manage facility booking request')

@section('content')

<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('frontdesk.facility-reservations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to List
    </a>
    <span class="badge font-monospace px-3 py-2 fs-6 rounded-pill" style="background: #334c42; color: #fff;">
        #{{ $reservation->reference_number }}
    </span>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        
        @if($hasConflict && $reservation->status === 'pending')
            <div class="alert alert-danger shadow-sm d-flex align-items-center mb-4">
                <i class="fa-solid fa-triangle-exclamation fs-3 me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Time Slot Conflict Detected</h5>
                    <p class="mb-0">Approving this reservation will conflict with another <strong>already approved or active</strong> reservation for this facility during this time. Please reject or reschedule.</p>
                </div>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-file-invoice me-2 text-primary"></i>Request &amp; Event Information
                </h5>
                @if($reservation->status === 'pending')
                    <span class="badge bg-warning text-dark border border-warning shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Pending</span>
                @elseif($reservation->status === 'approved')
                    <span class="badge bg-success shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Approved</span>
                @elseif($reservation->status === 'active')
                    <span class="badge bg-primary shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Active (In Use)</span>
                @elseif($reservation->status === 'completed')
                    <span class="badge bg-info text-dark shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Completed</span>
                @elseif($reservation->status === 'cancelled')
                    <span class="badge bg-secondary shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Cancelled</span>
                @else
                    <span class="badge bg-danger shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Rejected</span>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    
                    {{-- BOOKER DETAILS --}}
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted fw-bold text-uppercase small mb-3">Booker Details</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width: 100px;">Name:</td>
                                <td class="fw-bold">{{ $reservation->booker_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td class="fw-bold"><a href="mailto:{{ $reservation->booker_email }}">{{ $reservation->booker_email }}</a></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Contact:</td>
                                <td class="fw-bold">{{ $reservation->booker_contact }}</td>
                            </tr>
                        </table>

                        {{-- EVENT DETAILS --}}
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted fw-bold text-uppercase small mb-2">Event Information</h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Event Name:</span>
                                <span class="fw-bold text-dark">{{ $reservation->event_name ?: 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Event Details / Purpose:</span>
                                <div class="p-2.5 rounded bg-light text-dark small" style="white-space: pre-line;">{{ $reservation->event_details ?: 'No additional details provided.' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- RESERVATION SCHEDULE DETAILS --}}
                    <div class="col-md-6">
                        <h6 class="text-muted fw-bold text-uppercase small mb-3">Reservation Schedule</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width: 110px;">Facility:</td>
                                <td>
                                    @if($reservation->isConsolidated())
                                        <span class="badge bg-info text-dark mb-1"><i class="fa-solid fa-layer-group me-1"></i> Consolidated Set</span><br>
                                        <span class="fw-bold text-primary fs-6">{{ $reservation->facilitySet->name ?? $reservation->facility_name }}</span>
                                        <div class="mt-1.5 p-2 bg-light rounded-2 border">
                                            <span class="text-muted d-block small fw-bold text-uppercase" style="font-size: 0.7rem;">Included Spaces:</span>
                                            <ul class="mb-0 ps-3 small">
                                                @foreach($reservation->all_facilities as $memberFac)
                                                    <li><span class="fw-bold">{{ $memberFac->name }}</span> @if($memberFac->capacity)<span class="text-muted">({{ $memberFac->capacity }} pax)</span>@endif</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="fw-bold text-primary">{{ $reservation->facility->name ?? 'Deleted Facility' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Billing Mode:</td>
                                <td class="fw-bold text-capitalize">{{ $reservation->billing_type ?? (($reservation->facilitySet ?? $reservation->facility)?->rate_type ?? 'hourly') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date:</td>
                                <td class="fw-bold">
                                    {{ $reservation->reservation_date->format('M d, Y') }}
                                    @if($reservation->end_date && $reservation->end_date->gt($reservation->reservation_date))
                                        <br><span class="text-success">&ndash; {{ $reservation->end_date->format('M d, Y') }}</span>
                                        <span class="badge bg-light text-dark border ms-1">{{ $reservation->total_days }} Days</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Daily Time:</td>
                                <td class="fw-bold">
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}
                                    <span class="text-muted fw-normal ms-1">({{ $reservation->duration_label }})</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Applied Rate:</td>
                                <td>
                                    @php
                                        $targetItem = $reservation->facilitySet ?? $reservation->facility;
                                        $rateValue = $reservation->billing_type === 'daily'
                                            ? ($targetItem?->effective_daily_rate ?? $targetItem?->rate)
                                            : ($targetItem?->effective_hourly_rate ?? $targetItem?->rate);
                                    @endphp
                                    @if($reservation->agreed_rate !== null)
                                        <span class="badge bg-warning text-dark fw-bold">
                                            <i class="fa-solid fa-handshake me-1"></i> Agreed Rate: ₱{{ number_format($reservation->agreed_rate, 2) }}
                                        </span>
                                    @else
                                        <span class="fw-bold">
                                            ₱{{ number_format($rateValue, 2) }}
                                            <span class="text-muted small fw-normal">/ {{ $reservation->billing_type === 'daily' ? 'day' : 'hr' }}</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        {{-- ACTUAL USAGE TRACKING (IF STARTED OR COMPLETED) --}}
                        @if($reservation->actual_start_time || $reservation->actual_end_time)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted fw-bold text-uppercase small mb-2">Usage Activity</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 110px;">Check-In:</td>
                                    <td class="fw-bold text-dark">{{ $reservation->actual_start_time?->format('M d, Y h:i A') ?? 'Not recorded' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Check-Out:</td>
                                    <td class="fw-bold text-dark">{{ $reservation->actual_end_time?->format('M d, Y h:i A') ?? 'In progress...' }}</td>
                                </tr>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- FINANCIAL SUMMARY --}}
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                        <div>
                            <span class="fw-bold text-muted text-uppercase d-block small">
                                {{ $reservation->final_amount !== null ? 'Final Total Amount' : 'Estimated Total Amount' }}
                            </span>
                            @if($reservation->final_amount !== null && $reservation->final_amount > $reservation->estimated_amount)
                                <span class="text-danger small fw-semibold">
                                    Includes ₱{{ number_format($reservation->final_amount - $reservation->estimated_amount, 2) }} excess time usage charge
                                </span>
                            @endif
                        </div>
                        <span class="fs-4 fw-bold text-success">
                            ₱{{ number_format($reservation->effective_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- TIMELINE TRACKER --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold text-uppercase small mb-3"><i class="fa-solid fa-timeline me-2"></i>Timeline &amp; Logs</h6>
                
                <div class="timeline position-relative ps-4 ms-2 border-start border-2 border-primary">
                    <div class="position-relative mb-4">
                        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-white rounded-circle shadow-sm" style="margin-left: -20px;"></span>
                        <div>
                            <div class="fw-bold text-dark">Submitted Request</div>
                            <div class="text-muted small">{{ $reservation->created_at->format('M d, Y h:i A') }}</div>
                            <div class="text-muted small"><i class="fa-solid fa-check-circle text-success me-1"></i> Terms &amp; Conditions accepted ({{ $reservation->terms_accepted_at?->format('M d, Y h:i A') }})</div>
                        </div>
                    </div>
                    
                    @if($reservation->status !== 'pending')
                    <div class="position-relative mb-4">
                        <span class="position-absolute top-0 start-0 translate-middle p-2 {{ in_array($reservation->status, ['approved', 'active', 'completed']) ? 'bg-success' : 'bg-danger' }} border border-white rounded-circle shadow-sm" style="margin-left: -20px;"></span>
                        <div>
                            <div class="fw-bold text-dark">Processed as {{ ucfirst($reservation->status) }}</div>
                            <div class="text-muted small">{{ $reservation->processed_at?->format('M d, Y h:i A') }} by {{ $reservation->processedBy?->full_name ?? 'Staff' }}</div>
                        </div>
                    </div>
                    @endif

                    @if($reservation->actual_start_time)
                    <div class="position-relative mb-4">
                        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-white rounded-circle shadow-sm" style="margin-left: -20px;"></span>
                        <div>
                            <div class="fw-bold text-dark">Customer Checked In (Facility in Use)</div>
                            <div class="text-muted small">{{ $reservation->actual_start_time->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                    @endif

                    @if($reservation->actual_end_time)
                    <div class="position-relative">
                        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-info border border-white rounded-circle shadow-sm" style="margin-left: -20px;"></span>
                        <div>
                            <div class="fw-bold text-dark">Customer Timed Out (Facility Closed)</div>
                            <div class="text-muted small">{{ $reservation->actual_end_time->format('M d, Y h:i A') }} &bull; Final Amount: ₱{{ number_format($reservation->effective_amount, 2) }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT SIDEBAR: OPERATIONAL CONTROLS --}}
    <div class="col-lg-4">
        
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4 text-center">
                <div class="text-uppercase fw-bold opacity-75 small mb-1">Reference Number</div>
                <h3 class="font-monospace mb-0">{{ $reservation->reference_number }}</h3>
            </div>
        </div>

        {{-- OPERATIONAL ACTIONS CARD --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 fw-bold text-dark border-bottom-0">
                <i class="fa-solid fa-sliders me-2 text-warning"></i>Facility Controls
            </div>
            <div class="card-body p-4 pt-0">

                {{-- 1. PENDING: APPROVE OR REJECT --}}
                @if($reservation->status === 'pending')
                    <form action="{{ route('frontdesk.facility-reservations.approve', $reservation) }}" method="POST" class="mb-3" onsubmit="return confirm('Are you sure you want to approve this reservation? An email confirmation will be sent to the booker.');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm rounded-3" {{ $hasConflict ? 'disabled' : '' }}>
                            <i class="fa-solid fa-check-circle me-2"></i> Approve Request
                        </button>
                        @if($hasConflict)
                            <div class="form-text text-danger mt-2 text-center fw-semibold"><i class="fa-solid fa-lock me-1"></i> Locked due to time conflict</div>
                        @endif
                    </form>

                    <hr class="text-muted">

                    <form action="{{ route('frontdesk.facility-reservations.reject', $reservation) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this reservation? An email notification will be sent to the booker.');">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Rejection Note (Optional)</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Reason for rejection (will be included in email)..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-bold rounded-3">
                            <i class="fa-solid fa-times-circle me-2"></i> Reject Request
                        </button>
                    </form>

                {{-- 2. APPROVED: CAN CHECK-IN, EXTEND, CANCEL --}}
                @elseif($reservation->status === 'approved')
                    <div class="d-grid gap-2">
                        <form action="{{ route('frontdesk.facility-reservations.check-in', $reservation) }}" method="POST" onsubmit="return confirm('Start this booking now and mark facility as Active/In-use?');">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm rounded-3">
                                <i class="fa-solid fa-play me-2"></i> Check-In / Start Booking
                            </button>
                        </form>

                        <button type="button" class="btn btn-outline-primary py-2.5 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#extendModal">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> Extend Booking
                        </button>

                        <button type="button" class="btn btn-outline-danger py-2.5 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fa-solid fa-ban me-2"></i> Cancel Reservation
                        </button>
                    </div>

                {{-- 3. ACTIVE: CAN TIME-OUT, EXTEND, CANCEL --}}
                @elseif($reservation->status === 'active')
                    <div class="d-grid gap-2">
                        <form action="{{ route('frontdesk.facility-reservations.time-out', $reservation) }}" method="POST" onsubmit="return confirm('End this booking now and record Time-Out? Any excess hours will automatically be computed.');">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm rounded-3">
                                <i class="fa-solid fa-stop me-2"></i> Check-Out / Time-Out
                            </button>
                        </form>

                        <button type="button" class="btn btn-outline-primary py-2.5 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#extendModal">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> Extend Booking
                        </button>

                        <button type="button" class="btn btn-outline-danger py-2.5 fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fa-solid fa-ban me-2"></i> Cancel Reservation
                        </button>
                    </div>

                {{-- 4. COMPLETED OR CANCELLED --}}
                @elseif($reservation->status === 'completed')
                    <div class="alert alert-info mb-0 text-center rounded-3">
                        <i class="fa-solid fa-circle-check fs-4 d-block mb-1"></i>
                        <strong>Booking Completed</strong>
                        <div class="small mt-1">This reservation was timed out and finalized.</div>
                    </div>
                @elseif($reservation->status === 'cancelled')
                    <div class="alert alert-secondary mb-0 text-center rounded-3">
                        <i class="fa-solid fa-ban fs-4 d-block mb-1"></i>
                        <strong>Booking Cancelled</strong>
                        <div class="small mt-1">This reservation was cancelled.</div>
                    </div>
                @endif

            </div>
        </div>

        @if($reservation->admin_notes)
        <div class="card shadow-sm border-0 border-top border-4 {{ $reservation->status === 'cancelled' ? 'border-secondary' : ($reservation->status === 'rejected' ? 'border-danger' : 'border-primary') }} mb-4">
            <div class="card-header bg-white fw-bold py-3"><i class="fa-solid fa-comment-dots me-2 text-primary"></i>Admin &amp; Action Notes</div>
            <div class="card-body p-4 text-muted bg-light" style="white-space: pre-line;">{{ $reservation->admin_notes }}</div>
        </div>
        @endif
        
    </div>
</div>

{{-- MODAL: EXTEND BOOKING --}}
<div class="modal fade" id="extendModal" tabindex="-1" aria-labelledby="extendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('frontdesk.facility-reservations.extend', $reservation) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="extendModalLabel">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Extend Reservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Extend the scheduled end time or end date for <strong>{{ $reservation->facility->name ?? 'this facility' }}</strong>. The system will check for conflicts and recalculate the estimated amount.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold">New End Date</label>
                        <input type="date" name="end_date" class="form-control"
                               min="{{ $reservation->reservation_date->toDateString() }}"
                               value="{{ $reservation->end_date?->toDateString() ?? $reservation->reservation_date->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">New Daily End Time</label>
                        <input type="time" name="end_time" class="form-control"
                               value="{{ substr($reservation->end_time, 0, 5) }}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Confirm Extension
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: CANCEL BOOKING --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('frontdesk.facility-reservations.cancel', $reservation) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="cancelModalLabel">
                        <i class="fa-solid fa-ban me-2"></i>Cancel Facility Reservation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-dark mb-3">
                        Are you sure you want to cancel reservation <strong>#{{ $reservation->reference_number }}</strong>? This will release the booked slot on the calendar.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Cancellation</label>
                        <textarea name="cancellation_notes" class="form-control" rows="3" placeholder="Reason (e.g. Booker requested cancellation, emergency event change)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Never Mind</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-ban me-1"></i> Cancel Reservation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
