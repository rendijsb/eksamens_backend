<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\UpdateNotificationPreferencesRequest;
use App\Http\Resources\Notifications\NotificationPreferenceResource;
use App\Models\Users\NotificationPreference;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function getNotificationPreferences(): NotificationPreferenceResource|JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $preferences = $user->notificationPreferences;

        if (!$preferences) {
            $preferences = NotificationPreference::createDefaultForUser($user->getId());
        }

        return new NotificationPreferenceResource($preferences);
    }

    public function updateNotificationPreferences(UpdateNotificationPreferencesRequest $request): NotificationPreferenceResource|JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $preferences = $user->notificationPreferences;

        if (!$preferences) {
            $preferences = NotificationPreference::createDefaultForUser($user->getId());
        }

        $preferences->update([
            NotificationPreference::ORDER_STATUS_UPDATES => $request->getOrderStatusUpdates(),
            NotificationPreference::PROMOTIONAL_EMAILS => $request->getPromotionalEmails(),
            NotificationPreference::NEWSLETTER_EMAILS => $request->getNewsletterEmails(),
            NotificationPreference::SECURITY_ALERTS => $request->getSecurityAlerts(),
            NotificationPreference::INVENTORY_ALERTS => $request->getInventoryAlerts(),
            NotificationPreference::REVIEW_REMINDERS => $request->getReviewReminders(),
            NotificationPreference::EMAIL_NOTIFICATIONS => $request->getEmailNotifications(),
        ]);

        if ($request->getNewsletterEmails() === false && $user->relatedNewsletter()) {
            $user->relatedNewsletter()->unsubscribe();
        }

        if ($request->getNewsletterEmails() === true) {
            $user->relatedNewsletter()->resubscribe();
        }

        return new NotificationPreferenceResource($preferences);
    }
}
