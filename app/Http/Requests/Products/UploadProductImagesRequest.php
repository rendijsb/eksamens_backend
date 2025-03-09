<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductImagesRequest extends FormRequest
{
    const PRODUCT_ID = 'productId';
    const IMAGES = 'images';

    public function rules(): array
    {
        return [
            self::IMAGES => 'required|array',
            self::IMAGES . '.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

    public function getImages(): array
    {
        return $this->file(self::IMAGES) ?? [];
    }
}
