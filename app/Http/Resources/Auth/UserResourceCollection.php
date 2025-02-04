<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'users' => UserResource::collection($this->collection)
        ];
    }
}
