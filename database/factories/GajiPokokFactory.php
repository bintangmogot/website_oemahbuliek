<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GajiPokok;
use App\Models\User;
use Carbon\Carbon;

class GajiPokokFactory extends Factory
{
    protected $model = GajiPokok::class;

    public function definition()
    {
        // Pilih user aktif dengan pengaturan gaji
        $user = User::where('status', 1)
                    ->has('pengaturanGaji')
                    ->inRandomOrder()
                    ->first();

        // Format periode bulan YYYY-MM
        $periode = Carbon::now()->subMonth()->format('Y-m');

        // Random jumlah jam kerja antara 80.00 - 200.00
        $jumlahJamKerja = $this->faker->randomFloat(2, 80, 200);

        // Random total menit terlambat antara 0 - 300 menit
        $totalMenitTerlambat = $this->faker->numberBetween(0, 300);

        // Tarif dari pengaturan gaji
        $tarifKerja = $user->pengaturanGaji->tarif_kerja_per_jam;
        $tarifPotong = $user->pengaturanGaji->potongan_terlambat_per_menit;

        // Hitung gaji kotor dan potongan
        $gajiKotor = (int) round($jumlahJamKerja * $tarifKerja);
        $totalPotongan = (int) ($totalMenitTerlambat * $tarifPotong);
        $totalGajiPokok = max(0, $gajiKotor - $totalPotongan);

        // Status pembayaran dan tanggal bayar
        $status = $this->faker->randomElement([0,1,2]);
        $tglBayar = $status > 0 ? Carbon::now()->toDateString() : null;

        // Tambahan untuk kolom baru
        $periodeStart = Carbon::parse($periode . '-01')->startOfMonth()->format('Y-m-d');
        $periodeEnd = Carbon::parse($periode . '-01')->endOfMonth()->format('Y-m-d');

        return [
            'users_id'                 => $user->id,
            'periode_start'            => $periodeStart,
            'periode_end'              => $periodeEnd,
            'periode_bulan'            => $periode,
            'tarif_per_jam'            => $tarifKerja,
            'tarif_potongan_per_menit' => $tarifPotong,
            'jumlah_jam_kerja'         => $jumlahJamKerja,
            'total_menit_terlambat'    => $totalMenitTerlambat,
            'gaji_kotor'               => $gajiKotor,
            'total_potongan'           => $totalPotongan,
            'total_gaji_pokok'         => $totalGajiPokok,
            'status_pembayaran'        => $status,
            'tgl_bayar'                => $tglBayar,
        ];
    }
}