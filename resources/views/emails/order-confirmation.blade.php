<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pasūtījuma apstiprinājums</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .order-details { background-color: #f5f5f5; padding: 15px; margin: 20px 0; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Paldies par jūsu pasūtījumu!</h1>
</div>

<div class="content">
    <p>Labdien, {{ $customerName }}!</p>

    <p>Mēs esam saņēmuši jūsu pasūtījumu un apstrādājam to. Jūs saņemsiet e-pastu, kad pasūtījums tiks nosūtīts.</p>

    <div class="order-details">
        <h3>Pasūtījuma detaļas</h3>
        <p><strong>Pasūtījuma numurs:</strong> {{ $orderNumber }}</p>
        <p><strong>Pasūtījuma datums:</strong> {{ $order->getCreatedAt()->format('d.m.Y H:i') }}</p>
        <p><strong>Kopējā summa:</strong> €{{ number_format($totalAmount, 2) }}</p>

        <h4>Pasūtītie produkti:</h4>
        @foreach($order->items as $item)
            <div style="border-bottom: 1px solid #ddd; padding: 10px 0;">
                <strong>{{ $item->getProductName() }}</strong><br>
                Daudzums: {{ $item->getQuantity() }}<br>
                Cena: €{{ number_format($item->getEffectivePrice(), 2) }}<br>
                Kopā: €{{ number_format($item->getTotalPrice(), 2) }}
            </div>
        @endforeach
    </div>

    @if($order->getShippingAddressDetails())
        <div class="order-details">
            <h3>Piegādes adrese</h3>
            @php $shipping = json_decode($order->getShippingAddressDetails(), true) @endphp
            <p>
                {{ $shipping['name'] }}<br>
                {{ $shipping['street_address'] }}
                @if($shipping['apartment']), {{ $shipping['apartment'] }}@endif<br>
                {{ $shipping['city'] }}, {{ $shipping['postal_code'] }}<br>
                {{ $shipping['country'] }}<br>
                Tel: {{ $shipping['phone'] }}
            </p>
        </div>
    @endif

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.frontend_url') }}/orders/{{ $order->getId() }}" class="button">Skatīt pasūtījumu</a>
    </p>

    <p>Ja jums ir jautājumi par jūsu pasūtījumu, lūdzu, sazinieties ar mums.</p>
</div>

<div class="footer">
    <p>Paldies, ka izvēlējāties mūs!<br>
        Jūsu komanda</p>
</div>
</body>
</html>
