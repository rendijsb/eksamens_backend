<?php

declare(strict_types=1);

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class GetProductReviewsRequest extends FormRequest
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
