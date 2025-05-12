<?php

declare(strict_types=1);

namespace App\Models\Coupons;

use App\Models\Orders\Order;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    const ID = 'id';
    const CODE = 'code';
    const TYPE = 'type';
    const VALUE = 'value';
    const MIN_ORDER_AMOUNT = 'min_order_amount';
    const MAX_DISCOUNT_AMOUNT = 'max_discount_amount';
    const USES_PER_USER = 'uses_per_user';
    const TOTAL_USES = 'total_uses';
    const USED_COUNT = 'used_count';
    const STARTS_AT = 'starts_at';
    const EXPIRES_AT = 'expires_at';
    const IS_ACTIVE = 'is_active';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::CODE,
        self::TYPE,
        self::VALUE,
        self::MIN_ORDER_AMOUNT,
        self::MAX_DISCOUNT_AMOUNT,
        self::USES_PER_USER,
        self::TOTAL_USES,
        self::USED_COUNT,
        self::STARTS_AT,
        self::EXPIRES_AT,
        self::IS_ACTIVE,
        self::DESCRIPTION,
    ];

    protected $casts = [
        self::IS_ACTIVE => 'boolean',
        self::STARTS_AT => 'datetime',
        self::EXPIRES_AT => 'datetime',
        self::VALUE => 'decimal:2',
        self::MIN_ORDER_AMOUNT => 'decimal:2',
        self::MAX_DISCOUNT_AMOUNT => 'decimal:2',
    ];

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_usages')
            ->withPivot(['order_id', 'discount_amount'])
            ->withTimestamps();
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getCode(): string
    {
        return $this->getAttribute(self::CODE);
    }

    public function getType(): string
    {
        return $this->getAttribute(self::TYPE);
    }

    public function getValue(): float
    {
        return (float) $this->getAttribute(self::VALUE);
    }

    public function getMinOrderAmount(): ?float
    {
        $value = $this->getAttribute(self::MIN_ORDER_AMOUNT);
        return $value !== null ? (float) $value : null;
    }

    public function getMaxDiscountAmount(): ?float
    {
        $value = $this->getAttribute(self::MAX_DISCOUNT_AMOUNT);
        return $value !== null ? (float) $value : null;
    }

    public function getUsesPerUser(): ?int
    {
        return $this->getAttribute(self::USES_PER_USER);
    }

    public function getTotalUses(): ?int
    {
        return $this->getAttribute(self::TOTAL_USES);
    }

    public function getUsedCount(): ?int
    {
        return $this->getAttribute(self::USED_COUNT);
    }

    public function getStartsAt(): Carbon
    {
        return $this->getAttribute(self::STARTS_AT);
    }

    public function getExpiresAt(): Carbon
    {
        return $this->getAttribute(self::EXPIRES_AT);
    }

    public function getIsActive(): bool
    {
        return $this->getAttribute(self::IS_ACTIVE);
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute(self::DESCRIPTION);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function isValid(): bool
    {
        $now = Carbon::now();
        return $this->getIsActive()
            && $now->greaterThanOrEqualTo($this->getStartsAt())
            && $now->lessThanOrEqualTo($this->getExpiresAt());
    }

    public function isUsageLimitReached(): bool
    {
        $totalUses = $this->getTotalUses();
        return $totalUses !== null && $this->getUsedCount() >= $totalUses;
    }

    public function hasUserExceededLimit(int $userId): bool
    {
        $usesPerUser = $this->getUsesPerUser();
        if ($usesPerUser === null) {
            return false;
        }

        $userUsageCount = $this->couponUsages()
            ->where(CouponUsage::USER_ID, $userId)
            ->count();

        return $userUsageCount >= $usesPerUser;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->getType() === 'percentage') {
            $discount = ($orderAmount * $this->getValue()) / 100;
            if ($this->getMaxDiscountAmount() !== null) {
                $discount = min($discount, $this->getMaxDiscountAmount());
            }
            return $discount;
        }

        return min($this->getValue(), $orderAmount);
    }

    public function canBeAppliedToAmount(float $amount): bool
    {
        $minAmount = $this->getMinOrderAmount();
        return $minAmount === null || $amount >= $minAmount;
    }
}
