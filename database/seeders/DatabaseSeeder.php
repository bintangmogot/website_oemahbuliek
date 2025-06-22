<?php

namespace Database\Seeders;

use App\Models\PengaturanGaji;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ShiftSeeder::class,
            JadwalShiftSeeder::class,
            PegawaiJadwalSeeder::class,
            PresensiSeeder::class,
            PengaturanGajiSeeder::class,
        
        ]);

    }
}
