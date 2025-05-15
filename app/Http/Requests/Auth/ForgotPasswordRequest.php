<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }

    public function messages(): array
    {
        return [
            'email.required' => 'E-pasta adrese ir obligāta.',
            'email.email' => 'Lūdzu, ievadiet derīgu e-pasta adresi.',
        ];
    }
}
