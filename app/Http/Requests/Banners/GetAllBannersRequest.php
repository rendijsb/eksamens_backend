<?php

declare(strict_types=1);

namespace App\Http\Requests\Banners;

use Illuminate\Foundation\Http\FormRequest;

class GetAllBannersRequest extends FormRequest
{
    const SEARCH = 'search';
    const SORT_BY = 'sort_by';
    const SORT_DIR = 'sort_dir';
    const STATUS = 'status';

    public function rules(): array
    {
        return [
            self::SEARCH => 'nullable|string|max:255',
            self::SORT_BY => 'nullable|string|in:id,name,price,sale_price,stock,status,created_at,slug,category_id',
            self::SORT_DIR => 'nullable|string|in:asc,desc',
            self::STATUS => 'nullable|boolean',
        ];
    }

    public function getSearch(): ?string
    {
        return $this->input(self::SEARCH);
    }

    public function getSortBy(): string
    {
        return $this->input(self::SORT_BY, 'id');
    }

    public function getSortDir(): string
    {
        return $this->input(self::SORT_DIR, 'desc');
    }

    public function getStatus(): ?bool
    {
        if (!$this->has(self::STATUS)) {
            return null;
        }

        $value = $this->input(self::STATUS);

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }
}
