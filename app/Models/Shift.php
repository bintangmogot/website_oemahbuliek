<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';
    protected $primaryKey = 'id';
/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
        protected $fillable = [
        'nama_shift',
        'jam_mulai',
        'jam_selesai',
        'toleransi_terlambat',
        'batas_lembur_min',
    ];

    // relasi ke jadwal
    public function jadwals()
    {
        return $this->hasMany(JadwalShift::class, 'shift_id');
    }
}
