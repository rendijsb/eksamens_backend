<?php

declare(strict_types=1);

namespace App\Http\Resources\Categories;

use App\Enums\Images\ImageTypeEnum;
use App\Models\Categories\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public $resource = Category::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'description' => $this->resource->getDescription(),
            'slug' => $this->resource->getSlug(),
            'image' => url('/' . ImageTypeEnum::CATEGORY->value . '/image/' . $this->resource->getRelatedImage()?->getImageLink()),
            'created_at' => $this->resource->getCreatedAt(),
            'products_count' => $this->resource->relatedProducts()?->count(),
        ];
    }
}
