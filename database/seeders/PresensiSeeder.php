<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalShift;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = User::where('email', 'pegawai@resto.test')->first();
        $jadwal = JadwalShift::first();

        Presensi::create([
            'users_id'           => $pegawai->id,
            'jadwal_shift_id'    => $jadwal->id,
            'tgl_presensi'       => now()->toDateString(),
            'shift_ke'           => 1,
            'jam_masuk'          => now()->setTime(07, 5),
            'jam_keluar'         => now()->setTime(15, 10),
            'status_kehadiran'   => 'hadir',
            'menit_terlambat'    => 5,
            'menit_lembur'       => 10,
            'upah_lembur'        => 15000,
            'potongan_terlambat' => 5000,
            'is_calculated'      => true,
            'keterangan'         => 'Terlambat sedikit',
        ]);

                // Generate 50 catatan presensi
        Presensi::factory()->count(50)->create();
    }
}
