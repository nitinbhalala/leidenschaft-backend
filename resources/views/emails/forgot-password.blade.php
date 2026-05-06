<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #A3937B;
            padding: 32px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }

        .body {
            padding: 40px 32px;
            color: #333333;
        }

        .body p {
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 16px;
        }

        .btn {
            display: inline-block;
            margin: 24px 0;
            padding: 14px 32px;
            background-color: #A3937B;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
        }

        .note {
            font-size: 13px;
            color: #888888;
            margin-top: 24px;
            border-top: 1px solid #eeeeee;
            padding-top: 16px;
        }

        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: #aaaaaa;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $customerName }}</strong>,</p>
            <p>We received a request to reset your password. Click the button below to set a new password. This link
                will expire in <strong>60 minutes</strong>.</p>

            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="btn">Reset Password</a>
            </div>

            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #555555;">{{ $resetLink }}</p>

            <p class="note">
                If you did not request a password reset, no action is needed — your password will remain unchanged.<br>
                This link will expire in 60 minutes.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
