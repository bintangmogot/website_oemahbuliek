<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalShift;

class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    public function definition()
    {
        $tglPresensi = $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');
        
        // Random shift times (jam kerja Indonesia umumnya)
        $jamMasukShift = $this->faker->randomElement(['08:00:00', '09:00:00', '10:00:00', '14:00:00', '18:00:00']);
        $jamKeluarShift = $this->faker->randomElement(['17:00:00', '18:00:00', '22:00:00', '02:00:00', '06:00:00']);
        
        // Simulasi keterlambatan (0-60 menit, kelipatan 5)
        $menitTerlambat = $this->faker->randomElement([0, 0, 0, 5, 10, 15, 20, 30, 45, 60]) * $this->faker->numberBetween(0, 1);
        
        // Jam masuk actual (bisa terlambat)
        $jamMasukActual = date('H:i:s', strtotime($jamMasukShift) + ($menitTerlambat * 60));
        
        // Jam keluar (normal atau lembur)
        $isLembur = $this->faker->boolean(30); // 30% kemungkinan lembur
        $jamKeluar = $isLembur ? 
            date('H:i:s', strtotime($jamKeluarShift) + $this->faker->numberBetween(1, 4) * 3600) : // lembur 1-4 jam
            $jamKeluarShift;
        
        // Status kehadiran berdasarkan keterlambatan
        $statusKehadiran = 1; // Present
        if ($menitTerlambat > 30) {
            $statusKehadiran = 2; // Late
        } elseif ($this->faker->boolean(5)) { // 5% absent
            $statusKehadiran = 0; // Absent
            $jamMasukActual = null;
            $jamKeluar = null;
        }

        return [
            'users_id' => User::factory(),
            'jadwal_shift_id' => $this->faker->boolean(90) ? JadwalShift::factory() : null, // 90% ada jadwal
            'tgl_presensi' => $tglPresensi,
            'jam_masuk' => $statusKehadiran > 0 ? $tglPresensi . ' ' . $jamMasukActual : null,
            'foto_masuk' => $statusKehadiran > 0 ? 'masuk_' . $this->faker->uuid . '.jpg' : null,
            'jam_keluar' => $statusKehadiran > 0 ? $tglPresensi . ' ' . $jamKeluar : null,
            'foto_keluar' => $statusKehadiran > 0 ? 'keluar_' . $this->faker->uuid . '.jpg' : null,
            'menit_terlambat' => $menitTerlambat,
            'status_kehadiran' => $statusKehadiran,
            'status_lembur' => $isLembur && $statusKehadiran > 0 ? $this->faker->randomElement([1, 2]) : 0,
            'status_approval' => $this->faker->randomElement([0, 1, 1, 1, 2]), // Lebih banyak approved
            'catatan_admin' => $this->faker->boolean(20) ? $this->faker->randomElement([
                'Terlambat karena macet',
                'Izin dokter',
                'Kebutuhan mendesak',
                'Lembur untuk project khusus',
                'Replacement shift'
            ]) : null,
        ];
    }

    /**
     * State untuk presensi normal (tidak terlambat)
     */
    public function normal()
    {
        return $this->state(function (array $attributes) {
            return [
                'menit_terlambat' => 0,
                'status_kehadiran' => 1,
                'status_lembur' => 0,
                'status_approval' => 1,
            ];
        });
    }

    /**
     * State untuk presensi terlambat
     */
    public function terlambat()
    {
        return $this->state(function (array $attributes) {
            $menitTerlambat = $this->faker->randomElement([15, 30, 45, 60, 90, 120]);
            return [
                'menit_terlambat' => $menitTerlambat,
                'status_kehadiran' => 2,
                'status_approval' => $this->faker->randomElement([0, 1, 2]),
            ];
        });
    }

    /**
     * State untuk presensi lembur
     */
    public function lembur()
    {
        return $this->state(function (array $attributes) {
            return [
                'status_lembur' => $this->faker->randomElement([1, 2]),
                'status_kehadiran' => 1,
                'status_approval' => 1,
            ];
        });
    }

    /**
     * State untuk absent
     */
    public function absent()
    {
        return $this->state(function (array $attributes) {
            return [
                'jam_masuk' => null,
                'foto_masuk' => null,
                'jam_keluar' => null,
                'foto_keluar' => null,
                'menit_terlambat' => 0,
                'status_kehadiran' => 0,
                'status_lembur' => 0,
                'status_approval' => 0,
                'catatan_admin' => $this->faker->randomElement([
                    'Tidak hadir tanpa keterangan',
                    'Sakit',
                    'Izin keluarga',
                    'Cuti'
                ])
            ];
        });
    }
}