<?php

declare(strict_types=1);

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceCollection extends ResourceCollection
{
    public $collects = OrderResource::class;
}
