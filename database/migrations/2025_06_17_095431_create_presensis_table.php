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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
                  
            $table->foreignId('users_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreignId('jadwal_shift_id')->nullable()
                  ->constrained('jadwal_shift')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Data presensi
            $table->date('tgl_presensi');
            $table->dateTime('jam_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->dateTime('jam_keluar')->nullable();
            $table->string('foto_keluar')->nullable();
            
            // Calculated fields
            $table->integer('menit_terlambat')->default(0)->comment('Menit keterlambatan (auto calculated)');
            
            // Status fields
            $table->tinyInteger('status_kehadiran')->default(0)->comment('0=Absent,1=Present,2=Late,3=Half Day');
            $table->tinyInteger('status_lembur')->default(0)->comment('0=No Overtime,1=Overtime,2=Overtime Approved');
            $table->tinyInteger('status_approval')->default(0)->comment('0=Pending,1=Approved,2=Rejected');
            
            // Admin notes
            $table->text('catatan_admin')->nullable();
            
            $table->timestamps();

            // Unique constraint - satu user tidak bisa presensi 2x untuk jadwal yang sama
            $table->unique(['users_id', 'tgl_presensi', 'jadwal_shift_id'], 'uq_presensi');
            
            // Indexes untuk performa
            $table->index(['users_id', 'tgl_presensi'], 'idx_user_date');
            $table->index('jadwal_shift_id', 'idx_presensi_jadwal');
            $table->index('tgl_presensi', 'idx_presensi_tgl');
            $table->index(['status_kehadiran', 'status_lembur'], 'idx_presensi_status');
            $table->index('status_approval', 'idx_presensi_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};