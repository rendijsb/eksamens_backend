<?php

declare(strict_types=1);

namespace App\Http\Requests\Coupons;

use Illuminate\Foundation\Http\FormRequest;

class GetAllCouponsRequest extends FormRequest
{
    const SEARCH = 'search';
    const TYPE = 'type';
    const STATUS = 'status';
    const SORT_BY = 'sort_by';
    const SORT_DIR = 'sort_dir';

    public function rules(): array
    {
        return [
            self::SEARCH => 'nullable|string|max:255',
            self::TYPE => 'nullable|string|in:percentage,fixed',
            self::STATUS => 'nullable|boolean',
            self::SORT_BY => 'nullable|string|in:id,code,type,value,used_count,starts_at,expires_at,created_at',
            self::SORT_DIR => 'nullable|string|in:asc,desc',
        ];
    }

    public function getSearch(): ?string
    {
        return $this->input(self::SEARCH);
    }

    public function getType(): ?string
    {
        return $this->input(self::TYPE);
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

    public function getSortBy(): string
    {
        return $this->input(self::SORT_BY, 'created_at');
    }

    public function getSortDir(): string
    {
        return $this->input(self::SORT_DIR, 'desc');
    }
}
