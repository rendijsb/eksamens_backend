<?php

declare(strict_types=1);

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductImageResourceCollection extends ResourceCollection
{
    public $collects = ProductImageResource::class;
}
