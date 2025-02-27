<?php

namespace Database\Seeders;

use App\Models\ApplicationChatSound;
use App\Models\WidgetSettings;
use Illuminate\Database\Seeder;

class ApplicationChatSoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicationSound = new ApplicationChatSound();
        $applicationSound->name = "Bell Ding";
        $applicationSound->file_name = "Bell Ding.mp3";
        $applicationSound->save();

        $applicationSound = new ApplicationChatSound();
        $applicationSound->name = "Classic Message Alert";
        $applicationSound->file_name = "Classic Message Alert.mp3";
        $applicationSound->save();

        $applicationSound = new ApplicationChatSound();
        $applicationSound->name = "Fuzzy";
        $applicationSound->file_name = "fuzzy.mp3";
        $applicationSound->save();

        $applicationSound = new ApplicationChatSound();
        $applicationSound->name = "Light Bulb";
        $applicationSound->file_name = "light-bulb.mp3";
        $applicationSound->save();
    }
}
