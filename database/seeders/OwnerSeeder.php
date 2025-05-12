<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Roles\RoleEnum;
use App\Models\Roles\Role;
use App\Models\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found! Make sure RoleSeeder has been run.');
            return;
        }

        $adminEmail = 'admin@example.com';
        $adminExists = User::where('email', $adminEmail)->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Site Owner',
                'email' => $adminEmail,
                'password' => Hash::make('password'),
                'role_id' => $adminRole->getId(),
                'phone' => '12345678',
                'email_verified_at' => now(),
            ]);

            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists, skipping creation.');
        }
    }
}
