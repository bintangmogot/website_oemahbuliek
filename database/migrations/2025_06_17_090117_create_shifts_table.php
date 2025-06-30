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
            $table->tinyInteger('is_shift_lembur')->default(0)->comment('0=normal, 1=lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('toleransi_terlambat'); // dalam menit
            $table->unsignedInteger('batas_lembur_min');    // dalam menit
            $table->tinyInteger('status')->default(1)->comment('0=Inactive, 1=Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift');
    }
}
