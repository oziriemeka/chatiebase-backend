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
        Schema::create('widget_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_id');
            $table->string('enable_emojis');
            $table->string('prevent_profanity');

            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_addons');
    }
};
