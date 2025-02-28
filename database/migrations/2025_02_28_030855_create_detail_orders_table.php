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
        Schema::create('detail_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger("id_product_outlet");
            $table->unsignedBigInteger('qty')->default(0);
            $table->unsignedBigInteger('price')->default(0);
            $table->timestamps();

            $table->foreign('id_order')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('id_product_outlet')->references('id')->on('product_outlets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_orders');
    }
};
