<?php

declare(strict_types=1);

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class GetAllOrdersRequest extends FormRequest
{
    const SEARCH = 'search';
    const SORT_BY = 'sort_by';
    const SORT_DIR = 'sort_dir';
    const STATUS = 'status';
    const PAYMENT_STATUS = 'payment_status';
    const PAGE = 'page';

    public function rules(): array
    {
        return [
            self::SEARCH => 'nullable|string|max:255',
            self::SORT_BY => 'nullable|string|in:id,order_number,customer_name,total_amount,status,payment_status,created_at',
            self::SORT_DIR => 'nullable|string|in:asc,desc',
            self::STATUS => 'nullable|string|in:pending,processing,completed,cancelled,failed',
            self::PAYMENT_STATUS => 'nullable|string|in:pending,paid,failed,refunded',
            self::PAGE => 'nullable|integer|min:1',
        ];
    }

    public function getSearch(): ?string
    {
        return $this->input(self::SEARCH);
    }

    public function getSortBy(): string
    {
        return $this->input(self::SORT_BY, 'created_at');
    }

    public function getSortDir(): string
    {
        return $this->input(self::SORT_DIR, 'desc');
    }

    public function getStatus(): ?string
    {
        return $this->input(self::STATUS);
    }

    public function getPaymentStatus(): ?string
    {
        return $this->input(self::PAYMENT_STATUS);
    }

    public function getPage(): ?int
    {
        return $this->has(self::PAGE) ? (int) $this->input(self::PAGE) : null;
    }
}
