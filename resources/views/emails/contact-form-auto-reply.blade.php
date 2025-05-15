<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ziņojums saņemts</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .success-box { background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .message-summary { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
        .contact-info { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
<div class="header">
    <h1>✅ Jūsu ziņojums ir saņemts!</h1>
</div>

<div class="content">
    <p>Labdien, {{ $contactData['name'] }}!</p>

    <div class="success-box">
        <h3>Paldies par jūsu ziņojumu!</h3>
        <p>Mēs esam saņēmuši jūsu ziņojumu un atbildēsim iespējami drīz.</p>
    </div>

    <div class="message-summary">
        <h4>Jūsu ziņojuma kopsavilkums:</h4>
        <p><strong>Temats:</strong> {{ $contactData['subject'] }}</p>
        <p><strong>Nosūtīts:</strong> {{ now()->format('d.m.Y H:i') }}</p>
    </div>

    <p>Parasti mēs atbildam uz jautājumiem 24-48 stundu laikā. Ja jums ir steidzams jautājums, lūdzu, zvaniet mums tieši.</p>

    <div class="contact-info">
        <h4>Saziņas informācija</h4>
        <p>📧 E-pasts: netnest777@gmail.com<br>
            📞 Tālrunis: +371 25759193<br>
            🕒 Darba laiks: P-Pk 9:00-17:00
        </p>
    </div>
</div>

<div class="footer">
    <p>Paldies, ka sazinieties ar NetNest!<br>
        Mūsu komanda</p>
</div>
</body>
</html>
