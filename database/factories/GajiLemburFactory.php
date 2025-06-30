<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GajiLembur;
use App\Models\Presensi;
use App\Models\User;

class GajiLemburFactory extends Factory
{
    protected $model = GajiLembur::class;

    public function definition()
    {
        // Buat presensi baru untuk setiap gaji lembur
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $presensi = Presensi::factory()->lembur()->create([
            'users_id' => $user->id
        ]);

        // Tipe lembur dengan probabilitas realistis
        $tipeLembur = $this->faker->randomElement([
            'overtime',     // 70% overtime biasa
            'overtime',
            'overtime',
            'overtime',
            'overtime',
            'overtime',
            'overtime',
            'shift_lembur', // 30% shift lembur
            'shift_lembur',
            'shift_lembur'
        ]);

        // Rate lembur per jam berdasarkan tipe
        if ($tipeLembur === 'shift_lembur') {
            $rateLemburPerJam = $this->faker->randomElement([
                35000, 40000, 45000, 50000, 60000, 75000, 100000
            ]);
        } else {
            $rateLemburPerJam = $this->faker->randomElement([
                15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000
            ]);
        }

        // Total jam lembur berdasarkan tipe
        if ($tipeLembur === 'shift_lembur') {
            $totalJamLembur = $this->faker->randomElement([
                4.0, 5.0, 6.0, 7.0, 8.0, 10.0, 12.0
            ]);
        } else {
            $totalJamLembur = $this->faker->randomElement([
                0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 6.0
            ]);
        }

        // Status pembayaran sederhana: 0=belum, 1=sudah dibayar
        $statusPembayaran = $this->faker->randomElement([0, 0, 1, 1, 1, 1]);
        
        // Tanggal bayar sederhana - random dalam 30 hari terakhir
        $tglBayar = null;
        if ($statusPembayaran == 1) {
            $randomDays = rand(1, 30);
            $tglBayar = now()->subDays($randomDays)->format('Y-m-d');
        }

        return [
            'users_id'           => $presensi->users_id,
            'presensi_id'        => $presensi->id,
            'tgl_lembur'         => $presensi->tgl_presensi,
            'tipe_lembur'        => $tipeLembur,
            'rate_lembur_per_jam'=> $rateLemburPerJam,
            'total_jam_lembur'   => $totalJamLembur,
            'total_gaji_lembur'  => round(($rateLemburPerJam * $totalJamLembur) / 1000) * 1000,
            'status_pembayaran'  => $statusPembayaran,
            'tgl_bayar'          => $tglBayar,
        ];
    }

    /** State: sudah dibayar */
    public function dibayar()
    {
        return $this->state(function(array $attributes) {
            $randomDays = rand(1, 30);
            return [
                'status_pembayaran' => 1,
                'tgl_bayar' => now()->subDays($randomDays)->format('Y-m-d'),
            ];
        });
    }

    /** State: belum dibayar */
    public function belumDibayar()
    {
        return $this->state(fn() => [
            'status_pembayaran' => 0,
            'tgl_bayar'         => null,
        ]);
    }

    /** State: jam lembur tinggi */
    public function jamTinggi()
    {
        return $this->state(fn() => [
            'total_jam_lembur'   => $this->faker->randomElement([6, 8, 10, 12, 14, 16]),
            'rate_lembur_per_jam'=> $this->faker->randomElement([40000, 50000, 60000, 75000, 100000]),
            'tipe_lembur'        => 'shift_lembur',
        ]);
    }

    /** State: overtime biasa */
    public function overtime()
    {
        return $this->state(fn() => [
            'tipe_lembur'        => 'overtime',
            'total_jam_lembur'   => $this->faker->randomElement([1.0, 1.5, 2.0, 2.5, 3.0, 4.0]),
            'rate_lembur_per_jam'=> $this->faker->randomElement([15000, 20000, 25000, 30000, 35000]),
        ]);
    }

    /** State: shift lembur */
    public function shiftLembur()
    {
        return $this->state(fn() => [
            'tipe_lembur'        => 'shift_lembur',
            'total_jam_lembur'   => $this->faker->randomElement([6.0, 7.0, 8.0, 10.0, 12.0]),
            'rate_lembur_per_jam'=> $this->faker->randomElement([40000, 50000, 60000, 75000, 100000]),
        ]);
    }
}