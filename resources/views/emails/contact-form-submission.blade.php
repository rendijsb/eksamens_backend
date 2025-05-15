<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jauns ziņojums no kontaktu formas</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .contact-details { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #007bff; }
        .message-box { background-color: #ffffff; border: 1px solid #e9ecef; padding: 20px; margin: 20px 0; }
        .label { font-weight: bold; color: #495057; }
        .value { color: #212529; margin-bottom: 10px; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>📧 Jauns ziņojums no kontaktu formas</h1>
</div>

<div class="content">
    <div class="contact-details">
        <h3>Klienta informācija</h3>
        <div class="value">
            <span class="label">Vārds:</span> {{ $contactData['name'] }}
        </div>
        <div class="value">
            <span class="label">E-pasts:</span> {{ $contactData['email'] }}
        </div>
        <div class="value">
            <span class="label">Temats:</span> {{ $contactData['subject'] }}
        </div>
        <div class="value">
            <span class="label">Nosūtīts:</span> {{ now()->format('d.m.Y H:i') }}
        </div>
    </div>

    <div class="message-box">
        <h3>Ziņojums</h3>
        <p>{!! nl2br(e($contactData['message'])) !!}</p>
    </div>

    <p style="font-style: italic; color: #6c757d; text-align: center;">
        Jūs varat atbildēt tieši uz šo e-pastu, un atbilde tiks nosūtīta klientam.
    </p>
</div>

<div class="footer">
    <p>Šis e-pasts tika automātiski ģenerēts no NetNest kontaktu formas.</p>
</div>
</body>
</html>
