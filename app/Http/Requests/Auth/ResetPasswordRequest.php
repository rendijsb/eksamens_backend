<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function getToken(): string
    {
        return $this->input('token');
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }

    public function getPassword(): string
    {
        return $this->input('password');
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Atiestatīšanas tokens ir obligāts.',
            'email.required' => 'E-pasta adrese ir obligāta.',
            'email.email' => 'Lūdzu, ievadiet derīgu e-pasta adresi.',
            'password.required' => 'Parole ir obligāta.',
            'password.min' => 'Parolei jābūt vismaz 8 rakstzīmēm.',
            'password.confirmed' => 'Paroles apstiprinājums nesakrīt.',
        ];
    }
}
