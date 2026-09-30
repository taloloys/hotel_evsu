<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facility Reservation Approved</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4eee6;
            color: #504538;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #ffffff;
            padding: 25px 25px 15px 25px;
            text-align: center;
            border-bottom: 4px solid #334e42;
        }
        .header img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 1;
            color: #627e71;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .badge {
            display: inline-block;
            background-color: #e8f0ec;
            color: #334e42;
            padding: 5px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .highlight-box {
            background-color: #f9f6f2;
            border: 1px solid #e0d8cd;
            border-radius: 6px;
            padding: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight-box .ref-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #827567;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .highlight-box .ref-number {
            font-size: 24px;
            font-weight: 700;
            color: #334e42;
            margin: 4px 0 10px 0;
            letter-spacing: 1px;
        }
        .highlight-box .estimated-total {
            font-size: 14px;
            color: #504538;
        }
        .highlight-box .estimated-total strong {
            color: #334e42;
            font-size: 16px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .detail-table td {
            padding: 11px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .detail-table td.label {
            color: #627e71;
            font-weight: 600;
            width: 40%;
        }
        .detail-table td.value {
            color: #111111;
            font-weight: 500;
            text-align: right;
        }
        .notice-box {
            background-color: #f8faf9;
            border-left: 4px solid #334e42;
            border-radius: 4px;
            padding: 14px 16px;
            margin: 25px 0 15px 0;
            font-size: 13px;
            color: #4f5d56;
            line-height: 1.5;
        }
        .system-disclaimer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #f0ece6;
            font-size: 12px;
            color: #827567;
            text-align: center;
            line-height: 1.5;
        }
        .footer {
            background-color: #f4eee6;
            padding: 15px 30px;
            text-align: center;
            font-size: 12px;
            color: #504538;
            border-top: 1px solid #e0d8cd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/htmd_side_brand.png') }}" alt="EVSU Ormoc - Hotel">
            <p>EVSU Lodging &amp; Conference Center</p>
        </div>
        <div class="content">
            <span class="badge">Reservation Approved</span>
            
            <h2 style="margin-top: 15px; font-size: 20px; color: #334e42;">Hello, {{ $reservation->booker_name }}!</h2>
            <p style="font-size: 14px; line-height: 1.6; color: #504538;">
                Great news! Your facility reservation request has been officially approved by our front desk team. Below are your confirmed booking details:
            </p>

            <div class="highlight-box">
                <div class="ref-label">Reference Number</div>
                <div class="ref-number">{{ $reservation->reference_number }}</div>
                <div class="estimated-total">
                    Estimated Amount: <strong>₱{{ number_format($reservation->estimated_amount, 2) }}</strong>
                </div>
            </div>

            <table class="detail-table">
                <tr>
                    <td class="label">Facility</td>
                    <td class="value">{{ $facility->name }}</td>
                </tr>
                @if(!empty($facility->building_location))
                <tr>
                    <td class="label">Location</td>
                    <td class="value">{{ $facility->building_location }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Reservation Date</td>
                    <td class="value">{{ $reservation->reservation_date->format('F d, Y (l)') }}</td>
                </tr>
                <tr>
                    <td class="label">Reserved Time</td>
                    <td class="value">
                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Duration</td>
                    <td class="value">{{ $reservation->duration_label ?? ($reservation->duration_hours . ' Hours') }}</td>
                </tr>
                <tr>
                    <td class="label">Booker Contact</td>
                    <td class="value">{{ $reservation->booker_contact }}</td>
                </tr>
            </table>

            <div class="notice-box">
                <strong>Important Reminders:</strong>
                <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                    <li>Please present your <strong>Reference Number</strong> at the Front Desk upon arrival.</li>
                    <li>We recommend arriving at least 15–30 minutes prior to your scheduled start time for setup and access coordination.</li>
                    <li>All reservations remain subject to the EVSU Lodging &amp; Conference Center Facility Terms &amp; Conditions.</li>
                </ul>
            </div>

            <div class="system-disclaimer">
                <p style="margin: 0; font-weight: 600;">This is an automated system email. Please do not reply directly.</p>
                <p style="margin: 4px 0 0 0;">If you have any questions or need to make changes, please contact or visit the Front Desk directly.</p>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.
        </div>
    </div>
</body>
</html>
