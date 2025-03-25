<?php

declare(strict_types=1);

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    const PRODUCT_ID = 'product_id';
    const QUANTITY = 'quantity';

    public function rules(): array
    {
        return [
            self::PRODUCT_ID => 'required|integer|exists:products,id',
            self::QUANTITY => 'integer|min:1|max:100',
        ];
    }

    public function getProductId(): int
    {
        return (int) $this->input(self::PRODUCT_ID);
    }

    public function getQuantity(): int
    {
        return (int) $this->input(self::QUANTITY, 1);
    }
}
