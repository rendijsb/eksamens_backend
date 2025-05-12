<?php

declare(strict_types=1);

namespace App\Http\Requests\Coupons;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    const CODE = 'code';
    const ORDER_AMOUNT = 'order_amount';

    public function rules(): array
    {
        return [
            self::CODE => 'required|string|max:50',
            self::ORDER_AMOUNT => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            self::CODE . '.required' => 'Kupona kods ir obligāts',
            self::ORDER_AMOUNT . '.required' => 'Pasūtījuma summa ir obligāta',
            self::ORDER_AMOUNT . '.min' => 'Pasūtījuma summa nevar būt negatīva',
        ];
    }

    public function getCode(): string
    {
        return strtoupper($this->input(self::CODE));
    }

    public function getOrderAmount(): float
    {
        return (float) $this->input(self::ORDER_AMOUNT);
    }
}
