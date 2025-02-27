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
        Schema::create('user_application_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->string('theme_color')->default('dark');
            $table->boolean('enable_browser_notification')->default(true);
            $table->string('language')->default('en');
            $table->unsignedBigInteger('new_chat_sound');
            $table->unsignedBigInteger('existing_chat_sound');
            $table->unsignedBigInteger('new_website_visitor_sound');
            $table->boolean('enable_sound_for_new_visitor')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('new_chat_sound')->references('id')->on('application_chat_sounds')->onDelete('cascade');
            $table->foreign('existing_chat_sound')->references('id')->on('application_chat_sounds')->onDelete('cascade');
            $table->foreign('new_website_visitor_sound')->references('id')->on('application_chat_sounds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_application_settings');
    }
};
