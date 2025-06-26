<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PengaturanGaji;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $provider = $this->faker->randomElement(['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853', '0857', '0858']);
        $noHp = $provider . $this->faker->numerify('########'); // 8 digit setelah prefix
        $jabatanList = ['Koki Senior','Kasir','Pelayan','Manajer'];

        return [
            'pengaturan_gaji_id' => function() {return PengaturanGaji::inRandomOrder()->first()?->id ?? 1;},
            'email'         => $this->faker->unique()->userName . '@' . $this->faker->randomElement(['gmail.com', 'yahoo.com', 'outlook.com']),
            'password'      => bcrypt('password'), // default
            'role'          => $this->faker->randomElement(['admin','pegawai']),
            'nama_lengkap'  => $this->faker->name(),
            'jabatan'       => $this->faker->randomElement($jabatanList),
            'no_hp'         => $noHp,
            'alamat'        => $this->faker->address(),
            'tgl_masuk'     => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'foto_profil'   => null,
            'remember_token'=> Str::random(10),
            'status'        => $this->faker->randomElement([1, 1, 1, 1, 0]), // 80% aktif, 20% non-aktif
        ];
    }
    // di UserFactory
    public function admin()
    {
        return $this->state(fn() => ['role'=>'admin']);
    }
    public function pegawai()
    {
        return $this->state(fn() => ['role'=>'pegawai']);
    }

}
