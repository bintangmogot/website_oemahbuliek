<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiLembur;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;


DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('gaji_lembur')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

class GajiLemburSeeder extends Seeder
{
    public function run()
    {
        // Ambil presensi yang status lemburnya > 0 (ada lembur)
        $presensiLembur = Presensi::where('status_lembur', '>', 0)->get();
        
        if ($presensiLembur->isEmpty()) {
            $this->command->info('Tidak ada presensi lembur ditemukan. Jalankan PresensiSeeder terlebih dahulu.');
            
            // Buat beberapa sample jika tidak ada
            $users = User::where('role', 'pegawai')->limit(10)->get();
            foreach ($users as $user) {
                // Buat presensi lembur
                $presensi = Presensi::factory()->lembur()->create([
                    'users_id' => $user->id,
                ]);
                
                // Buat gaji lembur untuk presensi ini
                GajiLembur::factory()->create([
                    'users_id' => $user->id,
                    'presensi_id' => $presensi->id,
                    'tgl_lembur' => $presensi->tgl_presensi,
                ]);
            }
            
            $this->command->info('Sample gaji lembur created: 10 records');
            return;
        }

        $totalGajiLembur = 0;

        // Untuk setiap presensi lembur, buat record gaji lembur
        foreach ($presensiLembur as $presensi) {
            // Pastikan belum ada gaji lembur untuk presensi ini
            if (!GajiLembur::where('presensi_id', $presensi->id)->exists()) {
                
                // Tentukan rate lembur berdasarkan jabatan user
                $rateLembur = $this->getRateLemburByJabatan($presensi->user->jabatan);
                
                // Hitung jam lembur berdasarkan jam kerja actual
                $jamLembur = $this->hitungJamLembur($presensi);
                
                // Hitung total gaji lembur
                $totalGajiLembur = round(($rateLembur * $jamLembur) / 1000) * 1000;
                
                // Tentukan status pembayaran random tapi realistis
                $statusPembayaran = $this->getStatusPembayaran($presensi->tgl_presensi);
                
                GajiLembur::create([
                    'users_id' => $presensi->users_id,
                    'presensi_id' => $presensi->id,
                    'tgl_lembur' => $presensi->tgl_presensi,
                    'total_jam_lembur' => $jamLembur,
                    'total_gaji_lembur' => $totalGajiLembur,
                    'rate_lembur_per_jam' => $rateLembur,
                    'tgl_bayar' => $statusPembayaran['tgl_bayar'],
                    'status_pembayaran' => $statusPembayaran['status'],
                ]);
                
                $totalGajiLembur++;
            }
        }

        // Buat beberapa sample tambahan dengan factory
        
        // Gaji lembur yang sudah dibayar
        GajiLembur::factory(15)->dibayar()->create();
        
        // Gaji lembur yang belum dibayar
        GajiLembur::factory(5)->belumDibayar()->create();
        
        // Gaji lembur sebagian
        GajiLembur::factory(3)->sebagian()->create();
        
        // Gaji lembur jam tinggi
        GajiLembur::factory(7)->jamTinggi()->create();

        $totalGajiLembur = GajiLembur::count();

        $this->command->info("Gaji lembur seeder completed: $totalGajiLembur gaji lembur records created.");
    }

    /**
     * Get rate lembur berdasarkan jabatan
     */
    private function getRateLemburByJabatan($jabatan)
    {
        $rates = [
            'Manajer' => [60000, 75000, 100000],
            'Koki' => [35000, 40000, 45000],
            'Kasir' => [25000, 30000, 35000],
            'Pelayan' => [20000, 25000, 30000],
        ];
        
        $jabatanRates = $rates[$jabatan] ?? [20000, 25000, 30000];
        
        return $jabatanRates[array_rand($jabatanRates)];
    }

    /**
     * Hitung jam lembur berdasarkan jam kerja
     */
    private function hitungJamLembur($presensi)
    {
        // Simulasi jam lembur (dalam kondisi nyata ini dihitung dari jam kerja actual)
        $jamLemburOptions = [1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 5.0, 6.0];
        
        // Lebih sering jam lembur yang wajar (1-3 jam)
        $weights = [25, 20, 15, 10, 10, 8, 7, 3, 2];
        
        return $this->weightedRandom($jamLemburOptions, $weights);
    }

    /**
     * Get status pembayaran berdasarkan tanggal
     */
    private function getStatusPembayaran($tglLembur)
    {
        $tglLemburCarbon = \Carbon\Carbon::parse($tglLembur);
        $sekarang = \Carbon\Carbon::now();
        
        // Jika lembur lebih dari 1 bulan yang lalu, kemungkinan besar sudah dibayar
        if ($tglLemburCarbon->diffInDays($sekarang) > 30) {
            $status = rand(1, 100) <= 90 ? 1 : 2; // 90% dibayar, 10% sebagian
            $tglBayar = $tglLemburCarbon->addDays(rand(7, 30))->format('Y-m-d');
        }
        // Jika 2 minggu - 1 bulan, 70% sudah dibayar
        elseif ($tglLemburCarbon->diffInDays($sekarang) > 14) {
            $rand = rand(1, 100);
            if ($rand <= 70) {
                $status = 1;
                $tglBayar = $tglLemburCarbon->addDays(rand(7, 21))->format('Y-m-d');
            } elseif ($rand <= 85) {
                $status = 2;
                $tglBayar = $tglLemburCarbon->addDays(rand(7, 21))->format('Y-m-d');
            } else {
                $status = 0;
                $tglBayar = null;
            }
        }
        // Jika masih baru, kemungkinan besar belum dibayar
        else {
            $status = rand(1, 100) <= 20 ? 1 : 0; // 20% sudah dibayar, 80% belum
            $tglBayar = $status == 1 ? $tglLemburCarbon->addDays(rand(1, 7))->format('Y-m-d') : null;
        }
        
        return [
            'status' => $status,
            'tgl_bayar' => $tglBayar
        ];
    }

    /**
     * Weighted random selection
     */
    private function weightedRandom($values, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $value;
            }
        }
        
        return $values[0]; // fallback
    }
}