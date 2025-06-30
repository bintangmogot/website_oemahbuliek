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
            $table->id();
            $table->unsignedBigInteger('pengaturan_gaji_id')->nullable();

            $table->string('email', 100)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['admin','pegawai'])->default('pegawai');

            $table->string('nama_lengkap');
            $table->string('jabatan', 50);
            $table->date('tgl_masuk');
            $table->date('tgl_resign')->nullable();
            $table->string('no_hp', 15)->unique();
            $table->text('alamat')->nullable();
            $table->string('foto_profil')->nullable();

            $table->tinyInteger('status')->default(1)->comment('0=Resigned, 1=Active');
            
            $table->timestamps();

            $table->foreign('pengaturan_gaji_id')
                ->references('id')->on('pengaturan_gaji')
                ->onDelete('set null')
                ->onUpdate('cascade');


            // Indexes
            $table->index('role');
            $table->index('nama_lengkap');
            $table->index('status');


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
