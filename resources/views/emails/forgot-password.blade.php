<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Your Password — {{ config('app.name') }}</title>
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
                                        <!-- Badge -->
                                        <td style="vertical-align:middle; text-align:right;">
                                            <span
                                                style="display:inline-block; padding:5px 14px; border:1px solid #ffffff; color:#ffffff; font-size:9px; font-weight:700; letter-spacing:2.5px; border-radius:2px; text-transform:uppercase;">
                                                Security Notice
                                            </span>
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

                                            <!-- Icon ring -->
                                            <div
                                                style="width:72px; height:72px; border-radius:50%; background:#FFF8F0; border:1.5px solid #E8D9C5; margin:0 auto 22px; text-align:center; line-height:72px;">
                                                <span style="font-size:30px; line-height:72px;">🔑</span>
                                            </div>

                                            <p
                                                style="margin:0 0 12px; font-family:'Playfair Display', Georgia, serif; font-size:30px; font-weight:500; color:#1A1A1A; letter-spacing:0.5px; line-height:1.3;">
                                                Reset Your<br />
                                                <em style="font-weight:400; color:#A3937B;">Password</em>
                                            </p>

                                            <p
                                                style="margin:12px auto 0; font-size:13.5px; color:#6B6560; line-height:1.9; max-width:480px;">
                                                Hello <strong style="color:#1A1A1A;">{{ $customerName }}</strong> — we
                                                received a request to reset your password. Click the button below to
                                                set a new one. This link will expire in
                                                <strong style="color:#1A1A1A;">60 minutes</strong>.
                                            </p>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  CTA SECTION                                -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:32px 40px; background:#FDFCFB; border-bottom:1px solid #EDE8E2;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td align="center">

                                            <!-- CTA Box -->
                                            <div
                                                style="border:1px dashed #D4C9BB; border-radius:3px; padding:28px 32px; background:#FFFFFF; text-align:center; max-width:420px; margin:0 auto;">
                                                <p
                                                    style="margin:0 0 6px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                    Secure Link
                                                </p>
                                                <p
                                                    style="margin:0 0 22px; font-size:12px; color:#8B8580; line-height:1.8;">
                                                    This link is unique to your account<br />and expires in 60 minutes.
                                                </p>

                                                <!-- Button -->
                                                <a href="{{ $resetLink }}"
                                                    style="display:inline-block; padding:15px 44px; background:#1A1A1A; color:#FFFFFF; text-decoration:none; font-size:11px; font-weight:700; letter-spacing:2.5px; text-transform:uppercase; border-radius:2px;">
                                                    Reset My Password
                                                </a>

                                                <p
                                                    style="margin:14px 0 0; font-size:10px; color:#C0B8B0; letter-spacing:0.5px;">
                                                    🔒 &nbsp;Encrypted &amp; Secure
                                                </p>
                                            </div>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  FALLBACK LINK                              -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:22px 40px; background:#F7F3EE; border-bottom:1px solid #EDE8E2;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <p
                                                style="margin:0 0 8px; font-size:9px; letter-spacing:2.5px; color:#A3937B; font-weight:700; text-transform:uppercase;">
                                                Button not working?
                                            </p>
                                            <p
                                                style="margin:0 0 8px; font-size:11.5px; color:#8B8580; line-height:1.7;">
                                                Copy and paste this link into your browser:
                                            </p>
                                            <p
                                                style="margin:0; font-size:11px; color:#A3937B; word-break:break-all; line-height:1.6; font-family:'DM Sans', Arial, sans-serif;">
                                                {{ $resetLink }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- ═══════════════════════════════════════════ -->
                        <!--  SECURITY NOTE                              -->
                        <!-- ═══════════════════════════════════════════ -->
                        <tr>
                            <td style="padding:22px 40px; background:#FDFCFB; border-bottom:1px solid #EDE8E2;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <!-- Icon -->
                                        <td
                                            style="vertical-align:top; width:32px; padding-right:14px; padding-top:2px;">
                                            <div
                                                style="width:28px; height:28px; border-radius:50%; background:#FFF8EB; border:1px solid #E8D9C5; text-align:center; line-height:28px;">
                                                <span style="font-size:13px; line-height:28px;">⚠️</span>
                                            </div>
                                        </td>
                                        <!-- Text -->
                                        <td style="vertical-align:top;">
                                            <p
                                                style="margin:0 0 4px; font-size:9px; letter-spacing:2.5px; color:#B07D1A; font-weight:700; text-transform:uppercase;">
                                                Didn't Request This?
                                            </p>
                                            <p style="margin:0; font-size:11.5px; color:#8B8580; line-height:1.8;">
                                                If you did not request a password reset, please ignore this email.
                                                Your password will remain unchanged and your account stays secure.
                                                If you're concerned, contact us immediately.
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
                                                Need help? We're always here.
                                                &nbsp;·&nbsp;
                                                <a href="mailto:{{ getSetting('store_email') }}"
                                                    style="color:#A3937B; text-decoration:none;">{{ getSetting('store_email') }}</a>
                                                &nbsp;·&nbsp;
                                                <span style="color:#9C9589;">+91 {{ getSetting('store_phone') }}</span>
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
