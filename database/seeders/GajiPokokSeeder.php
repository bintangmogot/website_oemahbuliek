<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GajiPokok;
use App\Models\User;
use Carbon\Carbon;

class GajiPokokSeeder extends Seeder
{
    public function run()
    {
        // 1) Truncate tabel dulu (disable FK sementara)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        GajiPokok::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2) Siapkan 6 periode (Y-m) terakhir
        $dates = collect();
        for ($i = 0; $i < 6; $i++) {
            $dates->push(
                Carbon::now()->subMonths($i)->startOfMonth()->format('Y-m-d')
            );
        }

        // 3) Ambil semua user aktif yang punya pengaturanGaji
        $users = User::where('status', 1)
            ->has('pengaturanGaji')
            ->get();

        // 4) Loop user × periode, hitung semua kolom, lalu firstOrCreate
        foreach ($users as $user) {
            $tarifKerja = $user->pengaturanGaji->tarif_kerja_per_jam;
            $tarifPotong = $user->pengaturanGaji->potongan_terlambat_per_menit;

            foreach ($dates as $bulan) {
                // Hitung data dummy
                $jumlahJamKerja = rand(100, 200);
                $totalMenitTerlambat = rand(0, 300);
                $gajiKotor = $jumlahJamKerja * $tarifKerja;
                $totalPotongan = $totalMenitTerlambat * $tarifPotong;
                $totalGajiPokok = $gajiKotor - $totalPotongan;
                $statusPembayaran = rand(0, 2);
                $tglBayar = $statusPembayaran > 0
                    ? Carbon::parse($bulan . '-01')->addDays(rand(1, 10))->toDateString()
                    : null;

                // Tambahan untuk kolom baru
                $periodeStart = Carbon::parse($bulan)->startOfMonth()->format('Y-m-d');
                $periodeEnd = Carbon::parse($bulan)->endOfMonth()->format('Y-m-d');

                GajiPokok::firstOrCreate(
                    [
                        'users_id' => $user->id,
                        'periode_bulan' => $bulan,
                    ],
                    [
                        'periode_start' => $periodeStart,
                        'periode_end' => $periodeEnd,
                        'tarif_per_jam' => $tarifKerja,
                        'tarif_potongan_per_menit' => $tarifPotong,
                        'jumlah_jam_kerja' => $jumlahJamKerja,
                        'total_menit_terlambat' => $totalMenitTerlambat,
                        'gaji_kotor' => $gajiKotor,
                        'total_potongan' => $totalPotongan,
                        'total_gaji_pokok' => $totalGajiPokok,
                        'status_pembayaran' => $statusPembayaran,
                        'tgl_bayar' => $tglBayar,
                    ]
                );
            }
        }

        $this->command->info('GajiPokokSeeder: ' . GajiPokok::count() . ' records created.');
    }
}