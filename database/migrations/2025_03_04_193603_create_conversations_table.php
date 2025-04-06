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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('conversation_id')->index();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('organization_id');
            $table->boolean('is_blocked')->default(0);
            $table->boolean('has_read')->default(0);
            $table->boolean('end_session')->default(0);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organization_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
