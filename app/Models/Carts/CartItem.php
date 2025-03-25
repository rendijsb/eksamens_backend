<?php

declare(strict_types=1);

namespace App\Models\Carts;

use App\Models\Products\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    const ID = 'id';
    const CART_ID = 'cart_id';
    const PRODUCT_ID = 'product_id';
    const QUANTITY = 'quantity';
    const PRICE = 'price';
    const SALE_PRICE = 'sale_price';
    const TOTAL_PRICE = 'total_price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::CART_ID,
        self::PRODUCT_ID,
        self::QUANTITY,
        self::PRICE,
        self::SALE_PRICE,
        self::TOTAL_PRICE,
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getCartId(): int
    {
        return $this->getAttribute(self::CART_ID);
    }

    public function getProductId(): int
    {
        return $this->getAttribute(self::PRODUCT_ID);
    }

    public function getQuantity(): int
    {
        return $this->getAttribute(self::QUANTITY);
    }

    public function getPrice(): float
    {
        return (float) $this->getAttribute(self::PRICE);
    }

    public function getSalePrice(): ?float
    {
        $salePrice = $this->getAttribute(self::SALE_PRICE);
        return $salePrice !== null ? (float) $salePrice : null;
    }

    public function getTotalPrice(): float
    {
        return (float) $this->getAttribute(self::TOTAL_PRICE);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function calculateTotalPrice(): float
    {
        $effectivePrice = $this->getSalePrice() ?? $this->getPrice();
        return $effectivePrice * $this->getQuantity();
    }

    protected static function booted(): void
    {
        static::saving(function (CartItem $cartItem) {
            $cartItem->setAttribute(self::TOTAL_PRICE, $cartItem->calculateTotalPrice());
        });
    }
}
