<?php

declare(strict_types=1);

namespace App\Http\Resources\Coupons;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CouponResourceCollection extends ResourceCollection
{
    public $collects = CouponResource::class;
}
