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
        Schema::create('information_departements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_information');
            $table->unsignedBigInteger('id_department');
            $table->timestamps();

            $table->foreign('id_information')->references('id')->on('informations')->onDelete('cascade');
            $table->foreign('id_department')->references('id')->on('departements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information_departements');
    }
};
