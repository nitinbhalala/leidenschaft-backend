<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your order is waiting — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
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
                            <td style="padding:32px 40px 28px; border-bottom:1px solid #F0EBE4; background:#1A1A1A;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <!-- Logo / Brand -->
                                        <td style="vertical-align:middle; width:55%;">
                                            <img src="{{ asset('Logo-White.png') }}" alt="{{ config('app.name') }}"
                                                width="160" height="40"
                                                style="display:block; width:160px; height:40px; object-fit:contain;"
                                                onerror="this.style.display='none'; document.getElementById('brand-text-hdr').style.display='block';" />
                                            <div id="brand-text-hdr"
                                                style="display:none; font-family:'Playfair Display', Georgia, serif; font-size:22px; font-weight:600; color:#FFFFFF; letter-spacing:1px;">
                                                {{ config('app.name') }}
                                            </div>
                                            <p
                                                style="margin:8px 0 0; font-size:10.5px; color:#ffffff; letter-spacing:1.5px; text-transform:uppercase;">
                                                Built to endure, designed to inspire.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  HERO MESSAGE                               -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:40px 40px 32px; background:#FDFCFB; border-bottom:1px solid #F0EBE4;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td align="center">

                                            <!-- Decorative icon ring -->
                                            <div
                                                style="width:72px; height:72px; border-radius:50%; background:#FFF8F0; border:1.5px solid #E8D9C5; margin:0 auto 22px; text-align:center; line-height:70px;">
                                                <span style="font-size:30px; line-height:72px;">🛋️</span>
                                            </div>

                                            <p
                                                style="margin:0 0 12px; font-family:'Playfair Display', Georgia, serif; font-size:30px; font-weight:500; color:#1A1A1A; letter-spacing:0.5px; line-height:1.3;">
                                                Your selection<br />
                                                <em style="font-weight:400; color:#A3937B;">is still waiting.</em>
                                            </p>

                                            <p
                                                style="margin:0; font-size:13.5px; color:#6B6560; line-height:1.9; max-width:480px; margin:12px auto 0;">
                                                Hello <strong
                                                    style="color:#1A1A1A;">{{ $order->customer_name }}</strong> — you
                                                left something
                                                beautiful behind. The pieces you chose reflect a distinct sense of space
                                                and living. All that's left is one small step.
                                            </p>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  ORDER REF STRIP                            -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:16px 40px; background:#F7F3EE; border-bottom:1px solid #EDE8E2;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="vertical-align:middle;">
                                            <p
                                                style="margin:0; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                Order Reference
                                            </p>
                                            <p
                                                style="margin:4px 0 0; font-family:'Playfair Display', Georgia, serif; font-size:15px; font-weight:600; color:#1A1A1A;">
                                                #{{ $order->order_number }}
                                            </p>
                                        </td>
                                        <td style="vertical-align:middle; text-align:right;">
                                            <p
                                                style="margin:0; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                Date
                                            </p>
                                            <p style="margin:4px 0 0; font-size:12px; color:#6B6560; font-weight:500;">
                                                {{ ($order->placed_at ?? $order->created_at)->format('d M Y') }}
                                            </p>
                                        </td>
                                        <td style="vertical-align:middle; text-align:right;">
                                            <p
                                                style="margin:0; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                Status
                                            </p>
                                            <p style="margin:4px 0 0;">
                                                <span
                                                    style="display:inline-block; padding:3px 10px; background:#FFF8EB; color:#B07D1A; font-size:9px; font-weight:700; letter-spacing:2px; border-radius:2px; text-transform:uppercase;">
                                                    PENDING
                                                </span>
                                            </p>
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
                                            Your Selected Pieces
                                        </td>
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:center; width:15%;">
                                            Unit Price
                                        </td>
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:center; width:10%;">
                                            Qty
                                        </td>
                                        <td
                                            style="padding:20px 0 12px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase; text-align:right; width:25%;">
                                            Amount
                                        </td>
                                    </tr>

                                    <!-- Items -->
                                    @foreach ($products as $item)
                                        <tr style="border-bottom:1px solid #F5F2EE;">
                                            <!-- Product name + image -->
                                            <td style="padding:14px 16px 14px 0; vertical-align:middle;">
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <!-- Thumbnail -->
                                                        <td style="vertical-align:middle; padding-right:14px;">
                                                            @if (!empty($item->image))
                                                                <img src="{{ Str::startsWith($item->image, ['http://', 'https://']) ? $item->image : config('app.url') . '/storage/' . $item->image }}"
                                                                    width="52" height="52"
                                                                    style="width:52px; height:52px; object-fit:cover; display:block; border-radius:2px; border:1px solid #EDE8E2;"
                                                                    alt="{{ $item->name }}" />
                                                            @else
                                                                <div
                                                                    style="width:52px; height:52px; background:#F5F2EE; display:table-cell; vertical-align:middle; text-align:center; border-radius:2px; border:1px solid #EDE8E2;">
                                                                    <span
                                                                        style="font-size:8px; color:#C0B8B0; letter-spacing:0.5px; display:block; padding-top:18px;">IMG</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <!-- Name -->
                                                        <td style="vertical-align:middle;">
                                                            <p
                                                                style="margin:0; font-family:'Playfair Display', Georgia, serif; font-size:13.5px; font-weight:500; color:#1A1A1A; line-height:1.4;">
                                                                {{ $item->name }}
                                                            </p>
                                                            <p
                                                                style="margin:3px 0 0; font-size:10px; color:#B0A898; letter-spacing:1px; text-transform:uppercase;">
                                                                Awaiting Payment
                                                            </p>
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
                        <!--  TOTALS + CTA ROW                           -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:28px 40px 32px; border-top:1px solid #EDE8E2; background:#FDFCFB;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>

                                        <!-- CTA Box -->
                                        <td style="vertical-align:top; padding-right:28px;">
                                            <div
                                                style="border:1px dashed #D4C9BB; border-radius:3px; padding:20px 22px; background:#FFFFFF; text-align:center;">
                                                <p
                                                    style="margin:0 0 6px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                    Secure Checkout
                                                </p>
                                                <p
                                                    style="margin:0 0 18px; font-size:11.5px; color:#8B8580; line-height:1.8;">
                                                    Complete your purchase safely<br />via Razorpay — takes under a
                                                    minute.
                                                </p>
                                                <!-- CTA Button -->
                                                <a href="{{ $retryUrl }}"
                                                    style="display:inline-block; padding:14px 36px; background:#1A1A1A; color:#FFFFFF; text-decoration:none; font-size:11px; font-weight:700; letter-spacing:2.5px; text-transform:uppercase; border-radius:2px;">
                                                    Complete My Order
                                                </a>
                                                <p
                                                    style="margin:12px 0 0; font-size:10px; color:#C0B8B0; letter-spacing:0.5px;">
                                                    🔒 &nbsp;256-bit SSL Encrypted · Powered by Razorpay
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Summary box -->
                                        <td style="vertical-align:top; width:220px;">
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
                        <!--  BRAND PROMISE STRIP                        -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td
                                style="padding:22px 40px; background:#F7F3EE; border-top:1px solid #EDE8E2; border-bottom:1px solid #EDE8E2;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="text-align:center;">
                                            <p
                                                style="margin:0; font-family:'Playfair Display', Georgia, serif; font-size:13px; font-style:italic; color:#8B8075; line-height:1.8;">
                                                Every piece at {{ config('app.name') }} is crafted with intention
                                                —<br />
                                                designed to outlast trends and elevate the spaces you live in.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  HELP ROW                                   -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:20px 40px; background:#FDFCFB;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="text-align:center;">
                                            <p
                                                style="margin:0; font-size:11px; color:#9C9589; line-height:1.9; letter-spacing:0.3px;">
                                                Questions? We're always here.
                                                &nbsp;·&nbsp;
                                                <a href="mailto:{{ getSetting('store_email') }}"
                                                    style="color:#A3937B; text-decoration:none;">{{ getSetting('store_email') }}</a>
                                                &nbsp;·&nbsp;
                                                <span style="color:#9C9589;">+91
                                                    {{ getSetting('store_phone') }}</span>
                                            </p>
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
                                                © {{ date('Y') }} {{ config('app.name') }} Curated Designs
                                            </p>
                                        </td>
                                        <td style="vertical-align:middle; text-align:right;">
                                            <a href="https://www.leidenschaft.in"
                                                style="font-size:9.5px; color:#ffffff !important; letter-spacing:1.5px; margin-right:20px; text-decoration:none;">
                                                www.leidenschaft.in
                                            </a>
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
