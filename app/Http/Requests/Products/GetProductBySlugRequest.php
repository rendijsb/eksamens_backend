<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class GetProductBySlugRequest extends FormRequest
{
    const SLUG = 'slug';

    public function rules(): array
    {
        return [
            self::SLUG => 'required|exists:products,slug',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::SLUG => $this->route(self::SLUG),
        ]);
    }

    public function getSlug(): string
    {
        return (string) $this->route(self::SLUG);
    }
}
