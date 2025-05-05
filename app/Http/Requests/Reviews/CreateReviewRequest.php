<?php

declare(strict_types=1);

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
{
    const PRODUCT_ID = 'product_id';
    const RATING = 'rating';
    const REVIEW_TEXT = 'review_text';

    public function rules(): array
    {
        return [
            self::PRODUCT_ID => 'required|exists:products,id',
            self::RATING => 'required|integer|min:1|max:5',
            self::REVIEW_TEXT => 'nullable|string|max:1000',
        ];
    }

    public function getProductId(): int
    {
        return (int) $this->input(self::PRODUCT_ID);
    }

    public function getRating(): int
    {
        return (int) $this->input(self::RATING);
    }

    public function getReviewText(): ?string
    {
        return $this->input(self::REVIEW_TEXT);
    }
}
