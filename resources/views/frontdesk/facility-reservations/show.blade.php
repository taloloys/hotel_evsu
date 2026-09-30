@extends('layouts.app')

@section('title', 'Review Reservation')
@section('pageTitle', 'Reservation Details')
@section('pageSubtitle', 'Review and process facility booking request')

@section('content')

<div class="mb-3">
    <a href="{{ route('frontdesk.facility-reservations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        
        @if($hasConflict && $reservation->status === 'pending')
            <div class="alert alert-danger shadow-sm d-flex align-items-center mb-4">
                <i class="fa-solid fa-triangle-exclamation fs-3 me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Time Slot Conflict Detected</h5>
                    <p class="mb-0">Approving this reservation will conflict with another <strong>already approved</strong> reservation for the same facility during this time. Please reject or advise the booker.</p>
                </div>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Request Information</h5>
                @if($reservation->status === 'pending')
                    <span class="badge bg-warning text-dark border border-warning shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Pending</span>
                @elseif($reservation->status === 'approved')
                    <span class="badge bg-success shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Approved</span>
                @else
                    <span class="badge bg-danger shadow-sm px-3 rounded-pill uppercase tracking-wider fs-6">Rejected</span>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    
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
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted fw-bold text-uppercase small mb-3">Reservation Details</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" style="width: 100px;">Facility:</td>
                                <td class="fw-bold text-primary">{{ $reservation->facility->name ?? 'Deleted Facility' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date:</td>
                                <td class="fw-bold">{{ $reservation->reservation_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Time:</td>
                                <td class="fw-bold">
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}
                                    <span class="text-muted fw-normal ms-1">({{ $reservation->duration_label }})</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                        <span class="fw-bold text-muted text-uppercase">Estimated Total Amount</span>
                        <span class="fs-4 fw-bold text-success">₱{{ number_format($reservation->estimated_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold text-uppercase small mb-3"><i class="fa-solid fa-timeline me-2"></i>Timeline Tracker</h6>
                
                <div class="timeline position-relative ps-4 ms-2 border-start border-2 border-primary">
                    <div class="position-relative mb-4">
                        <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary border border-white rounded-circle shadow-sm" style="margin-left: -20px;"></span>
                        <div>
                            <div class="fw-bold text-dark">Submitted Request</div>
                            <div class="text-muted small">{{ $reservation->created_at->format('M d, Y h:i A') }}</div>
                            <div class="text-muted small"><i class="fa-solid fa-check-circle text-success me-1"></i> Terms & Conditions accepted ({{ $reservation->terms_accepted_at?->format('M d, Y h:i A') }})</div>
                        </div>
                    </div>
                    
                    @if($reservation->status !== 'pending')
                    <div class="position-relative">
                        <span class="position-absolute top-0 start-0 translate-middle p-2 {{ $reservation->status === 'approved' ? 'bg-success' : 'bg-danger' }} border border-white rounded-circle shadow-sm" style="margin-left: -20px;"></span>
                        <div>
                            <div class="fw-bold text-dark">Marked as {{ ucfirst($reservation->status) }}</div>
                            <div class="text-muted small">{{ $reservation->processed_at?->format('M d, Y h:i A') }} by {{ $reservation->processedBy?->full_name ?? 'System' }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4 text-center">
                <div class="text-uppercase fw-bold opacity-75 small mb-1">Reference Number</div>
                <h3 class="font-monospace mb-0">{{ $reservation->reference_number }}</h3>
            </div>
        </div>

        @if($reservation->status === 'pending')
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold text-dark border-bottom-0">
                <i class="fa-solid fa-bolt me-2 text-warning"></i>Actions
            </div>
            <div class="card-body p-4 pt-0">
                
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
            </div>
        </div>
        @else
            @if($reservation->admin_notes)
            <div class="card shadow-sm border-0 border-top border-danger border-4">
                <div class="card-header bg-white fw-bold py-3"><i class="fa-solid fa-comment-dots me-2 text-danger"></i>Admin Notes</div>
                <div class="card-body p-4 text-muted bg-light">
                    {{ $reservation->admin_notes }}
                </div>
            </div>
            @endif
        @endif
        
    </div>
</div>
@endsection
