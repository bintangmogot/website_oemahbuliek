<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaiJadwalsTable extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_jadwal', function (Blueprint $table) {
            $table->foreignId('jadwal_shift_id')
                  ->constrained('jadwal_shift')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('users_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->primary(['jadwal_shift_id','users_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_jadwal');
    }
}
