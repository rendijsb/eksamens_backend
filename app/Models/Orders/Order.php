<?php

declare(strict_types=1);

namespace App\Models\Orders;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use App\Enums\Payments\TransactionStatusEnum;
use App\Models\Users\Address;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const ORDER_NUMBER = 'order_number';
    const TOTAL_AMOUNT = 'total_amount';
    const STATUS = 'status';
    const PAYMENT_METHOD = 'payment_method';
    const PAYMENT_STATUS = 'payment_status';
    const TRANSACTION_ID = 'transaction_id';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const BILLING_ADDRESS_ID = 'billing_address_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_PHONE = 'customer_phone';
    const SHIPPING_ADDRESS_DETAILS = 'shipping_address_details';
    const BILLING_ADDRESS_DETAILS = 'billing_address_details';
    const NOTES = 'notes';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::ORDER_NUMBER,
        self::TOTAL_AMOUNT,
        self::STATUS,
        self::PAYMENT_METHOD,
        self::PAYMENT_STATUS,
        self::TRANSACTION_ID,
        self::SHIPPING_ADDRESS_ID,
        self::BILLING_ADDRESS_ID,
        self::CUSTOMER_NAME,
        self::CUSTOMER_EMAIL,
        self::CUSTOMER_PHONE,
        self::SHIPPING_ADDRESS_DETAILS,
        self::BILLING_ADDRESS_DETAILS,
        self::NOTES,
    ];

    protected $casts = [
        self::STATUS => OrderStatusEnum::class,
        self::PAYMENT_STATUS => PaymentStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, self::SHIPPING_ADDRESS_ID);
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, self::BILLING_ADDRESS_ID);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getUserId(): int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getOrderNumber(): string
    {
        return $this->getAttribute(self::ORDER_NUMBER);
    }

    public function getTotalAmount(): float
    {
        return (float) $this->getAttribute(self::TOTAL_AMOUNT);
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->getAttribute(self::STATUS);
    }

    public function getPaymentMethod(): string
    {
        return $this->getAttribute(self::PAYMENT_METHOD);
    }

    public function getPaymentStatus(): PaymentStatusEnum
    {
        return $this->getAttribute(self::PAYMENT_STATUS);
    }

    public function getTransactionId(): ?string
    {
        return $this->getAttribute(self::TRANSACTION_ID);
    }

    public function getShippingAddressId(): ?int
    {
        return $this->getAttribute(self::SHIPPING_ADDRESS_ID);
    }

    public function getBillingAddressId(): ?int
    {
        return $this->getAttribute(self::BILLING_ADDRESS_ID);
    }

    public function getCustomerName(): string
    {
        return $this->getAttribute(self::CUSTOMER_NAME);
    }

    public function getCustomerEmail(): string
    {
        return $this->getAttribute(self::CUSTOMER_EMAIL);
    }

    public function getCustomerPhone(): ?string
    {
        return $this->getAttribute(self::CUSTOMER_PHONE);
    }

    public function getShippingAddressDetails(): ?string
    {
        return $this->getAttribute(self::SHIPPING_ADDRESS_DETAILS);
    }

    public function getBillingAddressDetails(): ?string
    {
        return $this->getAttribute(self::BILLING_ADDRESS_DETAILS);
    }

    public function getNotes(): ?string
    {
        return $this->getAttribute(self::NOTES);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function isPending(): bool
    {
        return $this->getStatus() === OrderStatusEnum::STATUS_PENDING->value;
    }

    public function isProcessing(): bool
    {
        return $this->getStatus() === OrderStatusEnum::STATUS_PROCESSING->value;
    }

    public function isCompleted(): bool
    {
        return $this->getStatus() === OrderStatusEnum::STATUS_COMPLETED->value;
    }

    public function isCancelled(): bool
    {
        return $this->getStatus() === OrderStatusEnum::STATUS_CANCELLED->value;
    }

    public function isFailed(): bool
    {
        return $this->getStatus() === OrderStatusEnum::STATUS_FAILED->value;
    }

    public function isPaymentCompleted(): bool
    {
        return $this->getPaymentStatus() === TransactionStatusEnum::COMPLETED->value;
    }

    public function isPaymentPending(): bool
    {
        return $this->getPaymentStatus() === TransactionStatusEnum::PENDING->value;
    }

    public function isPaymentFailed(): bool
    {
        return $this->getPaymentStatus() === TransactionStatusEnum::FAILED->value;
    }

    public function isRefunded(): bool
    {
        return $this->getPaymentStatus() === TransactionStatusEnum::REFUNDED->value;
    }
}
