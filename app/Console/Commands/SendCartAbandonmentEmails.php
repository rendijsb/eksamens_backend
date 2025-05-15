<?php

namespace App\Console\Commands;

use App\Mail\CartAbandonment;
use App\Models\Carts\Cart;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCartAbandonmentEmails extends Command
{
    protected $signature = 'cart:send-abandonment-emails';
    protected $description = 'Send cart abandonment emails to users';

    public function handle(): void
    {
        $this->info('Sending cart abandonment emails...');

        $carts = Cart::whereNotNull('user_id')
            ->whereHas('items')
            ->where('updated_at', '>', now()->subDays(3))
            ->with(['user', 'items.product'])
            ->get();

        foreach ($carts as $cart) {
            if ($cart->user) {
                Mail::to($cart->user->getEmail())->send(new CartAbandonment($cart, $cart->user));
                $this->info("Sent abandonment email to: {$cart->user->getEmail()}");
            }
        }

        $this->info("Processed {$carts->count()} abandoned carts.");
    }
}
