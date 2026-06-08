<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balasan dari Vetra</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .logo {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #0d9488;
            font-weight: 600;
        }
        .message-box {
            background: #f0fdfa;
            border-left: 4px solid #0d9488;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message-box h3 {
            margin-top: 0;
            color: #0d9488;
            font-size: 16px;
        }
        .original-message {
            background: #f9fafb;
            border-left: 4px solid #d1d5db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .original-message h4 {
            margin-top: 0;
            color: #6b7280;
            font-size: 14px;
            text-transform: uppercase;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #0d9488;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #0d9488;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .info-row {
            margin: 10px 0;
            font-size: 14px;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🐾</div>
            <h1>VETRA</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">Platform Kesehatan Hewan Peliharaan</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Halo {{ $contactMessage->name }},</p>
            
            <p>Terima kasih telah menghubungi kami. Tim Vetra telah merespons pesan Anda.</p>

            <!-- Admin Reply -->
            <div class="message-box">
                <h3>📩 Balasan dari {{ $adminName }}</h3>
                <p style="margin: 0; white-space: pre-wrap;">{{ $contactMessage->admin_reply }}</p>
            </div>

            <!-- Original Message -->
            <div class="original-message">
                <h4>Pesan Anda:</h4>
                <div class="info-row">
                    <span class="info-label">Subjek:</span> {{ $contactMessage->subject }}
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span> {{ $contactMessage->created_at->format('d F Y, H:i') }}
                </div>
                <p style="margin-top: 15px; white-space: pre-wrap;">{{ $contactMessage->message }}</p>
            </div>

            <p>Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk membalas email ini atau menghubungi kami kembali melalui website.</p>

            <center>
                <a href="{{ config('app.url') }}/kontak" class="button">Hubungi Kami Lagi</a>
            </center>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Vetra - Platform Kesehatan Hewan Peliharaan</strong></p>
            <p style="margin: 0 0 10px 0;">
                Email: <a href="mailto:support@vetra.id">support@vetra.id</a> | 
                Website: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                Email ini dikirim secara otomatis. Untuk balasan, silakan reply email ini atau hubungi kami melalui website.
            </p>
        </div>
    </div>
</body>
</html>
