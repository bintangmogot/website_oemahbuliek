<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Pastikan nama tabel dan kolom sesuai yang ada di database
            $table->foreign('id_akun')
                  ->references('email')  // atau 'id' jika primary key adalah 'id'
                  ->on('users')        // pastikan nama tabel benar
                  ->onUpdate('cascade') 
                  ->onDelete('cascade');
                  
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropForeign(['id_akun']);
        });
    }
};