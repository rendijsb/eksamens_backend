<?php

declare(strict_types=1);

namespace App\Http\Resources\Notifications;

use App\Models\Users\NotificationPreference;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationPreferenceResource extends JsonResource
{
    public $resource = NotificationPreference::class;

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'user_id' => $this->resource->getUserId(),
            'order_status_updates' => $this->resource->getOrderStatusUpdates(),
            'promotional_emails' => $this->resource->getPromotionalEmails(),
            'newsletter_emails' => $this->resource->getNewsletterEmails(),
            'security_alerts' => $this->resource->getSecurityAlerts(),
            'product_recommendations' => $this->resource->getProductRecommendations(),
            'inventory_alerts' => $this->resource->getInventoryAlerts(),
            'price_drop_alerts' => $this->resource->getPriceDropAlerts(),
            'review_reminders' => $this->resource->getReviewReminders(),
            'email_notifications' => $this->resource->getEmailNotifications(),
            'sms_notifications' => $this->resource->getSmsNotifications(),
            'created_at' => $this->resource->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
