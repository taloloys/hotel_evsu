<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Folio Statement</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4eee6; color: #504538; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #ffffff; padding: 25px 25px 15px 25px; text-align: center; border-bottom: 4px solid #334e42; }
        .header img { max-width: 100%; height: auto; max-height: 80px; margin-bottom: 10px; }
        .header h1 { display: none; }
        .header p { margin: 5px 0 0 0; opacity: 1; color: #627e71; font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 30px; font-size: 14px; line-height: 1.6; }
        .footer { background-color: #f4eee6; padding: 15px 30px; text-align: center; font-size: 12px; color: #504538; border-top: 1px solid #e0d8cd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/htmd_side_brand.png') }}" alt="EVSU Ormoc - Hotel">
            <h1>EVSU Ormoc - Hotel</h1>
            <p>Folio Statement Details</p>
        </div>
        <div class="content">
            <p>Dear {{ $guest ? ucfirst(strtolower($guest->first_name)) : 'Valued Guest' }},</p>
            <p>Thank you for your stay at EVSU Ormoc - Hotel. Please find attached a PDF copy of your detailed folio statement for your reference.</p>
            <p>If you have any questions regarding this statement, please do not hesitate to contact our front desk.</p>
            <p>Warm regards,<br><strong>EVSU Ormoc - Hotel Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.</p>
            <p>This is an automated system email. Please do not reply directly.</p>
        </div>
    </div>
</body>
</html>
