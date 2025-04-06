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
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('message_id');
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('conversation_id');
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('sender')->nullable();
            $table->string('action_type')->default(0)->comment("0 = message, 1 = user_joined, 2 = attachments, 3 = user_left");
            $table->longText('message')->nullable();
            $table->unsignedBigInteger('attachment_id')->nullable();
            $table->boolean('is_deleted')->default(0)->comment('1 for deleted, 0 for not deleted');
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('conversation_messages')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
    }
};
