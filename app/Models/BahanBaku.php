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
     * Relasi ke riwayat stok. Satu bahan baku punya banyak riwayat.
     */
    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class);
    }

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

    protected function kategoriLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->kategori) {
                'Bahan Makanan' => 'Bahan Makanan',
                'Bumbu' => 'Bumbu',
                'Bahan Minuman' => 'Bahan Minuman',
                default => 'Tidak Diketahui',
            },
        );
    }
    
// app/Models/Produk.php

/**
 * Scope a query to only include products with a specific stock status label.
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @param string $label ('Habis', 'Hampir Habis', 'Tersedia')
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeWhereStokLabel($query, $label)
{
    if ($label === 'Habis') {
        return $query->where('stok_terkini', '=', 0);
    }

    if ($label === 'Hampir Habis') {
        // Menggunakan whereColumn untuk membandingkan dua kolom
        return $query->where('stok_terkini', '>', 0)
                     ->whereColumn('stok_terkini', '<=', 'stok_minimum');
    }

    if ($label === 'Tersedia') {
        return $query->whereColumn('stok_terkini', '>', 'stok_minimum');
    }

    return $query; // Kembalikan query tanpa perubahan jika label tidak cocok
}

}