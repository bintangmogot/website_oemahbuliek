<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GajiPokok extends Model
{
    use HasFactory;

    protected $table = 'gaji_pokok';

    protected $fillable = [
        'users_id',
        'periode_bulan', 'periode_start', 'periode_end',
        'tarif_per_jam', 'tarif_potongan_per_menit',
        'jumlah_jam_kerja',
        'jumlah_menit_terlambat',
        'gaji_kotor',
        'total_potongan',
        'total_gaji_pokok',
        'status_pembayaran',
        'tgl_bayar',
    ];

    protected $casts = [
        'periode_bulan' => 'date',
        'periode_start' => 'date',
        'periode_end' => 'date',
        'tgl_bayar' => 'date',
        'tarif_per_jam' => 'integer',
        'tarif_potongan_per_menit' => 'integer',
        'jumlah_jam_kerja' => 'integer',
        'jumlah_menit_terlambat' => 'integer',
        'gaji_kotor' => 'integer',
        'total_gaji_pokok' => 'integer',
        'total_potongan' => 'integer',
        'status_pembayaran' => 'integer',
    ];

    // Constants untuk status pembayaran
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;

    const STATUS_PEMBAYARAN_LABELS = [
        self::STATUS_UNPAID => 'Belum Dibayar',
        self::STATUS_PAID => 'Sudah Dibayar',
    ];

    /**
     * Relasi ke tabel users
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }


    /**
     * Relasi ke tabel presensi
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'gaji_pokok_id')
        ->orderBy('tgl_presensi', 'desc');
    }

    /**
     * Relasi ke presensi dalam periode ini saja (TAMBAHAN BARU)
     */
    public function presensiPeriodeIni(): HasMany
    {
        return $this->hasMany(Presensi::class, 'gaji_pokok_id')
                    ->whereBetween('tgl_presensi', [$this->periode_awal, $this->periode_akhir])
                    ->orderBy('tgl_presensi', 'desc');
    }

    /**
     * Relasi ke presensi yang terlambat dalam periode ini (TAMBAHAN BARU)
     */
    public function presensiTerlambat(): HasMany
    {
        return $this->hasMany(Presensi::class, 'gaji_pokok_id')
                    ->whereBetween('tgl_presensi', [$this->periode_awal, $this->periode_akhir])
                    ->where('status_kehadiran', Presensi::STATUS_KEHADIRAN_LATE)
                    ->orderBy('tgl_presensi', 'desc');
    }

    /**
     * Relasi ke presensi yang hadir normal dalam periode ini 
     */
    public function presensiHadir(): HasMany
    {
        return $this->hasMany(Presensi::class, 'gaji_pokok_id')
                    ->whereBetween('tgl_presensi', [$this->periode_awal, $this->periode_akhir])
                    ->where('status_kehadiran', Presensi::STATUS_KEHADIRAN_PRESENT)
                    ->orderBy('tgl_presensi', 'desc');
    }


    /**
     * Accessor untuk label status pembayaran
     */
    public function getStatusPembayaranLabelAttribute(): string
    {
        return self::STATUS_PEMBAYARAN_LABELS[$this->status_pembayaran] ?? 'Unknown';
    }

    /**
     * Accessor untuk total gaji setelah potongan
     */
    public function getTotalGajiBersihAttribute(): int
    {
        return $this->total_gaji - $this->total_potongan_terlambat;
    }

    /**
     * Scope untuk filter berdasarkan status pembayaran
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pembayaran', $status);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('users_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriode($query, $periodeAwal, $periodeAkhir = null)
    {
        $query->where('periode_awal', '>=', $periodeAwal);
        
        if ($periodeAkhir) {
            $query->where('periode_akhir', '<=', $periodeAkhir);
        }

        return $query;
    }

    /**
     * Scope untuk gaji yang belum dibayar
     */
    public function scopeBelumDibayar($query)
    {
        return $query->where('status_pembayaran', self::STATUS_UNPAID);
    }

    /**
     * Scope untuk gaji yang sudah dibayar
     */
    public function scopeSudahDibayar($query)
    {
        return $query->where('status_pembayaran', self::STATUS_PAID);
    }

    /**
     * Method untuk menandai gaji sebagai sudah dibayar
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status_pembayaran' => self::STATUS_PAID,
            'tgl_bayar' => now()->toDateString(),
        ]);
    }


    /**
     * Method untuk menghitung gaji pokok berdasarkan jam kerja dan tarif
     */
    public function hitungGajiPokok(int $tarifPerJam): void
    {
        $this->total_gaji = $this->jumlah_jam_kerja * $tarifPerJam;
        $this->save();
    }

    /**
     * Method untuk menambah potongan terlambat
     */
    public function tambahPotonganTerlambat(int $jumlahPotongan): void
    {
        $this->total_potongan_terlambat += $jumlahPotongan;
        $this->save();
    }
}