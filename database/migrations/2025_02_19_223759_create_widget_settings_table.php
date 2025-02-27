<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widget_settings', function (Blueprint $table) {
            $table->longText('default_theme_options');
            $table->longText('theme_text_options');
            $table->longText('welcome_message_options');
            $table->longText('background_image_options');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_settings');
    }
};
