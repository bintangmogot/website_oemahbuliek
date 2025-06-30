<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiLembur;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GajiLemburSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('gaji_lembur')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil presensi yang status lemburnya > 0 (ada lembur)
        $presensiLembur = Presensi::where('status_lembur', '>', 0)->get();
        
        if ($presensiLembur->isEmpty()) {
            $this->command->info('Tidak ada presensi lembur ditemukan. Membuat sample data...');
            
            // Buat sample data
            $users = User::where('role', 'pegawai')->limit(10)->get();
            foreach ($users as $user) {
                $presensi = Presensi::factory()->lembur()->create([
                    'users_id' => $user->id,
                ]);
                
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
            if (!GajiLembur::where('presensi_id', $presensi->id)->exists()) {
                
                $tipeLembur = $this->getTipeLembur($presensi);
                $rateLembur = $this->getRateLemburByJabatan($presensi->user->jabatan, $tipeLembur);
                $jamLembur = $this->hitungJamLembur($tipeLembur);
                $totalGajiLemburValue = round(($rateLembur * $jamLembur) / 1000) * 1000;
                
                // Status pembayaran sederhana
                $statusPembayaran = rand(0, 100) <= 70 ? 1 : 0; // 70% sudah dibayar
                
                $tglBayar = null;
                if ($statusPembayaran == 1) {
                    $randomDays = rand(1, 30);
                    $tglBayar = now()->subDays($randomDays)->format('Y-m-d');
                }
                
                GajiLembur::create([
                    'users_id' => $presensi->users_id,
                    'presensi_id' => $presensi->id,
                    'tgl_lembur' => $presensi->tgl_presensi,
                    'tipe_lembur' => $tipeLembur,
                    'total_jam_lembur' => $jamLembur,
                    'total_gaji_lembur' => $totalGajiLemburValue,
                    'rate_lembur_per_jam' => $rateLembur,
                    'tgl_bayar' => $tglBayar,
                    'status_pembayaran' => $statusPembayaran,
                ]);
                
                $totalGajiLembur++;
            }
        }

        // Buat sample tambahan dengan factory
        GajiLembur::factory(15)->overtime()->dibayar()->create();
        GajiLembur::factory(5)->shiftLembur()->belumDibayar()->create();
        GajiLembur::factory(7)->jamTinggi()->create();

        $totalGajiLembur = GajiLembur::count();
        $this->command->info("Gaji lembur seeder completed: $totalGajiLembur gaji lembur records created.");
    }

    /**
     * Tentukan tipe lembur berdasarkan kondisi presensi
     */
    private function getTipeLembur($presensi)
    {
        $tanggal = \Carbon\Carbon::parse($presensi->tgl_presensi);
        
        // Weekend lebih cenderung shift lembur
        if ($tanggal->isWeekend()) {
            return rand(1, 100) <= 70 ? 'shift_lembur' : 'overtime';
        }
        
        // Jam lembur tinggi cenderung shift lembur
        if ($presensi->status_lembur >= 4) {
            return rand(1, 100) <= 60 ? 'shift_lembur' : 'overtime';
        }
        
        // Default overtime
        return rand(1, 100) <= 80 ? 'overtime' : 'shift_lembur';
    }

    /**
     * Get rate lembur berdasarkan jabatan dan tipe lembur
     */
    private function getRateLemburByJabatan($jabatan, $tipeLembur = 'overtime')
    {
        $baseRates = [
            'Manajer' => [60000, 75000, 100000],
            'Koki' => [35000, 40000, 45000],
            'Kasir' => [25000, 30000, 35000],
            'Pelayan' => [20000, 25000, 30000],
        ];
        
        $jabatanRates = $baseRates[$jabatan] ?? [20000, 25000, 30000];
        $baseRate = $jabatanRates[array_rand($jabatanRates)];
        
        // Shift lembur rate lebih tinggi
        if ($tipeLembur === 'shift_lembur') {
            $baseRate = round($baseRate * 1.3 / 5000) * 5000; // 30% lebih tinggi
        }
        
        return $baseRate;
    }

    /**
     * Hitung jam lembur berdasarkan tipe lembur
     */
    private function hitungJamLembur($tipeLembur = 'overtime')
    {
        if ($tipeLembur === 'shift_lembur') {
            return fake()->randomElement([4.0, 5.0, 6.0, 7.0, 8.0, 10.0, 12.0]);
        } else {
            return fake()->randomElement([1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 5.0]);
        }
    }
}