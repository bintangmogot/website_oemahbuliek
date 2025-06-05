<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PegawaiSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // Menambahkan data dummy ke tabel pegawai
        DB::table('pegawai')->insert([
            // [
            //     'id_akun'       => 1, // sesuaikan dengan data id_akun yang ada
            //     'nama_lengkap'  => 'Budi Santoso',
            //     'jabatan'       => 'Koki',
            //     'no_hp'         => '081234567890',
            //     'alamat'        => 'Jl. Merdeka No.1',
            //     'tgl_masuk'     => now()->subDays(30), // 30 hari yang lalu
            // ],
            [
                'id_akun'       => 3,
                'nama_lengkap'  => 'Siti Aminah',
                'jabatan'       => 'Kasir',
                'no_hp'         => '082345678901',
                'tgl_masuk'     => now()->subDays(15), // 15 hari yang lalu
                'alamat'         => 'Jl. Sudirman No.99',
            ],
        //     [
        //         'id_akun'       => 2,
        //         'nama_lengkap'  => 'Joko Widodo',
        //         'jabatan'       => 'Pelayan',
        //         'no_hp'         => '083456789012',
        //         'tgl_masuk'     => now()->subDays(10), // 10 hari yang lalu
        //         'alamat'        => 'Jl. Pahlawan No.5',
        //     ],

        ]);
    }
}
