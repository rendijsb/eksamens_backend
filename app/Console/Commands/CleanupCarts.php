<?php

namespace App\Console\Commands;

use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use Illuminate\Console\Command;

class CleanupCarts extends Command
{
    public function handle(): void
    {
        $cutoffDate = now()->subDays(30);

        $abandonedCarts = Cart::whereNull('user_id')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        foreach ($abandonedCarts as $cart) {
            CartItem::where('cart_id', $cart->id)->delete();

            $cart->delete();
        }

        $this->info('Cleaned up ' . count($abandonedCarts) . ' abandoned carts');
    }
}
