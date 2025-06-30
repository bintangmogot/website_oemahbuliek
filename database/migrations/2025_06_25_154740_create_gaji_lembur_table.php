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
        Schema::create('gaji_lembur', function (Blueprint $table) {
            $table->id(); // INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            
            // Foreign Keys
            $table->foreignId('users_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreignId('presensi_id')
                  ->constrained('presensi')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            
            // Data Lembur
            $table->date('tgl_lembur');
            $table->decimal('total_jam_lembur', 8, 2)->default(0)->comment('Total jam lembur dalam desimal (misal: 1.5 = 1 jam 30 menit)');
            $table->integer('total_gaji_lembur')->default(0)->comment('Total gaji lembur dalam rupiah');

            $table->enum('tipe_lembur', ['shift_lembur', 'overtime'])->nullable();
            
            // Snapshot
            $table->integer('rate_lembur_per_jam')->default(0)->comment('Snapshot rate lembur per jam saat transaksi');

            // Payment Info
            $table->date('tgl_bayar')->nullable();
            $table->tinyInteger('status_pembayaran')->default(0)->comment('0=Unpaid,1=Paid');
            $table->text('keterangan_lembur')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['users_id', 'tgl_lembur'], 'idx_gl_user_tgl');
            $table->index('status_pembayaran', 'idx_gl_status');
            $table->index('tgl_lembur', 'idx_gl_tgl_lembur');
            $table->index('tgl_bayar', 'idx_gl_tgl_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_lembur');
    }
};