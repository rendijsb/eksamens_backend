<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Enums\Images\ImageTypeEnum;
use App\Models\Users\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    protected ?string $token = null;

    public function toArray($request): array
    {
        $profileImage = $this->resource->getProfileImage()
            ? Storage::disk('s3')->url(ImageTypeEnum::PROFILE->value . '/' . $this->resource->getProfileImage())
            : null;

        $data = [
            'id' => $this->resource->id,
            User::NAME => $this->resource->getName(),
            User::EMAIL => $this->resource->getEmail(),
            'role' => $this->resource->getRoleId(),
            'phone' => $this->resource->getPhone(),
            'created_at' => $this->resource->getCreatedAt(),
            'profile_image' => $profileImage,
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
