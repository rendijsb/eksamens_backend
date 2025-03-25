<?php

declare(strict_types=1);

namespace App\Http\Requests\Carts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    const ITEM_ID = 'item_id';
    const QUANTITY = 'quantity';

    public function rules(): array
    {
        return [
            self::QUANTITY => 'required|integer|min:0|max:100',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::ITEM_ID => $this->route(self::ITEM_ID),
        ]);
    }

    public function getItemId(): int
    {
        return (int) $this->route(self::ITEM_ID);
    }

    public function getQuantity(): int
    {
        return (int) $this->input(self::QUANTITY);
    }
}
