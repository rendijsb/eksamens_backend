<?php

declare(strict_types=1);

namespace App\Http\Resources\Pages;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AboutPageResourceCollection extends ResourceCollection
{
    public $collects = AboutPageResource::class;
}
