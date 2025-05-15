<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Novērtējiet savu pieredzi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #fd7e14; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .review-box { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0; border-radius: 5px; text-align: center; }
        .product-item { background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .button { background-color: #fd7e14; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 10px; }
        .stars { font-size: 24px; color: #ffc107; margin: 10px 0; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>⭐ Dalieties ar savu pieredzi!</h1>
</div>

<div class="content">
    <p>Labdien, {{ $customerName }}!</p>

    <div class="review-box">
        <h3>Kā jums patika jūsu pasūtījums?</h3>
        <div class="stars">⭐⭐⭐⭐⭐</div>
        <p>Jūsu atsauksme ir svarīga mums un citiem klientiem. Dalieties ar savu pieredzi!</p>
    </div>

    <h4>Jūsu pasūtītie produkti:</h4>
    @foreach($order->items as $item)
        <div class="product-item">
            <strong>{{ $item->getProductName() }}</strong>
            <p style="text-align: center; margin-top: 15px;">
                <a href="{{ config('app.frontend_url') }}/products/{{ $item->product?->getSlug() }}#reviews" class="button">
                    Novērtēt produktu
                </a>
            </p>
        </div>
    @endforeach

    <div style="text-align: center; margin-top: 30px; background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h4>Vai nevēlaties novērtēt katru produktu atsevišķi?</h4>
        <a href="{{ config('app.frontend_url') }}/orders/{{ $order->getId() }}/review" class="button">
            Novērtēt visu pasūtījumu
        </a>
    </div>

    <p style="text-align: center; color: #6c757d; font-style: italic;">
        Jūsu atsauksme palīdz mums uzlabot servisu un palīdz citiem klientiem izvēlēties.
    </p>
</div>

<div class="footer">
    <p>Paldies, ka izvēlējāties NetNest!<br>
        Mēs novērtējam jūsu uzticību.</p>
</div>
</body>
</html>
