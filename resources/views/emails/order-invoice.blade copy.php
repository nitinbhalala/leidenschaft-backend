<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
        }

        .container {
            max-width: 640px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #1a1a2e;
            padding: 32px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 22px;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .header p {
            color: #aaaaaa;
            font-size: 13px;
        }

        .invoice-badge {
            background-color: #f0f0f0;
            border-bottom: 1px solid #e0e0e0;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-badge-left h2 {
            font-size: 18px;
            color: #1a1a2e;
        }

        .invoice-badge-left p {
            font-size: 13px;
            color: #777777;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: capitalize;
            background-color: #d4edda;
            color: #155724;
        }

        .body {
            padding: 32px;
        }

        .section {
            margin-bottom: 28px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999999;
            margin-bottom: 10px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 6px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-size: 13px;
            color: #777777;
            padding: 4px 0;
            width: 140px;
        }

        .info-value {
            display: table-cell;
            font-size: 13px;
            color: #333333;
            font-weight: 500;
            padding: 4px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table.items thead tr {
            background-color: #f8f8f8;
        }

        table.items th {
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #777777;
            border-bottom: 2px solid #eeeeee;
        }

        table.items th.right,
        table.items td.right {
            text-align: right;
        }

        table.items td {
            padding: 12px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .item-name {
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 2px;
        }

        .item-qty {
            font-size: 12px;
            color: #999999;
        }

        .totals-table {
            width: 100%;
            font-size: 13px;
            margin-top: 8px;
        }

        .totals-table td {
            padding: 6px 0;
        }

        .totals-table td.label {
            color: #777777;
        }

        .totals-table td.value {
            text-align: right;
            font-weight: 500;
        }

        .totals-table tr.total-row td {
            border-top: 2px solid #eeeeee;
            padding-top: 12px;
            font-size: 15px;
            font-weight: bold;
            color: #1a1a2e;
        }

        .thank-you {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin-top: 8px;
        }

        .thank-you p {
            font-size: 14px;
            color: #555555;
            line-height: 1.6;
        }

        .footer {
            background-color: #1a1a2e;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #aaaaaa;
        }

        .footer a {
            color: #cccccc;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- Header --}}
        <div class="header">
            <h1>{{ strtoupper(config('app.name')) }}</h1>
            <p>Official Order Invoice</p>
        </div>

        {{-- Invoice Badge --}}
        <div class="invoice-badge" style="padding: 16px 32px; background-color: #f8f8f8; border-bottom: 1px solid #e0e0e0;">
            <div>
                <h2 style="font-size: 18px; color: #1a1a2e;">Invoice</h2>
                <p style="font-size: 13px; color: #777777; margin-top: 2px;">{{ $order->order_number }}</p>
            </div>
            <span class="status-badge">{{ ucfirst($order->status) }}</span>
        </div>

        <div class="body">

            {{-- Order Info --}}
            <div class="section">
                <p class="section-title">Order Details</p>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Order Date</span>
                        <span class="info-value">
                            {{ ($order->placed_at ?? $order->created_at)->format('d M Y, h:i A') }}
                        </span>
                    </div>
                    <div class="info-row" style="display: table-row;">
                        <span class="info-label" style="display: table-cell; font-size: 13px; color: #777777; padding: 4px 0; width: 140px;">Payment Method</span>
                        <span class="info-value" style="display: table-cell; font-size: 13px; color: #333333; font-weight: 500; padding: 4px 0;">
                            @if($order->payment && $order->payment->method)
                            @php
                            $methodMap = [
                            'card' => 'Razorpay (Credit Card)',
                            'upi' => 'Razorpay (UPI)',
                            'netbanking' => 'Razorpay (Net Banking)',
                            'wallet' => 'Razorpay (Wallet)',
                            'emi' => 'Razorpay (EMI)',
                            ];
                            @endphp
                            {{ $methodMap[$order->payment->method] ?? 'Razorpay (' . ucfirst($order->payment->method) . ')' }}
                            @else
                            Razorpay
                            @endif
                        </span>
                    </div>
                    @if($order->payment && $order->payment->payment_id)
                    <div class="info-row" style="display: table-row;">
                        <span class="info-label" style="display: table-cell; font-size: 13px; color: #777777; padding: 4px 0; width: 140px;">Payment ID</span>
                        <span class="info-value" style="display: table-cell; font-size: 13px; color: #333333; font-weight: 500; padding: 4px 0;">
                            {{ $order->payment->payment_id }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="section">
                <p class="section-title">Billing & Shipping</p>
                <div class="info-grid">
                    <div class="info-row" style="display: table-row;">
                        <span class="info-label" style="display: table-cell; font-size: 13px; color: #777777; padding: 4px 0; width: 140px;">Name</span>
                        <span class="info-value" style="display: table-cell; font-size: 13px; color: #333333; font-weight: 500; padding: 4px 0;">{{ $order->customer_name }}</span>
                    </div>
                    <div class="info-row" style="display: table-row;">
                        <span class="info-label" style="display: table-cell; font-size: 13px; color: #777777; padding: 4px 0; width: 140px;">Email</span>
                        <span class="info-value" style="display: table-cell; font-size: 13px; color: #333333; font-weight: 500; padding: 4px 0;">{{ $order->customer_email }}</span>
                    </div>
                    <div class="info-row" style="display: table-row;">
                        <span class="info-label" style="display: table-cell; font-size: 13px; color: #777777; padding: 4px 0; width: 140px;">Phone</span>
                        <span class="info-value" style="display: table-cell; font-size: 13px; color: #333333; font-weight: 500; padding: 4px 0;">{{ $order->customer_phone }}</span>
                    </div>
                    <div class="info-row" style="display: table-row;">
                        <span class="info-label" style="display: table-cell; font-size: 13px; color: #777777; padding: 4px 0; width: 140px;">Address</span>
                        <span class="info-value" style="display: table-cell; font-size: 13px; color: #333333; font-weight: 500; padding: 4px 0;">
                            {{ $order->shipping_address }},
                            {{ $order->city }},
                            {{ $order->state }} - {{ $order->postal_code }},
                            {{ $order->country }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="section">
                <p class="section-title">Order Items</p>
                <table class="items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="right">Price</th>
                            <th class="right">Qty</th>
                            <th class="right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="item-name">
                                    {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                </div>
                                <div class="item-qty">SKU: {{ $item->product?->sku ?? '—' }}</div>
                            </td>
                            <td class="right">&#8377;{{ number_format($item->price, 2) }}</td>
                            <td class="right">{{ $item->quantity }}</td>
                            <td class="right">&#8377;{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="section" style="max-width: 300px; margin-left: auto;">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">&#8377;{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax (18% GST)</td>
                        <td class="value">&#8377;{{ number_format($order->tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Shipping</td>
                        <td class="value">
                            @if($order->shipping > 0)
                            &#8377;{{ number_format($order->shipping, 2) }}
                            @else
                            <span style="color: #28a745;">Free</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">Total</td>
                        <td class="value">&#8377;{{ number_format($order->total, 2) }}</td>
                    </tr>
                </table>
            </div>

            {{-- Thank You --}}
            <div class="thank-you">
                <p>Thank you for your order, <strong>{{ $order->customer_name }}</strong>!</p>
                <p style="margin-top: 6px; font-size: 13px; color: #888888;">
                    If you have any questions about your order, reply to this email or contact our support team.
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin-top: 6px;">This is an automatically generated invoice. Please do not reply directly.</p>
        </div>

    </div>
</body>

</html>