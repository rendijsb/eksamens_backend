<?php

declare(strict_types=1);

namespace App\Models\Carts;

use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const SESSION_ID = 'session_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::SESSION_ID,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getUserId(): ?int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getSessionId(): ?string
    {
        return $this->getAttribute(self::SESSION_ID);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function getTotalPrice(): float
    {
        return $this->items->sum('total_price');
    }

    public function getTotalItems(): int
    {
        return $this->items->sum('quantity');
    }
}
