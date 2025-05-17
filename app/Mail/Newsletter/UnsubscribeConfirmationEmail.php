<?php

declare(strict_types=1);

namespace App\Mail\Newsletter;

use App\Models\Newsletter\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnsubscribeConfirmationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(NewsletterSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function build()
    {
        return $this->subject('Jūs esat veiksmīgi izrakstijie - NetNest')
            ->view('emails.newsletter.unsubscribe-confirmation')
            ->with([
                'subscription' => $this->subscription,
                'resubscribeUrl' => config('app.frontend_url') . '/#newsletter',
            ]);
    }
}
