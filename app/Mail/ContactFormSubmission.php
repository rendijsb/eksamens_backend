<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    public function build()
    {
        return $this->subject('Jauns ziņojums no kontaktu formas: ' . $this->contactData['subject'])
            ->replyTo($this->contactData['email'], $this->contactData['name'])
            ->view('emails.contact-form-submission')
            ->with([
                'contactData' => $this->contactData,
            ]);
    }
}
