<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

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
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';  
    protected $fillable = [
        'email',
        'password',
        'role', 
    ];
    
    // Cek role
    public static function getIsAdminAttribute()
    {
    $email = session('email');

    // Mengambil data dengan kondisi tertentu (WHERE)
    $users = DB::table('users')->where('email', $email)->get();
        $nama = DB::table('pegawai')
        ->leftJoin('users', 'pegawai.id_akun', '=', 'users.email') // JOIN users ON pegawai.id_akun = users.email
        ->select('pegawai.*', 'users.*') // pilih kolom yang diinginkan
        ->where('users.email', $email) // filter berdasarkan email
        ->get();

    }

        // Relasi one-to-one dengan Pegawai
    public function relationtopegawai()
    {
        return $this->hasOne(Pegawai::class, 'id_akun', 'email');
    }


// public function isPegawai()
//     {
//          return 'pegawai';
//     }


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


