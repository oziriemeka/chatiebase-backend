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
        Schema::create('widget_contact_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widget_id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('messenger')->nullable();
            $table->string('telegram')->nullable();
            $table->longText('twitter')->nullable();
            $table->longText('whatsapp')->nullable();
            $table->longText('instagram')->nullable();
            $table->timestamps();

            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_contact_information');
    }
};
