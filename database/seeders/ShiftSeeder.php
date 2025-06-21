<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run()
    {

        Shift::firstOrCreate([
            'nama_shift' => 'Pagi',
        ], [
            'jam_mulai'            => '07:00:00',
            'jam_selesai'          => '15:00:00',
            'toleransi_terlambat'  => 10,
            'batas_lembur_min'     => 60,
        ]);

        Shift::firstOrCreate([
            'nama_shift' => 'Siang',
        ], [
            'jam_mulai' => '15:00:00',
            'jam_selesai' => '23:00:00',
            'toleransi_terlambat' => 5,
            'batas_lembur_min' => 60,
        ]);

        Shift::firstOrCreate([
            'nama_shift' => 'Sore',
        ], [
            'jam_mulai'            => '15:00:00',
            'jam_selesai'          => '22:00:00',
            'toleransi_terlambat'  => 10,
            'batas_lembur_min'     => 60,
        ]);

        // 3 shift master
        // Shift::factory()->count(3)->create();
    }
}
