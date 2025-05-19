<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Users\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class EmailDispatchService
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function sendEmail(
        string   $email,
        Mailable $mailable,
        ?User    $user = null,
        ?string  $notificationType = null
    ): bool
    {
        if (!$user || !$notificationType) {
            Mail::to($email)->send($mailable);
            return true;
        }

        if ($this->notificationService->canSendEmail($user, $notificationType)) {
            Mail::to($email)->send($mailable);
            return true;
        } else {
            return false;
        }
    }

    public function sendBulkEmails(
        array    $recipients,
        callable $mailableFactory,
        string   $notificationType
    ): array
    {
        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            $user = $recipient instanceof User ? $recipient : null;
            $email = $user ? $user->getEmail() : $recipient;

            if ($user && !$this->notificationService->canSendEmail($user, $notificationType)) {
                $skipped++;
                continue;
            }

            $mailable = $mailableFactory($recipient);
            Mail::to($email)->send($mailable);
            $sent++;
        }

        return [
            'sent' => $sent,
            'skipped' => $skipped,
            'failed' => $failed,
            'total' => count($recipients)
        ];
    }
}
