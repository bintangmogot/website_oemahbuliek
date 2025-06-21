<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'email'         => $this->faker->unique()->safeEmail(),
            'password'      => bcrypt('password'), // default
            'role'          => 'pegawai',
            'nama_lengkap'  => $this->faker->name(),
            'jabatan'       => $this->faker->randomElement(['Koki','Pelayan','Kasir','Manajer']),
            'no_hp'         => $this->faker->numerify('08#########'), // antara 10–12 digit
            'alamat'        => $this->faker->address(),
            'tgl_masuk'     => $this->faker->dateTimeBetween('-1 year', 'now'),
            'foto_profil'   => null,
            'remember_token'=> Str::random(10),
        ];
    }
}
