<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shift;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition()
    {
        $shifts = [
            ['nama_shift' => 'Pagi',   'jam_mulai' => '07:00:00', 'jam_selesai' => '15:00:00', 'toleransi_terlambat' => 10, 'batas_lembur_min' => 30],
            ['nama_shift' => 'Siang',   'jam_mulai' => '15:00:00', 'jam_selesai' => '23:00:00', 'toleransi_terlambat' => 5,  'batas_lembur_min' => 60],
            ['nama_shift' => 'Lembur','jam_mulai' => '23:00:00', 'jam_selesai' => '07:00:00', 'toleransi_terlambat' => 0,  'batas_lembur_min' => 120],
        ];

        return $this->faker->randomElement($shifts);
    }
}
