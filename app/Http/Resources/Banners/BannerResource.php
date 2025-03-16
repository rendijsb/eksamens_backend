<?php

declare(strict_types=1);

namespace App\Http\Resources\Banners;

use App\Enums\Images\ImageTypeEnum;
use App\Models\Banners\Banner;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public $resource = Banner::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'title' => $this->resource->getTitle(),
            'subtitle' => $this->resource->getSubtitle(),
            'button_link' => $this->resource->getButtonLink(),
            'button_text' => $this->resource->getButtonText(),
            'is_active' => $this->resource->getIsActive(),
            'created_at' => $this->resource->getCreatedAt(),
            'image_link' => url('/' . ImageTypeEnum::BANNER->value . '/image/' . $this->resource->getRelatedImage()?->getImageLink()),
        ];
    }
}
