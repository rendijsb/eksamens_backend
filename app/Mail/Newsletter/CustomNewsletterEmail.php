<?php

declare(strict_types=1);

namespace App\Mail\Newsletter;

use App\Models\Newsletter\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomNewsletterEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subscription;
    public $customSubject;
    public $content;

    public function __construct(NewsletterSubscription $subscription, string $subject, string $content)
    {
        $this->subscription = $subscription;
        $this->customSubject = $subject;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->customSubject)
            ->view('emails.newsletter.custom')
            ->with([
                'subscription' => $this->subscription,
                'content' => $this->content,
                'unsubscribeUrl' => route('newsletter.unsubscribe.view', $this->subscription->getToken()),
            ]);
    }
}
