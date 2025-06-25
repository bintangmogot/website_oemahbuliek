<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GajiLembur extends Model
{
    use HasFactory;

    protected $table = 'gaji_lembur';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id',
        'pengaturan_gaji_id',
        'presensi_id',
        'tgl_lembur',
        'total_jam_lembur',
        'total_gaji_lembur',
        'tgl_bayar',
        'status_pembayaran'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tgl_lembur' => 'date',
        'tgl_bayar' => 'date',
        'total_jam_lembur' => 'decimal:2',
        'total_gaji_lembur' => 'integer',
        'status_pembayaran' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constants untuk status pembayaran
    const STATUS_PEMBAYARAN_UNPAID = 0;
    const STATUS_PEMBAYARAN_PAID = 1;
    const STATUS_PEMBAYARAN_PARTIAL = 2;

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relasi ke PengaturanGaji
     */
    public function pengaturanGaji()
    {
        return $this->belongsTo(PengaturanGaji::class, 'pengaturan_gaji_id');
    }

    /**
     * Relasi ke Presensi
     */
    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    /**
     * Scope untuk filter berdasarkan status pembayaran
     */
    public function scopeByStatusPembayaran($query, $status)
    {
        return $query->where('status_pembayaran', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal lembur
     */
    public function scopeByTanggalLembur($query, $tanggal)
    {
        return $query->whereDate('tgl_lembur', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan bulan dan tahun
     */
    public function scopeByBulanTahun($query, $bulan, $tahun)
    {
        return $query->whereMonth('tgl_lembur', $bulan)
                    ->whereYear('tgl_lembur', $tahun);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('users_id', $userId);
    }

    /**
     * Scope untuk gaji lembur yang belum dibayar
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status_pembayaran', self::STATUS_PEMBAYARAN_UNPAID);
    }

    /**
     * Scope untuk gaji lembur yang sudah dibayar
     */
    public function scopePaid($query)
    {
        return $query->where('status_pembayaran', self::STATUS_PEMBAYARAN_PAID);
    }

    /**
     * Get status pembayaran label
     */
    public function getStatusPembayaranLabelAttribute()
    {
        switch ($this->status_pembayaran) {
            case self::STATUS_PEMBAYARAN_UNPAID:
                return 'Belum Dibayar';
            case self::STATUS_PEMBAYARAN_PAID:
                return 'Sudah Dibayar';
            case self::STATUS_PEMBAYARAN_PARTIAL:
                return 'Dibayar Sebagian';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get status pembayaran badge class untuk UI
     */
    public function getStatusPembayaranBadgeAttribute()
    {
        switch ($this->status_pembayaran) {
            case self::STATUS_PEMBAYARAN_UNPAID:
                return 'badge-danger';
            case self::STATUS_PEMBAYARAN_PAID:
                return 'badge-success';
            case self::STATUS_PEMBAYARAN_PARTIAL:
                return 'badge-warning';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Format total jam lembur
     */
    public function getFormattedTotalJamLemburAttribute()
    {
        $jam = floor($this->total_jam_lembur);
        $menit = ($this->total_jam_lembur - $jam) * 60;
        
        if ($menit > 0) {
            return "{$jam} jam {$menit} menit";
        }
        
        return "{$jam} jam";
    }

    /**
     * Format total gaji lembur dalam rupiah
     */
    public function getFormattedTotalGajiLemburAttribute()
    {
        return 'Rp ' . number_format($this->total_gaji_lembur, 0, ',', '.');
    }

    /**
     * Check apakah sudah dibayar
     */
    public function isPaid()
    {
        return $this->status_pembayaran === self::STATUS_PEMBAYARAN_PAID;
    }

    /**
     * Check apakah belum dibayar
     */
    public function isUnpaid()
    {
        return $this->status_pembayaran === self::STATUS_PEMBAYARAN_UNPAID;
    }

    /**
     * Check apakah dibayar sebagian
     */
    public function isPartial()
    {
        return $this->status_pembayaran === self::STATUS_PEMBAYARAN_PARTIAL;
    }

    /**
     * Hitung total gaji lembur berdasarkan pengaturan gaji
     */
    public function hitungTotalGajiLembur()
    {
        if ($this->pengaturanGaji && $this->pengaturanGaji->gaji_lembur_per_jam > 0) {
            $this->total_gaji_lembur = $this->total_jam_lembur * $this->pengaturanGaji->gaji_lembur_per_jam;
            $this->save();
            return $this->total_gaji_lembur;
        }
        
        return 0;
    }

    /**
     * Mark sebagai sudah dibayar
     */
    public function markAsPaid($tanggalBayar = null)
    {
        $this->update([
            'status_pembayaran' => self::STATUS_PEMBAYARAN_PAID,
            'tgl_bayar' => $tanggalBayar ?? Carbon::now()->toDateString()
        ]);
    }

    /**
     * Mark sebagai belum dibayar
     */
    public function markAsUnpaid()
    {
        $this->update([
            'status_pembayaran' => self::STATUS_PEMBAYARAN_UNPAID,
            'tgl_bayar' => null
        ]);
    }

    /**
     * Static method untuk mendapatkan total gaji lembur berdasarkan user dan periode
     */
    public static function getTotalGajiLemburByUserAndPeriod($userId, $startDate, $endDate)
    {
        return self::where('users_id', $userId)
                  ->whereBetween('tgl_lembur', [$startDate, $endDate])
                  ->sum('total_gaji_lembur');
    }

    /**
     * Static method untuk mendapatkan total jam lembur berdasarkan user dan periode
     */
    public static function getTotalJamLemburByUserAndPeriod($userId, $startDate, $endDate)
    {
        return self::where('users_id', $userId)
                  ->whereBetween('tgl_lembur', [$startDate, $endDate])
                  ->sum('total_jam_lembur');
    }

    /**
     * Boot method untuk event handling
     */
    protected static function boot()
    {
        parent::boot();

        // Event ketika membuat record baru
        static::creating(function ($gajiLembur) {
            // Auto-hitung total gaji lembur jika belum diset
            if ($gajiLembur->total_gaji_lembur == 0 && $gajiLembur->pengaturanGaji) {
                $gajiLembur->total_gaji_lembur = $gajiLembur->total_jam_lembur * $gajiLembur->pengaturanGaji->gaji_lembur_per_jam;
            }
        });

        // Event ketika mengupdate record
        static::updating(function ($gajiLembur) {
            // Auto-hitung ulang jika jam lembur berubah
            if ($gajiLembur->isDirty('total_jam_lembur') && $gajiLembur->pengaturanGaji) {
                $gajiLembur->total_gaji_lembur = $gajiLembur->total_jam_lembur * $gajiLembur->pengaturanGaji->gaji_lembur_per_jam;
            }
        });
    }
}