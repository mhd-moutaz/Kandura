<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            direction: rtl;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
        }
        .info-value {
            color: #111827;
        }
        .button {
            display: inline-block;
            background-color: #4F46E5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاتورة طلبك جاهزة</h1>
        <p>Your Invoice is Ready</p>
    </div>

    <div class="content">
        <p>عزيزي/عزيزتي {{ $order->user->name }},</p>
        <p>Dear {{ $order->user->name }},</p>

        <p>نشكرك على طلبك! تم إكمال طلبك بنجاح وفاتورتك جاهزة.</p>
        <p>Thank you for your order! Your order has been completed successfully and your invoice is ready.</p>

        <div style="margin: 20px 0;">
            <div class="info-row">
                <span class="info-label">رقم الطلب / Order ID:</span>
                <span class="info-value">#{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">رقم الفاتورة / Invoice Number:</span>
                <span class="info-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">المجموع / Total:</span>
                <span class="info-value">{{ number_format($order->total, 2) }} ر.س</span>
            </div>
            <div class="info-row">
                <span class="info-label">حالة الطلب / Status:</span>
                <span class="info-value">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <p>الفاتورة مرفقة بهذا البريد الإلكتروني بصيغة PDF.</p>
        <p>Your invoice is attached to this email as a PDF file.</p>
    </div>

    <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>Thank you for your business</p>
        <p style="margin-top: 10px;">
            <strong>كندورة / Kandura</strong>
        </p>
    </div>
</body>
</html>
