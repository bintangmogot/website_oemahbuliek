<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanGaji;

class PengaturanGajiSeeder extends Seeder
{
    public function run()
    {
        $pengaturanGaji = [
            [
                'nama' => 'Staff Koki',
                'tarif_kerja_per_jam' => 25000,
                'tarif_lembur_per_jam' => 40000,
                'potongan_terlambat_per_menit' => 500,
                'status' => 1,
            ],
            [
                'nama' => 'Staff Koki',
                'tarif_kerja_per_jam' => 20000,
                'tarif_lembur_per_jam' => 30000,
                'potongan_terlambat_per_menit' => 400,
                'status' => 1,
            ],
            [
                'nama' => 'Staff Part Time',
                'tarif_kerja_per_jam' => 14000,
                'tarif_lembur_per_jam' => 18000,
                'potongan_terlambat_per_menit' => 300,
                'status' => 1,
            ],
            [
                'nama' => 'Staff Weekend Only',
                'tarif_kerja_per_jam' => 16000,
                'tarif_lembur_per_jam' => 20000,
                'potongan_terlambat_per_menit' => 350,
                'status' => 1,
            ],
            [
                'nama' => 'Staff Kontrak Lama (Discontinued)',
                'tarif_kerja_per_jam' => 10000,
                'tarif_lembur_per_jam' => 12000,
                'potongan_terlambat_per_menit' => 200,
                'status' => 0,
            ],
        ];

        $factory = PengaturanGaji::factory()->count(10);
        $factory->create();
        // Reset unique untuk faker global
        \Faker\Factory::create()->unique(true);

        $this->command->info('PengaturanGaji seeder completed: ' . count($pengaturanGaji) . ' pengaturan gaji created.');

    }
}