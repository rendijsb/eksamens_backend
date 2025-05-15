<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    public function build()
    {
        return $this->subject('Mēs saņēmām jūsu ziņojumu - NetNest')
            ->view('emails.contact-form-auto-reply')
            ->with([
                'contactData' => $this->contactData,
            ]);
    }
}
