<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'gaji_pokok_id',
        'gaji_lembur_id',
        'users_id',
        'jadwal_shift_id',
        'tgl_presensi',
        'jam_masuk',
        'foto_masuk',
        'jam_keluar',
        'foto_keluar',
        'menit_terlambat',
        'status_kehadiran',
        'status_lembur',
        'status_approval',
        'catatan_admin'
    ];

    protected $casts = [
        'tgl_presensi' => 'date',
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
        'menit_terlambat' => 'integer',
        'status_kehadiran' => 'integer',
        'status_lembur' => 'integer',
        'status_approval' => 'integer',
    ];

    // Constants untuk status
    const STATUS_KEHADIRAN_ABSENT = 0;
    const STATUS_KEHADIRAN_PRESENT = 1;
    const STATUS_KEHADIRAN_LATE = 2;
    const STATUS_KEHADIRAN_HALF_DAY = 3;

    const STATUS_LEMBUR_NO = 0;
    const STATUS_LEMBUR_OVERTIME = 1;
    const STATUS_LEMBUR_APPROVED = 2;
    public const STATUS_LEMBUR_SHIFT = 3;     // Untuk shift lembur
    const STATUS_LEMBUR_SHIFT_OVERTIME = 4; // Shift lembur yang memiliki jam overtime


    const STATUS_APPROVAL_PENDING = 0;
    const STATUS_APPROVAL_APPROVED = 1;
    const STATUS_APPROVAL_REJECTED = 2;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Set timezone default untuk model ini
        Carbon::setLocale('id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // Relasi ke JadwalShift
    public function jadwalShift()
    {
        return $this->belongsTo(JadwalShift::class, 'jadwal_shift_id');
    }
    
    public function gajiPokok()
    {
        return $this->belongsTo(GajiPokok::class, 'gaji_pokok_id');
    }

    //   Relasi ke GajiLembur (one-to-one)
    public function gajiLembur()
    {
        return $this->hasOne(GajiLembur::class, 'presensi_id');
    }

// Method untuk menghitung jam kerja efektif (check in sampai check out dalam batas shift)
public function calculateEffectiveWorkHours(): int
    {
        // Gunakan jam keluar dari properti model, yang sudah di-set dari controller.
        if (!$this->jam_masuk || !$this->jam_keluar || !$this->jadwalShift || !$this->jadwalShift->shift) {
            return 0;
        }

        $shift = $this->jadwalShift->shift;
        $jamMasukPegawai = Carbon::parse($this->jam_masuk);
        $jamKeluarPegawai = Carbon::parse($this->jam_keluar);
        
        // PERBAIKAN ERROR: Ambil hanya bagian tanggal dari tgl_presensi untuk menghindari error parsing.
        $tanggalPresensi = Carbon::parse($this->tgl_presensi)->toDateString();
        
        // Jam shift
        $jamMulaiShift = Carbon::parse($tanggalPresensi . ' ' . $shift->jam_mulai);
        $jamSelesaiShift = Carbon::parse($tanggalPresensi . ' ' . $shift->jam_selesai);
        
        // Jika shift melewati tengah malam (misal: 22:00 - 06:00)
        if ($jamSelesaiShift->lessThan($jamMulaiShift)) {
            // Jika jam keluar pegawai berada di hari yang sama dengan jam masuk (artinya belum lewat tengah malam),
            // maka jam selesai shift harus di hari berikutnya.
            if ($jamKeluarPegawai->isSameDay($jamMasukPegawai)) {
                 $jamSelesaiShift->addDay();
            }
        }
        
        // Batas jam kerja untuk gaji pokok:
        // - Mulai: tidak boleh sebelum jam mulai shift
        // - Selesai: tidak boleh melebihi jam selesai shift
        $jamMulaiEfektif = $jamMasukPegawai->max($jamMulaiShift);
        $jamSelesaiEfektif = $jamKeluarPegawai->min($jamSelesaiShift);
        
        // Hitung jam kerja dalam batas shift
        if ($jamSelesaiEfektif->greaterThan($jamMulaiEfektif)) {
            return $jamSelesaiEfektif->diffInMinutes($jamMulaiEfektif);
        }
        
        return 0;
    }

    // Accessor untuk mengconvert datetime ke timezone Indonesia
    public function getJamMasukAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Asia/Jakarta') : null;
    }

    public function getJamKeluarAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Asia/Jakarta') : null;
    }

    // Scope untuk presensi hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_presensi', Carbon::now('Asia/Jakarta')->toDateString());
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('users_id', $userId);
    }

    // Scope untuk pending approval
    public function scopePendingApproval($query)
    {
        return $query->where('status_approval', self::STATUS_APPROVAL_PENDING);
    }

    // Method untuk mengecek apakah sudah check in
    public function isCheckedIn(): bool
    {
        return !is_null($this->jam_masuk);
    }

    // Method untuk mengecek apakah sudah check out
    public function isCheckedOut(): bool
    {
        return !is_null($this->jam_keluar);
    }

    // Method untuk mengecek apakah bisa check in (sesuai jadwal)
    public function canCheckIn(): bool
    {
        if (!$this->jadwalShift) {
            return false;
        }

        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $jadwalTanggal = Carbon::parse($this->jadwalShift->tanggal)->startOfDay();
        
        // Hanya bisa check in pada hari H
        return $today->isSameDay($jadwalTanggal) && !$this->isCheckedIn();
    }

    // Method untuk mengecek apakah bisa check out
    public function canCheckOut(): bool
    {
        return $this->isCheckedIn() && !$this->isCheckedOut();
    }

    // Method untuk menghitung keterlambatan
    public function calculateLateness(): int
    {
        if (!$this->jam_masuk || !$this->jadwalShift || !$this->jadwalShift->shift) {
            return 0;
        }

        $jamMasukShift = Carbon::createFromFormat('H:i:s', $this->jadwalShift->shift->jam_mulai, 'Asia/Jakarta');
        $jamMasukActual = Carbon::parse($this->jam_masuk)->setTimezone('Asia/Jakarta');
        
        // Set tanggal yang sama untuk perbandingan
        $jamMasukShift->setDate($jamMasukActual->year, $jamMasukActual->month, $jamMasukActual->day);
        
        if ($jamMasukActual->greaterThan($jamMasukShift)) {
            return $jamMasukActual->diffInMinutes($jamMasukShift);
        }
        
        return 0;
    }

    // Method untuk menghitung durasi kerja
    public function calculateWorkDuration(): int
    {
        if (!$this->jam_masuk || !$this->jam_keluar) {
            return 0;
        }

        $jamMasuk = Carbon::parse($this->jam_masuk)->setTimezone('Asia/Jakarta');
        $jamKeluar = Carbon::parse($this->jam_keluar)->setTimezone('Asia/Jakarta');
        
        return $jamKeluar->diffInMinutes($jamMasuk);
    }

    // Method untuk mengecek apakah pulang lebih awal
    public function isEarlyCheckout(): bool
    {
        if (!$this->jam_keluar || !$this->jadwalShift || !$this->jadwalShift->shift) {
            return false;
        }

        $jamSelesaiShift = Carbon::createFromFormat('H:i:s', $this->jadwalShift->shift->jam_selesai, 'Asia/Jakarta');
        $jamKeluarActual = Carbon::parse($this->jam_keluar)->setTimezone('Asia/Jakarta');
        
        // Set tanggal yang sama untuk perbandingan
        $jamSelesaiShift->setDate($jamKeluarActual->year, $jamKeluarActual->month, $jamKeluarActual->day);
        
        return $jamKeluarActual->lessThan($jamSelesaiShift);
    }

// Method untuk menghitung overtime dengan batas minimum
public function calculateOvertime(): int
{
    if (!$this->jam_keluar || !$this->jadwalShift || !$this->jadwalShift->shift) {
        return 0;
    }

    $jamSelesaiShift = Carbon::createFromFormat('H:i:s', $this->jadwalShift->shift->jam_selesai, 'Asia/Jakarta');
    $jamKeluarActual = Carbon::parse($this->jam_keluar)->setTimezone('Asia/Jakarta');
    
    // Set tanggal yang sama untuk perbandingan
    $jamSelesaiShift->setDate($jamKeluarActual->year, $jamKeluarActual->month, $jamKeluarActual->day);
    
    if ($jamKeluarActual->greaterThan($jamSelesaiShift)) {
        $totalMenitOver = $jamKeluarActual->diffInMinutes($jamSelesaiShift);
        $batasMinimumLembur = $this->jadwalShift->shift->batas_lembur_min ?? 0;
        
        // Jika waktu over kurang dari batas minimum, tidak dihitung lembur
        if ($totalMenitOver < $batasMinimumLembur) {
            return 0;
        }
        
        // Kembalikan waktu lembur yang sudah dikurangi batas minimum
        return $totalMenitOver - $batasMinimumLembur;
    }
    
    return 0;
}

// Method tambahan untuk mendapatkan total waktu over (sebelum dikurangi batas minimum)
public function getTotalOvertimeMinutes(): int
{
    if (!$this->jam_keluar || !$this->jadwalShift || !$this->jadwalShift->shift) {
        return 0;
    }

    $jamSelesaiShift = Carbon::createFromFormat('H:i:s', $this->jadwalShift->shift->jam_selesai, 'Asia/Jakarta');
    $jamKeluarActual = Carbon::parse($this->jam_keluar)->setTimezone('Asia/Jakarta');
    
    // Set tanggal yang sama untuk perbandingan
    $jamSelesaiShift->setDate($jamKeluarActual->year, $jamKeluarActual->month, $jamKeluarActual->day);
    
    if ($jamKeluarActual->greaterThan($jamSelesaiShift)) {
        return $jamKeluarActual->diffInMinutes($jamSelesaiShift);
    }
    
    return 0;
}

    // Method untuk mendapatkan label status kehadiran
    public function getStatusKehadiranLabelAttribute(): string
    {
        return match($this->status_kehadiran) {
            self::STATUS_KEHADIRAN_ABSENT => 'Tidak Hadir',
            self::STATUS_KEHADIRAN_PRESENT => 'Hadir',
            self::STATUS_KEHADIRAN_LATE => 'Terlambat',
            self::STATUS_KEHADIRAN_HALF_DAY => 'Pulang Lebih Awal',
            default => 'Tidak Diketahui'
        };
    }

    // Method untuk mendapatkan label status lembur
    public function getStatusLemburLabelAttribute()
    {
        switch ($this->status_lembur) {
            case self::STATUS_LEMBUR_NO: return 'Tidak Lembur';
            case self::STATUS_LEMBUR_OVERTIME: return 'Overtime';
            case self::STATUS_LEMBUR_APPROVED: return 'Lembur Disetujui';
            case self::STATUS_LEMBUR_SHIFT: return 'Shift Lembur';
            case self::STATUS_LEMBUR_SHIFT_OVERTIME: return 'Shift Lembur + Overtime';
            default: return null;
        }
    }
    

    public function getStatusLemburBadgeAttribute(): string
{
    return match($this->status_lembur) {
        self::STATUS_LEMBUR_NO       => 'bg-secondary',
        self::STATUS_LEMBUR_OVERTIME => 'bg-warning',
        self::STATUS_LEMBUR_APPROVED => 'bg-success',
        self::STATUS_LEMBUR_SHIFT    => 'bg-info',
        default                      => 'bg-secondary',
    };
}

    // Method untuk mendapatkan label status approval
    public function getStatusApprovalLabelAttribute(): string
    {
        return match($this->status_approval) {
            self::STATUS_APPROVAL_PENDING => 'Menunggu Persetujuan',
            self::STATUS_APPROVAL_APPROVED => 'Disetujui',
            self::STATUS_APPROVAL_REJECTED => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    // Method untuk mendapatkan warna badge status
    public function getStatusApprovalColorAttribute(): string
    {
        return match($this->status_approval) {
            self::STATUS_APPROVAL_PENDING => 'warning',
            self::STATUS_APPROVAL_APPROVED => 'success',
            self::STATUS_APPROVAL_REJECTED => 'danger',
            default => 'secondary'
        };
    }



// UNTUK GAJI POKOK
// Di Model Presensi
public function getJamKerjaEfektifAttribute(): int
{
    return $this->calculateEffectiveWorkHours();
}

public function getJamKerjaEfektifFormattedAttribute(): string
{
    $durasi = $this->calculateEffectiveWorkHours();
    $jam = floor($durasi / 60);
    $menit = $durasi % 60;
    return "{$jam}j {$menit}m";
}

// Accessor untuk menit_terlambat
public function getMinuteTerlambatAttribute()
{
    return $this->calculateLateness();
}
}