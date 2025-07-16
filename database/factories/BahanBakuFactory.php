<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BahanBaku>
 */
class BahanBakuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Daftar bahan baku yang realistis untuk restoran
        $bahan = [
            'Beras Pandan Wangi', 'Ayam Fillet Paha', 'Bawang Merah', 'Bawang Putih', 'Cabai Rawit Merah',
            'Minyak Goreng', 'Gula Pasir', 'Garam Meja', 'Kecap Manis', 'Saus Tiram', 'Telur Ayam',
            'Tepung Terigu', 'Daging Sapi Giling', 'Tomat Segar', 'Daun Bawang', 'Jeruk Nipis'
        ];

        return [
            'nama' => $this->faker->unique()->randomElement($bahan),
            'kategori' => $this->faker->randomElement(['Bahan Makanan', 'Bumbu', 'Bahan Minuman']),
            'satuan' => $this->faker->randomElement([0, 1]), // 0: gram, 1: pcs
            // Stok awal kita set 0, nanti akan di-update oleh Seeder setelah riwayat dibuat
            'stok_terkini' => 0, 
            'stok_minimum' => $this->faker->numberBetween(100, 1000),
        ];
    }
}