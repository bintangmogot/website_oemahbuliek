<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengaturanGajisTable extends Migration
{
    public function up()
    {
        Schema::create('pengaturan_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->integer('tarif_kerja_per_jam')->default(0);
            $table->integer('tarif_lembur_per_jam')->default(0);
            $table->integer('potongan_terlambat_per_menit')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0=Inactive,1=Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengaturan_gaji');
    }
}
