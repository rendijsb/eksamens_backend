<?php

namespace App\Models\Wishlists;

use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const PRODUCT_ID = 'product_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::PRODUCT_ID,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getUserId(): int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getProductId(): int
    {
        return $this->getAttribute(self::PRODUCT_ID);
    }
}
