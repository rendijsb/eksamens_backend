<?php

declare(strict_types=1);

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class CreateCategoryRequest extends FormRequest
{
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';

    public function rules(): array
    {
        return [
            self::NAME => 'required|max:255',
            self::DESCRIPTION => 'required|max:255',
            self::IMAGE => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

    public function getImage(): UploadedFile
    {
        return $this->file(self::IMAGE);
    }
}
