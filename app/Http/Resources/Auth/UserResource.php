<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Models\Users\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected ?string $token = null;

    public function toArray($request): array
    {
        $data = [
            'id' => $this->resource->id,
            User::NAME => $this->resource->getName(),
            User::EMAIL => $this->resource->getEmail(),
            'role' => $this->resource->getRoleId(),
            'phone' => $this->resource->getPhone(),
            'created_at' => $this->resource->getCreatedAt(),
        ];

        if ($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }

    public function withToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }
}
