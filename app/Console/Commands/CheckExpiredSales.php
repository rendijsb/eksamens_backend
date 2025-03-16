<?php

namespace App\Console\Commands;

use App\Models\Products\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckExpiredSales extends Command
{
    protected $signature = 'app:check-expired-sales';
    protected $description = 'Check and expire sales that have reached their end date';

    public function handle(): void
    {
        $this->info('Checking for expired sales...');

        $expiredSales = Product::whereNotNull('sale_price')
            ->whereNotNull('sale_ends_at')
            ->where('sale_ends_at', '<', Carbon::now())
            ->get();

        foreach ($expiredSales as $product) {
            $this->info("Expiring sale for product: {$product->getName()} (ID: {$product->getId()})");
            $product->update([
                'sale_price' => null,
                'sale_ends_at' => null
            ]);
        }

        $this->info("Processed {$expiredSales->count()} expired sales.");
    }
}
