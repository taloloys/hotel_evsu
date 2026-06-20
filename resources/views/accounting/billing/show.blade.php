@extends('layouts.app')

@section('title', 'Folio Invoice details')
@section('pageTitle', 'Guest Folio Details')
@section('pageSubtitle', 'Statement of account for Folio #' . $folio->folio_number)

@section('content')

<!-- ACTION BUTTONS -->
<div class="d-flex justify-content-between mb-4 d-print-none">
    <a href="{{ route('accounting.billing') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Invoices
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fa-solid fa-print me-1"></i> Print Statement
    </button>
</div>

<!-- INVOICE PRINT AREA -->
<div class="card border-0 shadow-sm p-4" id="printable-folio">
    <div class="row border-bottom pb-4 mb-4">
        <!-- Hotel Info -->
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="me-3" style="width: 70px; height: 60px; object-fit: contain;">
                <div>
                    <h4 class="fw-bold mb-0">Don Felipe Hotel</h4>
                    <small class="text-muted">Accounting & Billing Department</small>
                </div>
            </div>
            <p class="text-muted small mb-0">
                Main Street, City Center<br>
                Contact: +63 (2) 123-4567 | billing@hoteldonfelipe.com
            </p>
        </div>
        <!-- Invoice Info -->
        <div class="col-md-6 text-md-end">
            <h2 class="text-muted text-uppercase fw-bold mb-1">Statement</h2>
            <div class="mb-1"><span class="text-muted">Folio No:</span> <strong class="text-dark">{{ $folio->folio_number }}</strong></div>
            <div class="mb-1"><span class="text-muted">Registration No:</span> {{ $folio->registration_number ?? 'N/A' }}</div>
            <div class="mb-1"><span class="text-muted">Account No:</span> {{ $folio->account_number ?? 'N/A' }}</div>
            <div>
                <span class="text-muted">Status:</span> 
                @if($folio->status === 'CLOSED')
                    <span class="badge bg-success">CLOSED</span>
                @else
                    <span class="badge bg-warning text-dark">OPEN / ACTIVE</span>
                @endif
            </div>
        </div>
    </div>

    <!-- GUEST & STAY INFORMATION -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-uppercase text-secondary fw-bold small border-bottom pb-1 mb-2">Guest Information</h6>
            @if($folio->guest)
                <h5 class="fw-bold mb-1">{{ $folio->guest->first_name }} {{ $folio->guest->last_name }}</h5>
                <p class="text-muted small mb-0">
                    Address: {{ $folio->guest->address_line1 }} {{ $folio->guest->address_line2 ?? '' }}<br>
                    Contact: {{ $folio->guest->contact_number ?? 'N/A' }}
                </p>
            @else
                <h5 class="text-muted">Walk-in Guest / Non-guest</h5>
            @endif
        </div>
        <div class="col-md-6">
            <h6 class="text-uppercase text-secondary fw-bold small border-bottom pb-1 mb-2">Stay Details</h6>
            <div class="row g-2 text-muted small">
                @if($folio->bookings->isNotEmpty())
                    @php $booking = $folio->bookings->first(); @endphp
                    <div class="col-6"><strong>Room:</strong> Room {{ $booking->room?->room_number ?? 'N/A' }} ({{ $booking->room?->room_type ?? 'N/A' }})</div>
                    <div class="col-6"><strong>Pax:</strong> {{ $folio->num_pax }} guest(s)</div>
                    <div class="col-6"><strong>Arrival:</strong> {{ $booking->arrival_date->toDateString() }}</div>
                    <div class="col-6"><strong>Departure:</strong> {{ $booking->departure_date->toDateString() }}</div>
                @else
                    <div class="col-12">No stays or room rentals attached to this folio.</div>
                @endif
                <div class="col-6"><strong>Billing Segment:</strong> {{ $folio->market_segment }}</div>
                <div class="col-6"><strong>Free Breakfasts:</strong> {{ $folio->num_free_breakfasts }}</div>
            </div>
        </div>
    </div>

    <!-- TRANSACTION BREAKDOWN -->
    <div class="mb-4">
        <h6 class="text-uppercase text-secondary fw-bold small border-bottom pb-1 mb-3">Transaction Details</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Ref / Document</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th class="text-end">Charges (+)</th>
                        <th class="text-end">Credits / Payments (-)</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = 0.00; @endphp
                    @forelse($folio->transactions as $tx)
                        @php 
                            $runningBalance += ($tx->charge_amount - $tx->credit_amount); 
                        @endphp
                        <tr>
                            <td>{{ $tx->transaction_date->toDateString() }}</td>
                            <td>{{ $tx->charge_number ?? 'TX-' . $tx->transaction_id }}</td>
                            <td>{{ $tx->charge_code }}</td>
                            <td>{{ $tx->chargeCode->description ?? $tx->reference_notes }}</td>
                            <td class="text-end">
                                @if($tx->charge_amount > 0)
                                    ₱{{ number_format($tx->charge_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end text-success">
                                @if($tx->credit_amount > 0)
                                    ₱{{ number_format($tx->credit_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-muted">{{ $tx->user?->full_name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No transactions logged on this folio yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SUMMARY SECTION -->
    <div class="row justify-content-end">
        <div class="col-md-5">
            <div class="card border-0 bg-light p-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Charges:</span>
                    <strong class="text-dark">₱{{ number_format($totalCharges, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted">Total Payments / Credits:</span>
                    <strong class="text-success">₱{{ number_format($totalCredits, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between pt-1">
                    <h5 class="fw-bold mb-0">Balance Due:</h5>
                    <h5 class="fw-bold mb-0 {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                        ₱{{ number_format($balance, 2) }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background: white !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .sidebar, .navbar, .d-print-none, header {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        #printable-folio {
            padding: 0 !important;
        }
    }
</style>

@endsection
