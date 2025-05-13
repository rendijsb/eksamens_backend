<?php

declare(strict_types=1);

namespace App\Http\Resources\Orders;

use App\Http\Resources\Transactions\PaymentTransactionResource;
use App\Models\Orders\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public $resource = Order::class;

    public function toArray($request): array
    {
        $shippingAddressDetails = $this->resource->getShippingAddressDetails()
            ? json_decode($this->resource->getShippingAddressDetails(), true)
            : null;

        $billingAddressDetails = $this->resource->getBillingAddressDetails()
            ? json_decode($this->resource->getBillingAddressDetails(), true)
            : null;

        $subtotal = $this->resource->getSubtotal();
        if ($subtotal <= 0 && $this->resource->relationLoaded('items')) {
            $subtotal = $this->resource->items->sum('total_price');
        }

        $couponDiscount = $this->resource->getCouponDiscount();

        return [
            'id' => $this->resource->getId(),
            'order_number' => $this->resource->getOrderNumber(),
            'subtotal' => $subtotal,
            'coupon_discount' => $couponDiscount,
            'total_amount' => $this->resource->getTotalAmount(),
            'status' => $this->resource->getStatus(),
            'payment_method' => $this->resource->getPaymentMethod(),
            'payment_status' => $this->resource->getPaymentStatus(),
            'transaction_id' => $this->resource->getTransactionId(),
            'customer_name' => $this->resource->getCustomerName(),
            'customer_email' => $this->resource->getCustomerEmail(),
            'customer_phone' => $this->resource->getCustomerPhone(),
            'shipping_address' => $shippingAddressDetails,
            'billing_address' => $billingAddressDetails,
            'notes' => $this->resource->getNotes(),
            'coupon' => $this->when($this->resource->hasCoupon(), function () {
                return [
                    'id' => $this->resource->getCouponId(),
                    'code' => $this->resource->getCouponCode(),
                    'discount_amount' => $this->resource->getCouponDiscount(),
                ];
            }),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'transactions' => PaymentTransactionResource::collection($this->whenLoaded('transactions')),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
