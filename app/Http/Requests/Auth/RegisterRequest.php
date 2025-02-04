<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';

    public function rules(): array
    {
        return [
            self::NAME => 'required|max:255',
            self::EMAIL => 'required|email|unique:users',
            self::PASSWORD => 'required|confirmed|min:8',
        ];
    }
}
