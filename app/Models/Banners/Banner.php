<?php

declare(strict_types=1);

namespace App\Models\Banners;

use App\Enums\Images\ImageTypeEnum;
use App\Models\Images\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    const ID = 'id';
    const TITLE = 'title';
    const SUBTITLE = 'subtitle';
    const BUTTON_TEXT = 'button_text';
    const BUTTON_LINK = 'button_link';
    const IS_ACTIVE = 'is_active';
    const SORT_ORDER = 'sort_order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::TITLE,
        self::SUBTITLE,
        self::BUTTON_TEXT,
        self::BUTTON_LINK,
        self::IS_ACTIVE,
        self::SORT_ORDER,
    ];

    protected $casts = [
        self::IS_ACTIVE => 'boolean',
        self::SORT_ORDER => 'integer',
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getTitle(): string
    {
        return $this->getAttribute(self::TITLE);
    }

    public function getSubtitle(): ?string
    {
        return $this->getAttribute(self::SUBTITLE);
    }

    public function getButtonText(): string
    {
        return $this->getAttribute(self::BUTTON_TEXT);
    }

    public function getButtonLink(): string
    {
        return $this->getAttribute(self::BUTTON_LINK);
    }

    public function getIsActive(): bool
    {
        return $this->getAttribute(self::IS_ACTIVE);
    }

    public function getSortOrder(): int
    {
        return $this->getAttribute(self::SORT_ORDER);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function getRelatedImage(): ?Image
    {
        return $this->hasOne(Image::class, Image::RELATED_ID, self::ID)
            ->where(Image::TYPE, ImageTypeEnum::BANNER->value)
            ->first();
    }
}
