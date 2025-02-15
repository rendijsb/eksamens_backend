<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'userId' => 'required|exists:users,id',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'userId' => $this->route('userId'),
        ]);
    }
}
