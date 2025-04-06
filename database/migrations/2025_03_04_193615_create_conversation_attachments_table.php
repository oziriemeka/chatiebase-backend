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
        Schema::create('conversation_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('attachment_id');
            $table->foreignId("message_id");
            $table->string('content');
            $table->string('extension');
            $table->string('size')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('conversation_messages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_attachments');
    }
};
