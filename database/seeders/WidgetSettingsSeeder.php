<?php

namespace Database\Seeders;

use App\Models\WidgetSettings;
use Illuminate\Database\Seeder;

class WidgetSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $widgetSettings = new WidgetSettings();
        $widgetSettings->default_theme_options =
        json_encode([
            [
                'id' => "gray",
                "name" => "Gray",
                "color_code" => "#ccc"
            ],
            [
                'id' => "black",
                "name" => "Black",
                "color_code" => "#000"
            ],
            [
                'id' => "red",
                "name" => "Red",
                "color_code" => "#ff2525"
            ],
            [
                'id' => "blue",
                "name" => "Blue",
                "color_code" => "#1f58df"
            ]
        ]);

        $widgetSettings->theme_text_options =
            json_encode([
                [
                    'id' => "theme_1",
                    "name" => "Questions ? Chat with us!",
                ],
                [
                    'id' => "theme_2",
                    "name" => "Questions ? Chat with me!",
                ],
                [
                    'id' => "theme_3",
                    "name" => "Ask us your questions!",
                ],
                [
                    'id' => "theme_4",
                    "name" => "Ask me your questions!",
                ],
                [
                    'id' => "theme_5",
                    "name" => "Chat with Support",
                ]
            ]);
        $widgetSettings->welcome_message_options =
            json_encode([

                [
               'id' => "theme_1",
               "name" => "How can we help you with your website",
           ],
           [
               'id' => "theme_2",
               "name" => "Hey, want to cha with us ?",
           ],
           [
               'id' => "theme_3",
               "name" => "Anything you want to ask?",
           ],
           [
               'id' => "theme_4",
               "name" => "Hello, ask us any question about our website",
           ]
        ]);
        $widgetSettings->background_image_options =
            json_encode([
            [
                'id' => "none",
                "name" => "None",
                "src" => "none.svg",
                "is_default" => true
            ],
            [
                'id' => "autumn",
                "name" => "Autumn",
                "src" => "autumn.svg",
                "is_default" => false
            ],
            [
                'id' => "hexagons",
                "name" => "Hexagons",
                "src" => "hexagons.svg",
                "is_default" => false
            ],
            [
                'id' => "jigsaw",
                "name" => "Jigsaw",
                "src" => "jigsaw.svg",
                "is_default" => false
            ],
            [
                'id' => "jupiter",
                "name" => "Jupiter",
                "src" => "jupiter.svg",
                "is_default" => false
            ],
            [
                'id' => "food",
                "name" => "Food",
                "src" => "food.svg",
                "is_default" => false
            ]
        ]);

        $widgetSettings->save();
    }
}
