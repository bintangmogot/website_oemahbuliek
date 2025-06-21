<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'jadwal_shift_id',
        'user_id',
        'tgl_presensi',
        'shift_ke',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran',
        'menit_terlambat',
        'menit_lembur',
        'upah_lembur',
        'potongan_terlambat',
        'is_calculated',
        'keterangan',
    ];

    // Relasi ke JadwalShift
    public function jadwal()
    {
        return $this->belongsTo(JadwalShift::class, 'jadwal_shift_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
