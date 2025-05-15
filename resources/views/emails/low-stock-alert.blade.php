<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Zemu krājumu brīdinājums</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .alert-box { background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .product-list { background-color: #f8f9fa; padding: 15px; margin: 20px 0; }
        .product-item { border-bottom: 1px solid #dee2e6; padding: 10px 0; display: flex; justify-content: space-between; align-items: center; }
        .stock-critical { color: #dc3545; font-weight: bold; }
        .stock-low { color: #ffc107; font-weight: bold; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>
<div class="header">
    <h1>⚠️ Zemu krājumu brīdinājums</h1>
</div>

<div class="content">
    <div class="alert-box">
        <h3>Nepieciešama darbība!</h3>
        <p>Šiem produktiem ir zemi krājumi un nepieciešams papildināt noliktavu:</p>
    </div>

    <div class="product-list">
        <h4>Produkti ar zemiem krājumiem:</h4>
        @foreach($products as $product)
            <div class="product-item">
                <div>
                    <strong>{{ $product->getName() }}</strong><br>
                    <small>SKU: {{ $product->getId() }}</small>
                </div>
                <div>
                    @if($product->getStock() <= 0)
                        <span class="stock-critical">🔴 Nav noliktavā</span>
                    @elseif($product->getStock() <= 5)
                        <span class="stock-critical">Atlikums: {{ $product->getStock() }}</span>
                    @else
                        <span class="stock-low">Atlikums: {{ $product->getStock() }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <p><strong>Kopā produktu ar zemiem krājumiem: {{ $products->count() }}</strong></p>
        <a href="{{ config('app.url') }}/admin/products?filter=low_stock" class="button">
            Pārvaldīt krājumus
        </a>
    </div>

    <div style="background-color: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 20px 0; border-radius: 5px;">
        <strong>Ieteikums:</strong><br>
        Regulāri pārbaudiet krājumus un laikus papildiniet noliktavu, lai izvairītos no pārdošanas zaudējumiem.
    </div>
</div>

<div class="footer">
    <p>Šis brīdinājums tika automātiski ģenerēts NetNest sistēmā.<br>
        {{ now()->format('d.m.Y H:i') }}</p>
</div>
</body>
</html>
