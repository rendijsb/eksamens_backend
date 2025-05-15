<?php

namespace App\Mail;

use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Paroles atiestatīšana - NetNest')
            ->view('emails.password-reset')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
                'resetUrl' => config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . urlencode($this->user->getEmail()),
            ]);
    }
}
