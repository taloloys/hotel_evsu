<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservation Confirmation</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #1b2a4a; color: #ffffff; padding: 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .header p { margin: 5px 0 0 0; opacity: 0.8; font-size: 14px; }
        .content { padding: 30px; }
        .badge { display: inline-block; background-color: #e3f2fd; color: #0d47a1; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detail-table td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-table td.label { color: #666; font-weight: 600; width: 40%; }
        .detail-table td.value { color: #111; font-weight: 500; text-align: right; }
        .footer { background-color: #f8f9fa; padding: 15px 30px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eeeeee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Don Felipe Hotel</h1>
            <p>EVSU Lodging & Conference Center</p>
        </div>
        <div class="content">
            <span class="badge">Reservation Confirmed</span>
            <h2 style="margin-top: 15px; font-size: 20px; color: #1b2a4a;">Hello, {{ $guest ? ($guest->first_name . ' ' . $guest->last_name) : 'Valued Guest' }}!</h2>
            <p>Thank you for choosing Don Felipe Hotel. Your room reservation has been successfully confirmed. Below are your booking details:</p>
            
            <table class="detail-table">
                <tr>
                    <td class="label">Folio Number</td>
                    <td class="value">#{{ $folio?->folio_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Room Number</td>
                    <td class="value">Room {{ $room?->room_number ?? 'Assigned on Arrival' }} ({{ $room?->room_type ?? 'Standard' }})</td>
                </tr>
                <tr>
                    <td class="label">Arrival Date & Time</td>
                    <td class="value">{{ \Carbon\Carbon::parse($booking->arrival_date)->format('M d, Y') }} at {{ $booking->arrival_time ?? '14:00' }}</td>
                </tr>
                <tr>
                    <td class="label">Departure Date</td>
                    <td class="value">{{ $booking->departure_date ? \Carbon\Carbon::parse($booking->departure_date)->format('M d, Y') : 'Open Stay' }}</td>
                </tr>
                <tr>
                    <td class="label">Number of Guests</td>
                    <td class="value">{{ $folio?->num_pax ?? 1 }} Pax</td>
                </tr>
                <tr>
                    <td class="label">Nightly Rate</td>
                    <td class="value">₱{{ number_format($folio?->net_rate ?? $room?->base_rate ?? 0, 2) }}</td>
                </tr>
            </table>

            <p style="margin-top: 25px; font-size: 14px; color: #555;">If you have any questions or need to make changes to your stay, please contact our Frontdesk team.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Don Felipe Hotel - EVSU. All rights reserved.
        </div>
    </div>
</body>
</html>
