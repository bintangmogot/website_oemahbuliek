<?php

namespace Database\Seeders;

use App\Models\PengaturanGaji;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Mulaikan database seeding...');

        $this->call([
            PengaturanGajiSeeder::class,
            UsersTableSeeder::class,
            ShiftSeeder::class,
            JadwalShiftSeeder::class,
            PresensiSeeder::class,
            GajiLemburSeeder::class, 

        ]);

        $this->command->info('✅ Database seeding Berhasil, Mantappu!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   Users: ' . \App\Models\User::count());
        $this->command->info('   Presensi: ' . \App\Models\Presensi::count());
        $this->command->info('   Gaji Lembur: ' . \App\Models\GajiLembur::count());
        $this->command->info('   Jadwal: ' . \App\Models\JadwalShift::count());
        $this->command->info('   Pengaturan Gaji: ' . PengaturanGaji::count());
        $this->command->info('   Shift: ' . \App\Models\Shift::count());
        $this->command->info('');
        $this->command->info('🔑 Default Login:');
        $this->command->info('   Email: admin@resto.test');
        $this->command->info('   Password: password123');
    }
}
