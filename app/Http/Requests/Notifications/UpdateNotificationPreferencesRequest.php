<?php

declare(strict_types=1);

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
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

    public function rules(): array
    {
        return [
            self::ORDER_STATUS_UPDATES => 'required|boolean',
            self::PROMOTIONAL_EMAILS => 'required|boolean',
            self::NEWSLETTER_EMAILS => 'required|boolean',
            self::SECURITY_ALERTS => 'required|boolean',
            self::PRODUCT_RECOMMENDATIONS => 'required|boolean',
            self::INVENTORY_ALERTS => 'required|boolean',
            self::PRICE_DROP_ALERTS => 'required|boolean',
            self::REVIEW_REMINDERS => 'required|boolean',
            self::EMAIL_NOTIFICATIONS => 'required|boolean',
            self::SMS_NOTIFICATIONS => 'required|boolean',
        ];
    }

    public function getOrderStatusUpdates(): bool
    {
        return (bool) $this->input(self::ORDER_STATUS_UPDATES);
    }

    public function getPromotionalEmails(): bool
    {
        return (bool) $this->input(self::PROMOTIONAL_EMAILS);
    }

    public function getNewsletterEmails(): bool
    {
        return (bool) $this->input(self::NEWSLETTER_EMAILS);
    }

    public function getSecurityAlerts(): bool
    {
        return (bool) $this->input(self::SECURITY_ALERTS);
    }

    public function getProductRecommendations(): bool
    {
        return (bool) $this->input(self::PRODUCT_RECOMMENDATIONS);
    }

    public function getInventoryAlerts(): bool
    {
        return (bool) $this->input(self::INVENTORY_ALERTS);
    }

    public function getPriceDropAlerts(): bool
    {
        return (bool) $this->input(self::PRICE_DROP_ALERTS);
    }

    public function getReviewReminders(): bool
    {
        return (bool) $this->input(self::REVIEW_REMINDERS);
    }

    public function getEmailNotifications(): bool
    {
        return (bool) $this->input(self::EMAIL_NOTIFICATIONS);
    }

    public function getSmsNotifications(): bool
    {
        return (bool) $this->input(self::SMS_NOTIFICATIONS);
    }
}
