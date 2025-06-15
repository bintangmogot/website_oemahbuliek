<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    User::create([ // Admin
        'email' => 'admin@resto.test',
        'password' => bcrypt('password123'),
        'role' => 'admin',
        'nama_lengkap'  => 'Budi Santoso',
        'jabatan'       => 'Koki',
        'no_hp'         => '081234567890',
        'alamat'        => 'Jl. Merdeka No.1',
        'tgl_masuk'     => now()->subDays(30), // 30 hari yang lalu
        'foto_profil'   => null, // Atau path ke foto profil jika ada
    ]);
    User::create([ // Pegawai
        'email' => 'pegawai@resto.test',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
        'nama_lengkap'  => 'Siti Aminah',
        'jabatan'       => 'Pelayan',
        'no_hp'         => '081234567891',
        'alamat'        => 'Jl. Merdeka No.2',
        'tgl_masuk'     => now()->subDays(20), // 20 hari yang lalu
        'foto_profil'   => null,
    ]);
    User::create([ // Pegawai
        'email' => 'oke@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
        'nama_lengkap'  => 'Oke Setiawan',
        'jabatan'       => 'Pelayan',
        'no_hp'         => '081234567892',
        'alamat'        => 'Jl. Merdeka No.3',
        'tgl_masuk'     => now()->subDays(10), // 10 hari yang lalu
        'foto_profil'   => null,
    ]);
    User::create([ // Pegawai
        'email' => 'mantap@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
        'nama_lengkap'  => 'Mantap Jaya',
        'jabatan'       => 'Pelayan',
        'no_hp'         => '081234567893',
        'alamat'        => 'Jl. Merdeka No.4',
        'tgl_masuk'     => now()->subDays(5), // 5 hari yang lalu
        'foto_profil'   => null,
    ]);
    User::create([ // Pegawai
        'email' => 'mantappu@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
        'nama_lengkap'  => 'Mantap Jaya',
        'jabatan'       => 'Pelayan',
        'no_hp'         => '081234567893',
        'alamat'        => 'Jl. Merdeka No.4',
        'tgl_masuk'     => now()->subDays(1), // 1 hari yang lalu
        'foto_profil'   => null,
    ]);
    }
}