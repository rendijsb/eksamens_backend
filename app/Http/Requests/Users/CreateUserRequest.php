<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const PHONE = 'phone';
    const ROLE = 'role';

    public function rules(): array
    {
        return [
            self::NAME => 'required|max:255',
            self::EMAIL => 'required|email|unique:users',
            self::PASSWORD => 'required|confirmed|min:8',
            self::PHONE => 'nullable|numeric|min:8',
            self::ROLE => 'required|exists:roles,id',
        ];
    }

    public function getName(): string
    {
        return $this->input(self::NAME);
    }

    public function getEmail(): string
    {
        return $this->input(self::EMAIL);
    }

    public function getPhone(): ?string
    {
        return $this->input(self::PHONE);
    }

    public function getPassword(): string
    {
        return $this->input(self::PASSWORD);
    }

    public function getRole(): int
    {
        return (int)$this->input(self::ROLE);
    }
}
