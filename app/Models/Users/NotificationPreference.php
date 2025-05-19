<?php

declare(strict_types=1);

namespace App\Models\Users;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const ORDER_STATUS_UPDATES = 'order_status_updates';
    const PROMOTIONAL_EMAILS = 'promotional_emails';
    const NEWSLETTER_EMAILS = 'newsletter_emails';
    const SECURITY_ALERTS = 'security_alerts';
    const PRODUCT_RECOMMENDATIONS = 'product_recommendations';
    const INVENTORY_ALERTS = 'inventory_alerts';
    const PRICE_DROP_ALERTS = 'price_drop_alerts';
    const REVIEW_REMINDERS = 'review_reminders';
    const EMAIL_NOTIFICATIONS = 'email_notifications';
    const SMS_NOTIFICATIONS = 'sms_notifications';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::ORDER_STATUS_UPDATES,
        self::PROMOTIONAL_EMAILS,
        self::NEWSLETTER_EMAILS,
        self::SECURITY_ALERTS,
        self::PRODUCT_RECOMMENDATIONS,
        self::INVENTORY_ALERTS,
        self::PRICE_DROP_ALERTS,
        self::REVIEW_REMINDERS,
        self::EMAIL_NOTIFICATIONS,
        self::SMS_NOTIFICATIONS,
    ];

    protected $casts = [
        self::ORDER_STATUS_UPDATES => 'boolean',
        self::PROMOTIONAL_EMAILS => 'boolean',
        self::NEWSLETTER_EMAILS => 'boolean',
        self::SECURITY_ALERTS => 'boolean',
        self::PRODUCT_RECOMMENDATIONS => 'boolean',
        self::INVENTORY_ALERTS => 'boolean',
        self::PRICE_DROP_ALERTS => 'boolean',
        self::REVIEW_REMINDERS => 'boolean',
        self::EMAIL_NOTIFICATIONS => 'boolean',
        self::SMS_NOTIFICATIONS => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getUserId(): int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getOrderStatusUpdates(): bool
    {
        return $this->getAttribute(self::ORDER_STATUS_UPDATES);
    }

    public function getPromotionalEmails(): bool
    {
        return $this->getAttribute(self::PROMOTIONAL_EMAILS);
    }

    public function getNewsletterEmails(): bool
    {
        return $this->getAttribute(self::NEWSLETTER_EMAILS);
    }

    public function getSecurityAlerts(): bool
    {
        return $this->getAttribute(self::SECURITY_ALERTS);
    }

    public function getProductRecommendations(): bool
    {
        return $this->getAttribute(self::PRODUCT_RECOMMENDATIONS);
    }

    public function getInventoryAlerts(): bool
    {
        return $this->getAttribute(self::INVENTORY_ALERTS);
    }

    public function getPriceDropAlerts(): bool
    {
        return $this->getAttribute(self::PRICE_DROP_ALERTS);
    }

    public function getReviewReminders(): bool
    {
        return $this->getAttribute(self::REVIEW_REMINDERS);
    }

    public function getEmailNotifications(): bool
    {
        return $this->getAttribute(self::EMAIL_NOTIFICATIONS);
    }

    public function getSmsNotifications(): bool
    {
        return $this->getAttribute(self::SMS_NOTIFICATIONS);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public static function createDefaultForUser(int $userId): self
    {
        return self::create([
            self::USER_ID => $userId,
            self::ORDER_STATUS_UPDATES => true,
            self::PROMOTIONAL_EMAILS => true,
            self::NEWSLETTER_EMAILS => true,
            self::SECURITY_ALERTS => true,
            self::INVENTORY_ALERTS => false,
            self::REVIEW_REMINDERS => true,
            self::EMAIL_NOTIFICATIONS => true,
        ]);
    }
}
