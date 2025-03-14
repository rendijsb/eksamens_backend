<?php

declare(strict_types=1);

namespace App\Http\Resources\Images;

use App\Models\Images\Image;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public $resource = Image::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'related_id' => $this->resource->getRelatedId(),
            'image_url' => url('/' . $this->resource->getType() .'/image/' . $this->resource->getImageLink()),
            'is_primary' => $this->resource->getIsPrimary(),
            'created_at' => $this->resource->getCreatedAt(),
        ];
    }
}
