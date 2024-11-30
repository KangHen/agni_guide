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
        Schema::create('artefacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('historic_site_id');
            $table->string('name');
            $table->text('description');
            $table->text('image')->nullable();
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
        Schema::dropIfExists('artefacts');
    }
};
