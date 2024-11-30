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
        Schema::create('historic_sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('village_code', 20)->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('name');
            $table->string('address');
            $table->text('description');
            $table->text('images')->nullable();
            $table->integer('status')->default(1);
            $table->integer('is_show')->nullable()->default(0);
            $table->integer('is_pinned')->nullable()->default(0);
            $table->integer('user_id');
            $table->softDeletesTz();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historic_sites');
    }
};
