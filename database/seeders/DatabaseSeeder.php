<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UsersTableSeeder::class);
        $this->call(PegawaiSeeder::class);
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

 // Buat data pegawai
        // $pegawai1 = Pegawai::create([
        // 'nama_lengkap'  => 'Budi Santoso',
        // 'jabatan'       => 'Koki',
        // 'no_hp'         => '081234567890',
        // 'alamat'        => 'Jl. Merdeka No.1',
        // 'tgl_masuk'     => now()->subDays(30), // 30 hari yang lalu
        // ]);

        // $pegawai2 = Pegawai::create([
        //         'nama_lengkap'  => 'Siti Aminah',
        //         'jabatan'       => 'Kasir',
        //         'no_hp'         => '082345678901',
        //         'tgl_masuk'     => now()->subDays(15), // 15 hari yang lalu
        //         'alamat'         => 'Jl. Sudirman No.99',
        // ]);

        // // Buat user dengan foreign key ke pegawai
        // User::create([
        //     'name' => 'Ahmad Suryanto',
        //     'email' => 'ahmad@company.com',
        //     'password' => bcrypt('password123'),
        //     'role' => 'admin',
        //     'id' => $pegawai1->id,
        // ]);

        // User::create([
        //     'name' => 'Siti Nurhaliza',
        //     'email' => 'siti@company.com',
        //     'password' => bcrypt('password123'),
        //     'role' => 'pegawai',
        //     'id' => $pegawai2->id,
        // ]);

        // // Buat akun user terpisah
        // User::create([
        //     'name' => 'Admin Sistem',
        //     'email' => 'admin@system.com',
        //     'password' => bcrypt('admin123'),
        //     'role' => 'admin',
        // ]);
    }
}
