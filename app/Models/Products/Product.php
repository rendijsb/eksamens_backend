<?php

declare(strict_types=1);

namespace App\Models\Products;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
    const SPECIFICATIONS = 'specifications';
    const ADDITIONAL_INFO = 'additional_info';
    const STATUS = 'status';
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
}
