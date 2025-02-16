<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class GetUserByIdRequest extends FormRequest
{
    const USER_ID = 'userId';
    public function rules(): array
    {
        return [
            self::USER_ID => 'required|exists:users,id',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::USER_ID => $this->route(self::USER_ID),
        ]);
    }

    public function getUserId(): int
    {
        return (int) $this->route(self::USER_ID);
    }
}
