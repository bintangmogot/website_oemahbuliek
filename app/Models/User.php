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
    protected $table = 'users';
    protected $primaryKey = 'email';
    protected $fillable = [
        'email',
        'password',
        'role', 
    ];
    
    // Cek role
public static function getIsAdminAttribute()
{
$email = session('email');

// $email = "mantap@gmail.com";
// Mengambil data dengan kondisi tertentu (WHERE)
$users = DB::table('users')->where('email', $email)->get();
//print_r($users);  
      $nama = DB::table('pegawai')
      ->leftJoin('users', 'pegawai.id_akun', '=', 'users.email') // JOIN users ON pegawai.id_akun = users.email
      ->select('pegawai.*', 'users.*') // pilih kolom yang diinginkan
      ->where('users.email', $email) // filter berdasarkan email
      ->get();
// echo "Yang lagi login: " . $nama->nama_lengkap . "<br>";
//     // return $this->role === 'admin';
//     if ($users[0]->role == 'admin') {
//         return 'admin';
//     }
//     return 'pegawai';
    
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


        // Relasi one-to-one dengan Pegawai
    public function relationtopegawai()
    {
        return $this->hasOne(Pegawai::class, 'id_akun', 'email');
    }
}
