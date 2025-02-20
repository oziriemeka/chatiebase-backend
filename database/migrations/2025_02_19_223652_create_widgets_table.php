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
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->binary('id_alias');
            $table->string('website_name');
            $table->string('website_domain');
            $table->string('website_icon')->default("default-icon.png");
            $table->unsignedBigInteger("organization_id");
            $table->longText('code');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organization_settings')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
