<?php

declare(strict_types=1);

namespace App\Http\Requests\Addresses;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAddressRequest extends FormRequest
{
    const ADDRESS_ID = 'addressId';

    public function rules(): array
    {
        return [
            self::ADDRESS_ID => 'required|exists:addresses,id',
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
}
