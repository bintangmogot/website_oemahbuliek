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
        'name' => 'Pemilik Restoran',
        'email' => 'admin@resto.test',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);
    User::create([ // Pegawai
        'name' => 'Pegawai Satu',
        'email' => 'pegawai@resto.test',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);
    User::create([ // Pegawai
        'name' => 'Pegawai Dua',
        'email' => 'oke@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);
    User::create([ // Pegawai
        'name' => 'Pegawai Tiga',
        'email' => 'mantap@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);
}
}
