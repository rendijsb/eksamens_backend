<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:check-expired-sales')->daily();
        $schedule->command('carts:cleanup')->daily();
        $schedule->command('cart:send-abandonment-emails')->daily();
        $schedule->command('products:check-low-stock')->weekly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
