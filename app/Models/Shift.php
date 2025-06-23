<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
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
        'status'
    ];

    protected $casts = [
        'toleransi_terlambat' => 'integer',
        'batas_lembur_min' => 'integer',
        'status' => 'integer',
    ];

    // relasi ke jadwal
    public function jadwals()
    {
        return $this->hasMany(JadwalShift::class, 'shift_id');
    }

  // Relasi many-to-many ke User melalui JadwalShift
    // Shift bisa digunakan oleh banyak user di berbagai tanggal
    public function users()
    {
        return $this->belongsToMany(User::class, 'jadwal_shift', 'shift_id', 'users_id')
                    ->withPivot('tanggal', 'status')
                    ->withTimestamps();
    }

// Scope untuk shift aktif
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function isActive(): bool
    {
        return $this->status === 1;
    }
}
