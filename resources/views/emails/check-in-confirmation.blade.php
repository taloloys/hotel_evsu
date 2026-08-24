<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Welcome - Checked In</title>
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

        .header h1 {
            display: none;
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
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .detail-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-table td.label {
            color: #627e71;
            font-weight: 600;
            width: 40%;
        }

        .detail-table td.value {
            color: #111;
            font-weight: 500;
            text-align: right;
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
            <h1>EVSU Ormoc - Hotel</h1>
            <p>Welcome! You are Checked In</p>
        </div>
        <div class="content">
            <span class="badge">Check-In Complete</span>
            <h2 style="margin-top: 15px; font-size: 20px; color: #334e42;">Welcome,
                {{ $guest ? ($guest->first_name . ' ' . $guest->last_name) : 'Guest' }}!</h2>
            <p>We are delighted to have you stay with us. Your check-in process is complete and your room is ready.</p>

            <table class="detail-table">
                <tr>
                    <td class="label">Room Number</td>
                    <td class="value">Room {{ $room?->room_number ?? 'N/A' }} ({{ $room?->room_type ?? 'Standard' }})
                    </td>
                </tr>
                <tr>
                    <td class="label">Folio Number</td>
                    <td class="value">#{{ $folio?->folio_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Check-In Time</td>
                    <td class="value">
                        {{ $booking->actual_check_in ? \Carbon\Carbon::parse($booking->actual_check_in)->format('M d, Y g:i A') : now()->format('M d, Y g:i A') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Expected Departure</td>
                    <td class="value">
                        {{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('M d, Y') : 'Open Stay' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Free Breakfast Count</td>
                    <td class="value">{{ $folio?->num_free_breakfasts ?? 0 }} Voucher(s)</td>
                </tr>
            </table>

            <p style="margin-top: 25px; font-size: 14px; color: #555;">Enjoy your stay! Please don't hesitate to reach
                out to the Front Desk for any assistance.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.
        </div>
    </div>
</body>

</html>