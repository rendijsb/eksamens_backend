<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Newsletter\SendNewsletterRequest;
use App\Mail\Newsletter\CustomNewsletterEmail;
use App\Models\Newsletter\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminNewsletterController extends Controller
{
    public function getSubscriberStats(): JsonResponse
    {
        $activeSubscribers = NewsletterSubscription::where('is_active', true)->count();
        $totalSubscribers = NewsletterSubscription::count();
        $inactiveSubscribers = $totalSubscribers - $activeSubscribers;

        return response()->json([
            'data' => [
                'active_subscribers' => $activeSubscribers,
                'total_subscribers' => $totalSubscribers,
                'inactive_subscribers' => $inactiveSubscribers,
            ]
        ]);
    }

    public function getSubscribers(): JsonResponse
    {
        $subscribers = NewsletterSubscription::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $subscribers
        ]);
    }

    public function sendNewsletter(SendNewsletterRequest $request): JsonResponse
    {
        $subject = $request->getSubject();
        $content = $request->getContentText();
        $sendToAll = $request->getSendToAll();

        $query = NewsletterSubscription::where('is_active', true);

        $subscribers = $query->get();

        if ($subscribers->isEmpty()) {
            return response()->json([
                'message' => 'Nav aktīvo abonentu',
                'success' => false
            ], 400);
        }

        $sentCount = 0;
        $errors = [];

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->getEmail())->send(
                    new CustomNewsletterEmail($subscriber, $subject, $content)
                );
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send newsletter to ' . $subscriber->getEmail(), [
                    'error' => $e->getMessage()
                ]);
                $errors[] = $subscriber->getEmail();
            }
        }

        $message = "Jaunumi nosūtīti {$sentCount} no {$subscribers->count()} abonentiem";

        if (!empty($errors)) {
            $message .= ". Neizdevās nosūtīt: " . count($errors) . " abonentiem";
        }

        return response()->json([
            'message' => $message,
            'success' => true,
            'data' => [
                'sent_count' => $sentCount,
                'total_subscribers' => $subscribers->count(),
                'errors_count' => count($errors)
            ]
        ]);
    }
}
