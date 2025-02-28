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

    protected $fillable = [
        self::PRODUCT_ID,
        self::IMAGE,
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }
}
