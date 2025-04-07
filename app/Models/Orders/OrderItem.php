<?php

declare(strict_types=1);

namespace App\Models\Orders;

use App\Models\Products\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_PRICE = 'product_price';
    const PRODUCT_SALE_PRICE = 'product_sale_price';
    const QUANTITY = 'quantity';
    const TOTAL_PRICE = 'total_price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::ORDER_ID,
        self::PRODUCT_ID,
        self::PRODUCT_NAME,
        self::PRODUCT_PRICE,
        self::PRODUCT_SALE_PRICE,
        self::QUANTITY,
        self::TOTAL_PRICE,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getOrderId(): int
    {
        return $this->getAttribute(self::ORDER_ID);
    }

    public function getProductId(): ?int
    {
        return $this->getAttribute(self::PRODUCT_ID);
    }

    public function getProductName(): string
    {
        return $this->getAttribute(self::PRODUCT_NAME);
    }

    public function getProductPrice(): float
    {
        return (float) $this->getAttribute(self::PRODUCT_PRICE);
    }

    public function getProductSalePrice(): ?float
    {
        $price = $this->getAttribute(self::PRODUCT_SALE_PRICE);
        return $price !== null ? (float) $price : null;
    }

    public function getQuantity(): int
    {
        return $this->getAttribute(self::QUANTITY);
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

    public function getEffectivePrice(): float
    {
        return $this->getProductSalePrice() ?? $this->getProductPrice();
    }
}
