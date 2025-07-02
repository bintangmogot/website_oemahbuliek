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
        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('tanggal');
            $table->enum('tipe_mutasi', ['masuk', 'produksi', 'rusak', 'penyesuaian']);
            $table->integer('kuantitas')->comment('Positif untuk masuk, Negatif untuk keluar');
            $table->decimal('harga_satuan', 10, 2)->nullable()->comment('Hanya diisi saat tipe mutasi = masuk');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok');
    }
};