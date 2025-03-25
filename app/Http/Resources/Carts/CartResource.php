<?php

declare(strict_types=1);

namespace App\Http\Resources\Carts;

use App\Models\Carts\Cart;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public $resource = Cart::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'user_id' => $this->resource->getUserId(),
            'session_id' => $this->resource->getSessionId(),
            'items' => CartItemResource::collection($this->resource->items),
            'total_price' => $this->resource->getTotalPrice(),
            'total_items' => $this->resource->getTotalItems(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
