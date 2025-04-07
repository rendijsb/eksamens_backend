<?php

declare(strict_types=1);

namespace App\Http\Resources\Transactions;

use App\Models\Orders\PaymentTransaction;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTransactionResource extends JsonResource
{
    public $resource = PaymentTransaction::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'transaction_id' => $this->resource->getTransactionId(),
            'amount' => $this->resource->getAmount(),
            'payment_method' => $this->resource->getPaymentMethod(),
            'status' => $this->resource->getStatus(),
            'payment_details' => $this->resource->getPaymentDetails(),
            'created_at' => $this->resource->getCreatedAt(),
        ];
    }
}
