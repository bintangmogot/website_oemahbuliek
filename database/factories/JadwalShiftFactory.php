<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JadwalShift;
use App\Models\Shift;
use App\Models\User;

class JadwalShiftFactory extends Factory
{
    protected $model = JadwalShift::class;

    public function definition(): array
    {
        // Ambil shift atau buat jika tidak ada
        $shift = Shift::inRandomOrder()->first() ?? Shift::factory()->create();

        // Ambil user role pegawai, atau buat user baru
        $user = User::where('role', 'pegawai')->inRandomOrder()->first() ?? User::factory()->create([
            'role' => 'pegawai'
        ]);

        return [
            'shift_id'   => $shift->id,
            'users_id'   => $user->id,
            'tanggal'    => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status'     => $this->faker->randomElement([0, 1, 2]), // 0=Cancelled, 1=Active, 2=Completed
        ];
    }
}
