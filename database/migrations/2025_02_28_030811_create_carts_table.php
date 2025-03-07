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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_product_outlet');
            $table->unsignedBigInteger('id_available_product')->nullable();
            $table->unsignedBigInteger('id_size_product')->nullable();
            $table->unsignedBigInteger('id_bean_product')->nullable();
            $table->unsignedBigInteger('id_milk_product')->nullable();
            $table->unsignedBigInteger('id_additional_product')->nullable();
            $table->bigInteger('qty')->unsigned();
            $table->text('note')->nullable();
            $table->boolean('is_checkout')->default(false);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_product_outlet')->references('id')->on('product_outlets')->onDelete('cascade');
            $table->foreign('id_available_product')->references('id')->on('available_products')->onDelete('cascade');
            $table->foreign('id_size_product')->references('id')->on('size_products')->onDelete('cascade');
            $table->foreign('id_bean_product')->references('id')->on('bean_products')->onDelete('cascade');
            $table->foreign('id_milk_product')->references('id')->on('milk_products')->onDelete('cascade');
            $table->foreign('id_additional_product')->references('id')->on('additional_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
