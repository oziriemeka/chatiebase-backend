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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable()->default('avatar.svg');
            $table->integer('is_admin')->default(0);
            $table->timestamp('last_login')->nullable();
            $table->integer('was_invited')->default(0);
            $table->unsignedBigInteger('was_invited_by')->nullable();
            $table->string('status')->default('pending')->comment("joined, pending, deactivated");
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verification_token_at')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->foreign('was_invited_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
