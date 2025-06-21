<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalShift extends Model
{
    use HasFactory;

    protected $table = 'jadwal_shift';
    protected $primaryKey = 'id';
protected $fillable = [
  'shift_id',
  'nama_periode',
  'mulai_berlaku',
  'berakhir_berlaku',
  'hari_kerja',
];

    protected $casts = [
      'mulai_berlaku'   => 'date',
      'berakhir_berlaku'=> 'date',
      'hari_kerja'      => 'string', // simpan di DB sebagai SET string
    ];

    // Accessor untuk bentuk array/hari readable
    public function getHariKerjaListAttribute()
    {
        $map = [
          'Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu',
          'Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu','Sun'=>'Minggu',
        ];
        return collect(explode(',', $this->hari_kerja))
               ->map(fn($d) => $map[$d] ?? $d)
               ->implode(', ');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function pegawaiJadwals()
    {
        return $this->hasMany(PegawaiJadwal::class, 'jadwal_shift_id');
    }
}
