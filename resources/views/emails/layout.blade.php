<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <style>
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #2D2D2D;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            text-decoration: none;
        }

        .email-content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #8B0000;
            margin: 0 0 20px 0;
        }

        .content-text {
            font-size: 16px;
            margin: 0 0 20px 0;
            color: #2D2D2D;
        }

        .highlight-box {
            background: #FBEAEB;
            border-left: 4px solid #8B0000;
            padding: 20px;
            margin: 24px 0;
            border-radius: 0 12px 12px 0;
        }

        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .benefits-list li {
            padding: 8px 0;
            position: relative;
            padding-left: 30px;
        }

        .benefits-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            top: 8px;
            color: #8B0000;
            font-weight: bold;
            font-size: 18px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);
            color: #ffffff;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(139, 0, 0, 0.3);
            transition: all 0.3s ease;
            margin: 20px 0;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 0, 0, 0.4);
            color: #ffffff;
            text-decoration: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #8B0000;
            transform: translateY(-4px);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #8B0000;
            display: block;
        }

        .stat-label {
            font-size: 14px;
            color: #2D2D2D;
            margin-top: 5px;
        }

        .email-footer {
            background: #2D2D2D;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .footer-content {
            font-size: 14px;
            line-height: 1.6;
        }

        .unsubscribe-link {
            color: #8B0000;
            text-decoration: none;
            font-weight: 500;
        }

        .unsubscribe-link:hover {
            text-decoration: underline;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #ffffff;
            font-size: 20px;
            text-decoration: none;
        }

        .social-links a:hover {
            color: #8B0000;
        }

        @media (max-width: 480px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }

            .email-header,
            .email-content,
            .email-footer {
                padding: 20px;
            }

            .greeting {
                font-size: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <a href="{{ config('app.frontend_url', url('/')) }}" class="logo" style="color: #ffffff; text-decoration: none;">
            {{ config('app.name', 'NetNest') }}
        </a>
    </div>

    <div class="email-content">
        @yield('content')
    </div>

    <div class="email-footer">
        <div class="footer-content">
            <div class="social-links">
                <a href="#" aria-label="Facebook">📘</a>
                <a href="#" aria-label="Instagram">📸</a>
                <a href="#" aria-label="Twitter">🐦</a>
            </div>

            <p>© {{ date('Y') }} NetNest. Visas tiesības aizsargātas.</p>
            <p>Liepāja, Latvija | netnest777@gmail.com | +371 25759193</p>

            @isset($unsubscribeUrl)
                <p style="margin-top: 20px;">
                    Vairs nevēlaties saņemt šos e-pastus?
                    <a href="{{ $unsubscribeUrl }}" class="unsubscribe-link">Izrakstīties šeit</a>
                </p>
            @endisset
        </div>
    </div>
</div>
</body>
</html>
