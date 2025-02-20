<?php

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCategoryRequest extends FormRequest
{
    const CATEGORY_ID = 'categoryId';

    public function rules(): array
    {
        return [
            self::CATEGORY_ID => 'required|exists:categories,id',
        ];
    }

    public function getCategoryId(): int
    {
        return (int) $this->route(self::CATEGORY_ID);
    }
}
