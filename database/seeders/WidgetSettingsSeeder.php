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
                    "title" => "Questions ? Chat with us!",
                ],
                [
                    'id' => "theme_2",
                    "title" => "Questions ? Chat with me!",
                ],
                [
                    'id' => "theme_3",
                    "title" => "Ask us your questions!",
                ],
                [
                    'id' => "theme_4",
                    "title" => "Ask me your questions!",
                ],
                [
                    'id' => "theme_5",
                    "title" => "Chat with Support",
                ]
            ]);
        $widgetSettings->welcome_message_options =
            json_encode([

                [
               'id' => "theme_1",
               "title" => "How can we help you with your website",
           ],
           [
               'id' => "theme_2",
               "title" => "Hey, want to cha with us ?",
           ],
           [
               'id' => "theme_3",
               "title" => "Anything you want to ask?",
           ],
           [
               'id' => "theme_4",
               "title" => "Hello, ask us any question about our website",
           ]
        ]);
        $widgetSettings->background_image_options =
            json_encode([
            [
                'id' => "theme_1",
                "title" => "Shapes",
                "src" => "shapes.svg",
                "is_default" => true
            ],
            [
                'id' => "theme_2",
                "title" => "4 Point Stars",
                "src" => "4-point-stars.svg",
                "is_default" => false
            ],
            [
                'id' => "theme_2",
                "title" => "Anchors Away",
                "src" => "anchors-away.svg",
                "is_default" => false
            ],
            [
                'id' => "theme_3",
                "title" => "Autumn",
                "src" => "autumn.svg",
                "is_default" => false
            ],
            [
                'id' => "theme_4",
                "title" => "Aztec",
                "src" => "aztec.svg",
                "is_default" => false
            ]
        ]);

        $widgetSettings->save();
    }
}
