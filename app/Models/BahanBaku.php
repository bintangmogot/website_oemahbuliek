<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama',
        'kategori',
        'satuan',
        'stok_terkini',
        'stok_minimum',
    ];

    /**
     * Mendapatkan label untuk satuan.
     * Digunakan di view untuk menampilkan 'gram' atau 'pcs'.
     */
    protected function satuanLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ((int)$this->satuan) {
                0 => 'gram',
                1 => 'pcs',
                default => 'Tidak Diketahui',
            },
        );
    }

    /**
     * Relasi ke riwayat stok. Satu bahan baku punya banyak riwayat.
     */
    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class);
    }
}