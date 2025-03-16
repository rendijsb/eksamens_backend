<?php

declare(strict_types=1);

namespace App\Models\Categories;

use App\Enums\Images\ImageTypeEnum;
use App\Enums\Products\ProductEnum;
use App\Models\Images\Image;
use App\Models\Products\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    const ID = 'id';
    const NAME = 'name';
    const SLUG = 'slug';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::NAME,
        self::DESCRIPTION,
        self::SLUG,
    ];

    public function getName(): string
    {
        return $this->getAttribute(self::NAME);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getSlug(): string
    {
        return $this->getAttribute(self::SLUG);
    }

    public function getDescription(): string
    {
        return $this->getAttribute(self::DESCRIPTION);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function relatedProducts(): ?HasMany
    {
        return $this->hasMany(Product::class, Product::CATEGORY_ID, self::ID);
    }

    public function relatedActiveProducts(): ?HasMany
    {
        return $this->hasMany(Product::class, Product::CATEGORY_ID, self::ID)
            ->where(Product::STATUS, '=', ProductEnum::ACTIVE);
    }

    public function getRelatedImage(): ?Image
    {
        return $this->hasOne(Image::class, Image::RELATED_ID, self::ID)
            ->where(Image::TYPE, ImageTypeEnum::CATEGORY->value)
            ->first();
    }
}
