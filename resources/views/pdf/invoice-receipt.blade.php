<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; padding: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 0; }
        .details-section { margin-top: 30px; margin-bottom: 30px; }
        .details-section::after { content: ""; clear: both; display: table; }
        .company-details { float: left; width: 50%; }
        .invoice-details { float: right; width: 50%; text-align: right; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f2f2f2; border-top: 1px solid #ddd; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; float: right; width: 40%; }
        .totals table { width: 100%; }
        .totals td { padding: 5px; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>INVOICE</h1>
            <p><strong>Luxzen Ride Services</strong></p>
        </div>

        <div class="details-section">
            <div class="company-details">
                <strong>Bill To:</strong><br>
                {{-- CORRECTED: Access customer through the trip relationship --}}
                {{ $invoice->trip->customer->name ?? 'N/A' }}<br>
                {{ $invoice->trip->customer->email ?? '' }}
            </div>
            <div class="invoice-details">
                <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                <strong>Date Issued:</strong> {{ \Carbon\Carbon::parse($invoice->issued_at)->format('F d, Y') }}<br>
                <strong>Trip ID:</strong> {{ $invoice->trip_id }}<br>
                <strong>Status:</strong> <span style="text-transform: capitalize;">{{ $invoice->status }}</span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Trip on {{ \Carbon\Carbon::parse($invoice->trip->scheduled_at)->format('M d, Y') }}<br>
                        <small>From: {{ $invoice->trip->pickup_location }} to {{ $invoice->trip->dropoff_location }}</small>
                    </td>
                    <td class="text-right">${{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">${{ number_format($invoice->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #333;"><strong>Total Amount Due:</strong></td>
                    <td class="text-right" style="border-top: 1px solid #333;"><strong>${{ number_format($invoice->amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for choosing Luxzen Ride Services. Payments can be made via the app.</p>
        </div>
    </div>
</body>
</html>