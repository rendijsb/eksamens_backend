<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grozā gaidošie produkti</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #ffc107; color: #212529; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .cart-items { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .button { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>🛒 Jūsu grozā vēl gaid produkti!</h1>
</div>

<div class="content">
    <p>Sveiki, {{ $name }}!</p>

    <p>Mēs pamanījām, ka jūs atstājāt produktus savā grozā. Neaizmirstiet tos!</p>

    <div class="cart-items">
        <h3>Produkti jūsu grozā:</h3>
        @foreach($cart->items as $item)
            <div style="border-bottom: 1px solid #ddd; padding: 10px 0; display: flex; align-items: center;">
                <div style="flex: 1;">
                    <strong>{{ $item->product->getName() }}</strong><br>
                    Daudzums: {{ $item->getQuantity() }}<br>
                    <span style="color: #28a745; font-weight: bold;">€{{ number_format($item->getTotalPrice(), 2) }}</span>
                </div>
            </div>
        @endforeach

        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #ddd;">
            <strong>Kopējā summa: €{{ number_format($cart->getTotalPrice(), 2) }}</strong>
        </div>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.frontend_url') }}/cart" class="button">Pabeigt pasūtījumu</a>
    </p>

    <p style="text-align: center; color: #6c757d; font-size: 14px;">
        Steidzieties! Produktu pieejamība var mainīties.
    </p>
</div>

<div class="footer">
    <p>Ja jums ir jautājumi, mēs esam šeit, lai palīdzētu.<br>
        Jūsu komanda</p>
</div>
</body>
</html>
