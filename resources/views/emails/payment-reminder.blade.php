<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your selection is waiting — {{ config('app.name') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0ebe4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 620px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* ── Header ── */
        .header {
            background-color: #2c2620;
            padding: 36px 32px 28px;
            text-align: center;
        }

        .header .brand {
            color: #c8b49a;
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 0 0 10px;
        }

        .header h1 {
            color: #ffffff;
            margin: 0 0 8px;
            font-size: 26px;
            font-weight: normal;
            letter-spacing: 1px;
            line-height: 1.3;
        }

        .header .tagline {
            color: rgba(200, 180, 154, 0.8);
            font-size: 13px;
            margin: 0;
            letter-spacing: 0.5px;
            font-style: italic;
        }

        /* ── Hero strip ── */
        .hero-strip {
            background: linear-gradient(135deg, #A3937B 0%, #8a7a64 100%);
            padding: 18px 32px;
            text-align: center;
        }

        .hero-strip p {
            margin: 0;
            color: #fff;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* ── Body ── */
        .body {
            padding: 36px 32px 20px;
            color: #333333;
        }

        .body p {
            font-size: 15px;
            line-height: 1.7;
            margin: 0 0 16px;
            color: #444;
        }

        .greeting {
            font-size: 16px !important;
            color: #222 !important;
        }

        .lead {
            font-size: 15px;
            color: #555 !important;
            border-left: 3px solid #c8b49a;
            padding-left: 14px;
            margin: 20px 0 28px !important;
            font-style: italic;
        }

        /* ── Order Meta ── */
        .order-ref {
            display: inline-block;
            background-color: #faf8f5;
            border: 1px solid #e8e0d5;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 13px;
            color: #888;
            margin-bottom: 28px;
            letter-spacing: 0.5px;
        }

        .order-ref strong {
            color: #555;
        }

        /* ── Section title ── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: #A3937B;
            margin: 0 0 16px;
        }

        /* ── Product table ── */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .product-table td {
            padding: 14px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #f5f0ea;
        }

        .product-table tr:last-child td {
            border-bottom: none;
        }

        .product-img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 6px;
            display: block;
        }

        .product-img-placeholder {
            width: 72px;
            height: 72px;
            background-color: #f0ebe4;
            border-radius: 6px;
        }

        .product-name {
            font-size: 14px;
            font-weight: bold;
            color: #222;
            margin: 0 0 4px;
            letter-spacing: 0.3px;
        }

        .product-qty {
            font-size: 12px;
            color: #aaa;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .product-price {
            font-size: 15px;
            font-weight: bold;
            color: #A3937B;
            text-align: right;
            white-space: nowrap;
        }

        /* ── Total ── */
        .total-box {
            background-color: #2c2620;
            border-radius: 8px;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 32px;
        }

        .total-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #c8b49a;
        }

        .total-amount {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
        }

        /* ── CTA ── */
        .cta-section {
            text-align: center;
            margin: 0 0 32px;
        }

        .cta-hint {
            font-size: 13px;
            color: #aaa;
            margin: 0 0 16px;
        }

        .btn {
            display: inline-block;
            padding: 17px 48px;
            background-color: #A3937B;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-sub {
            display: block;
            font-size: 11px;
            color: #bbb;
            margin-top: 12px;
            letter-spacing: 0.5px;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid #f0ebe4;
            margin: 28px 0;
        }

        /* ── Brand promise ── */
        .promise {
            text-align: center;
            padding: 0 16px 4px;
        }

        .promise p {
            font-size: 13px !important;
            color: #999 !important;
            line-height: 1.6;
            margin: 0;
        }

        /* ── Note ── */
        .note {
            font-size: 12px;
            color: #bbb;
            text-align: center;
            padding: 0 16px;
            line-height: 1.6;
        }

        .note a {
            color: #A3937B;
            text-decoration: none;
        }

        /* ── Footer ── */
        .footer {
            background-color: #2c2620;
            text-align: center;
            padding: 20px 32px;
            margin-top: 32px;
        }

        .footer p {
            margin: 0;
            font-size: 11px;
            color: rgba(200, 180, 154, 0.6);
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- Header --}}
        <div class="header">
            <p class="brand">{{ config('app.name') }}</p>
            <h1>Your selection<br>is still waiting for you.</h1>
            <p class="tagline">Thoughtfully curated. Ready to be yours.</p>
        </div>

        {{-- Hero strip --}}
        <div class="hero-strip">
            <p>You left something special behind — and we kept it safe.</p>
        </div>

        {{-- Body --}}
        <div class="body">

            <p class="greeting">Hello <strong>{{ $order->customer_name }}</strong>,</p>

            <p class="lead">
                Great taste takes a moment to decide — and yours already has.
                The pieces you chose reflect a distinct sense of space and living.
                All that's left is one small step.
            </p>

            {{-- Order ref --}}
            <span class="order-ref">
                Order Ref: <strong>{{ $order->order_number }}</strong>
                &nbsp;&middot;&nbsp;
                {{ $order->placed_at?->format('d M Y') ?? $order->created_at->format('d M Y') }}
            </span>

            {{-- Products --}}
            <p class="section-title">Your Selected Pieces</p>

            @if ($products->isNotEmpty())
                <table class="product-table">
                    @foreach ($products as $item)
                        <tr>
                            <td style="width: 82px;">
                                @if ($item->image)
                                    <img src="{{ config('app.url') . '/storage/' . $item->image }}"
                                        alt="{{ $item->name }}" class="product-img" />
                                @else
                                    <div class="product-img-placeholder"></div>
                                @endif
                            </td>
                            <td style="padding-left: 14px;">
                                <p class="product-name">{{ $item->name }}</p>
                                <p class="product-qty">Qty &nbsp;{{ $item->quantity }}</p>
                            </td>
                            <td class="product-price">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif

            {{-- Total --}}
            <div class="total-box">
                <span class="total-label">Total Amount</span>
                <span class="total-amount">₹{{ number_format($order->total, 2) }}</span>
            </div>

            {{-- CTA --}}
            <div class="cta-section">
                <p class="cta-hint">Secure checkout &nbsp;·&nbsp; Powered by Razorpay</p>
                <a href="{{ $retryUrl }}" class="btn">Secure My Order</a>
                <span class="btn-sub">Takes less than a minute</span>
            </div>

            <hr class="divider">

            {{-- Brand promise --}}
            <div class="promise">
                <p>
                    Every piece at {{ config('app.name') }} is crafted with intention —<br>
                    designed to outlast trends and elevate the spaces you live in.
                </p>
            </div>

            <hr class="divider">

            <p class="note">
                Questions? We're here to help.<br>
                <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
                &nbsp;&middot;&nbsp; +91 98993 17185
            </p>

        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>

    </div>
</body>

</html>
