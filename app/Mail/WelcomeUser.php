<?php

namespace App\Mail;

use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Laipni lūdzam mūsu veikalā!')
            ->view('emails.welcome-user')
            ->with([
                'user' => $this->user,
                'name' => $this->user->getName(),
            ]);
    }
}
