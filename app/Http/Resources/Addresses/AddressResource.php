<?php

declare(strict_types=1);

namespace App\Http\Resources\Addresses;

use App\Models\Users\Address;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public $resource = Address::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'phone' => $this->resource->getPhone(),
            'street_address' => $this->resource->getStreetAddress(),
            'apartment' => $this->resource->getApartment(),
            'city' => $this->resource->getCity(),
            'state' => $this->resource->getState(),
            'postal_code' => $this->resource->getPostalCode(),
            'country' => $this->resource->getCountry(),
            'is_default' => $this->resource->getIsDefault(),
            'type' => $this->resource->getType(),
            'full_address' => $this->resource->getFullAddress(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
