<?php

declare(strict_types=1);

namespace App\Http\Resources\Categories;

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
            'created_at' => $this->resource->getCreatedAt(),
        ];
    }
}
