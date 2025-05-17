<?php

declare(strict_types=1);

namespace App\Models\Products;

use App\Enums\Images\ImageTypeEnum;
use App\Models\Categories\Category;
use App\Models\Images\Image;
use App\Models\Reviews\Review;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    const ID = 'id';
    const CATEGORY_ID = 'category_id';
    const NAME = 'name';
    const SLUG = 'slug';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const SALE_PRICE = 'sale_price';
    const STOCK = 'stock';
    const SOLD = 'sold';
    const SPECIFICATIONS = 'specifications';
    const ADDITIONAL_INFO = 'additional_info';
    const STATUS = 'status';
    const SALE_ENDS_AT = 'sale_ends_at';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::CATEGORY_ID,
        self::NAME,
        self::DESCRIPTION,
        self::SLUG,
        self::PRICE,
        self::SALE_PRICE,
        self::STOCK,
        self::SPECIFICATIONS,
        self::ADDITIONAL_INFO,
        self::STATUS,
        self::SALE_ENDS_AT,
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

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function getStock(): int
    {
        return $this->getAttribute(self::STOCK);
    }

    public function getSold(): int
    {
        return $this->getAttribute(self::SOLD);
    }

    public function getPrice(): int|string
    {
        return $this->getAttribute(self::PRICE);
    }

    public function getSalePrice(): int|string|null
    {
        return $this->getAttribute(self::SALE_PRICE);
    }

    public function getSpecifications(): ?string
    {
        return $this->getAttribute(self::SPECIFICATIONS);
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->getAttribute(self::ADDITIONAL_INFO);
    }

    public function getStatus(): string
    {
        return $this->getAttribute(self::STATUS);
    }

    public function getCategoryId(): int
    {
        return $this->getAttribute(self::CATEGORY_ID);
    }

    public function getRelatedCategory(): Category
    {
        return $this->hasOne(Category::class, Category::ID, self::CATEGORY_ID)->first();
    }

    public function getAllImages(): HasMany
    {
        return $this->hasMany(Image::class, Image::RELATED_ID)
            ->where(Image::TYPE, ImageTypeEnum::PRODUCT->value);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(Image::class, Image::RELATED_ID)
            ->where(Image::TYPE, ImageTypeEnum::PRODUCT->value)
            ->where(Image::IS_PRIMARY, true);
    }

    public function getRelatedPrimaryImage(): ?Image
    {
        $primary = $this->primaryImage()->first();

        if (!$primary) {
            $primary = $this->getAllImages()->first();
        }

        return $primary;
    }

    public function getSaleEndsAt(): ?Carbon
    {
        $date = $this->getAttribute(self::SALE_ENDS_AT);
        return $date ? new Carbon($date) : null;
    }

    public function isSaleActive(): bool
    {
        $salePrice = $this->getAttribute(self::SALE_PRICE);
        $saleEndsAt = $this->getAttribute(self::SALE_ENDS_AT);

        if (!$salePrice) {
            return false;
        }

        if (!$saleEndsAt) {
            return true;
        }

        return Carbon::now()->lt(new Carbon($saleEndsAt));
    }

    public function getEffectivePrice(): string|int
    {
        return $this->isSaleActive() ? $this->getSalePrice() : $this->getPrice();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where(Review::IS_APPROVED, true);
    }

    public function getAverageRating(): string|int|null
    {
        return $this->approvedReviews()->avg(Review::RATING);
    }

    public function getReviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }
}
