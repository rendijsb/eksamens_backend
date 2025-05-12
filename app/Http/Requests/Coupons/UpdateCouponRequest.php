<?php

declare(strict_types=1);

namespace App\Http\Requests\Coupons;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    const COUPON_ID = 'couponId';
    const CODE = 'code';
    const TYPE = 'type';
    const VALUE = 'value';
    const MIN_ORDER_AMOUNT = 'min_order_amount';
    const MAX_DISCOUNT_AMOUNT = 'max_discount_amount';
    const USES_PER_USER = 'uses_per_user';
    const TOTAL_USES = 'total_uses';
    const STARTS_AT = 'starts_at';
    const EXPIRES_AT = 'expires_at';
    const IS_ACTIVE = 'is_active';
    const DESCRIPTION = 'description';

    public function rules(): array
    {
        return [
            self::CODE => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($this->route(self::COUPON_ID))
            ],
            self::TYPE => 'required|in:percentage,fixed',
            self::VALUE => 'required|numeric|min:0',
            self::MIN_ORDER_AMOUNT => 'nullable|numeric|min:0',
            self::MAX_DISCOUNT_AMOUNT => 'nullable|numeric|min:0',
            self::USES_PER_USER => 'nullable|integer|min:1',
            self::TOTAL_USES => 'nullable|integer|min:1',
            self::STARTS_AT => 'required|date',
            self::EXPIRES_AT => 'required|date|after:starts_at',
            self::IS_ACTIVE => 'required|boolean',
            self::DESCRIPTION => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            self::CODE . '.unique' => 'Kupona kods jau eksistē',
            self::TYPE . '.in' => 'Nepareizs kupona tips',
            self::VALUE . '.min' => 'Kupona vērtība nevar būt negatīva',
            self::EXPIRES_AT . '.after' => 'Kupona beigu datums jābūt pēc sākuma datuma',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::COUPON_ID => $this->route(self::COUPON_ID),
        ]);
    }

    public function getCouponId(): int
    {
        return (int) $this->route(self::COUPON_ID);
    }

    public function getCode(): string
    {
        return strtoupper($this->input(self::CODE));
    }

    public function getType(): string
    {
        return $this->input(self::TYPE);
    }

    public function getValue(): float
    {
        return (float) $this->input(self::VALUE);
    }

    public function getMinOrderAmount(): ?float
    {
        return $this->input(self::MIN_ORDER_AMOUNT) ? (float) $this->input(self::MIN_ORDER_AMOUNT) : null;
    }

    public function getMaxDiscountAmount(): ?float
    {
        return $this->input(self::MAX_DISCOUNT_AMOUNT) ? (float) $this->input(self::MAX_DISCOUNT_AMOUNT) : null;
    }

    public function getUsesPerUser(): ?int
    {
        return $this->input(self::USES_PER_USER) ? (int) $this->input(self::USES_PER_USER) : null;
    }

    public function getTotalUses(): ?int
    {
        return $this->input(self::TOTAL_USES) ? (int) $this->input(self::TOTAL_USES) : null;
    }

    public function getStartsAt(): string
    {
        return $this->input(self::STARTS_AT);
    }

    public function getExpiresAt(): string
    {
        return $this->input(self::EXPIRES_AT);
    }

    public function getIsActive(): bool
    {
        return (bool) $this->input(self::IS_ACTIVE);
    }

    public function getDescription(): ?string
    {
        return $this->input(self::DESCRIPTION);
    }
}
