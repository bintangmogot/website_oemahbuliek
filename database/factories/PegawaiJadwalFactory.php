<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PegawaiJadwal;
use App\Models\User;
use App\Models\JadwalShift;

class PegawaiJadwalFactory extends Factory
{
    protected $model = PegawaiJadwal::class;

public function definition(): array
{
    static $usedPairs = [];

    do {
        $user_id = User::inRandomOrder()->first()->id;
        $jadwal_id = JadwalShift::inRandomOrder()->first()->id;
        $pairKey = "$user_id-$jadwal_id";
    } while (in_array($pairKey, $usedPairs));

    $usedPairs[] = $pairKey;

    return [
        'users_id' => $user_id,
        'jadwal_shift_id' => $jadwal_id,
    ];
}

}
