<?php

declare(strict_types=1);

namespace App\Models\Products;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const IMAGE = 'image';
    const IS_PRIMARY = 'is_primary';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::PRODUCT_ID,
        self::IMAGE,
        self::IS_PRIMARY
    ];

    protected $casts = [
        self::IS_PRIMARY => 'boolean',
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getProductId(): int
    {
        return $this->getAttribute(self::PRODUCT_ID);
    }

    public function getIsPrimary(): bool
    {
        return $this->getAttribute(self::IS_PRIMARY);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }
}
