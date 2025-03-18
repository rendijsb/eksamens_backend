<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class GetRelatedProductsRequest extends FormRequest
{
    const CATEGORY_ID = 'category_id';
    const EXCLUDE_ID = 'exclude_id';
    const LIMIT = 'limit';

    public function rules(): array
    {
        return [
            self::CATEGORY_ID => 'required|integer|exists:categories,id',
            self::EXCLUDE_ID => 'sometimes|integer|exists:products,id',
            self::LIMIT => 'sometimes|integer|min:1|max:10',
        ];
    }

    public function getCategoryId(): int
    {
        return (int) $this->input(self::CATEGORY_ID);
    }

    public function getExcludeId(): ?int
    {
        return $this->has(self::EXCLUDE_ID) ? (int) $this->input(self::EXCLUDE_ID) : null;
    }

    public function getLimit(): int
    {
        return (int) $this->input(self::LIMIT, 4);
    }
}
