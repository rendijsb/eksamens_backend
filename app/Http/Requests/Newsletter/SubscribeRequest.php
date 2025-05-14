<?php

declare(strict_types=1);

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    const EMAIL = 'email';

    public function rules(): array
    {
        return [
            self::EMAIL => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            self::EMAIL . '.required' => 'E-pasta adrese ir obligāta',
            self::EMAIL . '.email' => 'Lūdzu, ievadiet derīgu e-pasta adresi',
        ];
    }

    public function getEmail(): string
    {
        return $this->input(self::EMAIL);
    }
}
