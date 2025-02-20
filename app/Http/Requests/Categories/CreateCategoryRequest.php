<?php

declare(strict_types=1);

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    const NAME = 'name';
    const DESCRIPTION = 'description';

    public function rules(): array
    {
        return [
            self::NAME => 'required|max:255',
            self::DESCRIPTION => 'required|max:255',
        ];
    }

    public function getName(): string
    {
        return $this->input(self::NAME);
    }

    public function getDescription(): string
    {
        return $this->input(self::DESCRIPTION);
    }
}
