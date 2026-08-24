<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
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

        .amount-box {
            background-color: #e8f0ec;
            border: 1px solid #627e71;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .amount-box .amount {
            font-size: 28px;
            font-weight: 700;
            color: #334e42;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
            <p>Official Payment Receipt</p>
        </div>
        <div class="content">
            <h2 style="margin-top: 5px; font-size: 18px; color: #333;">Payment Received for
                {{ $guest ? ($guest->first_name . ' ' . $guest->last_name) : 'Guest' }}</h2>
            <p>Thank you for your payment. Below are the transaction details for your records:</p>

            <div class="amount-box">
                <div style="font-size: 13px; color: #555; text-transform: uppercase; font-weight: 600;">Amount Paid
                </div>
                <div class="amount">₱{{ number_format($transaction->credit_amount, 2) }}</div>
            </div>

            <table class="detail-table">
                <tr>
                    <td class="label">Reference / Charge #</td>
                    <td class="value">{{ $transaction->charge_number ?? ('TXN-' . $transaction->transaction_id) }}</td>
                </tr>
                <tr>
                    <td class="label">Folio Number</td>
                    <td class="value">#{{ $folio?->folio_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Method</td>
                    <td class="value">{{ str_replace('_', ' ', $transaction->payment_method ?? 'Cash') }}</td>
                </tr>
                <tr>
                    <td class="label">Transaction Date</td>
                    <td class="value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Notes / Details</td>
                    <td class="value">{{ $transaction->reference_notes ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Remaining Balance</td>
                    <td class="value"
                        style="font-weight: 700; color: {{ ($folio?->balance ?? 0) > 0 ? '#c62828' : '#2e7d32' }};">
                        ₱{{ number_format($folio?->balance ?? 0, 2) }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} EVSU Ormoc - Hotel. All rights reserved.
        </div>
    </div>
</body>

</html>