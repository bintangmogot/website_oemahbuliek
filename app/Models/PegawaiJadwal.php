<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiJadwal extends Model
{
    use HasFactory;

    protected $table = 'pegawai_jadwal';
    public $incrementing = false;           // karena composite PK
    protected $primaryKey = ['jadwal_shift_id','users_id'];
    protected $keyType = 'int';
    protected $fillable = ['jadwal_shift_id','users_id'];

    public function jadwalShift()
    {
        return $this->belongsTo(JadwalShift::class, 'jadwal_shift_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // non-standar: composite PK
    public function getKeyName()
    {
        return $this->primaryKey;
    }
}
