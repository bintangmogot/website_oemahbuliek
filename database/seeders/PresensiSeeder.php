<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        // Truncate table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('presensi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Ambil semua pegawai
        $users = User::where('role', 'pegawai')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('Tidak ada pegawai ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // 2. Pastikan ada shift dan jadwal shift
        $this->ensureShiftsExist();
        $jadwalShifts = JadwalShift::with('shift')->get();

        if ($jadwalShifts->isEmpty()) {
            $this->command->error('Tidak ada jadwal shift ditemukan.');
            return;
        }

        // 3. Generate presensi untuk 3 bulan terakhir
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        $created = 0;
        $max = 100; // Batas maksimal factory

        // 4. Loop per user, per hari
        foreach ($users as $user) {
            $current = $startDate->copy();
            
            while ($current->lte($endDate) && $created < $max) {
                // Lewatkan Minggu, dan hanya 85% hadir
                if ($current->dayOfWeek !== Carbon::SUNDAY && rand(1, 100) <= 85) {
                    $tgl = $current->toDateString();
                    $jadwalShift = $jadwalShifts->random();

                    // Cek dulu apakah sudah ada presensi untuk kombinasi ini
                    $exists = Presensi::where([
                        'users_id' => $user->id,
                        'jadwal_shift_id' => $jadwalShift->id,
                        'tgl_presensi' => $tgl,
                    ])->exists();

                    if (!$exists) {
                        // Tentukan state berdasarkan probabilitas
                        $r = rand(1, 100);
                        $state = 'normal';
                        
                        if ($r <= 70) {
                            $state = 'normal';
                        } elseif ($r <= 85) {
                            $state = 'terlambat';
                        } elseif ($r <= 95) {
                            $state = 'lembur';
                        } else {
                            $state = 'absent';
                        }

                        // Buat presensi dengan waktu yang valid
                        $this->createPresensiWithValidTimes($user->id, $jadwalShift, $tgl, $state);
                        $created++;
                    }
                }
                
                $current->addDay();

                // Kalau sudah mencapai batas, keluar dari loop
                if ($created >= $max) {
                    break 2; // Keluar dari while dan foreach
                }
            }
        }

        $this->command->info("Presensi seeder completed: {$created} presensi records created.");
    }

    /**
     * Pastikan shift dan jadwal shift ada
     */
    private function ensureShiftsExist()
    {
        // Buat shift jika belum ada
        if (Shift::count() == 0) {
            $shifts = [
                ['nama_shift' => 'Pagi', 'jam_masuk' => '08:00:00', 'jam_keluar' => '16:00:00'],
                ['nama_shift' => 'Siang', 'jam_masuk' => '14:00:00', 'jam_keluar' => '22:00:00'],
                ['nama_shift' => 'Malam', 'jam_masuk' => '22:00:00', 'jam_keluar' => '06:00:00'],
            ];
            
            foreach ($shifts as $shift) {
                Shift::create($shift);
            }
        }

        // Buat jadwal shift jika belum ada
        if (JadwalShift::count() == 0) {
            $shifts = Shift::all();
            foreach ($shifts as $shift) {
                JadwalShift::create([
                    'shift_id' => $shift->id,
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Buat presensi dengan waktu yang valid
     */
    private function createPresensiWithValidTimes($userId, $jadwalShift, $tanggal, $state)
    {
        $presensiData = [
            'users_id' => $userId,
            'jadwal_shift_id' => $jadwalShift->id,
            'tgl_presensi' => $tanggal,
        ];

        // Jika absent, langsung buat dengan data kosong
        if ($state === 'absent') {
            $presensiData = array_merge($presensiData, [
                'jam_masuk' => null,
                'foto_masuk' => null,
                'jam_keluar' => null,
                'foto_keluar' => null,
                'status_kehadiran' => 0,
                'status_lembur' => 0,
                'status_approval' => 0,
                'catatan_admin' => collect([
                    'Tidak hadir tanpa keterangan',
                    'Sakit',
                    'Izin keluarga',
                    'Cuti'
                ])->random()
            ]);
        } else {
            // Generate waktu yang valid berdasarkan jadwal shift
            $timeData = $this->generateValidTimes($jadwalShift, $tanggal, $state);
            
            $presensiData = array_merge($presensiData, $timeData, [
                'foto_masuk' => 'masuk_' . fake()->uuid . '.jpg',
                'foto_keluar' => 'keluar_' . fake()->uuid . '.jpg',
                'status_kehadiran' => $state === 'terlambat' ? 2 : 1,
                'status_lembur' => $state === 'lembur' ? fake()->randomElement([1, 2]) : 0,
                'status_approval' => fake()->randomElement([0, 1, 1, 1, 2]),
                'catatan_admin' => fake()->boolean(20) ? fake()->randomElement([
                    'Terlambat karena macet',
                    'Izin dokter',
                    'Kebutuhan mendesak',
                    'Lembur untuk project khusus',
                    'Replacement shift'
                ]) : null,
            ]);
        }

        Presensi::create($presensiData);
    }

    /**
     * Generate waktu masuk dan keluar yang valid
     */
    private function generateValidTimes($jadwalShift, $tanggal, $state)
    {
        // Parse jadwal shift
        $jamMasukShift = Carbon::parse($tanggal . ' ' . $jadwalShift->shift->jam_masuk);
        $jamKeluarShift = Carbon::parse($tanggal . ' ' . $jadwalShift->shift->jam_keluar);
        
        // Handle shift malam (melewati tengah malam)
        if ($jamKeluarShift->lt($jamMasukShift)) {
            $jamKeluarShift->addDay();
        }

        $jamMasukActual = $jamMasukShift->copy();
        $jamKeluarActual = $jamKeluarShift->copy();

        // Sesuaikan berdasarkan state
        switch ($state) {
            case 'terlambat':
                // Tambahkan keterlambatan 5-120 menit
                $menitTerlambat = fake()->randomElement([5, 10, 15, 20, 30, 45, 60, 90, 120]);
                $jamMasukActual->addMinutes($menitTerlambat);
                break;
                
            case 'lembur':
                // Tambahkan lembur 1-4 jam
                $jamLembur = fake()->numberBetween(1, 4);
                $jamKeluarActual->addHours($jamLembur);
                break;
                
            case 'normal':
            default:
                // Variasi kecil untuk realisme (-5 sampai +10 menit untuk masuk)
                $jamMasukActual->addMinutes(fake()->numberBetween(-5, 10));
                // Variasi untuk keluar (-10 sampai +15 menit)
                $jamKeluarActual->addMinutes(fake()->numberBetween(-10, 15));
                break;
        }

        // VALIDASI: Pastikan jam masuk tidak lebih besar dari jam keluar
        if ($jamMasukActual->gte($jamKeluarActual)) {
            // Set jam keluar minimal 4 jam setelah jam masuk
            $jamKeluarActual = $jamMasukActual->copy()->addHours(4);
        }

        return [
            'jam_masuk' => $jamMasukActual->format('Y-m-d H:i:s'),
            'jam_keluar' => $jamKeluarActual->format('Y-m-d H:i:s'),
        ];
    }
}