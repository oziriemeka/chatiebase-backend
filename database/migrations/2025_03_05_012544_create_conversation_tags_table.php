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
        Schema::create('conversation_tags', function (Blueprint $table) {
            // Comment: Oziri Emeka
            // Restricting to no-polymorphic relationship because of the possibility of large datasets
            // i.e. all Crud operations (including delete, edit, update and patch) will be done using just this table
            $table->id();
            $table->string('name');
            $table->foreignId('conversation_id');
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_tags');
    }
};
