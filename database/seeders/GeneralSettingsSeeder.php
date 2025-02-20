<?php

namespace Database\Seeders;

use App\Models\GeneralSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = new GeneralSettings();
        $settings->admin_from_name = 'Chatiebase';
        $settings->admin_from_email = 'admin@chatiebase.com';
        $settings->save();
    }

}
