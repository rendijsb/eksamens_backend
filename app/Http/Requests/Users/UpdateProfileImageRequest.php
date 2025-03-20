<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdateProfileImageRequest extends FormRequest
{
    private const PROFILE_IMAGE = 'profile_image';

    public function rules(): array
    {
        return [
            self::PROFILE_IMAGE => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function getProfileImage(): UploadedFile
    {
        return $this->file(self::PROFILE_IMAGE);
    }
}
