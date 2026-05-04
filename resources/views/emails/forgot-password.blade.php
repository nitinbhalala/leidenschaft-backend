<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password — Leidenschaft</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Mulish:wght@700&display=swap"
        rel="stylesheet" />

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand: #A3937B;
            --brand-dark: #8B7D68;
            --brand-light: #F9F7F5;
            --text-dark: #242424;
            --text-muted: #8B8B8B;
            --text-subtle: #B2A99A;
            --border: #E8E4DF;
        }

        body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
            padding: 64px 16px;
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Card ─────────────────────────────────────── */
        .card {
            width: 100%;
            max-width: 576px;
            border-radius: 2px;
            overflow: hidden;
            border: 1px solid #f0ede9;
        }

        /* ── Header ───────────────────────────────────── */
        .card-header {
            background: var(--brand);
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before,
        .card-header::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: #fff;
            opacity: .10;
            filter: blur(40px);
        }

        .card-header::before {
            width: 160px;
            height: 160px;
            top: -40px;
            left: -40px;
        }

        .card-header::after {
            width: 160px;
            height: 160px;
            bottom: -40px;
            right: -40px;
        }

        .logo-wrap {
            position: relative;
            z-index: 1;
        }

        /* Wordmark rendered in pure CSS since SVG isn't available */
        .logo-text {
            font-family: 'Mulish', sans-serif;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: 3px;
            color: #fff;
            text-transform: uppercase;
        }

        /* ── Body ─────────────────────────────────────── */
        .card-body {
            padding: 36px 40px 28px;
        }

        .greeting {
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 16px;
        }

        .greeting .name {
            font-weight: 600;
            color: var(--brand);
        }

        .body-copy {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 28px;
        }

        /* CTA */
        .btn-wrap {
            display: flex;
            justify-content: center;
            padding: 8px 0 24px;
        }

        .btn-reset {
            display: inline-block;
            width: 100%;
            background: var(--brand);
            color: #fff;
            text-align: center;
            text-decoration: none;
            padding: 16px 48px;
            border-radius: 999px;
            font-family: 'Mulish', sans-serif;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1.2px;
            box-shadow: 0 6px 20px rgba(163, 147, 123, .35);
            transition: background .25s, box-shadow .25s, transform .2s;
            cursor: pointer;
            border: none;
        }

        .btn-reset:hover {
            background: var(--brand-dark);
            box-shadow: 0 10px 28px rgba(163, 147, 123, .45);
            transform: translateY(-2px);
        }

        /* Security note */
        .security-note {
            background: var(--brand-light);
            border-left: 4px solid var(--brand);
            padding: 14px 16px;
            border-radius: 0 2px 2px 0;
            margin-bottom: 28px;
        }

        .security-note p {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.65;
        }

        .security-note .label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .security-note .expiry {
            color: var(--brand);
            font-weight: 700;
        }

        /* Divider */
        .divider {
            position: relative;
            padding: 16px 0;
            text-align: center;
            margin-bottom: 4px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            position: relative;
            background: #fff;
            padding: 0 10px;
            font-size: 11px;
            color: var(--text-subtle);
        }

        /* Copy link */
        .copy-link {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 14px 16px;
            font-size: 11px;
            color: var(--brand);
            word-break: break-all;
            cursor: pointer;
            transition: background .2s;
            user-select: all;
        }

        .copy-link:hover {
            background: var(--brand-light);
        }

        /* ── Footer ───────────────────────────────────── */
        .card-footer {
            background: var(--brand-light);
            padding: 24px 16px;
            text-align: center;
            border-top: 1px solid var(--border);
        }

        .footer-copy {
            font-size: 11px;
            color: var(--text-subtle);
            letter-spacing: .08em;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 12px;
        }

        .footer-links a {
            font-size: 10px;
            color: var(--brand);
            text-decoration: none;
            letter-spacing: .06em;
            transition: text-decoration .15s;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* ── Responsive ───────────────────────────────── */
        @media (max-width: 480px) {
            .card-body {
                padding: 28px 24px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="card">

        <!-- Header -->
        <div class="card-header">
            <div class="logo-wrap">
                <span class="logo-text">{{ config('app.name') }}</span>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">

            <p class="greeting">
                Hi <span class="name">{{ $customerName }}</span>,
            </p>

            <p class="body-copy">
                We received a request to reset your password for your {{ config('app.name') }} account.
                Click the button below to securely set a new password.
            </p>

            <!-- CTA -->
            <div class="btn-wrap">
                <a href="{{ $resetLink }}" class="btn-reset">Reset Password</a>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <p>
                    <span class="label">Security Note:</span>
                    This link will expire in <span class="expiry">60 minutes</span> for your protection.
                    If you did not request this, please ignore this email.
                </p>
            </div>

            <!-- Divider -->
            <div class="divider">
                <span>or copy link</span>
            </div>

            <!-- Copy Link -->
            <div class="copy-link">
                {{ $resetLink }}
            </div>

        </div>

        <!-- Footer -->
        <div class="card-footer">
            <p class="footer-copy">© 2026 {{ config('app.name') }} — Designed for Inspiration</p>
            <div class="footer-links">
                <a href="#">Home</a>
                <a href="#">Support</a>
            </div>
        </div>

    </div>

</body>

</html>
