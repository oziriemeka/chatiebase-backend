<?php

namespace Database\Seeders;

use App\Models\ApplicationChatSound;
use App\Models\ApplicationSettings;
use App\Models\WidgetSettings;
use Illuminate\Database\Seeder;

class ApplicationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $applicationSettings = new ApplicationSettings();
        $applicationSettings->bundle_info = "1.12.23.8";
        $applicationSettings->about_us_link = url("/about-us");
        $applicationSettings->save();
    }
}
