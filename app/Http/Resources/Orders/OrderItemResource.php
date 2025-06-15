<?php

declare(strict_types=1);

namespace App\Http\Resources\Orders;

use App\Models\Orders\OrderItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderItemResource extends JsonResource
{
    public $resource = OrderItem::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'product_id' => $this->resource->getProductId(),
            'product_name' => $this->resource->getProductName(),
            'product_price' => $this->resource->getProductPrice(),
            'product_sale_price' => $this->resource->getProductSalePrice(),
            'quantity' => $this->resource->getQuantity(),
            'total_price' => $this->resource->getTotalPrice(),
            'product' => $this->when($this->resource->product, function () {
                $product = $this->resource->product;
                $primaryImage = $product->getRelatedPrimaryImage();

                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'slug' => $product->getSlug(),
                    'image' => $primaryImage
                        ? Storage::disk('s3')->url($primaryImage->getType() . '/' . $primaryImage->getImageLink())
                        : null,
                ];
            }),
        ];
    }
}
