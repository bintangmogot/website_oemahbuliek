<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id('id'); // Primary key
            $table->string('id_akun', 100)->unique(); 
            $table->string('nama_lengkap');
            $table->string('jabatan', 50);
            $table->date('tgl_masuk');
            $table->string('no_hp', 15);
            $table->string('alamat')->nullable();
        
        });

        {
        // Trigger untuk INSERT
        DB::unprepared('
            CREATE TRIGGER check_pegawai_role_insert 
            BEFORE INSERT ON pegawai
            FOR EACH ROW
            BEGIN
                DECLARE user_role VARCHAR(20);
                DECLARE user_exists INT DEFAULT 0;
                
                -- Cek apakah user ada
                SELECT COUNT(*) INTO user_exists FROM users WHERE email = NEW.id_akun;
                
                IF user_exists = 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "User dengan ID tersebut tidak ditemukan";
                END IF;
                
                -- Ambil role user
                SELECT role INTO user_role FROM users WHERE email = NEW.id_akun;
                
                -- Validasi role
                IF user_role != "pegawai" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Hanya user dengan role pegawai yang bisa ditambahkan ke tabel pegawai";
                END IF;
            END
        ');

        // Trigger untuk UPDATE
        DB::unprepared('
            CREATE TRIGGER check_pegawai_role_update 
            BEFORE UPDATE ON pegawai
            FOR EACH ROW
            BEGIN
                DECLARE user_role VARCHAR(20);
                DECLARE user_exists INT DEFAULT 0;
                
                -- Cek apakah user ada
                SELECT COUNT(*) INTO user_exists FROM users WHERE email = NEW.id_akun;
                
                IF user_exists = 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "User dengan ID tersebut tidak ditemukan";
                END IF;
                
                -- Ambil role user
                SELECT role INTO user_role FROM users WHERE email = NEW.id_akun;
                
                -- Validasi role
                IF user_role != "pegawai" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Hanya user dengan role pegawai yang bisa diupdate di tabel pegawai";
                END IF;
            END
        ');
    }
    }


    public function down()
    {

        // Drop triggers saat rollback
        DB::unprepared('DROP TRIGGER IF EXISTS check_pegawai_role_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_pegawai_role_update');

        Schema::dropIfExists('pegawai');
    }
};
