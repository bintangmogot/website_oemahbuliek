<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalShift;
use Carbon\Carbon;

class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    public function definition()
    {
        $tglPresensi = $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');

        // Untuk menghindari masalah, kita akan set waktu default dulu
        // Nanti akan di-override di state atau di seeder dengan jadwal shift yang benar
        $jamMasuk = $tglPresensi . ' 08:00:00';
        $jamKeluar = $tglPresensi . ' 17:00:00';

        // Status kehadiran default
        $statusKehadiran = 1; // Present
        $isAbsent = $this->faker->boolean(5); // 5% absent

        if ($isAbsent) {
            $statusKehadiran = 0; // Absent
            $jamMasuk = null;
            $jamKeluar = null;
        }

        return [
            'users_id' => User::factory(),
            'jadwal_shift_id' => $this->faker->boolean(90) ? JadwalShift::factory() : null,
            'tgl_presensi' => $tglPresensi,
            'jam_masuk' => $jamMasuk,
            'foto_masuk' => $statusKehadiran > 0 ? 'masuk_' . $this->faker->uuid . '.jpg' : null,
            'jam_keluar' => $jamKeluar,
            'foto_keluar' => $statusKehadiran > 0 ? 'keluar_' . $this->faker->uuid . '.jpg' : null,
            'status_kehadiran' => $statusKehadiran,
            'status_lembur' => 0,
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
            // Akan diatur waktu yang tepat di seeder berdasarkan jadwal shift
            return [
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
            // Waktu akan diatur di seeder dengan menambahkan keterlambatan
            return [
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

    /**
     * Method helper untuk membuat waktu yang valid berdasarkan jadwal shift
     */
    public function withValidTimes($jadwalShift, $tanggal, $state = 'normal')
    {
        return $this->state(function (array $attributes) use ($jadwalShift, $tanggal, $state) {
            if ($attributes['status_kehadiran'] == 0) {
                // Jika absent, return data absent
                return [
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                ];
            }

            // Parse jadwal shift
            $jamMasukShift = Carbon::parse($tanggal . ' ' . $jadwalShift->jam_masuk);
            $jamKeluarShift = Carbon::parse($tanggal . ' ' . $jadwalShift->jam_keluar);

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
                    $menitTerlambat = $this->faker->randomElement([5, 10, 15, 20, 30, 45, 60, 90, 120]);
                    $jamMasukActual->addMinutes($menitTerlambat);
                    break;

                case 'lembur':
                    // Tambahkan lembur 1-4 jam
                    $jamLembur = $this->faker->numberBetween(1, 4);
                    $jamKeluarActual->addHours($jamLembur);
                    break;

                case 'normal':
                default:
                    // Variasi kecil untuk realisme (-5 sampai +10 menit)
                    $jamMasukActual->addMinutes($this->faker->numberBetween(-5, 10));
                    $jamKeluarActual->addMinutes($this->faker->numberBetween(-10, 15));
                    break;
            }

            // Pastikan jam masuk tidak lebih besar dari jam keluar
            if ($jamMasukActual->gte($jamKeluarActual)) {
                $jamKeluarActual = $jamMasukActual->copy()->addHours(8); // Minimal 8 jam kerja
            }

            return [
                'jam_masuk' => $jamMasukActual->format('Y-m-d H:i:s'),
                'jam_keluar' => $jamKeluarActual->format('Y-m-d H:i:s'),
            ];
        });
    }
}