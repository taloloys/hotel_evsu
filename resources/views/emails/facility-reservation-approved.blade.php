<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facility Reservation Approved</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4eee6; color: #504538; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #ffffff; padding: 25px 25px 15px 25px; text-align: center; border-bottom: 4px solid #334e42; }
        .header img { max-width: 100%; height: auto; max-height: 80px; margin-bottom: 10px; }
        .header h1 { display: none; }
        .header p { margin: 5px 0 0 0; opacity: 1; color: #627e71; font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 30px; }
        .badge { display: inline-block; background-color: #e8f0ec; color: #334e42; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detail-table td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-table td.label { color: #627e71; font-weight: 600; width: 40%; }
        .detail-table td.value { color: #111; font-weight: 500; text-align: right; }
        .footer { background-color: #f4eee6; padding: 15px 30px; text-align: center; font-size: 12px; color: #504538; border-top: 1px solid #e0d8cd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/htmd_side_brand.png') }}" alt="EVSU Ormoc - Hotel">
            <h1>EVSU Ormoc - Hotel</h1>
            <p>EVSU Lodging & Conference Center</p>
        </div>
        <div class="content">
            <span class="badge">Reservation Approved</span>
            <h2 style="margin-top: 15px; font-size: 20px; color: #334e42;">Hello, {{ $reservation->booker_name }}!</h2>
            <p>Thank you for choosing EVSU Ormoc - Hotel. Your facility reservation has been approved. Below are your booking details:</p>
            
            <table class="detail-table">
                <tr>
                    <td class="label">Reference Number</td>
                    <td class="value">{{ $reservation->reference_number }}</td>
                </tr>
                <tr>
                    <td class="label">Facility</td>
                    <td class="value">{{ $facility->name }}</td>
                </tr>
                <tr>
                    <td class="label">Reservation Date</td>
                    <td class="value">{{ $reservation->reservation_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Time</td>
                    <td class="value">{{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}</td>
                </tr>
                <tr>
                    <td class="label">Duration</td>
                    <td class="value">{{ $reservation->duration_label }}</td>
                </tr>
                <tr>
                    <td class="label">Estimated Amount</td>
                    <td class="value">₱{{ number_format($reservation->estimated_amount, 2) }}</td>
                </tr>
            </table>

            <p style="margin-top: 25px; font-size: 14px; color: #555;">Please remember that by booking, you have agreed to the Facility Terms & Conditions. If you have any questions or need to make changes, please contact our Frontdesk team.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.
        </div>
    </div>
</body>
</html>
