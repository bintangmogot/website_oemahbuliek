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
        'pengaturan_gaji_id','email','password','role',
        'nama_lengkap','jabatan','tgl_masuk','no_hp','alamat','foto_profil', 'status'
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
        'status' => 'integer',
        'pengaturan_gaji_id' => 'integer',
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
        public function presensi()
    {
        return $this->hasMany(Presensi::class, 'users_id', 'id');
    }

    // public function gajiPokok()
    // {
    //     return $this->hasMany(GajiPokok::class, 'id_users', 'id_users');
    // }

    // public function gajiLembur()
    // {
    //     return $this->hasMany(GajiLembur::class, 'id_users', 'id_users');
    // }

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


    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::ACTIVE);
    }
}


