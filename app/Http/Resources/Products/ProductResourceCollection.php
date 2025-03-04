<?php

declare(strict_types=1);

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResourceCollection extends ResourceCollection
{
    public $collects = ProductResource::class;
}
