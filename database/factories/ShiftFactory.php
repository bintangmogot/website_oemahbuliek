<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shift;
use Illuminate\Support\Str;


class ShiftFactory extends Factory
{
    protected $model = Shift::class;
    public function definition(): array
    {
        // Randomly generate shift name
        $prefix = $this->faker->randomElement(['Pagi','Siang','Malam','Dinihari','Lembur']);
        $suffix = Str::random(3);
        $namaShift = "$prefix-$suffix";

        // Generate random start time between 00:00 and 23:00
        $jamMulai = $this->faker->time('H:i:00');
        // Durasi shift antara 6 hingga 10 jam
        $durasi = $this->faker->numberBetween(6, 10) * 3600;
        // Hitung jam selesai
        $timestampMulai = strtotime($jamMulai);
        $jamSelesai = date('H:i:00', $timestampMulai + $durasi);

        // Toleransi terlambat antara 0-15 menit
        $toleransi = $this->faker->numberBetween(0, 15);
        // Batas lembur minimal antara 30-120 menit
        $batasLembur = $this->faker->randomElement([30, 45, 60, 90, 120]);
        // Tandai shift lembur acak (20% chance)
        $isLembur = $this->faker->boolean(20) ? 1 : 0;

        return [
            'nama_shift'           => $namaShift,
            'jam_mulai'            => $jamMulai,
            'jam_selesai'          => $jamSelesai,
            'toleransi_terlambat'  => $toleransi,
            'batas_lembur_min'     => $batasLembur,
            'is_shift_lembur'      => $isLembur,
            'status'               => 1,
        ];
    }
}
