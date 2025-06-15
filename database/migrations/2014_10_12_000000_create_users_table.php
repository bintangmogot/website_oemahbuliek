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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');  // Tambahkan kolom ID sebagai primary key
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['admin','pegawai'])->default('pegawai');

            $table->string('nama_lengkap');
            $table->string('jabatan', 50);
            $table->date('tgl_masuk')->nullable();
            $table->string('no_hp', 15);
            $table->text('alamat')->nullable();
            $table->string('foto_profil')->nullable();

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
