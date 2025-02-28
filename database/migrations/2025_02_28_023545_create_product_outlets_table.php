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
        Schema::create('product_outlets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product');
            $table->unsignedBigInteger('id_outlet');
            $table->bigInteger('product_price')->unsigned();
            $table->enum('is_active',['yes','no'])->default('yes');
            $table->timestamps();

            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('id_outlet')->references('id')->on('outlets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_outlets');
    }
};
