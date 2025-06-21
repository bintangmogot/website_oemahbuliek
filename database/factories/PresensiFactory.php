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
        // Ambil pegawai acak
        $user = User::where('role', 'pegawai')->inRandomOrder()->first()
              ?? User::factory()->create(['role' => 'pegawai']);

        // Ambil jadwal acak
        $jadwal = JadwalShift::inRandomOrder()->first()
               ?? JadwalShift::factory()->create();

        // Pilih tanggal presensi di antara periode jadwal
        $start = Carbon::parse($jadwal->mulai_berlaku);
        $end   = Carbon::parse($jadwal->berakhir_berlaku);
        $tgl   = $this->faker->dateTimeBetween($start, $end)->format('Y-m-d');

        // Hitung jam masuk/keluar berdasarkan shift dan toleransi
        $shift = $jadwal->shift;
        $waktuMasukIdeal  = Carbon::parse($shift->waktu_mulai);
        $waktuKeluarIdeal = Carbon::parse($shift->waktu_selesai);

        // Masuk bisa terlambat hingga toleransi
        $jamMasuk  = (clone $waktuMasukIdeal)
                      ->addMinutes($this->faker->numberBetween(0, $shift->toleransi_terlambat));

        // Keluar bisa lembur hingga batas lembur
        $jamKeluar = (clone $waktuKeluarIdeal)
                      ->addMinutes($this->faker->numberBetween(0, $shift->batas_lembur));

        // Hitung keterlambatan dan lembur
        $menitTerlambat = max(0, $jamMasuk->diffInMinutes($waktuMasukIdeal, false));
        $menitLembur    = max(0, $jamKeluar->diffInMinutes($waktuKeluarIdeal, false));

        return [
            'jadwal_shift_id'     => $jadwal->id,
            'users_id'            => $user->id,
            'tgl_presensi'        => $tgl,
            'shift_ke'            => 1, // default 1, atau random 1/2 jika ada multi shift
            'jam_masuk'           => $jamMasuk->format('Y-m-d H:i:s'),
            'jam_keluar'          => $jamKeluar->format('Y-m-d H:i:s'),
            'status_kehadiran'    => $menitTerlambat > 0 ? 'Terlambat' : 'Hadir',
            'menit_terlambat'     => $menitTerlambat,
            'menit_lembur'        => $menitLembur,
            'upah_lembur'         => $menitLembur * 5000, // misal tarif per menit
            'potongan_terlambat'  => $menitTerlambat * 2000, // misal potongan per menit
            'is_calculated'       => true,
            'keterangan'          => $this->faker->optional()->sentence(),
        ];
    }
}
