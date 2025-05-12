<?php

declare(strict_types=1);

namespace App\Http\Resources\Pages;

use App\Models\Pages\Contact;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public $resource = Contact::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'address' => $this->resource->getAddress(),
            'email' => $this->resource->getEmail(),
            'phone' => $this->resource->getPhone(),
            'working_hours' => $this->resource->getWorkingHours(),
            'map_embed_code' => $this->resource->getMapEmbedCode(),
            'additional_info' => $this->resource->getAdditionalInfo(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
