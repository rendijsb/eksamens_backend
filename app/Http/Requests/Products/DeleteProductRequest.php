<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class DeleteProductRequest extends FormRequest
{
    const PRODUCT_ID = 'productId';

    public function rules(): array
    {
        return [
            self::PRODUCT_ID => 'required|exists:products,id',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::PRODUCT_ID => $this->route(self::PRODUCT_ID),
        ]);
    }

    public function getProductId(): int
    {
        return (int) $this->route(self::PRODUCT_ID);
    }
}
