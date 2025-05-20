<?php

namespace Database\Seeders;

use App\Models\Users\NotificationPreference;
use App\Models\Users\User;
use Illuminate\Database\Seeder;

class NotificationPreferencesSeeder extends Seeder
{
    public function run(): void
    {
        $usersWithoutPreferences = User::whereDoesntHave('notificationPreferences')->get();

        foreach ($usersWithoutPreferences as $user) {
            NotificationPreference::createDefaultForUser($user->getId());
        }

        $this->command->info('Created default notification preferences for ' . $usersWithoutPreferences->count() . ' users.');
    }
}
