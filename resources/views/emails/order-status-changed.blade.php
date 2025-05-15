<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pasūtījuma statuss mainījies</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .status-update { background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Pasūtījuma statuss atjaunināts</h1>
</div>

<div class="content">
    <p>Labdien, {{ $customerName }}!</p>

    <div class="status-update">
        <h3>Jūsu pasūtījuma statuss ir mainījies</h3>
        <p><strong>Pasūtījuma numurs:</strong> {{ $order->getOrderNumber() }}</p>
        <p><strong>Jaunais statuss:</strong> {{ $currentStatus }}</p>
    </div>

    @if($order->getStatus()->value === 'processing')
        <p>Jūsu pasūtījums tiek apstrādāts. Mēs drīzumā jums nosūtīsim informāciju par piegādi.</p>
    @elseif($order->getStatus()->value === 'completed')
        <p>Jūsu pasūtījums ir sekmīgi pabeigts. Paldies, ka izvēlējāties mūs!</p>
    @elseif($order->getStatus()->value === 'cancelled')
        <p>Jūsu pasūtījums ir atcelts. Ja jums ir jautājumi, lūdzu, sazinieties ar mūsu atbalsta dienestu.</p>
    @endif

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/orders/' . $order->getId()) }}" class="button">Skatīt pasūtījumu</a>
    </p>
</div>

<div class="footer">
    <p>Ja jums ir jautājumi, mēs esam šeit, lai palīdzētu.<br>
        Jūsu komanda</p>
</div>
</body>
</html>
