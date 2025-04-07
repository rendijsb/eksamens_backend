<?php

declare(strict_types=1);

namespace App\Models\Orders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const TRANSACTION_ID = 'transaction_id';
    const AMOUNT = 'amount';
    const PAYMENT_METHOD = 'payment_method';
    const STATUS = 'status';
    const PAYMENT_DETAILS = 'payment_details';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::ORDER_ID,
        self::TRANSACTION_ID,
        self::AMOUNT,
        self::PAYMENT_METHOD,
        self::STATUS,
        self::PAYMENT_DETAILS,
    ];

    protected $casts = [
        self::PAYMENT_DETAILS => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getOrderId(): int
    {
        return $this->getAttribute(self::ORDER_ID);
    }

    public function getTransactionId(): ?string
    {
        return $this->getAttribute(self::TRANSACTION_ID);
    }

    public function getAmount(): float
    {
        return (float) $this->getAttribute(self::AMOUNT);
    }

    public function getPaymentMethod(): string
    {
        return $this->getAttribute(self::PAYMENT_METHOD);
    }

    public function getStatus(): string
    {
        return $this->getAttribute(self::STATUS);
    }

    public function getPaymentDetails(): array
    {
        return $this->getAttribute(self::PAYMENT_DETAILS) ?? [];
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }
}
