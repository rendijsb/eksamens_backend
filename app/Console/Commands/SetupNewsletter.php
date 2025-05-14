<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupNewsletter extends Command
{
    protected $signature = 'newsletter:setup';
    protected $description = 'Set up newsletter functionality with database migration';

    public function handle()
    {
        $this->info('Setting up newsletter functionality...');

        $this->call('migrate');

        $this->info('Newsletter setup completed successfully!');
    }
}
