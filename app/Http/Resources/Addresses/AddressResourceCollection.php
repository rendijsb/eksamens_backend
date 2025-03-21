<?php

declare(strict_types=1);

namespace App\Http\Resources\Addresses;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressResourceCollection extends ResourceCollection
{
    public $collects = AddressResource::class;
}
