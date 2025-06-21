<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresensisTable extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_shift_id')
                  ->constrained('jadwal_shift')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->foreignId('users_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->date('tgl_presensi');
            $table->smallInteger('shift_ke');
            $table->dateTime('jam_masuk')->nullable();
            $table->dateTime('jam_keluar')->nullable();
            $table->string('status_kehadiran', 50);
            $table->integer('menit_terlambat')->default(0);
            $table->integer('menit_lembur')->default(0);
            $table->integer('upah_lembur')->default(0);
            $table->integer('potongan_terlambat')->default(0);
            $table->boolean('is_calculated')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['users_id','tgl_presensi'], 'idx_presensi_user');
            $table->index('jadwal_shift_id', 'idx_presensi_jadwal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
}
