<?php

declare(strict_types=1);

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SubscribeRequest;
use App\Mail\Newsletter\UnsubscribeConfirmationEmail;
use App\Mail\Newsletter\WelcomeEmail;
use App\Models\Newsletter\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $email = $request->getEmail();

        $existingSubscription = NewsletterSubscription::where(NewsletterSubscription::EMAIL, $email)->first();

        if ($existingSubscription) {
            if ($existingSubscription->getIsActive()) {
                return response()->json([
                    'message' => 'Jūs jau esat pierakstījies jaunumiem',
                    'success' => false
                ], 422);
            } else {
                $existingSubscription->resubscribe();
                $subscription = $existingSubscription;
            }
        } else {
            $subscription = NewsletterSubscription::create([
                NewsletterSubscription::EMAIL => $email,
            ]);
        }

        Mail::to($email)->send(new WelcomeEmail($subscription));

        return response()->json([
            'message' => 'Paldies par pierakstīšanos! Pārbaudiet savu e-pastu.',
            'success' => true
        ]);
    }

    public function unsubscribeAPI(Request $request): JsonResponse
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Marķieris ir obligāts',
                'success' => false
            ], 400);
        }

        $subscription = NewsletterSubscription::where(NewsletterSubscription::TOKEN, $token)->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Nederīga atrakstīšanās saite',
                'success' => false
            ], 404);
        }

        if (!$subscription->getIsActive()) {
            return response()->json([
                'message' => 'Jūs jau esat atrakstījies no jaunumiem',
                'success' => true
            ]);
        }

        $email = $subscription->getEmail();

        $subscription->unsubscribe();

        Mail::to($email)->send(new UnsubscribeConfirmationEmail($subscription));

        return response()->json([
            'message' => 'Jūs esat veiksmīgi atrakstījies no jaunumiem.',
            'success' => true
        ]);
    }


    public function unsubscribeView(string $token)
    {
        $subscription = NewsletterSubscription::where(NewsletterSubscription::TOKEN, $token)->first();

        if (!$subscription) {
            return view('newsletter.unsubscribe-invalid');
        }

        if (!$subscription->getIsActive()) {
            return view('newsletter.unsubscribe-invalid');
        }

        $frontendUrl = config('app.frontend_url', 'http://localhost:4200');
        $redirectUrl = rtrim($frontendUrl, '/') . '/newsletter/unsubscribe/' . $token;

        return redirect()->to($redirectUrl);
    }

    public function confirmUnsubscribeWeb(string $token): JsonResponse
    {
        $subscription = NewsletterSubscription::where(NewsletterSubscription::TOKEN, $token)->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Nederīga atrakstīšanās saite',
                'success' => false
            ], 404);
        }

        if (!$subscription->getIsActive()) {
            return response()->json([
                'message' => 'Jūs jau esat atrakstījies no jaunumiem',
                'success' => true
            ]);
        }

        $email = $subscription->getEmail();
        $subscription->unsubscribe();
        Mail::to($email)->send(new UnsubscribeConfirmationEmail($subscription));

        return response()->json([
            'message' => 'Jūs esat veiksmīgi atrakstījies no jaunumiem.',
            'success' => true
        ]);

    }
}
