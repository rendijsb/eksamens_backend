<?php

declare(strict_types=1);

namespace App\Http\Resources\Carts;

use App\Enums\Images\ImageTypeEnum;
use App\Models\Carts\CartItem;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public $resource = CartItem::class;

    public function toArray($request): array
    {
        $product = $this->resource->product;

        return [
            'id' => $this->resource->getId(),
            'product_id' => $this->resource->getProductId(),
            'quantity' => $this->resource->getQuantity(),
            'price' => $this->resource->getPrice(),
            'sale_price' => $this->resource->getSalePrice(),
            'total_price' => $this->resource->getTotalPrice(),
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'slug' => $product->getSlug(),
                'image' => $product->getRelatedPrimaryImage()?->getImageLink()
                    ? url('/' . ImageTypeEnum::PRODUCT->value . '/image/' . $product->getRelatedPrimaryImage()?->getImageLink())
                    : null,
                'stock' => $product->getStock(),
                'category' => $product->getRelatedCategory()->getName(),
                'is_sale_active' => $product->isSaleActive(),
            ],
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
