<?php

declare(strict_types=1);

namespace App\Models\Newsletter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsletterSubscription extends Model
{
    const ID = 'id';
    const EMAIL = 'email';
    const TOKEN = 'token';
    const IS_ACTIVE = 'is_active';
    const SUBSCRIBED_AT = 'subscribed_at';
    const UNSUBSCRIBED_AT = 'unsubscribed_at';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::EMAIL,
        self::TOKEN,
        self::IS_ACTIVE,
        self::SUBSCRIBED_AT,
    ];

    protected $casts = [
        self::IS_ACTIVE => 'boolean',
        self::SUBSCRIBED_AT => 'datetime',
        self::UNSUBSCRIBED_AT => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($subscription) {
            if (!$subscription->token) {
                $subscription->token = Str::random(32);
            }
            if (!$subscription->subscribed_at) {
                $subscription->subscribed_at = now();
            }
        });
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::EMAIL);
    }

    public function getToken(): string
    {
        return $this->getAttribute(self::TOKEN);
    }

    public function getIsActive(): bool
    {
        return $this->getAttribute(self::IS_ACTIVE);
    }

    public function getSubscribedAt(): Carbon
    {
        return $this->getAttribute(self::SUBSCRIBED_AT);
    }

    public function getUnsubscribedAt(): ?Carbon
    {
        return $this->getAttribute(self::UNSUBSCRIBED_AT);
    }

    public function unsubscribe(): void
    {
        $this->update([
            self::IS_ACTIVE => false,
            self::UNSUBSCRIBED_AT => now(),
        ]);
    }

    public function resubscribe(): void
    {
        $this->update([
            self::IS_ACTIVE => true,
            self::UNSUBSCRIBED_AT => null,
        ]);
    }
}
