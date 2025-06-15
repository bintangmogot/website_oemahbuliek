<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'email','password','role',
        'nama_lengkap','jabatan','tgl_masuk','no_hp','alamat','foto_profil'
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
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

}


