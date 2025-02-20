<?php

declare(strict_types=1);

namespace App\Http\Resources\Categories;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->toArray();
    }
}
