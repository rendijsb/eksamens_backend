<?php

namespace App\Mail;

use App\Models\Coupons\Coupon;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CouponNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $coupon;
    public $user;

    public function __construct(Coupon $coupon, User $user)
    {
        $this->coupon = $coupon;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('🎉 Ekskluzīvs kupons tikai jums!')
            ->view('emails.coupon-notification')
            ->with([
                'coupon' => $this->coupon,
                'user' => $this->user,
                'name' => $this->user->getName(),
            ]);
    }
}
