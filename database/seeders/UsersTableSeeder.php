<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PengaturanGaji;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // (1) Truncate jika perlu agar seeder bisa di-run berkali-kali tanpa duplikat
        // User::truncate();
        // PengaturanGaji::truncate();


        // (2) Buat beberapa PengaturanGaji
        $gajis = [
            ['nama'=>'Gaji Koki Senior', 'tarif_kerja_per_jam'=>45000, 'tarif_lembur_per_jam'=>50000, 'potongan_terlambat_per_menit'=>1000, 'status'=>1],
            ['nama'=>'Gaji Kasir',        'tarif_kerja_per_jam'=>35000, 'tarif_lembur_per_jam'=>35000, 'potongan_terlambat_per_menit'=>5000, 'status'=>1],
            ['nama'=>'Gaji Pelayan',      'tarif_kerja_per_jam'=>20000, 'tarif_lembur_per_jam'=>30000, 'potongan_terlambat_per_menit'=>500,  'status'=>1],
            ['nama'=>'Gaji Manajer',      'tarif_kerja_per_jam'=>40000, 'tarif_lembur_per_jam'=>60000, 'potongan_terlambat_per_menit'=>1500, 'status'=>1],
        ];
        foreach ($gajis as $data) {
            PengaturanGaji::create($data);
        }

        // (3) Ambil semua ID PengaturanGaji
        $gajiIds = PengaturanGaji::pluck('id')->toArray();

        // (4) Buat Admin (gunakan salah satu pengaturan gaji)
        User::create([
            'pengaturan_gaji_id' => $gajiIds[array_rand($gajiIds)],
            'email'              => 'admin@resto.test',
            'password'           => bcrypt('password123'),
            'role'               => 'admin',
            'nama_lengkap'       => 'Bintang Surya',
            'jabatan'            => 'Owner',
            'no_hp'              => '081234567888',
            'alamat'             => 'Jl. Merdeka No.1',
            'tgl_masuk'          => now()->subDays(30),
            'status'             => 1,
        ]);

        // (5) Buat beberapa Pegawai manual
        $manual = [
            ['email'=>'pegawai@resto.test','nama_lengkap'=>'Siti Aminah','tgl_masuk'=>now()->subDays(20)],
            ['email'=>'oke@gmail.com',       'nama_lengkap'=>'Oke Setiawan','tgl_masuk'=>now()->subDays(10)],
            ['email'=>'mantap@gmail.com',    'nama_lengkap'=>'Mantap Jaya','tgl_masuk'=>now()->subDays(5)],
        ];
        foreach ($manual as $m) {
            User::create([
                'pengaturan_gaji_id' => $gajiIds[array_rand($gajiIds)],
                'email'              => $m['email'],
                'password'           => bcrypt('password123'),
                'role'               => 'pegawai',
                'nama_lengkap'       => $m['nama_lengkap'],
                'jabatan'            => 'Pelayan',
                'no_hp'              => '081234567'.rand(01,999),
                'alamat'             => 'Jl. Merdeka No.'.rand(2,10),
                'tgl_masuk'          => $m['tgl_masuk'],
                'status'             => 1,
            ]);
        }

        // (6) Bulk create dengan Factory—cukup random-kan pengaturan_gaji_id
        User::factory(20)
            ->make()
            ->each(function ($user) use ($gajiIds) {
                $user->pengaturan_gaji_id = $gajiIds[array_rand($gajiIds)];
                $user->save();
            });

        $this->command->info("UsersTableSeeder: ".User::count()." users created.");
    }

        // Buat admin dengan function admin()
        // User::factory()->admin()->create([
        // 'email'=>'admin@resto.test','nama_lengkap'=>'Bintang Surya','jabatan'=>'Manajer'
        // ]);
}




