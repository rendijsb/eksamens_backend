<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Users\NotificationPreference;
use App\Models\Users\User;
use Illuminate\Support\Collection;

class NotificationService
{
    public function canSendEmail(User $user, string $type): bool
    {
        $preferences = $user->notificationPreferences;

        if (!$preferences) {
            $preferences = NotificationPreference::createDefaultForUser($user->getId());
        }

        if (!$preferences->getEmailNotifications()) {
            return false;
        }

        if (in_array($type, ['inventory', 'low_stock']) && !$this->isAdminOrModerator($user)) {
            return false;
        }

        return match ($type) {
            'order_status' => $preferences->getOrderStatusUpdates(),
            'promotional' => $preferences->getPromotionalEmails(),
            'newsletter' => $preferences->getNewsletterEmails(),
            'security' => $preferences->getSecurityAlerts(),
            'inventory', 'low_stock' => $this->isAdminOrModerator($user) && $preferences->getInventoryAlerts(),
            'review_reminder' => $preferences->getReviewReminders(),
            default => true,
        };
    }

    public function getUsersForNotificationType(string $type): Collection
    {
        $query = User::whereHas('notificationPreferences', function ($q) use ($type) {
            $q->where('email_notifications', true);

            match ($type) {
                'order_status' => $q->where('order_status_updates', true),
                'promotional' => $q->where('promotional_emails', true),
                'newsletter' => $q->where('newsletter_emails', true),
                'security' => $q->where('security_alerts', true),
                'inventory', 'low_stock' => $q->where('inventory_alerts', true),
                'review_reminder' => $q->where('review_reminders', true),
                default => null,
            };
        });

        if (in_array($type, ['inventory', 'low_stock'])) {
            $query->whereHas('relatedRole', function ($q) {
                $q->whereIn('name', ['admin', 'moderator']);
            });
        }

        return $query->get();
    }

    private function isAdminOrModerator(User $user): bool
    {
        $user->load('relatedRole');
        return in_array($user->relatedRole?->getName(), ['admin', 'moderator']);
    }
}
