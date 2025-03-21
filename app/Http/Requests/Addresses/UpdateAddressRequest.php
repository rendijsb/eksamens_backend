<?php

declare(strict_types=1);

namespace App\Http\Requests\Addresses;

use App\Models\Users\Address;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    const ADDRESS_ID = 'addressId';

    public function rules(): array
    {
        return [
            Address::NAME => 'required|string|max:255',
            Address::PHONE => 'required|string|max:20',
            Address::STREET_ADDRESS => 'required|string|max:255',
            Address::APARTMENT => 'nullable|string|max:50',
            Address::CITY => 'required|string|max:100',
            Address::STATE => 'nullable|string|max:100',
            Address::POSTAL_CODE => 'required|string|max:20',
            Address::COUNTRY => 'required|string|max:100',
            Address::IS_DEFAULT => 'sometimes|boolean',
            Address::TYPE => 'required|in:shipping,billing,both',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::ADDRESS_ID => $this->route(self::ADDRESS_ID),
        ]);
    }

    public function getAddressId(): int
    {
        return (int) $this->route(self::ADDRESS_ID);
    }

    public function getName(): string
    {
        return $this->input(Address::NAME);
    }

    public function getPhone(): string
    {
        return $this->input(Address::PHONE);
    }

    public function getStreetAddress(): string
    {
        return $this->input(Address::STREET_ADDRESS);
    }

    public function getApartment(): ?string
    {
        return $this->input(Address::APARTMENT);
    }

    public function getCity(): string
    {
        return $this->input(Address::CITY);
    }

    public function getState(): ?string
    {
        return $this->input(Address::STATE);
    }

    public function getPostalCode(): string
    {
        return $this->input(Address::POSTAL_CODE);
    }

    public function getCountry(): string
    {
        return $this->input(Address::COUNTRY);
    }

    public function getIsDefault(): bool
    {
        $value = $this->input(Address::IS_DEFAULT);

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }

    public function getType(): string
    {
        return $this->input(Address::TYPE);
    }
}
