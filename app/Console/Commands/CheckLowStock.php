<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlert;
use App\Models\Products\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckLowStock extends Command
{
    protected $signature = 'products:check-low-stock';
    protected $description = 'Check for products with low stock and send alert';

    public function handle(): void
    {
        $this->info('Checking for low stock products...');

        $lowStockProducts = Product::where('stock', '<=', 10)
            ->where('status', 'active')
            ->get();

        if ($lowStockProducts->isNotEmpty()) {
            $adminEmail = env('ADMIN_EMAIL', config('mail.from.address'));
            Mail::to($adminEmail)->send(new LowStockAlert($lowStockProducts));

            $this->info("Sent low stock alert for {$lowStockProducts->count()} products to {$adminEmail}");
        } else {
            $this->info('No low stock products found.');
        }
    }
}
