<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Official Guest Folio - EVSU Hotel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #000000;
            margin: 0;
            padding: 20px;
        }
        .folio-container {
            max-width: 720px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #dcdcdc;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 1px solid #000000;
            padding-bottom: 15px;
        }
        .hotel-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 20px;
            line-height: 1.5;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 15px;
        }
        .ledger-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
        }
        .ledger-table td {
            padding: 5px 4px;
        }
        .summary-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 11px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    @php
        $booking = $folio->bookings->sortByDesc('booking_id')->first();
        $room    = $booking?->room;
    @endphp

    <div class="folio-container">
        
        {{-- Header block --}}
        <table class="header-table">
            <tr>
                <td style="width: 20%; vertical-align: top;">
                    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.png" alt="EVSU Hotel" style="width: 70px; height: auto;" />
                </td>
                <td style="width: 60%; text-align: center; vertical-align: top;">
                    <h3 class="hotel-title">EVSU Hotel</h3>
                    <div style="font-size: 10px; margin-top: 3px; line-height: 1.3; color: #333;">
                        Bonifacio Street, Ormoc City<br>
                        Tel. Nos. 255-3580 &bull; Fax No. 561-9620<br>
                        Email: hdfelipe@yahoo.com
                    </div>
                    <h4 style="font-size: 13px; font-weight: bold; margin: 12px 0 0 0; text-transform: uppercase; letter-spacing: 1px;">Guest Folio Statement</h4>
                </td>
                <td style="width: 20%; text-align: right; font-size: 10px; font-weight: bold; line-height: 1.4; vertical-align: top;">
                    <div>REG. NO. &nbsp;: {{ $folio->registration_number ?? '—' }}</div>
                    <div>FOLIO NO. : {{ $folio->folio_number }}</div>
                </td>
            </tr>
        </table>

        {{-- Metadata grid --}}
        <table class="meta-table">
            <tr>
                <td style="width: 15%; font-weight: bold;">DATE</td>
                <td style="width: 35%;">: {{ now()->format('m/d/Y') }}</td>
                <td style="width: 15%; font-weight: bold;">ROOM(S)</td>
                <td style="width: 35%;">
                    : @if($folio->bookings->count() > 1)
                        @foreach($folio->bookings->sortBy('booking_id') as $b)
                            Room {{ $b->room?->room_number }} [{{ $b->arrival_date?->format('m/d') }} to {{ $b->departure_date?->format('m/d') ?? 'Open' }}]{{ !$loop->last ? '; ' : '' }}
                        @endforeach
                      @else
                        Room {{ $booking?->room?->room_number ?? 'N/A' }}
                      @endif
                    <br>&nbsp;&nbsp;Rate: <strong>₱{{ number_format($folio->net_rate ?? ($booking?->room?->base_rate ?? 0), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">GUEST NAME</td>
                <td>: {{ strtoupper($folio->guest?->last_name ?? '') }}, {{ strtoupper($folio->guest?->first_name ?? '') }}</td>
                <td style="font-weight: bold;">STATUS</td>
                <td>: <strong>{{ strtoupper($folio->status) }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">ADDRESS</td>
                <td colspan="3">: {{ strtoupper($folio->guest?->address_line1 ?? '') }} {{ strtoupper($folio->guest?->address_line2 ?? '') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">CHECK-IN</td>
                <td>: {{ $booking?->arrival_date?->format('m/d/Y') ?? 'N/A' }}</td>
                <td style="font-weight: bold;">CHECK-OUT</td>
                <td>: {{ $booking?->departure_date?->format('m/d/Y') ?? 'N/A' }} (PAX: {{ $folio->num_pax }})</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">TIME</td>
                <td>: {{ $booking?->arrival_time ? \Carbon\Carbon::parse($booking->arrival_time)->format('h:i A') : 'N/A' }}</td>
                <td style="font-weight: bold;">TIME</td>
                <td>: {{ $booking?->departure_time ? \Carbon\Carbon::parse($booking->departure_time)->format('h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">PAYMENT</td>
                <td>: {{ strtoupper($folio->payment_method ?? 'NONE') }}</td>
                <td style="font-weight: bold;">FRONTDESK</td>
                <td>: {{ strtoupper(auth()->user()?->full_name ?? auth()->user()?->username ?? 'STAFF') }}</td>
            </tr>
        </table>

        {{-- Itemized Ledger Table --}}
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 15%;">DATE</th>
                    <th style="width: 45%;">REFERENCE</th>
                    <th style="text-align: right; width: 13%;">CHARGE</th>
                    <th style="text-align: right; width: 13%;">CREDIT</th>
                    <th style="text-align: right; width: 14%;">BALANCE</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $runningBal = 0.00;
                    $printSumRoom = 0.00;
                    $printSumRestaurant = 0.00;
                    $printSumLaundry = 0.00;
                    $printSumTax = 0.00;
                    $printSumDiscounts = 0.00;
                    $printSumOther = 0.00;
                    $printSumPayments = 0.00;
                @endphp
                @foreach($folio->transactions->sortBy('timestamp') as $txn)
                    @php
                        $runningBal += ($txn->charge_amount - $txn->credit_amount);
                        $printCode = (int) $txn->charge_code;
                        $printCat  = $txn->chargeCode?->category ?? 'HOTEL';

                        if ($printCat === 'PAYMENT') {
                            $printSumPayments += $txn->credit_amount;
                        } elseif ($printCode === 100 || $printCode === 103) {
                            $printSumRoom += $txn->charge_amount;
                        } elseif ($printCode === 104 || $printCode === 105) {
                            $printSumLaundry += $txn->charge_amount;
                        } elseif ($printCode === 200) {
                            $printSumRestaurant += $txn->charge_amount;
                        } elseif ($printCat === 'TAX_SERVICE') {
                            $printSumTax += $txn->charge_amount;
                        } elseif ($printCode === 201) {
                            $printSumDiscounts += $txn->charge_amount;
                        } else {
                            $printSumOther += $txn->charge_amount;
                        }
                    @endphp
                    <tr>
                        <td>{{ $txn->transaction_date->format('m/d/Y') }}</td>
                        <td>
                            {{ $txn->chargeCode?->description ?? 'CHARGE' }}
                            @if($txn->reference_notes) — {{ $txn->reference_notes }} @endif
                            @if($txn->charge_number) ({{ $txn->charge_number }}) @endif
                        </td>
                        <td style="text-align: right;">
                            {{ $txn->charge_amount > 0 ? number_format($txn->charge_amount, 2) : '' }}
                        </td>
                        <td style="text-align: right;">
                            {{ $txn->credit_amount > 0 ? number_format($txn->credit_amount, 2) : '' }}
                        </td>
                        <td style="text-align: right;">{{ number_format($runningBal, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #000; border-bottom: 3px double #000; font-weight: bold;">
                    <td colspan="4" style="padding: 8px 0;">Total Balance - ₱</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($runningBal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center; font-size: 10px; font-weight: bold; font-style: italic; margin-bottom: 20px;">
            *** Nothing follows ***
        </div>

        {{-- Summary Breakdown --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Remarks:</div>
                    <div style="border-bottom: 1px dashed #ccc; height: 35px; width: 100%;"></div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div style="font-weight: bold; font-style: italic; margin-bottom: 6px;">SUMMARY:</div>
                    <table style="width: 100%; border-collapse: collapse; line-height: 1.5;">
                        @if($printSumRoom > 0)
                            <tr>
                                <td>ROOM CHARGES</td>
                                <td style="text-align: right;">{{ number_format($printSumRoom, 2) }}</td>
                            </tr>
                        @endif
                        @if($printSumRestaurant > 0)
                            <tr>
                                <td>RESTAURANT / FOOD</td>
                                <td style="text-align: right;">{{ number_format($printSumRestaurant, 2) }}</td>
                            </tr>
                        @endif
                        @if($printSumLaundry > 0)
                            <tr>
                                <td>LAUNDRY CHARGES</td>
                                <td style="text-align: right;">{{ number_format($printSumLaundry, 2) }}</td>
                            </tr>
                        @endif
                        @if($printSumTax > 0)
                            <tr>
                                <td>TAXES &amp; SERVICE CHARGES</td>
                                <td style="text-align: right;">{{ number_format($printSumTax, 2) }}</td>
                            </tr>
                        @endif
                        @if($printSumDiscounts > 0)
                            <tr>
                                <td>DISCOUNTS / COMPLIMENTARY</td>
                                <td style="text-align: right;">{{ number_format($printSumDiscounts, 2) }}</td>
                            </tr>
                        @endif
                        @if($printSumOther > 0)
                            <tr>
                                <td>OTHER CHARGES</td>
                                <td style="text-align: right;">{{ number_format($printSumOther, 2) }}</td>
                            </tr>
                        @endif
                        @if($printSumPayments > 0)
                            <tr style="border-top: 1px solid #999;">
                                <td>TOTAL PAYMENTS</td>
                                <td style="text-align: right;">({{ number_format($printSumPayments, 2) }})</td>
                            </tr>
                        @endif
                        <tr style="border-top: 1px solid #000; border-bottom: 3px double #000; font-weight: bold;">
                            <td style="padding: 5px 0;">OUTSTANDING BALANCE</td>
                            <td style="padding: 5px 0; text-align: right;">₱ {{ number_format($runningBal, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin-top: 25px; text-align: center; font-size: 11px; color: #666; border-top: 1px solid #eee; padding-top: 15px;">
            Thank you for staying at <strong>EVSU Hotel</strong>! We look forward to welcoming you back.
        </div>

    </div>
</body>
</html>
