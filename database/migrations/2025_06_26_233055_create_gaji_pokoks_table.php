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
        Schema::create('gaji_pokok', function (Blueprint $table) {
            $table->id();

            $table->foreignId('users_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            // $table->foreignId('pengaturan_gaji_id')
            // ->constrained('pengaturan_gaji')
            // ->onDelete('cascade')
            // ->onUpdate('cascade');
            
            $table->date('periode_start');
            $table->date('periode_end');
            $table->date('periode_bulan'); // YYYY-MM

            // -- Snapshot tarif saat generate gaji
            $table->integer('tarif_per_jam')->default(0);
            $table->integer('tarif_potongan_per_menit')->default(0);

            $table->decimal('jumlah_jam_kerja', 8, 2)->default(0);
            $table->integer('total_menit_terlambat')->default(0);
            $table->integer('gaji_kotor')->default(0);
            $table->integer('total_potongan')->default(0);
            $table->integer('total_gaji_pokok')->default(0);
            $table->tinyInteger('status_pembayaran')->default(0)
                  ->comment('0=Unpaid, 1=Paid, 2=Partial');
            $table->date('tgl_bayar')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['users_id', 'periode_bulan']);
            $table->index(['periode_bulan', 'status_pembayaran']);
            $table->index(['periode_start', 'periode_end']);
            $table->index(['users_id', 'periode_start', 'periode_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_pokok');
    }
};
