<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PegawaiJadwal;
use App\Models\User;
use App\Models\JadwalShift;

class PegawaiJadwalSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = User::where('email', 'pegawai@resto.test')->first();
        $jadwal = JadwalShift::first();

        PegawaiJadwal::firstOrCreate([
            'users_id'        => $pegawai->id,
            'jadwal_shift_id' => $jadwal->id,
        ]);
                // Contoh: buat 20 assignment pegawai ke jadwal
        PegawaiJadwal::factory()->count(5)->create();
    }
}
