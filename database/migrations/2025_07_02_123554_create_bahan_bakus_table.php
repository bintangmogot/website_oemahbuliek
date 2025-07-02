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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kategori', ['Bahan Makanan', 'Bumbu', 'Bahan Minuman']);
            $table->tinyInteger('satuan')->comment('0: gram, 1: pcs');
            $table->integer('stok_terkini')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};