<?php

declare(strict_types=1);

namespace App\Mail\Newsletter;

use App\Models\Newsletter\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(NewsletterSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function build()
    {
        return $this->subject('Laipni lūdzam NetNest jaunumu sarakstā!')
            ->view('emails.newsletter.welcome')
            ->with([
                'subscription' => $this->subscription,
                'unsubscribeUrl' => route('newsletter.unsubscribe.view', $this->subscription->getToken()),
            ]);
    }
}
