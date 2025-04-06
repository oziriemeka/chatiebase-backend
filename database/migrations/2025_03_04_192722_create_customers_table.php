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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id');
            $table->foreignId('organization_id')->index();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable()->default('default-avatar.png');
            $table->string('unique_identifier')->unique();
            $table->string('ip_address')->nullable();
            $table->string('country');

            $table->foreignId('assigned_to_id')->nullable();
            $table->foreignId('assigned_by_id')->nullable()->index();
            $table->dateTime('assigned_at')->nullable()->index();
            $table->string('status')->default(0)->comment("0 = pending, 1 = accepted, 2 = rejected");
            $table->timestamps();

            $table->foreign('assigned_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organization_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

