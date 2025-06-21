<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalShiftsTable extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode', 100);
            $table->foreignId('shift_id')
                  ->constrained('shift')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->date('mulai_berlaku')->nullable();
            $table->date('berakhir_berlaku')->nullable();
            $table->set('hari_kerja', ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']);
            $table->timestamps();

            $table->index('shift_id', 'idx_jadwal_shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift');
    }
}
