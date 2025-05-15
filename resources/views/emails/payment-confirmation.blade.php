<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maksājums apstiprinās</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .payment-success { background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px; text-align: center; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>✅ Maksājums veiksmīgi saņemts!</h1>
</div>

<div class="content">
    <p>Labdien, {{ $customerName }}!</p>

    <div class="payment-success">
        <h3>Jūsu maksājums ir apstrādāts</h3>
        <p><strong>Pasūtījuma numurs:</strong> {{ $order->getOrderNumber() }}</p>
        <p><strong>Maksātā summa:</strong> €{{ number_format($order->getTotalAmount(), 2) }}</p>
        <p><strong>Maksājuma metode:</strong> {{ $order->getPaymentMethod() }}</p>
        @if($order->getTransactionId())
            <p><strong>Transakcijas ID:</strong> {{ $order->getTransactionId() }}</p>
        @endif
    </div>

    <p>Jūsu pasūtījums tagad tiks apstrādāts un drīzumā nosūtīts. Jūs saņemsiet e-pastu ar izsekošanas informāciju, kad pasūtījums tiks nosūtīts.</p>

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/orders/' . $order->getId()) }}" class="button">Skatīt pasūtījumu</a>
    </p>
</div>

<div class="footer">
    <p>Paldies par jūsu pasūtījumu!<br>
        Jūsu komanda</p>
</div>
</body>
</html>
