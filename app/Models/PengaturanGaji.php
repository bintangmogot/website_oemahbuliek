<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanGaji extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_gaji';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'tarif_kerja_per_jam',
        'tarif_lembur_per_jam',
        'potongan_terlambat_per_menit',
        'status'
    ];

    protected $casts = [
        'tarif_kerja_per_jam' => 'integer',
        'tarif_lembur_per_jam' => 'integer',
        'potongan_terlambat_per_menit' => 'integer',
        'status' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'pengaturan_gaji_id', 'id');
    }
}