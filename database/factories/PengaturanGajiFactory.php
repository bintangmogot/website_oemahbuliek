<?php
namespace Database\Factories;

use App\Models\PengaturanGaji;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengaturanGajiFactory extends Factory
{
    protected $model = PengaturanGaji::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->unique()->word,
            'tarif_kerja_per_jam' => $this->faker->numberBetween(5000,20000),
            'tarif_lembur_per_jam' => $this->faker->numberBetween(10000,30000),
            'potongan_terlambat_per_menit' => $this->faker->numberBetween(100,1000),
            'status' => 1,
        ];
    }
}