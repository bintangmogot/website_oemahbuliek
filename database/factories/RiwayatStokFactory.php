<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatStok>
 */
class RiwayatStokFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Factory ini hanya mendefinisikan struktur dasar.
        // Logika kuantitas dan harga akan diatur di dalam Seeder
        // agar lebih terkontrol dan logis.
        return [
            'tanggal' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'keterangan' => $this->faker->randomElement(['Pembelian dari supplier', 'Digunakan untuk produksi harian', null]),
        ];
    }
}