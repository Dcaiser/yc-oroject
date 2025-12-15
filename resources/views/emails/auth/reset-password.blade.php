<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 32px 0;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(13, 148, 136, 0.12);
            overflow: hidden;
            border: 1px solid rgba(15, 118, 110, 0.08);
        }
        .header {
            text-align: center;
            padding: 32px 24px 12px;
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.08), rgba(5, 150, 105, 0.08));
        }
        .header h1 {
            margin: 18px 0 6px;
            font-size: 26px;
            font-weight: 700;
            color: #065f46;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: rgba(15, 118, 110, 0.8);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .body {
            padding: 28px 32px 12px;
            font-size: 15px;
            line-height: 1.7;
        }
        .body strong {
            color: #0f766e;
        }
        .cta {
            text-align: center;
            margin: 32px 0 40px;
        }
        .cta a {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border-radius: 8px;
            letter-spacing: 0.04em;
            transition: transform 0.18s ease;
        }
        .cta a:hover {
            transform: translateY(-2px);
        }
        .footer {
            padding: 20px 32px 28px;
            font-size: 13px;
            color: rgba(71, 85, 105, 0.85);
            background-color: rgba(226, 232, 240, 0.45);
        }
        .footer p {
            margin: 6px 0;
        }
        .footer strong {
            color: #0f766e;
        }
        @media (max-width: 560px) {
            .container {
                margin: 0 16px;
            }
            .body {
                padding: 24px 20px 8px;
                font-size: 14px;
            }
            .cta a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Reset Password</h1>
                <p>{{ $appName }}</p>
            </div>
            <div class="body">
                <p>Halo <strong>{{ $userName }}</strong>,</p>
                <p>Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah untuk melanjutkan proses reset password.</p>
                <div class="cta">
                    <a href="{{ $resetUrl }}" target="_blank" rel="noopener">Reset Password</a>
                </div>
                <p>Link reset password ini akan kedaluwarsa dalam 60 menit. Jika Anda tidak merasa meminta reset password, abaikan email ini dan akun Anda akan tetap aman.</p>
            </div>
            <div class="footer">
                <p>Salam hangat,</p>
                <p><strong>{{ $appName }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
