<?php

declare(strict_types=1);

namespace App\Http\Resources\Products;

use App\Enums\Images\ImageTypeEnum;
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
            'sold' => $this->resource->getSold(),
            'specifications' => $this->resource->getSpecifications(),
            'additional_info' => $this->resource->getAdditionalInfo(),
            'status' => $this->resource->getStatus(),
            'category' => $this->resource->getRelatedCategory()->getName(),
            'primary_image' => $this->resource->getRelatedPrimaryImage()?->getImageLink()
                ? url('/' . ImageTypeEnum::PRODUCT->value . '/image/' . $this->resource->getRelatedPrimaryImage()?->getImageLink())
                : null,
            'is_sale_active' => $this->resource->isSaleActive(),
            'sale_ends_at' => $this->resource->getSaleEndsAt(),
            'average_rating' => $this->resource->getAverageRating(),
            'reviews_count' => $this->resource->getReviewsCount(),
            'category_id' => $this->resource->getCategoryId(),
            'created_at' => $this->resource->getCreatedAt(),
        ];
    }
}
