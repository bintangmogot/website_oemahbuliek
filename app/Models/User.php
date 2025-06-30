<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\UserStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

//PENTING SUPAYA MENGGUNAKAN EMAIL SEBAGAI PRIMARY KEY DAN TIDAK AUTO INCREMENT KARENA EMAIL BUKAN ANGKA. 
//LARAVEL MENGGUNAKAN ID AUTO INCREMENT SECARA DEFAULT
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';  
    protected $fillable = [
        'pengaturan_gaji_id',
        'email','password','role',
        'nama_lengkap','jabatan','tgl_masuk', 'tgl_resign','no_hp','alamat','foto_profil', 'status'
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
    * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

   /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_masuk' => 'date',
        'tgl_resign' => 'date',
        'status' => 'integer',
        'pengaturan_gaji_id' => 'integer',
    ];

        // Definisikan constants untuk setiap status
    const STATUS_RESIGNED  = 0;
    const STATUS_ACTIVE    = 1;

    // Mapping integer ke label
    const STATUS_LABELS = [
        self::STATUS_RESIGNED  => 'Resign',
        self::STATUS_ACTIVE    => 'Aktif',
    ];


    // Cek role
public static function getIsAdminAttribute()
{
    // Coba ambil dari session
    $email = Session::get('email');

    if ($email) {
        $user = DB::table('users')->where('email', $email)->first();
        return $user && $user->role === 'admin';
    }

    // Fallback ke Auth user
    if (Auth::check()) {
        return Auth::user()->role === 'admin';
    }

    // Tidak ada session atau auth
    return false;
}

    public function pengaturanGaji()
    {
        return $this->belongsTo(PengaturanGaji::class, 'pengaturan_gaji_id', 'id');
    }


    // Relasi ke semua JadwalShift yang di-assign ke user ini
    public function jadwalShifts()
    {
        return $this->hasMany(JadwalShift::class, 'users_id', 'id');
    }

    // Relasi many-to-many ke Shift melalui JadwalShift
    // User bisa memiliki banyak shift di berbagai tanggal
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'jadwal_shift', 'users_id', 'shift_id')
                    ->withPivot('tanggal', 'status')
                    ->withTimestamps();
    }

        public function presensi()
    {
        return $this->hasMany(Presensi::class, 'users_id', 'id')
        ->orderBy('tgl_presensi', 'desc');
    }

      /**
     * Satu user bisa punya banyak gaji pokok (per periode)
     */
    public function gajiPokok()
    {
        return $this->hasMany(GajiPokok::class, 'users_id')
                    ->orderBy('periode_awal', 'desc');

    }

    public function gajiLembur()
    {
        return $this->hasMany(GajiLembur::class, 'users_id');
    }

    // public function pegawaiJadwal()
    // {
    //     return $this->hasMany(PegawaiJadwal::class, 'id_users', 'id_users');
    // }

    // public function transaksi()
    // {
    //     return $this->hasMany(Transaksi::class, 'id_users', 'id_users');
    // }

    // public function produksi()
    // {
    //     return $this->hasMany(Produksi::class, 'id_users', 'id_users');
    // }

    // public function mutasiStok()
    // {
    //     return $this->hasMany(MutasiStok::class, 'id_users', 'id_users');
    // }

    // public function mutasiMenu()
    // {
    //     return $this->hasMany(MutasiMenu::class, 'id_users', 'id_users');
    // }



    // STATUS USERS

    // Accessor untuk mendapatkan label berdasarkan status

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'Unknown';
    }

    // Static helper untuk form atau validasi

  public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeResigned($query)
    {
        return $query->where('status', self::STATUS_RESIGNED);
    }

}