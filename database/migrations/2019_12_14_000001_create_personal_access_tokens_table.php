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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        
                // {
    //     // Trigger untuk INSERT
    //     DB::unprepared('
    //         CREATE TRIGGER check_pegawai_role_insert 
    //         BEFORE INSERT ON pegawai
    //         FOR EACH ROW
    //         BEGIN
    //             DECLARE user_role VARCHAR(20);
    //             DECLARE user_exists INT DEFAULT 0;
                
    //             -- Cek apakah user ada
    //             SELECT COUNT(*) INTO user_exists FROM users WHERE email = NEW.id_akun;
                
    //             IF user_exists = 0 THEN
    //                 SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "User dengan ID tersebut tidak ditemukan";
    //             END IF;
                
    //             -- Ambil role user
    //             SELECT role INTO user_role FROM users WHERE email = NEW.id_akun;
                
    //             -- Validasi role
    //             IF user_role != "pegawai" THEN
    //                 SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Hanya user dengan role pegawai yang bisa ditambahkan ke tabel pegawai";
    //             END IF;
    //         END
    //     ');

    //     // Trigger untuk UPDATE
    //     DB::unprepared('
    //         CREATE TRIGGER check_pegawai_role_update 
    //         BEFORE UPDATE ON pegawai
    //         FOR EACH ROW
    //         BEGIN
    //             DECLARE user_role VARCHAR(20);
    //             DECLARE user_exists INT DEFAULT 0;
                
    //             -- Cek apakah user ada
    //             SELECT COUNT(*) INTO user_exists FROM users WHERE email = NEW.id_akun;
                
    //             IF user_exists = 0 THEN
    //                 SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "User dengan ID tersebut tidak ditemukan";
    //             END IF;
                
    //             -- Ambil role user
    //             SELECT role INTO user_role FROM users WHERE email = NEW.id_akun;
                
    //             -- Validasi role
    //             IF user_role != "pegawai" THEN
    //                 SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Hanya user dengan role pegawai yang bisa diupdate di tabel pegawai";
    //             END IF;
    //         END
    //     ');
    // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
