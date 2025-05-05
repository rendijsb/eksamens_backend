<?php

declare(strict_types=1);

namespace App\Models\Reviews;

use App\Models\Products\Product;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const USER_ID = 'user_id';
    const RATING = 'rating';
    const REVIEW_TEXT = 'review_text';
    const IS_APPROVED = 'is_approved';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::PRODUCT_ID,
        self::USER_ID,
        self::RATING,
        self::REVIEW_TEXT,
        self::IS_APPROVED
    ];

    protected $casts = [
        self::IS_APPROVED => 'boolean',
        self::RATING => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getProductId(): int
    {
        return $this->getAttribute(self::PRODUCT_ID);
    }

    public function getUserId(): int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getRating(): int
    {
        return $this->getAttribute(self::RATING);
    }

    public function getReviewText(): ?string
    {
        return $this->getAttribute(self::REVIEW_TEXT);
    }

    public function getIsApproved(): bool
    {
        return $this->getAttribute(self::IS_APPROVED);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }
}
