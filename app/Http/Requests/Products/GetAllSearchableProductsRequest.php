<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class GetAllSearchableProductsRequest extends FormRequest
{
    const SEARCH = 'search';
    const CATEGORY_ID = 'category_id';
    const MIN_PRICE = 'min_price';
    const MAX_PRICE = 'max_price';
    const SORT_BY = 'sort_by';
    const SORT_DIR = 'sort_dir';
    const PAGE = 'page';
    const PER_PAGE = 'per_page';
    const CREATED_AT = 'created_at';

    public function rules(): array
    {
        return [
            self::SEARCH => 'sometimes|string|nullable',
            self::CATEGORY_ID => 'sometimes|integer|nullable',
            self::MIN_PRICE => 'sometimes|numeric|min:0|nullable',
            self::MAX_PRICE => 'sometimes|numeric|gte:min_price|nullable',
            self::SORT_BY => 'sometimes|string|in:name,price,created_at,sold',
            self::SORT_DIR => 'sometimes|string|in:asc,desc',
            self::PAGE => 'sometimes|integer|min:1',
            self::PER_PAGE => 'sometimes|integer|min:1|max:50',
        ];
    }

    public function getSearch(): ?string
    {
        return $this->input(self::SEARCH);
    }

    public function getCategoryId(): ?int
    {
        return $this->integer(self::CATEGORY_ID);
    }

    public function getMinPrice(): ?float
    {
        return $this->input(self::MIN_PRICE) !== null ? (float) $this->input(self::MIN_PRICE) : null;
    }

    public function getMaxPrice(): ?float
    {
        return $this->input(self::MAX_PRICE) !== null ? (float) $this->input(self::MAX_PRICE) : null;
    }

    public function getSortBy(): string
    {
        return $this->input(self::SORT_BY, self::CREATED_AT);
    }

    public function getSortDir(): string
    {
        return $this->input(self::SORT_DIR, 'desc');
    }

    public function getPerPage(): int
    {
        return (int) $this->input(self::PER_PAGE, 12);
    }
}
