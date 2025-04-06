<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ApplicationSettingsSeeder::class,
            ApplicationChatSoundSeeder::class,
            GeneralSettingsSeeder::class,
            CountrySeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            WidgetSettingsSeeder::class,
            ConversationSeeder::class
        ]);
    }
}
