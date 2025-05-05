<?php

declare(strict_types=1);

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    const ORDER_ID = 'orderId';
    const STATUS = 'status';

    public function rules(): array
    {
        return [
            self::STATUS => 'required|string|in:pending,processing,completed,cancelled,failed',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::ORDER_ID => $this->route(self::ORDER_ID),
        ]);
    }

    public function getOrderId(): int
    {
        return (int) $this->route(self::ORDER_ID);
    }

    public function getStatus(): string
    {
        return $this->input(self::STATUS);
    }
}
