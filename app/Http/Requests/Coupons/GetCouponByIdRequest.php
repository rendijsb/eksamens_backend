<?php

declare(strict_types=1);

namespace App\Http\Requests\Coupons;

use Illuminate\Foundation\Http\FormRequest;

class GetCouponByIdRequest extends FormRequest
{
    const COUPON_ID = 'couponId';

    public function rules(): array
    {
        return [
            self::COUPON_ID => 'required|exists:coupons,id',
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
}
