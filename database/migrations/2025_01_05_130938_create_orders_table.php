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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->string('order_code');
            $table->string('status')->default('pending');
            $table->integer('quantity')->default(1);
            $table->string('coupon')->nullable();
            $table->string('discount')->nullable();
            $table->string('total')->default(0);
            $table->string('grand_total')->default(0);
            $table->string('customer_note')->nullable();
            $table->string('bank_note')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_data')->nullable();
            $table->softDeletesTz();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
