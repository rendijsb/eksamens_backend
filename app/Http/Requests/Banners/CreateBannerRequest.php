<?php

declare(strict_types=1);

namespace App\Http\Requests\Banners;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class CreateBannerRequest extends FormRequest
{
    const TITLE = 'title';
    const SUBTITLE = 'subtitle';
    const IS_ACTIVE = 'is_active';
    const BUTTON_TEXT = 'button_text';
    const BUTTON_LINK = 'button_link';
    const IMAGE = 'image';

    public function rules(): array
    {
        return [
            self::TITLE => 'required|string|max:255',
            self::SUBTITLE => 'required|string|max:255',
            self::IS_ACTIVE => 'required|boolean',
            self::BUTTON_TEXT => 'required|string',
            self::BUTTON_LINK => 'required|string',
            self::IMAGE => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function getTitle(): string
    {
        return $this->input(self::TITLE);
    }

    public function getSubtitle(): string
    {
        return $this->input(self::SUBTITLE);
    }

    public function getIsActive(): bool
    {
        $value = $this->input(self::IS_ACTIVE);

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }

    public function getButtonText(): string
    {
        return $this->input(self::BUTTON_TEXT);
    }

    public function getButtonLink(): string
    {
        return $this->input(self::BUTTON_LINK);
    }

    public function getImage(): UploadedFile
    {
        return $this->file(self::IMAGE);
    }
}
