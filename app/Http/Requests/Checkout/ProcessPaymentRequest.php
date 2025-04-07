<?php

declare(strict_types=1);

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    const PAYMENT_INTENT_ID = 'payment_intent_id';
    const ORDER_ID = 'order_id';

    public function rules(): array
    {
        return [
            self::PAYMENT_INTENT_ID => 'required|string',
            self::ORDER_ID => 'required|exists:orders,id',
        ];
    }

    public function getPaymentIntentId(): string
    {
        return $this->input(self::PAYMENT_INTENT_ID);
    }

    public function getOrderId(): int
    {
        return (int) $this->input(self::ORDER_ID);
    }
}
