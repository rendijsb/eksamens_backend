<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlert;
use App\Models\Products\Product;
use App\Services\EmailDispatchService;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'products:check-low-stock';
    protected $description = 'Check for products with low stock and send alert';

    private EmailDispatchService $emailDispatchService;
    private NotificationService $notificationService;

    public function __construct(
        EmailDispatchService $emailDispatchService,
        NotificationService $notificationService
    ) {
        parent::__construct();
        $this->emailDispatchService = $emailDispatchService;
        $this->notificationService = $notificationService;
    }

    public function handle(): void
    {
        $this->info('Checking for low stock products...');

        $lowStockProducts = Product::where('stock', '<=', 10)
            ->where('status', 'active')
            ->get();

        if ($lowStockProducts->isNotEmpty()) {
            $recipients = $this->notificationService->getUsersForNotificationType('inventory');

            if ($recipients->isNotEmpty()) {
                $results = $this->emailDispatchService->sendBulkEmails(
                    $recipients->toArray(),
                    fn($user) => new LowStockAlert($lowStockProducts),
                    'inventory'
                );

                $this->info("Sent low stock alerts: {$results['sent']} sent, {$results['skipped']} skipped");
            } else {
                $this->info('No users have enabled inventory alerts');
            }
        } else {
            $this->info('No low stock products found.');
        }
    }
}
