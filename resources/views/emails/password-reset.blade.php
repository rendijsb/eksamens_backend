<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paroles atiestatīšana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .reset-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 25px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
        }
        .reset-box h3 {
            margin-top: 0;
            color: #333;
        }
        .button {
            background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.3);
        }
        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-size: 14px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .url-fallback {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            color: #6c757d;
            word-break: break-all;
            margin-top: 20px;
        }
        .logo {
            font-size: 16px;
            opacity: 0.9;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔐 Paroles atiestatīšana</h1>
        <div class="logo">NetNest</div>
    </div>

    <div class="content">
        <p>Labdien, <strong>{{ $user->getName() }}</strong>!</p>

        <div class="reset-box">
            <h3>Pieprasīta paroles maiņa</h3>
            <p>Jūs saņēmāt šo e-pastu, jo mēs saņēmām paroles atiestatīšanas pieprasījumu jūsu kontam.</p>

            <p style="margin: 25px 0;">
                <a href="{{ $resetUrl }}" class="button">Atiestatīt paroli</a>
            </p>

            <p style="font-size: 14px; color: #6c757d;">
                <strong>Svarīgi:</strong> Šī saite derīga tikai 60 minūtes no nosūtīšanas brīža.
            </p>
        </div>

        <div class="warning">
            <strong>⚠️ Drošības iemesli:</strong><br>
            Ja jūs <strong>nepieprasījāt</strong> paroles atiestatīšanu, droši ignorējiet šo e-pastu. Jūsu parole netiks mainīta, un jūsu konts paliks drošībā.
        </div>

        <p style="font-size: 14px; color: #6c757d;">
            Ja jums ir problēmas ar pogas "Atiestatīt paroli" nospiešanu, kopējiet un ielīmējiet šo saiti savā pārlūkprogrammā:
        </p>
        <div class="url-fallback">
            {{ $resetUrl }}
        </div>
    </div>

    <div class="footer">
        <p><strong>NetNest komanda</strong><br>
            Šis e-pasts tika nosūtīts automātiski. Lūdzu, neatbildiet uz šo e-pastu.</p>
        <p style="margin-top: 15px; font-size: 12px;">
            © {{ date('Y') }} NetNest. Visas tiesības aizsargātas.
        </p>
    </div>
</div>
</body>
</html>
