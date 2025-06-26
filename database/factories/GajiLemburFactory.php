<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GajiLembur;
use App\Models\Presensi;

class GajiLemburFactory extends Factory
{
    protected $model = GajiLembur::class;

    public function definition()
    {
        // Ambil satu presensi dengan status_lembur > 0 yang sudah ada, atau buat satu
        $presensi = Presensi::where('status_lembur', '>', 0)
                     ->inRandomOrder()
                     ->first()
                 ?? Presensi::factory()->lembur()->create();

        // Rate lembur per jam (kelipatan 5000)
        $rateLemburPerJam = $this->faker->randomElement([
            15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000, 60000, 75000, 100000
        ]);

        // Total jam lembur (kelipatan 0.5 jam)
        $totalJamLembur = $this->faker->randomElement([
            0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 6.0, 8.0
        ]);

        return [
            // Ikuti presensi yang sudah ada
            'users_id'           => $presensi->users_id,
            'presensi_id'        => $presensi->id,
            'tgl_lembur'         => $presensi->tgl_presensi,

            // Hitung gaji lembur dan rate
            'rate_lembur_per_jam'=> $rateLemburPerJam,
            'total_jam_lembur'   => $totalJamLembur,
            'total_gaji_lembur'  => fn(array $attrs) => round(($attrs['rate_lembur_per_jam'] * $attrs['total_jam_lembur']) / 1000) * 1000,

            // Status pembayaran: 0=belum, 1=dibayar, 2=partial
            'status_pembayaran'  => $this->faker->randomElement([0, 1, 1, 1, 2]),

            // Tanggal bayar jika status > 0
            'tgl_bayar'          => function(array $attrs) {
                return $attrs['status_pembayaran'] > 0
                    ? $this->faker->dateTimeBetween($attrs['tgl_lembur'], 'now')->format('Y-m-d')
                    : null;
            },
        ];
    }

    /** State: sudah dibayar penuh */
    public function dibayar()
    {
        return $this->state(fn() => [
            'status_pembayaran' => 1,
            'tgl_bayar'         => now()->toDateString(),
        ]);
    }

    /** State: belum dibayar sama sekali */
    public function belumDibayar()
    {
        return $this->state(fn() => [
            'status_pembayaran' => 0,
            'tgl_bayar'         => null,
        ]);
    }

    /** State: dibayar sebagian */
    public function sebagian()
    {
        return $this->state(fn() => [
            'status_pembayaran' => 2,
            // set tgl_bayar antara tgl_lembur dan sekarang
            'tgl_bayar' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ]);
    }

    /** State: jam lembur tinggi */
    public function jamTinggi()
    {
        return $this->state(fn() => [
            'total_jam_lembur'   => $this->faker->randomElement([4, 5, 6, 8, 10, 12]),
            'rate_lembur_per_jam'=> $this->faker->randomElement([40000, 50000, 60000, 75000, 100000]),
        ]);
    }
}
