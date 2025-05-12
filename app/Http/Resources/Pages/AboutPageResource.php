<?php

declare(strict_types=1);

namespace App\Http\Resources\Pages;

use App\Models\Pages\AboutPage;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutPageResource extends JsonResource
{
    public $resource = AboutPage::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'title' => $this->resource->getTitle(),
            'content' => $this->resource->getContent(),
            'is_active' => $this->resource->getIsActive(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
