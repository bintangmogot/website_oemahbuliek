<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalShift;
use App\Models\Shift;

class JadwalShiftSeeder extends Seeder
{
    public function run()
    {
        $shift = Shift::where('nama_shift', 'Pagi')->first();

        JadwalShift::create([
            'shift_id'          => $shift->id,
            'nama_periode'      => 'Periode .',
            'mulai_berlaku'     => now()->subDays(5)->toDateString(),
            'berakhir_berlaku'  => now()->addDays(5)->toDateString(),
            'hari_kerja'        => 'Mon',
        ]);
        // Contoh: buat 10 periode jadwal
        JadwalShift::factory()->count(10)->create();
    }
}
