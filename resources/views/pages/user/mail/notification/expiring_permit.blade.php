<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .email-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }

        .email-header {
            margin-bottom: 20px;
        }

        .smile-icon {
            width: 50px;
            height: 50px;
        }

        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 10px;
        }

        .emoji {
            font-size: 1.5em;
        }

        p {
            font-size: 14px;
            color: #666666;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .verify-button {
            display: inline-block;
            background: #0053B2;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background 0.3s;
        }

        .verify-button:hover {
            background: #0053B2;
            color: #ffffff;
        }

        .email-footer {
            font-size: 12px;
            color: #888888;
            border-top: 1px solid #eeeeee;
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .footer-note {
            margin-top: 10px;
            font-style: italic;
            color: #aaaaaa;
        }

        .text-left {
            text-align: left;
        }

        .text-left p {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="rolling_sync_logo" class="smile-icon">
        </div>
        <h1>Your Permit Will Be Expired</h1>

        <div class="text-left">
            <p><b>Departemen</b> : {{ $mailData['departemen'] }}</p>
            <p><b>Seksi</b> : {{ $mailData['seksi'] }}</p>
            <p><b>Jumlah</b> : {{ $mailData['jumlah'] }} permit</p>
        </div>

        <a href="{{ $mailData['url'] }}" class="verify-button">See Detail</a>

        <div class="email-footer">
            <p class="footer-note">
                This email is sent automatically by the system. You do not need to reply to this email, thank you.
            </p>
        </div>
    </div>
</body>
</html>
