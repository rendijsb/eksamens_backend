<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    const USER_ID = 'userId';
    const NAME = 'name';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const ROLE = 'role';
    const PASSWORD = 'password';
    const PASSWORD_CONFIRMATION = 'password_confirmation';

    public function rules(): array
    {
        return [
            self::NAME => 'required|string|max:255',
            self::EMAIL => 'required|email|unique:users,email,' . $this->route(self::USER_ID),
            self::PHONE => 'nullable|string',
            self::ROLE => 'required|exists:roles,id',
            self::PASSWORD => 'sometimes|min:8|confirmed',
        ];
    }

    public function getUserId(): int
    {
        return (int) $this->route(self::USER_ID);
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

    public function getRole(): int
    {
        return (int) $this->input(self::ROLE);
    }

    public function getPassword(): ?string
    {
        return $this->input(self::PASSWORD);
    }
}
