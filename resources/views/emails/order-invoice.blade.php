<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice — {{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet" />
</head>

<body
    style="margin:0; padding:0; background-color:#F5F0EB; font-family:'DM Sans', Arial, sans-serif; -webkit-font-smoothing:antialiased;">

    <div style="padding:40px 16px 60px;">

        <!-- Outer wrapper -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:680px; margin:0 auto;">
            <tr>
                <td>

                    <!-- Main card -->
                    <table width="100%" cellpadding="0" cellspacing="0" border="0"
                        style="background:#FFFFFF; border-radius:3px; overflow:hidden; box-shadow:0 4px 6px rgba(0,0,0,0.04), 0 20px 60px rgba(0,0,0,0.08);">

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  DECORATIVE TOP BAR                        -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td
                                style="height:4px; background:linear-gradient(90deg, #C4A882 0%, #A3937B 40%, #8B7355 100%);">
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  HEADER                                     -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:32px 40px 28px; border-bottom:1px solid #F0EBE4;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <!-- Logo / Brand -->
                                        <td style="vertical-align:top; width:55%;">
                                            <!-- Logo image – falls back to text if broken -->
                                            <img src="{{ asset('Logo-Black.png') }}" alt="Leidenschaft" width="160"
                                                height="40"
                                                style="display:block; width:160px; height:40px; object-fit:contain;"
                                                onerror="this.style.display='none'; document.getElementById('brand-text').style.display='block';" />
                                            <div id="brand-text"
                                                style="display:none; font-family:'Playfair Display', Georgia, serif; font-size:22px; font-weight:600; color:#1A1A1A; letter-spacing:1px;">
                                                Leidenschaft</div>
                                            <p
                                                style="margin:10px 0 0; font-size:11.5px; color:#9C9589; line-height:1.75; max-width:260px;">
                                                Built to endure, designed to inspire.<br />High-quality curated
                                                furniture.
                                            </p>
                                        </td>
                                        <!-- Invoice title -->
                                        <td style="vertical-align:top; text-align:right;">
                                            <p
                                                style="font-family:'Playfair Display', Georgia, serif; font-size:36px; font-weight:400; color:#1A1A1A; letter-spacing:3px; margin:0 0 6px;">
                                                INVOICE
                                            </p>
                                            <p
                                                style="margin:0; font-size:12px; font-weight:500; color:#A3937B; letter-spacing:2.5px;">
                                                #{{ $order->order_number }}
                                            </p>
                                            <p style="margin:8px 0 0;">
                                                @php
                                                    $statusColors = [
                                                        'paid' => [
                                                            'bg' => '#EDFAF3',
                                                            'color' => '#1D7A4A',
                                                            'label' => 'PAID',
                                                        ],
                                                        'pending' => [
                                                            'bg' => '#FFF8EB',
                                                            'color' => '#B07D1A',
                                                            'label' => 'PENDING',
                                                        ],
                                                        'cancelled' => [
                                                            'bg' => '#FFF0F0',
                                                            'color' => '#C0392B',
                                                            'label' => 'CANCELLED',
                                                        ],
                                                    ];
                                                    $sc =
                                                        $statusColors[$order->payment_status ?? 'paid'] ??
                                                        $statusColors['paid'];
                                                @endphp
                                                <span
                                                    style="display:inline-block; padding:4px 12px; background:{{ $sc['bg'] }}; color:{{ $sc['color'] }}; font-size:9.5px; font-weight:700; letter-spacing:2px; border-radius:2px;">
                                                    {{ $sc['label'] }}
                                                </span>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  BILLING + INVOICE DETAILS                  -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:28px 40px; border-bottom:1px solid #F0EBE4; background:#FDFCFB;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <!-- Billed To -->
                                        <td style="vertical-align:top; width:50%; padding-right:24px;">
                                            <p
                                                style="margin:0 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                Billed To
                                            </p>
                                            <p
                                                style="margin:0 0 3px; font-family:'Playfair Display', Georgia, serif; font-size:15px; font-weight:600; color:#1A1A1A;">
                                                {{ $order->customer_name }}
                                            </p>
                                            <p style="margin:0 0 3px; font-size:12px; color:#6B6560; line-height:1.6;">
                                                {{ $order->customer_email }}
                                            </p>
                                            <p style="margin:0 0 10px; font-size:12px; color:#6B6560; line-height:1.6;">
                                                {{ $order->customer_phone }}
                                            </p>
                                            <p style="margin:0; font-size:11.5px; color:#8B8580; line-height:1.9;">
                                                {{ $order->shipping_address }},<br />
                                                {{ $order->city }},<br />
                                                {{ $order->state }} — {{ $order->postal_code }},<br />
                                                {{ $order->country }}
                                            </p>
                                        </td>

                                        <!-- Divider -->
                                        <td style="width:1px; background:#EDE8E2; padding:0;"></td>

                                        <!-- Invoice Details -->
                                        <td style="vertical-align:top; padding-left:28px;">
                                            <p
                                                style="margin:0 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                Invoice Details
                                            </p>
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td
                                                        style="font-size:11.5px; color:#9C9589; padding-bottom:8px; white-space:nowrap;">
                                                        Date Issued</td>
                                                    <td
                                                        style="font-size:11.5px; color:#1A1A1A; font-weight:500; padding-bottom:8px; padding-left:12px; text-align:right;">
                                                        {{ ($order->placed_at ?? $order->created_at)->format('d M Y') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-size:11.5px; color:#9C9589; padding-bottom:8px; white-space:nowrap;">
                                                        Time</td>
                                                    <td
                                                        style="font-size:11.5px; color:#1A1A1A; font-weight:500; padding-bottom:8px; padding-left:12px; text-align:right;">
                                                        {{ ($order->placed_at ?? $order->created_at)->format('h:i A') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-size:11.5px; color:#9C9589; padding-bottom:8px; white-space:nowrap;">
                                                        Order ID</td>
                                                    <td
                                                        style="font-size:11px; color:#1A1A1A; font-weight:600; padding-bottom:8px; padding-left:12px; text-align:right; letter-spacing:0.5px;">
                                                        {{ $order->order_number }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-size:11.5px; color:#9C9589; padding-bottom:0; white-space:nowrap;">
                                                        Payment</td>
                                                    <td
                                                        style="font-size:11.5px; color:#1A1A1A; font-weight:500; padding-left:12px; text-align:right;">
                                                        @php
                                                            $methodMap = [
                                                                'card' => 'Razorpay · Card',
                                                                'upi' => 'Razorpay · UPI',
                                                                'netbanking' => 'Razorpay · Net Banking',
                                                                'wallet' => 'Razorpay · Wallet',
                                                                'emi' => 'Razorpay · EMI',
                                                            ];
                                                            $method = $order->payment?->method;
                                                        @endphp
                                                        {{ $method ? $methodMap[$method] ?? 'Razorpay · ' . ucfirst($method) : 'Razorpay' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  PRODUCT TABLE                              -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:0 40px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                    <!-- Table head -->
                                    <tr style="border-bottom:2px solid #EDE8E2;">
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; width:50%;">
                                            Product
                                        </td>
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:center; width:20%;">
                                            Unit Price
                                        </td>
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:center; width:10%;">
                                            Qty
                                        </td>
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:right; width:20%;">
                                            Amount
                                        </td>
                                    </tr>

                                    <!-- Items -->
                                    @foreach ($order->items as $item)
                                        <tr style="border-bottom:1px solid #F5F2EE;">
                                            <!-- Product name + image -->
                                            <td style="padding:14px 16px 14px 0; vertical-align:middle;">
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <!-- Thumbnail -->
                                                        <td style="vertical-align:middle; padding-right:14px;">
                                                            @if ($item->product && $item->product->images->isNotEmpty())
                                                                <img src="{{ $item->product->images->first()->image }}"
                                                                    width="52" height="52"
                                                                    style="width:52px; height:52px; object-fit:cover; display:block; border-radius:2px; border:1px solid #EDE8E2;"
                                                                    alt="{{ $item->product->name }}"
                                                                    onerror="this.outerHTML='<div style=\'width:52px;height:52px;background:#F5F2EE;display:flex;align-items:center;justify-content:center;border-radius:2px;border:1px solid #EDE8E2;\'><span style=\'font-size:8px;color:#C0B8B0;letter-spacing:0.5px;\'>IMG</span></div>'" />
                                                            @else
                                                                <div
                                                                    style="width:52px; height:52px; background:#F5F2EE; display:table-cell; vertical-align:middle; text-align:center; border-radius:2px; border:1px solid #EDE8E2;">
                                                                    <span
                                                                        style="font-size:8px; color:#C0B8B0; letter-spacing:0.5px;">IMG</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <!-- Name -->
                                                        <td style="vertical-align:middle;">
                                                            <p
                                                                style="margin:0; font-family:'Playfair Display', Georgia, serif; font-size:13.5px; font-weight:500; color:#1A1A1A; line-height:1.4;">
                                                                {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                                            </p>
                                                            @if ($item->product && $item->product->sku)
                                                                <p
                                                                    style="margin:3px 0 0; font-size:10px; color:#B0A898; letter-spacing:1px;">
                                                                    SKU: {{ $item->product->sku }}
                                                                </p>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <!-- Unit price -->
                                            <td style="padding:14px 8px; text-align:center; vertical-align:middle;">
                                                <span style="font-size:12.5px; color:#4A4540;">₹
                                                    {{ number_format((float) $item->price, 2) }}</span>
                                            </td>
                                            <!-- Qty -->
                                            <td style="padding:14px 8px; text-align:center; vertical-align:middle;">
                                                <span
                                                    style="display:inline-block; width:28px; height:28px; line-height:28px; background:#F5F2EE; border-radius:50%; font-size:12px; color:#4A4540; font-weight:600; text-align:center;">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <!-- Total -->
                                            <td style="padding:14px 0; text-align:right; vertical-align:middle;">
                                                <span
                                                    style="font-family:'Playfair Display', Georgia, serif; font-size:14px; font-weight:600; color:#1A1A1A;">
                                                    ₹ {{ number_format((float) $item->price * $item->quantity, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  TOTALS + TERMS ROW                         -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:28px 40px 32px; border-top:1px solid #EDE8E2; background:#FDFCFB;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>

                                        <!-- Terms & Notes -->
                                        <td style="vertical-align:top; padding-right:32px;">
                                            <div
                                                style="border:1px dashed #D4C9BB; border-radius:3px; padding:18px 20px; background:#FFFFFF;">
                                                <p
                                                    style="margin:0 0 8px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                    Terms &amp; Notes
                                                </p>
                                                <p style="margin:0; font-size:11px; color:#8B8580; line-height:1.9;">
                                                    Thank you for choosing Leidenschaft. All items are crafted with
                                                    precision and care.
                                                    Please keep this invoice for your records.
                                                    For any queries, contact us at
                                                    <span
                                                        style="color:#A3937B;">{{ getSetting('store_email') }}</span>
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Summary box -->
                                        <td style="vertical-align:top; width:240px;">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <!-- Subtotal -->
                                                <tr>
                                                    <td style="font-size:12px; color:#8B8580; padding-bottom:10px;">
                                                        Subtotal</td>
                                                    <td
                                                        style="font-size:12px; color:#2A2420; text-align:right; padding-bottom:10px;">
                                                        ₹ {{ number_format((float) $order->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                                <!-- Shipping -->
                                                <tr>
                                                    <td style="font-size:12px; color:#8B8580; padding-bottom:10px;">
                                                        Shipping</td>
                                                    <td
                                                        style="font-size:12px; color:#2A2420; text-align:right; padding-bottom:10px;">
                                                        @if ((float) $order->shipping == 0)
                                                            <span style="color:#1D7A4A; font-weight:600;">FREE</span>
                                                        @else
                                                            ₹ {{ number_format((float) $order->shipping, 2) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <!-- Tax -->
                                                <tr>
                                                    <td style="font-size:12px; color:#8B8580; padding-bottom:14px;">Tax
                                                        (GST 18%)</td>
                                                    <td
                                                        style="font-size:12px; color:#2A2420; text-align:right; padding-bottom:14px;">
                                                        ₹ {{ number_format((float) $order->tax, 2) }}
                                                    </td>
                                                </tr>
                                                <!-- Divider -->
                                                <tr>
                                                    <td colspan="2"
                                                        style="height:1px; background:#EDE8E2; padding:0; font-size:0;">
                                                    </td>
                                                </tr>
                                                <!-- Grand total -->
                                                <tr>
                                                    <td
                                                        style="padding-top:14px; font-size:10px; letter-spacing:2px; font-weight:700; color:#1A1A1A; text-transform:uppercase; font-family:'DM Sans', Arial, sans-serif;">
                                                        Total Amount
                                                    </td>
                                                    <td style="padding-top:14px; text-align:right;">
                                                        <span
                                                            style="font-family:'Playfair Display', Georgia, serif; font-size:22px; font-weight:600; color:#A3937B;">
                                                            ₹ {{ number_format((float) $order->total, 2) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>

                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  FOOTER                                     -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td
                                style="height:4px; background:linear-gradient(90deg, #C4A882 0%, #A3937B 40%, #8B7355 100%);">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:16px 40px; background:#1A1A1A;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="vertical-align:middle;">
                                            <p
                                                style="margin:0; font-size:9.5px; color:#ffffff; letter-spacing:2px; text-transform:uppercase;">
                                                © {{ date('Y') }} Leidenschaft Curated Designs
                                            </p>
                                        </td>
                                        <td style="vertical-align:middle; text-align:right;">
                                            <a href="https://www.leidenschaft.in"
                                                style="font-size:9.5px; color:#ffffff !important; letter-spacing:1.5px; margin-right:20px; text-decoration:none;">
                                                www.leidenschaft.in
                                            </a>
                                            <span style="font-size:9.5px; color:#ffffff; letter-spacing:1.5px;">+91
                                                {{ getSetting('store_phone') }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                    </table>
                    <!-- /Main card -->

                </td>
            </tr>
        </table>
        <!-- /Outer wrapper -->

    </div>

</body>

</html>
