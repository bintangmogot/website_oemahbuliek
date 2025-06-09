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
        'role' => 'admin'
    ]);
    User::create([ // Pegawai
        'email' => 'pegawai@resto.test',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);
    User::create([ // Pegawai
        'email' => 'oke@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);
    User::create([ // Pegawai
        'email' => 'mantap@gmail.com',
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);
}
}
