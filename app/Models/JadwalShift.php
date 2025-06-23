<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalShift extends Model
{
    use HasFactory;

    protected $table = 'jadwal_shift';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'shift_id',
        'users_id',
        'tanggal',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'shift_id' => 'integer',
        'users_id' => 'integer',
        'status' => 'integer',
    ];

    // Relasi ke Shift
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');    
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }



    // Scope untuk jadwal aktif
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope untuk tanggal tertentu
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('users_id', $userId);
    }

    public function isActive(): bool
    {
        return $this->status === 1;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            0 => 'Dibatalkan',
            1 => 'Aktif',
            2 => 'Selesai',
            default => 'Tidak Diketahui'
        };
    }
}