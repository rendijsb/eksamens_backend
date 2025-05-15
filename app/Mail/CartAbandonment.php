<?php

namespace App\Mail;

use App\Models\Carts\Cart;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CartAbandonment extends Mailable
{
    use Queueable, SerializesModels;

    public $cart;
    public $user;

    public function __construct(Cart $cart, User $user)
    {
        $this->cart = $cart;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Aizmirsāt kaut ko grozā?')
            ->view('emails.cart-abandonment')
            ->with([
                'cart' => $this->cart,
                'user' => $this->user,
                'name' => $this->user->getName(),
            ]);
    }
}
