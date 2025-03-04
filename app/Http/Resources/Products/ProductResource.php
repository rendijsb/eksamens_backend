<?php

declare(strict_types=1);

namespace App\Http\Resources\Products;

use App\Models\Products\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public $resource = Product::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'description' => $this->resource->getDescription(),
            'slug' => $this->resource->getSlug(),
            'price' => $this->resource->getPrice(),
            'sale_price' => $this->resource->getSalePrice(),
            'stock' => $this->resource->getStock(),
            'specifications' => $this->resource->getSpecifications(),
            'additional_info' => $this->resource->getAdditionalInfo(),
            'status' => $this->resource->getStatus(),
            'category' => $this->resource->getRelatedCategory()->getName(),
            'created_at' => $this->resource->getCreatedAt(),
        ];
    }
}
