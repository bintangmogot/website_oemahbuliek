<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JadwalShift;
use App\Models\Shift;

class JadwalShiftFactory extends Factory
{
    protected $model = JadwalShift::class;

    public function definition()
    {
        // Pilih shift random
        $shift = Shift::inRandomOrder()->first() ?? Shift::factory()->create();

        // Rentang minggu depan
        $start = $this->faker->dateTimeBetween('now', '+1 month');
        $end   = (clone $start)->modify('+6 days');

        $hari = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        // Pilih subset random hari kerja
        $hariKerja = $this->faker->randomElements($hari, $this->faker->numberBetween(2,7));

        return [
            'shift_id'       => $shift->id,
            'nama_periode'   => 'Periode '.$start->format('d/m/Y').'–'.$end->format('d/m/Y'),
            'mulai_berlaku'  => $start->format('d-m-Y'),
            'berakhir_berlaku'=> $end->format('d-m-Y'),
            'hari_kerja'     => implode(',', $hariKerja),
        ];
    }
}
