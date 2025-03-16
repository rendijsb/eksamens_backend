<?php

declare(strict_types=1);

namespace App\Http\Resources\Banners;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BannerResourceCollection extends ResourceCollection
{
    public $collects = BannerResource::class;
}
