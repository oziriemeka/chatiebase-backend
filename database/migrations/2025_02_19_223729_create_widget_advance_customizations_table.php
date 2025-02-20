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
        Schema::create('widget_advance_customizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_id');
            $table->longText('chat_appearance');
            $table->longText('chat_visibility');
            $table->longText('chat_behaviour');
            $table->longText('chat_restrictions');
            $table->longText('chat_security');
            $table->timestamps();

            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_advance_customizations');
    }
};
