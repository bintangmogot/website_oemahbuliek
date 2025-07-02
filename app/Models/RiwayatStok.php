<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stok';

    protected $fillable = [
        'bahan_baku_id',
        'user_id',
        'tanggal',
        'tipe_mutasi',
        'kuantitas',
        'harga_satuan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    /**
     * Relasi ke bahan baku. Satu riwayat milik satu bahan baku.
     */
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    /**
     * Relasi ke user. Satu riwayat dicatat oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}