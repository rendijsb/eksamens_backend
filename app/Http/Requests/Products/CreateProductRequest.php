<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    const CATEGORY_ID = 'category_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const SALE_PRICE = 'sale_price';
    const STOCK = 'stock';
    const SPECIFICATIONS = 'specifications';
    const ADDITIONAL_INFO = 'additional_info';
    const STATUS = 'status';

    public function rules(): array
    {
        return [
            self::CATEGORY_ID => 'required|integer|exists:categories,id',
            self::NAME => 'required|max:255',
            self::DESCRIPTION => 'required',
            self::PRICE => 'required|numeric|min:0',
            self::SALE_PRICE => 'nullable|numeric|min:0',
            self::STOCK => 'required|integer|min:0',
            self::SPECIFICATIONS => 'nullable',
            self::ADDITIONAL_INFO => 'nullable',
            self::STATUS => 'nullable|in:active,inactive',
        ];
    }

    public function getCategoryId(): int
    {
        return (int) $this->input(self::CATEGORY_ID);
    }

    public function getName(): string
    {
        return $this->input(self::NAME);
    }

    public function getDescription(): string
    {
        return $this->input(self::DESCRIPTION);
    }

    public function getPrice(): int
    {
        return (int) $this->input(self::PRICE);
    }

    public function getSalePrice(): ?int
    {
        return $this->has(self::SALE_PRICE) ? (int) $this->input(self::SALE_PRICE) : null;
    }

    public function getStock(): int
    {
        return (int) $this->input(self::STOCK);
    }

    public function getSpecifications(): ?string
    {
        return $this->input(self::SPECIFICATIONS);
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->input(self::ADDITIONAL_INFO);
    }

    public function getStatus(): ?string
    {
        return $this->input(self::STATUS);
    }
}
