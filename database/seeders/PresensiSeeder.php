<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('presensi')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

class PresensiSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada user dan jadwal shift
        // 1. ambil semua pegawai
        $users = User::where('role', 'pegawai')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('Tidak ada pegawai ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Buat jadwal shift sample jika belum ada
        if (JadwalShift::count() == 0) {
            $shifts = [
                ['nama_shift' => 'Pagi', 'jam_masuk' => '08:00:00', 'jam_keluar' => '16:00:00'],
                ['nama_shift' => 'Siang', 'jam_masuk' => '14:00:00', 'jam_keluar' => '22:00:00'],
                ['nama_shift' => 'Malam', 'jam_masuk' => '22:00:00', 'jam_keluar' => '06:00:00'],
            ];
            
            foreach ($shifts as $shift) {
                JadwalShift::create($shift);
            }
        }

        $jadwalShifts = JadwalShift::all();

        // Generate presensi untuk 3 bulan terakhir
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        $created = 0;
        $max = 100; //batas maksimal factory

        // 4) Loop per user, per hari
        foreach ($users as $user) {
            $current = $startDate->copy();
            while ($current->lte($endDate) && $created < $max) {
                // Lewatkan Minggu, dan hanya 85% hadir
                if ($current->dayOfWeek !== Carbon::SUNDAY && rand(1, 100) <= 85) {
                    $tgl  = $current->toDateString();
                    $js   = $jadwalShifts->random();

                    // Cek dulu apakah sudah ada presensi untuk kombinasi ini
                    $exists = Presensi::where([
                        'users_id'        => $user->id,
                        'jadwal_shift_id' => $js->id,
                        'tgl_presensi'    => $tgl,
                    ])->exists();

                    if (! $exists) {
                        // Pilih state factory berdasarkan probabilitas
                        $r = rand(1, 100);
                        if ($r <= 70) {
                            $factory = Presensi::factory()->normal();
                        } elseif ($r <= 85) {
                            $factory = Presensi::factory()->terlambat();
                        } elseif ($r <= 95) {
                            $factory = Presensi::factory()->lembur();
                        } else {
                            $factory = Presensi::factory()->absent();
                        }

                        // Buat record
                        $factory->create([
                            'users_id'        => $user->id,
                            'jadwal_shift_id' => $js->id,
                            'tgl_presensi'    => $tgl,
                        ]);

                        $created++;
                    }
                }
                $current->addDay();

                // kalau sudah mencapai batas, keluar dari loop
                if ($created >= $max) {
                    break 2; // keluar dari while dan foreach
                }
            }
        }

        $this->command->info("Presensi seeder completed: {$created} presensi records created.");
    }
}