<?php

declare(strict_types=1);

namespace App\Http\Resources\Categories;

use App\Models\Categories\Category;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    public $resource = Category::class;

    public function toArray($request): array
    {
        $image = $this->resource->getRelatedImage();

        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'description' => $this->resource->getDescription(),
            'slug' => $this->resource->getSlug(),
            'image' => $image ? Storage::disk('s3')->url($image->getType() . '/' . $image->getImageLink()) : null,
            'created_at' => $this->resource->getCreatedAt(),
            'products_count' => $this->resource->related_products_count ?? $this->resource->relatedProducts()?->count(),
            'active_products_count' => $this->resource->active_products_count ?? $this->resource->relatedActiveProducts()?->count(),
        ];
    }
}
