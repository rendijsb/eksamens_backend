<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            User::EMAIL => 'required|email',
            User::PASSWORD => 'required',
        ];
    }
}
