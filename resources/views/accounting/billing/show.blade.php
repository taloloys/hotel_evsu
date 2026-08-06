@extends('layouts.app')

@section('title', 'Folio Invoice details')
@section('pageTitle', 'Guest Folio Details')
@section('pageSubtitle', 'Statement of account for Folio #' . $folio->folio_number)

@section('content')

<!-- ACTION BUTTONS -->
<div class="d-flex justify-content-between mb-4 d-print-none">
    <a href="{{ route('accounting.billing') }}" class="btn btn-primary">
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
                    <h4 class="fw-bold mb-0">EVSU Hotel</h4>
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
                    <div class="col-6"><strong>Arrival:</strong> {{ $booking->arrival_date?->toDateString() ?? 'N/A' }}</div>
                    <div class="col-6"><strong>Departure:</strong> {{ $booking->departure_date?->toDateString() ?? 'N/A' }}</div>
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
                            <td>{{ $tx->transaction_date?->toDateString() ?? 'N/A' }}</td>
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

    {{-- High-fidelity printable folio view --}}
    <div class="d-none d-print-block print-only-folio" style="font-family: Arial, sans-serif; color: #000000; background: #ffffff; padding: 20px; line-height: 1.4; width: 100%;">
        
        {{-- Header block --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 15px;">
            <tr>
                <!-- Left Logo -->
                <td style="width: 20%; vertical-align: top;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 75px; height: auto; object-fit: contain;">
                </td>
                <!-- Center Hotel Info -->
                <td style="width: 60%; text-align: center; vertical-align: top;">
                    <h3 style="font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">EVSU Hotel</h3>
                    <div style="font-size: 10px; margin-top: 3px; line-height: 1.3;">
                        Bonifacio Street, Ormoc City<br>
                        Tel. Nos. 255-3580 &bull; Fax No. 561-9620<br>
                        Email: hdfelipe@yahoo.com
                    </div>
                    <h4 style="font-size: 13px; font-weight: bold; margin: 12px 0 0 0; text-transform: uppercase; letter-spacing: 1px;">Guest Folio</h4>
                </td>
                <!-- Right Registration/Folio Numbers -->
                <td style="width: 20%; text-align: right; font-size: 10px; font-weight: bold; line-height: 1.4; vertical-align: top; padding-top: 5px;">
                    <div>REG. NO. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $folio->registration_number ?? '—' }}</div>
                    <div>FOLIO NO. : {{ $folio->folio_number }}</div>
                </td>
            </tr>
        </table>

        @php
            $booking = $folio->bookings->sortByDesc('booking_id')->first();
        @endphp

        {{-- Metadata grid --}}
        <table style="width: 100%; font-size: 11px; margin-bottom: 20px; line-height: 1.5; border-collapse: collapse;">
            <tr>
                <td style="width: 13%; font-weight: bold; vertical-align: top;">DATE</td>
                <td style="width: 37%; vertical-align: top;">: {{ now()->format('m/d/Y') }}</td>
                <td style="width: 13%; font-weight: bold; vertical-align: top;">ROOM</td>
                <td style="width: 37%; vertical-align: top;" colspan="3">
                    : {{ $booking?->room?->room_number ?? 'N/A' }}<br>
                    &nbsp;&nbsp;<strong>{{ number_format($folio->net_rate ?? ($booking?->room?->base_rate ?? 0), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">GUEST NAME</td>
                <td style="vertical-align: top;">: {{ strtoupper($folio->guest?->last_name ?? '') }}, {{ strtoupper($folio->guest?->first_name ?? '') }}</td>
                <td style="vertical-align: top;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">ADDRESS</td>
                <td style="vertical-align: top;">: {{ strtoupper($folio->guest?->address_line1 ?? '') }} {{ strtoupper($folio->guest?->address_line2 ?? '') }}</td>
                <td style="vertical-align: top;" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">CHECK-IN</td>
                <td style="vertical-align: top;">: {{ $booking?->arrival_date?->format('m/d/Y') ?? 'N/A' }}</td>
                <td style="font-weight: bold; vertical-align: top;">CHECK-OUT</td>
                <td style="vertical-align: top;">: {{ $booking?->departure_date?->format('m/d/Y') ?? 'N/A' }}</td>
                <td style="font-weight: bold; vertical-align: top; width: 12%;">PERSON/S</td>
                <td style="vertical-align: top; width: 8%;">: {{ $folio->num_pax }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">TIME</td>
                <td style="vertical-align: top;">: {{ $booking?->arrival_time ? \Carbon\Carbon::parse($booking->arrival_time)->format('h:i A') : 'N/A' }}</td>
                <td style="font-weight: bold; vertical-align: top;">TIME</td>
                <td style="vertical-align: top;" colspan="3">: {{ $booking?->departure_time ? \Carbon\Carbon::parse($booking->departure_time)->format('h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">PAYMENT</td>
                <td style="vertical-align: top;">: {{ strtoupper($folio->payment_method ?? 'NONE') }}</td>
                <td style="font-weight: bold; vertical-align: top;">F/DESK</td>
                <td style="vertical-align: top;">: {{ strtoupper(auth()->user()?->full_name ?? auth()->user()?->username ?? 'SYSTEM') }}</td>
                <td style="font-weight: bold; vertical-align: top;">SYMBOL</td>
                <td style="vertical-align: top;">: </td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">MODE</td>
                <td style="vertical-align: top;">: </td>
                <td style="vertical-align: top;" colspan="2">&nbsp;</td>
                <td style="vertical-align: top;" colspan="2">&nbsp;&nbsp;{{ $folio->symbol }}</td>
            </tr>
        </table>

        {{-- Transactions table --}}
        <table style="width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">
                    <th style="padding: 5px 0; text-align: left; width: 15%;">DATE</th>
                    <th style="padding: 5px 0; text-align: left; width: 45%;">REFERENCE</th>
                    <th style="padding: 5px 0; text-align: right; width: 13%;">CHARGE</th>
                    <th style="padding: 5px 0; text-align: right; width: 13%;">CREDIT</th>
                    <th style="padding: 5px 0; text-align: right; width: 14%;">BALANCE</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $runningBal = 0.00;
                    $roomSalesSum = 0.00;
                    $govTaxSum = 0.00;
                    $laundrySum = 0.00;
                    $otherChargesSum = 0.00;
                @endphp
                @foreach($folio->transactions->sortBy('timestamp') as $txn)
                    @php
                        $runningBal += ($txn->charge_amount - $txn->credit_amount);
                        
                        // Summary classification
                        if ($txn->charge_amount > 0) {
                            $code = (int)$txn->charge_code;
                            if ($code === 100 || $code === 103) {
                                $roomSalesSum += $txn->charge_amount;
                            } elseif ($code === 101 || $code === 102) {
                                $govTaxSum += $txn->charge_amount;
                            } elseif ($code === 104 || $code === 105) {
                                $laundrySum += $txn->charge_amount;
                            } else {
                                $otherChargesSum += $txn->charge_amount;
                            }
                        }
                    @endphp
                    <tr>
                        <td style="padding: 4px 0;">{{ $txn->transaction_date?->format('m/d/Y') ?? 'N/A' }}</td>
                        <td style="padding: 4px 0;">
                            {{ $txn->chargeCode?->description ?? 'CHARGE' }}
                            @if($txn->reference_notes) — {{ $txn->reference_notes }} @endif
                            @if($txn->charge_number) ({{ $txn->charge_number }}) @endif
                        </td>
                        <td style="padding: 4px 0; text-align: right;">
                            {{ $txn->charge_amount > 0 ? number_format($txn->charge_amount, 2) : '' }}
                        </td>
                        <td style="padding: 4px 0; text-align: right;">
                            {{ $txn->credit_amount > 0 ? number_format($txn->credit_amount, 2) : '' }}
                        </td>
                        <td style="padding: 4px 0; text-align: right;">{{ number_format($runningBal, 2) }}</td>
                    </tr>
                @endforeach
                {{-- Total balance row --}}
                <tr style="border-top: 1px solid #000; border-bottom: 3px double #000; font-weight: bold;">
                    <td colspan="4" style="padding: 8px 0;">Total Balance - P</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($runningBal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Nothing follows centered --}}
        <div style="text-align: center; font-size: 10px; font-weight: bold; font-style: italic; margin-bottom: 25px;">
            *** Nothing follows ***
        </div>

        {{-- Summary & Remarks --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; font-size: 11px;">
            <!-- Left Remarks -->
            <div style="width: 50%;">
                <div style="font-weight: bold; margin-bottom: 5px;">Remarks :</div>
                <div style="border-bottom: 1px dashed #ccc; height: 40px; width: 90%;"></div>
            </div>
            <!-- Right Summary -->
            <div style="width: 45%;">
                <div style="font-weight: bold; font-style: italic; margin-bottom: 8px;">SUMMARY :</div>
                <table style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                    @if($roomSalesSum > 0)
                        <tr>
                            <td>ROOM SALES</td>
                            <td style="text-align: right;">{{ number_format($roomSalesSum, 2) }}</td>
                        </tr>
                    @endif
                    @if($govTaxSum > 0)
                        <tr>
                            <td>GOVERNMENT TAX</td>
                            <td style="text-align: right;">{{ number_format($govTaxSum, 2) }}</td>
                        </tr>
                    @endif
                    @if($laundrySum > 0)
                        <tr>
                            <td>LAUNDRY SERVICE AND PRESSING</td>
                            <td style="text-align: right;">{{ number_format($laundrySum, 2) }}</td>
                        </tr>
                    @endif
                    @if($otherChargesSum > 0)
                        <tr>
                            <td>OTHER CHARGES</td>
                            <td style="text-align: right;">{{ number_format($otherChargesSum, 2) }}</td>
                        </tr>
                    @endif
                    <tr style="border-top: 1px solid #000; border-bottom: 3px double #000; font-weight: bold;">
                        <td style="padding: 5px 0;">Total</td>
                        <td style="padding: 5px 0; text-align: right;">{{ number_format($roomSalesSum + $govTaxSum + $laundrySum + $otherChargesSum, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .sidebar, .navbar, header, footer, .d-print-none, .main-content > .card {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        /* Hide normal screen folio container */
        #printable-folio {
            display: none !important;
        }
        /* Show print-only folio */
        .print-only-folio {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
            background: #ffffff !important;
            color: #000000 !important;
        }
    }
</style>
@endpush

@endsection
