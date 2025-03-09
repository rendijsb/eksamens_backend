<?php

declare(strict_types=1);

namespace App\Http\Resources\Products;

use App\Models\Products\ProductImage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public $resource = ProductImage::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'product_id' => $this->resource->getProductId(),
            'image_url' => url('/products/image/' . $this->resource->getImage()),
            'is_primary' => $this->resource->getIsPrimary(),
            'created_at' => $this->resource->getCreatedAt(),
        ];
    }
}
