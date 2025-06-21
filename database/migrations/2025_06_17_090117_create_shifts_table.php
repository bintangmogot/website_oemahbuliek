<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    public function up()
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift', 50)->unique();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('toleransi_terlambat'); // dalam menit
            $table->unsignedInteger('batas_lembur_min');    // dalam menit
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift');
    }
}
