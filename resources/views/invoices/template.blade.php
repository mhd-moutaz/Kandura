<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            direction: ltr;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .invoice-info-row {
            display: table-row;
        }
        .invoice-info-cell {
            display: table-cell;
            padding: 5px;
            width: 50%;
        }
        .invoice-info-cell strong {
            font-weight: bold;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .totals {
            margin-top: 20px;
            text-align: left;
        }
        .totals table {
            width: 300px;
            margin-left: auto;
        }
        .totals table td {
            border: none;
            padding: 5px;
        }
        .totals .grand-total {
            font-size: 16px;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Kandura Invoice</h1>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-row">
                <div class="invoice-info-cell">
                    <strong>Invoice Number:</strong> {{ $invoiceNumber }}
                </div>
                <div class="invoice-info-cell">
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}
                </div>
            </div>
            <div class="invoice-info-row">
                <div class="invoice-info-cell">
                    <strong>Order ID:</strong> #{{ $order->id }}
                </div>
                <div class="invoice-info-cell">
                    <strong>Status:</strong> {{ ucfirst($order->status) }}
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="section-title">Customer Information</div>
        <div class="invoice-info">
            <div class="invoice-info-row">
                <div class="invoice-info-cell">
                    <strong>Name:</strong> {{ $order->user->name }}
                </div>
                <div class="invoice-info-cell">
                    <strong>Email:</strong> {{ $order->user->email }}
                </div>
            </div>
            <div class="invoice-info-row">
                <div class="invoice-info-cell">
                    <strong>Phone:</strong> {{ $order->user->phone }}
                </div>
                <div class="invoice-info-cell">
                    <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        @if($order->address)
        <div class="section-title">Shipping Address</div>
        <p><strong>Street:</strong> {{ $order->address->street ?? '' }}</p>
        <p><strong>House Number:</strong> {{ $order->address->house_number ?? '' }}</p>
        @endif

        <!-- Order Items -->
        <div class="section-title">Order Details</div>
        <table>
            <thead>
                <tr>
                    <th>Design</th>
                    <th>Measurement</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td>{{ $item->design->name['en'] ?? 'N/A' }}</td>
                    <td>{{ $item->measurement->name ?? 'Custom' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                @if($order->total_before_discount)
                <tr>
                    <td>Subtotal:</td>
                    <td>{{ number_format($order->total_before_discount, 2) }}</td>
                </tr>
                @endif
                @if($order->discount_amount > 0)
                <tr>
                    <td>Discount @if($order->coupon)({{ $order->coupon->code }})@endif:</td>
                    <td>-{{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td><strong>Grand Total:</strong></td>
                    <td><strong>{{ number_format($order->total, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        @if($order->note)
        <div class="section-title">Notes</div>
        <p>{{ $order->note }}</p>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business</p>
            <p>This invoice was generated electronically</p>
        </div>
    </div>
</body>
</html>
