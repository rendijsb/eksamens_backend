<?php

declare(strict_types=1);

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class GetAllCategoriesRequest extends FormRequest
{
    const SEARCH = 'search';
    const SORT_BY = 'sort_by';
    const SORT_DIR = 'sort_dir';

    public function rules(): array
    {
        return [
            self::SEARCH => 'nullable|string|max:255',
            self::SORT_BY => 'nullable|string|in:id,name,description,created_at,slug',
            self::SORT_DIR => 'nullable|string|in:asc,desc',
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
}
