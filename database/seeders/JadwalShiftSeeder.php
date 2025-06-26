<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalShift;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;


DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('jadwal_shift')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

class JadwalShiftSeeder extends Seeder
{
    public function run()
    {
        // Ambil shift dengan nama 'Pagi', atau buat jika tidak ada
        $shift = Shift::where('nama_shift', 'Pagi')->first();
        
        if (!$shift) {
            // Buat shift default jika tidak ada
            $shift = Shift::create([
                'nama_shift' => 'Pagi',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'toleransi_terlambat' => 15,
                'batas_lembur_min' => 60,
                'status' => 1
            ]);
        }

        // Ambil user role 'pegawai', atau buat jika tidak ada
        $user = User::where('role', 'pegawai')->inRandomOrder()->first();
        
        if (!$user) {
            $user = User::factory()->create([
                'role' => 'pegawai',
            ]);
        }

        // Membuat satu jadwal shift khusus (contoh)
        JadwalShift::firstOrCreate([
            'shift_id'   => $shift->id, 
            'users_id'   => $user->id,
            'tanggal'    => now()->addDays(1)->toDateString(), // Tanggal besok
            'status'     => 1, // Status aktif
        ]);

        // Contoh: buat 10 periode jadwal shift secara acak
        // Pastikan factory JadwalShift menggunakan kolom yang benar
        JadwalShift::factory()->count(50)->create();

        $this->command->info('Jadwal Shift seeder completed: ' . JadwalShift::count() . ' jadwal created.');
    }
}