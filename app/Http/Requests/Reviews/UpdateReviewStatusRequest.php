<?php

declare(strict_types=1);

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewStatusRequest extends FormRequest
{
    const REVIEW_ID = 'reviewId';
    const IS_APPROVED = 'is_approved';

    public function rules(): array
    {
        return [
            self::REVIEW_ID => 'required|exists:reviews,id',
            self::IS_APPROVED => 'required|boolean',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::REVIEW_ID => $this->route(self::REVIEW_ID),
        ]);
    }

    public function getReviewId(): int
    {
        return (int) $this->route(self::REVIEW_ID);
    }

    public function getIsApproved(): bool
    {
        $value = $this->input(self::IS_APPROVED);

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }
}
