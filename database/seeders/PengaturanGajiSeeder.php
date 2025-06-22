<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanGaji;

class PengaturanGajiSeeder extends Seeder
{
    public function run()
    {
        PengaturanGaji::factory()->count(5)->create();
    }
}