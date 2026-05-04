<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice - {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Mulish:wght@300;600;700;800&display=swap"
        rel="stylesheet" />
</head>

<body
    style="box-sizing:border-box; margin:0; padding:48px 16px; min-height:100vh; background-color:#F9F7F5; font-family:'DM Sans',sans-serif; color:#242424;">

    <!-- Wrapper -->
    <div style="max-width:896px; margin:0 auto;">

        <!-- Invoice Card -->
        <div
            style="background:#ffffff; box-shadow:0 20px 60px rgba(0,0,0,0.10), 0 6px 20px rgba(0,0,0,0.05); border-radius:2px; overflow:hidden;">

            <!-- Header -->
            <div
                style="padding:40px 32px; display:flex; justify-content:space-between; align-items:flex-start; border-bottom:1px solid #f5f5f5; flex-wrap:wrap; gap:24px;">
                <div>
                    <a href="/"
                        style="font-family:'Mulish',sans-serif; font-weight:800; font-size:20px; letter-spacing:3px; color:#242424; text-transform:uppercase; text-decoration:none;">{{ config('app.name') }}</a>
                    <p style="margin-top:16px; font-size:13px; color:#8B8B8B; max-width:280px; line-height:1.6;">Built
                        to endure, designed to inspire. High-quality curated furniture for your space.</p>
                </div>
                <div style="text-align:right;">
                    <h1
                        style="font-family:'Mulish',sans-serif; font-size:40px; font-weight:300; color:#242424; letter-spacing:3px; margin:0;">
                        Invoice</h1>
                    <p style="margin-top:8px; font-size:14px; color:#A3937B; font-weight:500; letter-spacing:2px;">
                        #{{ $order->order_number }}</p>
                </div>
            </div>

            <!-- Info Grid -->
            <div
                style="padding:32px; display:grid; grid-template-columns:1fr 1fr; gap:32px; border-bottom:1px solid #fafafa;">

                <!-- Billed To -->
                <div>
                    <span
                        style="font-size:11px; letter-spacing:1.8px; color:#A3937B; font-weight:700; display:block; margin-bottom:12px; text-transform:uppercase;">Billed
                        To</span>
                    <p
                        style="font-family:'Mulish',sans-serif; font-weight:600; font-size:16px; color:#242424; margin:0 0 4px 0;">
                        {{ $order->customer_name }}</p>
                    <p style="font-size:14px; color:#8B8B8B; line-height:1.6; margin:0 0 2px 0;">
                        {{ $order->customer_email }}
                    </p>
                    <p style="font-size:14px; color:#8B8B8B; line-height:1.6; margin:0 0 2px 0;">+91
                        {{ $order->customer_phone }}</p>
                    <p style="margin-top:8px; font-size:14px; color:#8B8B8B; line-height:1.7; margin:8px 0 0 0;">
                        {{ $order->shipping_address }},<br>
                        {{ $order->city }},<br>
                        {{ $order->state }} — {{ $order->postal_code }},<br>
                        {{ $order->country }}
                    </p>
                </div>

                <!-- Invoice Details -->
                <div style="text-align:right;">
                    <span
                        style="font-size:11px; letter-spacing:1.8px; color:#A3937B; font-weight:700; display:block; margin-bottom:12px; text-transform:uppercase;">Invoice
                        Details</span>
                    <div style="display:flex; justify-content:flex-end; gap:16px; margin-bottom:8px;">
                        <span style="font-size:14px; color:#8B8B8B;">Date Issued:</span>
                        <span
                            style="font-size:14px; color:#242424; font-weight:500;">{{ ($order->placed_at ?? $order->created_at)->format('d M Y, h:i A') }}</span>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:16px; margin-bottom:8px;">
                        <span style="font-size:14px; color:#8B8B8B;">Order ID:</span>
                        <span style="font-size:14px; color:#242424; font-weight:500;">{{ $order->order_number }}</span>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:16px; margin-bottom:8px;">
                        <span style="font-size:14px; color:#8B8B8B;">Payment Method:</span>
                        <span style="font-size:14px; color:#242424; font-weight:500;">
                            @if ($order->payment && $order->payment->method)
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
                </div>

            </div>

            <!-- Product Table -->
            <div style="padding:32px;">
                <table style="width:100%; border-collapse:collapse; text-align:left;">
                    <thead>
                        <tr style="border-bottom:2px solid #f0f0f0;">
                            <th
                                style="padding:16px 0; font-size:11px; letter-spacing:1.8px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                Product</th>
                            <th
                                style="padding:16px; font-size:11px; letter-spacing:1.8px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:center;">
                                Price</th>
                            <th
                                style="padding:16px; font-size:11px; letter-spacing:1.8px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:center;">
                                Qty</th>
                            <th
                                style="padding:16px 0; font-size:11px; letter-spacing:1.8px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:right;">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr style="border-bottom:1px solid #fafafa;">
                                <td style="padding:24px 0; font-size:14px; color:#242424; vertical-align:middle;">
                                    <div style="display:flex; align-items:center; gap:20px;">
                                        @if ($item->product && $item->product->images->isNotEmpty())
                                            <img src="{{ $item->product->images->first()->image }}"
                                                style="width:80px; height:80px; object-fit:cover; flex-shrink:0;"
                                                alt="{{ $item->product->name }}" />
                                        @else
                                            <div
                                                style="width:80px; height:80px; background:#f5f5f5; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:10px; color:#cccccc;">
                                                No Image</div>
                                        @endif
                                        <div>
                                            <p
                                                style="font-family:'Mulish',sans-serif; font-weight:600; font-size:15px; color:#242424; margin:0 0 4px 0;">
                                                {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                            </p>
                                            <p style="font-size:12px; color:#8B8B8B; margin:0;">ID:
                                                {{ $item->product_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    style="padding:24px 16px; font-size:14px; color:#242424; vertical-align:middle; text-align:center;">
                                    ₹ {{ number_format($item->price, 2) }}</td>
                                <td
                                    style="padding:24px 16px; font-size:14px; color:#242424; vertical-align:middle; text-align:center;">
                                    {{ $item->quantity }}</td>
                                <td
                                    style="padding:24px 0; font-size:14px; color:#242424; vertical-align:middle; text-align:right; font-weight:600;">
                                    ₹ {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div
                style="padding:40px 32px; background:rgba(249,247,245,0.4); display:flex; justify-content:space-between; gap:40px; flex-wrap:wrap; border-top:1px solid #f0f0f0;">

                <!-- Terms -->
                <div
                    style="flex:1; min-width:240px; padding:24px; border:1px dashed rgba(163,147,123,0.3); border-radius:2px; background:rgba(255,255,255,0.5);">
                    <h4
                        style="font-size:12px; letter-spacing:3px; color:#A3937B; font-weight:700; margin:0 0 8px 0; text-transform:uppercase;">
                        Terms &amp; Notes</h4>
                    <p style="font-size:12px; color:#8B8B8B; line-height:1.8; margin:0;">
                        Thank you for choosing Leidenschaft. All items are crafted with precision.
                        Please keep this invoice for your records. For any queries regarding delivery
                        or assembly, contact us at info@leidenschaft.in.
                    </p>
                </div>

                <!-- Totals -->
                <div style="width:320px; flex-shrink:0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <span style="font-size:14px; color:#8B8B8B;">Subtotal</span>
                        <span style="font-size:14px; color:#242424;">₹ {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <span style="font-size:14px; color:#8B8B8B;">Shipping</span>
                        <span style="font-size:14px; color:#242424;">
                            @if ($order->shipping > 0)
                                ₹ {{ number_format($order->shipping, 2) }}
                            @else
                                Free
                            @endif
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <span style="font-size:14px; color:#8B8B8B;">Tax (GST 18%)</span>
                        <span style="font-size:14px; color:#242424;">₹ {{ number_format($order->tax, 2) }}</span>
                    </div>
                    <hr style="border:none; border-top:1px solid #e5e5e5; margin:16px 0;" />
                    <div style="display:flex; justify-content:space-between; align-items:center; padding-top:4px;">
                        <span
                            style="font-family:'Mulish',sans-serif; font-weight:700; font-size:13px; letter-spacing:2px; color:#242424; text-transform:uppercase;">Total
                            Amount</span>
                        <span style="font-family:'Mulish',sans-serif; font-weight:700; font-size:26px; color:#A3937B;">₹
                            {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div
                style="padding:24px 32px; background:#ffffff; border-top:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <p style="font-size:11px; color:#8B8B8B; letter-spacing:0.1em; margin:0;">© {{ date('Y') }}
                    {{ config('app.name') }} Curated
                    Designs</p>
                <div style="display:flex; gap:24px;">
                    <span style="font-size:11px; color:#8B8B8B; letter-spacing:0.1em;">www.leidenschaft.in</span>
                    <span style="font-size:11px; color:#8B8B8B; letter-spacing:0.1em;">+91 98993 17185</span>
                </div>
            </div>

        </div>
        <!-- /invoice-card -->
    </div>

</body>

</html>
