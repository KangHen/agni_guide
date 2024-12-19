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
        Schema::table('historic_sites', function (Blueprint $table) {
            $table->integer('read_count')->default(0)->after('is_show');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historic_sites', function (Blueprint $table) {
            $table->dropColumn('read_count');
        });
    }
};
