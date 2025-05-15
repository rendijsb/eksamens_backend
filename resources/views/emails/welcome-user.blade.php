<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laipni lūdzam!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .welcome-box { background-color: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Laipni lūdzam mūsu veikalā!</h1>
</div>

<div class="content">
    <p>Sveiki, {{ $name }}!</p>

    <div class="welcome-box">
        <h3>Paldies, ka pievienojāties mums!</h3>
        <p>Jūsu konts ir veiksmīgi izveidots. Tagad jūs varat:</p>
        <ul>
            <li>Pārlūkot mūsu produktu kataloga</li>
            <li>Saņemt personalizētus piedāvājumus</li>
            <li>Sekot līdzi saviem pasūtījumiem</li>
            <li>Saglabāt savas adreses un maksājuma metodes</li>
            <li>Izveidot vēlmju sarakstu</li>
        </ul>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.frontend_url') }}" class="button">Sākt iepirkties</a>
    </p>

    <p>Ja jums ir jautājumi, mūsu atbalsta komanda ir gatava palīdzēt.</p>
</div>

<div class="footer">
    <p>Laimīgas iepirkšanās!<br>
        Jūsu komanda</p>
</div>
</body>
</html>
