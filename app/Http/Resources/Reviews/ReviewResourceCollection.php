<?php

declare(strict_types=1);

namespace App\Http\Resources\Reviews;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReviewResourceCollection extends ResourceCollection
{
    public $collects = ReviewResource::class;
}
