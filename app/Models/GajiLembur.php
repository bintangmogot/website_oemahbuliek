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
        'presensi_id',
        'tgl_lembur',
        'total_jam_lembur',
        'total_gaji_lembur',
        'tipe_lembur',
        'rate_lembur_per_jam', 
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
        'rate_lembur_per_jam' => 'integer',
        'status_pembayaran' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constants untuk status pembayaran
    const STATUS_PEMBAYARAN_UNPAID = 0;
    const STATUS_PEMBAYARAN_PAID = 1;
    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relasi ke Presensi
     */
    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    public function shift()
{
    return $this->hasOneThrough(
        Shift::class,
        Presensi::class,
        'id', // Foreign key pada presensi table
        'id', // Foreign key pada shifts table  
        'presensi_id', // Local key pada gaji_lembur table
        'jadwal_shift_id' // Local key pada presensi table
    )->join('jadwal_shift', 'presensi.jadwal_shift_id', '=', 'jadwal_shift.id')
     ->where('jadwal_shift.shift_id', '=', 'shifts.id')
     ->select('shifts.*');
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


    // Method untuk mendapatkan nama shift dengan fallback
public function getNamaShiftAttribute()
{
    if (!$this->presensi) {
        $this->load('presensi');
    }
    
    if (!$this->presensi) {
        return null;
    }
    
    if (!$this->presensi->relationLoaded('jadwalShift')) {
        $this->presensi->load('jadwalShift.shift');
    }
    
    return $this->presensi->jadwalShift && 
           $this->presensi->jadwalShift->shift 
           ? $this->presensi->jadwalShift->shift->nama_shift 
           : null;
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
                return 'bg-danger';
            case self::STATUS_PEMBAYARAN_PAID:
                return 'bg-success';
            default:
                return 'bg-secondary';
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
     * Format rate lembur per jam dalam rupiah
     */
    public function getFormattedRateLemburPerJamAttribute()
    {
        return 'Rp ' . number_format($this->rate_lembur_per_jam, 0, ',', '.');
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
     * Hitung total gaji lembur berdasarkan rate yang tersimpan
     */
    public function hitungTotalGajiLembur()
    {
        if ($this->rate_lembur_per_jam > 0) {
            $this->total_gaji_lembur = $this->total_jam_lembur * $this->rate_lembur_per_jam;
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
        if (empty($gajiLembur->rate_lembur_per_jam)) {
            $user = User::with('pengaturanGaji')->find($gajiLembur->users_id);
            if ($user && $user->pengaturanGaji) {
                $gajiLembur->rate_lembur_per_jam = $user->pengaturanGaji->tarif_lembur_per_jam;
            } else {
                // Default rate berdasarkan tipe shift
                $presensi = Presensi::with('jadwalShift.shift')->find($gajiLembur->presensi_id);
                if ($presensi && $presensi->jadwalShift && $presensi->jadwalShift->shift) {
                    $shift = $presensi->jadwalShift->shift;
                    if ($shift->is_shift_lembur == 1) {
                        // Rate untuk shift lembur bisa berbeda
                        $gajiLembur->rate_lembur_per_jam = $shift->tarif_shift_lembur ?? 75000; // Default rate shift lembur
                    } else {
                        // Rate untuk overtime normal
                        $gajiLembur->rate_lembur_per_jam = 50000; // Default rate overtime
                    }
                } else {
                    $gajiLembur->rate_lembur_per_jam = 50000; // Fallback default
                }
            }
        }
            
            // Auto-hitung total gaji lembur
            if ($gajiLembur->rate_lembur_per_jam > 0) {
                $gajiLembur->total_gaji_lembur = $gajiLembur->total_jam_lembur * $gajiLembur->rate_lembur_per_jam;
            }
        });

        // Event ketika mengupdate record
        static::updating(function ($gajiLembur) {
            // Auto-hitung ulang jika jam lembur berubah, tapi rate tidak berubah
            if ($gajiLembur->isDirty('total_jam_lembur') && $gajiLembur->rate_lembur_per_jam > 0) {
                $gajiLembur->total_gaji_lembur = $gajiLembur->total_jam_lembur * $gajiLembur->rate_lembur_per_jam;
            }
        });
    }

// Di Model GajiLembur
public function getJamMulaiAttribute()
{
    // Jika sudah ada nilai di database
    if (!empty($this->attributes['jam_mulai'])) {
        return $this->attributes['jam_mulai'];
    }

    // Convert tanggal ke format string
    $tanggalLembur = $this->tgl_lembur instanceof Carbon 
                     ? $this->tgl_lembur->format('Y-m-d') 
                     : $this->tgl_lembur;

    $presensi = Presensi::where('users_id', $this->users_id)
                       ->whereDate('tgl_presensi', $tanggalLembur)
                       ->whereIn('status_lembur', [1, 2])
                       ->first();

    return $presensi && $presensi->jam_masuk ? 
           Carbon::parse($presensi->jam_masuk)->format('H:i') : null;
}

public function getJamSelesaiAttribute()
{
    // Jika sudah ada nilai di database
    if (!empty($this->attributes['jam_selesai'])) {
        return $this->attributes['jam_selesai'];
    }

    // Convert tanggal ke format string
    $tanggalLembur = $this->tgl_lembur instanceof Carbon 
                     ? $this->tgl_lembur->format('Y-m-d') 
                     : $this->tgl_lembur;

    $presensi = Presensi::where('users_id', $this->users_id)
                       ->whereDate('tgl_presensi', $tanggalLembur)
                       ->whereIn('status_lembur', [1, 2])
                       ->first();

    return $presensi && $presensi->jam_keluar ? 
           Carbon::parse($presensi->jam_keluar)->format('H:i') : null;
}


// Tambahkan method ini di Model GajiLembur sebelum boot() method

/**
 * Check apakah ini shift lembur
 */
public function isShiftLembur()
{
    return $this->tipe_lembur === 'shift_lembur';
}

/**
 * Get tipe lembur (shift lembur atau overtime)
 */
// public function getTipeLemburAttribute()
// {
//     // Coba akses langsung melalui relasi yang sudah di-load
//     if ($this->presensi && 
//         $this->presensi->jadwalShift && 
//         $this->presensi->jadwalShift->shift) {
//         return $this->presensi->jadwalShift->shift->is_shift_lembur == 1 ? 'Shift Lembur' : 'Overtime';
//     }
    
//     // Fallback ke method isShiftLembur jika relasi belum di-load
//     return $this->isShiftLembur() ? 'Shift Lembur' : 'Overtime';
// }

/**
 * Get label untuk tipe lembur dengan badge
 */
public function getTipeLemburBadgeAttribute()
{
    return $this->tipe_lembur === 'Shift Lembur' ? 'bg-info' : 'bg-warning';
}

public function getTipeLemburLabelAttribute()
{
    return $this->isShiftLembur() ? 'Shift Lembur' : 'Overtime';
}
/**
 * Get keterangan lengkap tentang perhitungan lembur
 */
public function getKeteranganLemburAttribute()
{
    if ($this->isShiftLembur()) {
        return "Shift Lembur - Seluruh {$this->formatted_total_jam_lembur} jam kerja dihitung sebagai lembur";
    } else {
        return "Overtime - Lembur {$this->formatted_total_jam_lembur} jam setelah jam kerja normal";
    }
}

/**
 * Scope untuk filter berdasarkan tipe shift
 */
public function scopeByTipeShift($query, $isShiftLembur = true)
{
    return $query->whereHas('presensi.jadwalShift.shift', function($q) use ($isShiftLembur) {
        $q->where('is_shift_lembur', $isShiftLembur ? 1 : 0);
    });
}

/**
 * Static method untuk create gaji lembur berdasarkan tipe shift
 */
public static function createFromPresensi(Presensi $presensi)
{
    // Pastikan relasi yang diperlukan sudah di-load
    $presensi->load(['jadwalShift.shift', 'user.pengaturanGaji']);

    // Jika presensi tidak memiliki jadwalShift atau shift, batalkan
    if (!$presensi->jadwalShift || !$presensi->jadwalShift->shift) {
        \Illuminate\Support\Facades\Log::warning("Presensi ID {$presensi->id} tidak memiliki jadwal shift yang valid");
        return null;
    }

    $shift = $presensi->jadwalShift->shift;
    $statusLembur = $presensi->status_lembur;
    
    // Logika penentuan apakah perlu dibuat record gaji lembur
    $shouldCreateRecord = false;
    $tipe = null;
    $jamLembur = 0;

    if ($shift->is_shift_lembur == 1) {
        // Shift Lembur: semua status lembur 1, 2, 3 akan dibuat record
        if (in_array($statusLembur, [1, 2, 3])) {
            $shouldCreateRecord = true;
            $tipe = 'shift_lembur';
            // Seluruh jam kerja efektif dihitung sebagai lembur
            $menitKerjaEfektif = $presensi->calculateEffectiveWorkHours(); // menit
            $jamLembur = round($menitKerjaEfektif / 60, 2);
        }
    } else {
        // Shift Normal: hanya status lembur 1 dan 2 (overtime) yang dibuat record
        if (in_array($statusLembur, [1, 2])) {
            $shouldCreateRecord = true;
            $tipe = 'overtime';
            // Hitung overtime normal: setelah jam_selesai + batas minimum
            $jamLembur = round($presensi->calculateOvertime() / 60, 2);
            
            // Jika tidak ada jam overtime, batalkan
            if ($jamLembur <= 0) {
                $shouldCreateRecord = false;
            }
        }
    }

    // Jika tidak perlu dibuat record, return null
    if (!$shouldCreateRecord) {
        return null;
    }

    // Buat atau perbarui record gaji_lembur
    $gajiLembur = self::updateOrCreate(
        [
            'presensi_id' => $presensi->id,
            'users_id'    => $presensi->users_id,
            'tgl_lembur'  => $presensi->tgl_presensi,
        ],
        [
            'total_jam_lembur'   => $jamLembur,
            'tipe_lembur'        => $tipe,
            'status_pembayaran'  => self::STATUS_PEMBAYARAN_UNPAID,
            'keterangan_lembur'  => $tipe === 'shift_lembur'
                ? "Shift Lembur – seluruh {$jamLembur} jam kerja efektif"
                : "Overtime – {$jamLembur} jam setelah jam kerja normal",
            // total_gaji_lembur dan rate akan di-handle di boot creating callback
        ]
    );

    \Illuminate\Support\Facades\Log::info("Gaji Lembur Created/Updated – ID: {$gajiLembur->id}, Jam: {$jamLembur}, Tipe: {$tipe}, Status Lembur: {$statusLembur}");

    return $gajiLembur;
}

/**
 * Scope untuk filter shift lembur saja
 */
public function scopeShiftLembur($query)
{
    return $query->where('tipe_lembur', 'shift_lembur');
}

/**
 * Scope untuk filter overtime saja
 */
public function scopeOvertime($query)
{
    return $query->where('tipe_lembur', 'overtime');
}

// Perbaikan 2: Scope untuk menampilkan SEMUA data lembur (shift lembur + overtime)
public function scopeSemuaLembur($query)
{
    return $query->where(function($q) {
        // Include shift lembur
        $q->where('tipe_lembur', 'shift_lembur')
          ->orWhereHas('presensi.jadwalShift.shift', function($shiftQuery) {
              $shiftQuery->where('is_shift_lembur', 1);
          })
          // Include overtime dari shift normal
          ->orWhere(function($overtimeQuery) {
              $overtimeQuery->where('tipe_lembur', 'overtime')
                           ->where('total_jam_lembur', '>', 0);
          })
          // Include overtime yang belum ada tipe_lembur tapi ada jam lembur
          ->orWhere(function($fallbackQuery) {
              $fallbackQuery->whereNull('tipe_lembur')
                           ->where('total_jam_lembur', '>', 0);
          });
    });
}

// Method untuk sinkronisasi tipe_lembur berdasarkan shift
public function syncTipeLembur()
{
    $tipeLembur = $this->isShiftLembur() ? 'shift_lembur' : 'overtime';
    
    if ($this->tipe_lembur !== $tipeLembur) {
        $this->update(['tipe_lembur' => $tipeLembur]);
    }
    
    return $tipeLembur;
}


/**
 * Accessor untuk tipe lembur
 */
public function getTipeLemburAttribute($value)
{
    // Jika sudah ada value di database, gunakan itu
    if (!empty($value)) {
        return $value === 'shift_lembur' ? 'Shift Lembur' : 'Overtime';
    }
    
    // Fallback: cek dari presensi jika tipe_lembur kosong
    if ($this->presensi) {
        if (!$this->presensi->relationLoaded('jadwalShift')) {
            $this->presensi->load('jadwalShift.shift');
        }
        
        $shift = $this->presensi->jadwalShift->shift ?? null;
        if ($shift && $shift->is_shift_lembur == 1) {
            return 'Shift Lembur';
        }
    }
    
    return 'Overtime'; // default
}

/**
 * Get raw tipe lembur value
 */
public function getTipeLemburRawAttribute()
{
    if (!empty($this->attributes['tipe_lembur'])) {
        return $this->attributes['tipe_lembur'];
    }
    
    // Fallback: cek dari presensi
    if ($this->presensi) {
        if (!$this->presensi->relationLoaded('jadwalShift')) {
            $this->presensi->load('jadwalShift.shift');
        }
        
        $shift = $this->presensi->jadwalShift->shift ?? null;
        return ($shift && $shift->is_shift_lembur == 1) ? 'shift_lembur' : 'overtime';
    }
    
    return 'overtime';
}

}