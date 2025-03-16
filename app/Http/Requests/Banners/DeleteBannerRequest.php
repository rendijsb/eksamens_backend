<?php

namespace App\Http\Requests\Banners;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBannerRequest extends FormRequest
{
    const BANNER_ID = 'bannerId';

    public function rules(): array
    {
        return [
            self::BANNER_ID => 'required|exists:banners,id',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            self::BANNER_ID => $this->route(self::BANNER_ID),
        ]);
    }

    public function getBannerId(): int
    {
        return (int) $this->route(self::BANNER_ID);
    }
}
