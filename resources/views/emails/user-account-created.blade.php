<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Created - EVSU Ormoc - Hotel</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4eee6; color: #504538; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #334e42; color: #ffffff; padding: 25px; text-align: center; border-bottom: 4px solid #c2a889; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .header p { margin: 5px 0 0 0; opacity: 1; color: #c2a889; font-size: 14px; font-weight: 600; }
        .content { padding: 30px; }
        .badge { display: inline-block; background-color: #e8f0ec; color: #334e42; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detail-table td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-table td.label { color: #627e71; font-weight: 600; width: 40%; }
        .detail-table td.value { color: #111; font-weight: 500; text-align: right; }
        .footer { background-color: #f4eee6; padding: 15px 30px; text-align: center; font-size: 12px; color: #504538; border-top: 1px solid #e0d8cd; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #334e42; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px; margin-top: 25px; text-align: center; }
        .btn:hover { background-color: #504538; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EVSU Ormoc - Hotel</h1>
            <p>System Account Details</p>
        </div>
        <div class="content">
            <span class="badge">Account Created</span>
            <h2 style="margin-top: 15px; font-size: 20px; color: #334e42;">Hello, {{ $user->full_name }}!</h2>
            <p>An account has been created for you in the EVSU Ormoc - Hotel system. Below are your login credentials and account details:</p>
            
            <table class="detail-table">
                <tr>
                    <td class="label">Full Name</td>
                    <td class="value">{{ $user->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Username</td>
                    <td class="value">{{ $user->username }}</td>
                </tr>
                <tr>
                    <td class="label">Role</td>
                    <td class="value">{{ $user->role?->description ?? $user->role?->role_name ?? 'Staff' }}</td>
                </tr>
                <tr>
                    <td class="label">Password</td>
                    <td class="value"><code>{{ $plainPassword }}</code></td>
                </tr>
            </table>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">Login to System</a>
            </div>

            <p style="margin-top: 25px; font-size: 14px; color: #555;">For security reasons, we strongly recommend that you change your password immediately after logging in for the first time.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.</p>
            <p>This is an automated system email. Please do not reply directly.</p>
        </div>
    </div>
</body>
</html>
