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
        Schema::create('privileges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the privilege (e.g., "Admin", "Customer Care Rep")
            $table->smallInteger('can_delete')->default(1); // Name of the privilege (e.g., "Admin", "Customer Care Rep")
            $table->unsignedBigInteger("organization_id");
            $table->timestamps();
            $table->foreign('organization_id')->references('id')->on('organization_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privileges');
    }
};
