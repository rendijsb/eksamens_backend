<?php

declare(strict_types=1);

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class UnsubscribeRequest extends FormRequest
{
    const TOKEN = 'token';

    public function rules(): array
    {
        return [
            self::TOKEN => 'required|string',
        ];
    }

    public function getToken(): string
    {
        return $this->input(self::TOKEN);
    }
}
