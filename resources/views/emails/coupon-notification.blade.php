<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Īpašs kupons</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .coupon-box { background-color: #fff3cd; border: 2px dashed #ffc107; padding: 20px; margin: 20px 0; text-align: center; border-radius: 10px; }
        .coupon-code { background-color: #ffc107; color: #212529; padding: 15px; font-size: 24px; font-weight: bold; margin: 10px 0; border-radius: 5px; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>🎉 Īpašs piedāvājums tikai jums!</h1>
</div>

<div class="content">
    <p>Sveiki, {{ $name }}!</p>

    <p>Mums ir lieliskas ziņas! Jums ir pieejams īpašs atlaides kupons:</p>

    <div class="coupon-box">
        <h3>{{ $coupon->getDescription() ?? 'Īpaša atlaide' }}</h3>
        <div class="coupon-code">{{ $coupon->getCode() }}</div>
        <p>
            <strong>
                @if($coupon->getType() === 'percentage')
                    Atlaide: {{ $coupon->getValue() }}%
                @else
                    Atlaide: €{{ number_format($coupon->getValue(), 2) }}
                @endif
            </strong>
        </p>

        @if($coupon->getMinOrderAmount())
            <p>Minimālā pasūtījuma summa: €{{ number_format($coupon->getMinOrderAmount(), 2) }}</p>
        @endif

        <p style="color: #dc3545;">
            <strong>Derīgs līdz: {{ $coupon->getExpiresAt()->format('d.m.Y H:i') }}</strong>
        </p>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.frontend_url') }}/products" class="button">Izmantot kuponu</a>
    </p>

    <p style="text-align: center; color: #6c757d; font-size: 14px;">
        Steidzieties! Kupons ir ierobežotā laikā.
    </p>
</div>

<div class="footer">
    <p>Laimīgas iepirkšanās ar atlaidi!<br>
        Jūsu komanda</p>
</div>
</body>
</html>
