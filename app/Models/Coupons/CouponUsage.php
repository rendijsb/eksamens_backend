<?php

declare(strict_types=1);

namespace App\Models\Coupons;

use App\Models\Orders\Order;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    const ID = 'id';
    const COUPON_ID = 'coupon_id';
    const USER_ID = 'user_id';
    const ORDER_ID = 'order_id';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::COUPON_ID,
        self::USER_ID,
        self::ORDER_ID,
        self::DISCOUNT_AMOUNT,
    ];

    protected $casts = [
        self::DISCOUNT_AMOUNT => 'decimal:2',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getCouponId(): int
    {
        return $this->getAttribute(self::COUPON_ID);
    }

    public function getUserId(): int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getOrderId(): int
    {
        return $this->getAttribute(self::ORDER_ID);
    }

    public function getDiscountAmount(): float
    {
        return (float) $this->getAttribute(self::DISCOUNT_AMOUNT);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }
}
