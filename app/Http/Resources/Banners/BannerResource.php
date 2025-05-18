<?php

declare(strict_types=1);

namespace App\Http\Resources\Banners;

use App\Models\Banners\Banner;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    public $resource = Banner::class;

    public function toArray($request): array
    {
        $image = $this->resource->getRelatedImage();

        return [
            'id' => $this->resource->getId(),
            'title' => $this->resource->getTitle(),
            'subtitle' => $this->resource->getSubtitle(),
            'button_link' => $this->resource->getButtonLink(),
            'button_text' => $this->resource->getButtonText(),
            'is_active' => $this->resource->getIsActive(),
            'created_at' => $this->resource->getCreatedAt(),
            'image_link' => $image ? Storage::disk('s3')->url($image->getType() . '/' . $image->getImageLink()) : null,
        ];
    }
}
