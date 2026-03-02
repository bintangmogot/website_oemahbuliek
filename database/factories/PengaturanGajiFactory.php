<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PengaturanGaji;

class PengaturanGajiFactory extends Factory
{
    protected $model = PengaturanGaji::class;

    /** 
     * Daftar posisi dan base rate default-nya 
     */
    protected static $rates = [
        'Store Manager' => 50000,
        'Assistant Manager' => 40000,
        'Head Chef' => 40000,
        'Line Cook' => 25000,
        'Staff Koki' => 22000,
        'Server' => 16000,
        'Staff Kasir' => 18000,
        'Bartender Assistant' => 17000,
        'Dishwasher' => 13000,
        // ... tambahkan sesuai kebutuhan
    ];

    public function definition()
    {
        // List posisi dan level
        $posisiList = [
            'Store Manager',
            'Assistant Manager',
            'Head Chef',
            'Line Cook',
            'Staff Koki',
            'Server',
            'Staff Kasir',
            'Bartender Assistant',
            'Dishwasher',
            'Staff Pelayan',
            'Staff Cleaning'
        ];
        $levelList = ['Junior', 'Senior', 'Lead', 'Trainee'];

        // Gabungkan posisi + level, lalu pakai unique() untuk nama
        $nama = $this->faker->unique()->randomElement($posisiList) . ' ' . $this->faker->randomElement($levelList) . ' ' . $this->faker->numberBetween(1, 1000);

        // Ambil base rate
        $base = self::$rates[$this->faker->randomElement($posisiList)] ?? 15000;

        // Hitung tarif
        $tarifKerja = $base + $this->faker->numberBetween(-5, 5) * 1000;
        $tarifLembur = (int) (round($tarifKerja * 1.5 / 1000) * 1000);
        $potongan = $this->faker->randomElement(range(200, 1000, 50));

        return [
            'nama' => $nama,
            'tarif_kerja_per_jam' => $tarifKerja,
            'tarif_lembur_per_jam' => $tarifLembur,
            'potongan_terlambat_per_menit' => $potongan,
            'status' => $this->faker->boolean(80),
        ];
    }

}
