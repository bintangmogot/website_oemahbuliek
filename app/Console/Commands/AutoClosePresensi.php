<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi;
use App\Models\GajiLembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoClosePresensi extends Command
{
    protected $signature = 'presensi:autoclose';
    protected $description = 'Menutup presensi yang lupa checkout secara otomatis setelah melewati batas waktu';

    public function handle()
    {
        $this->info('Memulai proses auto-close presensi...');
        Log::info('Cron Job: Memulai AutoClosePresensi.');

        // Cari presensi yang sudah check-in, belum check-out, dan jadwalnya sudah lewat
        $stuckPresensis = Presensi::whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->whereHas('jadwalShift', function ($query) {
                // Logika untuk menemukan jadwal yang "terjebak"
                // Contoh: jadwal yang jam selesainya + 3 jam sudah lewat dari sekarang
                $batasWaktu = Carbon::now()->subHours(3);
                $query->where('tanggal', '<=', $batasWaktu->toDateString())
                      ->whereHas('shift', function ($shiftQuery) use ($batasWaktu) {
                          // Ini adalah logika yang disederhanakan, perlu penyesuaian untuk shift malam
                          // Untuk sekarang, kita anggap jam selesai + 3 jam sudah lewat
                      });
            })
            ->with('jadwalShift.shift')
            ->get();
        
        if ($stuckPresensis->isEmpty()) {
            $this->info('Tidak ada presensi yang perlu ditutup.');
            Log::info('Cron Job: Tidak ada presensi yang perlu ditutup.');
            return;
        }

        $this->info("Ditemukan {$stuckPresensis->count()} presensi yang akan ditutup.");
        Log::info("Cron Job: Ditemukan {$stuckPresensis->count()} presensi untuk ditutup.");

        foreach ($stuckPresensis as $presensi) {
            $shift = $presensi->jadwalShift->shift;
            $tanggalPresensi = Carbon::parse($presensi->tgl_presensi)->toDateString();
            $jamSelesaiShift = Carbon::parse($tanggalPresensi . ' ' . $shift->jam_selesai);
            
            if ($jamSelesaiShift->lessThan(Carbon::parse($tanggalPresensi . ' ' . $shift->jam_mulai))) {
                $jamSelesaiShift->addDay();
            }

            // PENALTI: Set jam keluar sama dengan jam selesai shift, overtime hangus
            $presensi->jam_keluar = $jamSelesaiShift;
            $jamKerjaEfektifMenit = $presensi->calculateEffectiveWorkHours($jamSelesaiShift);

            $presensi->update([
                'jam_keluar' => $jamSelesaiShift,
                'jam_kerja_efektif' => $jamKerjaEfektifMenit,
                'status_approval' => Presensi::STATUS_APPROVAL_PENDING,
                'status_lembur' => Presensi::STATUS_LEMBUR_NO,
                'catatan_admin' => 'Ditutup otomatis oleh sistem karena lupa checkout. Perlu direview.'
            ]);

            // Hapus record gaji lembur jika ada
            GajiLembur::where('presensi_id', $presensi->id)->delete();

            $this->info("Presensi ID: {$presensi->id} untuk user ID: {$presensi->users_id} telah ditutup.");
            Log::info("Cron Job: Presensi ID {$presensi->id} ditutup.");
        }

        $this->info('Proses auto-close presensi selesai.');
        Log::info('Cron Job: AutoClosePresensi selesai.');
    }
}