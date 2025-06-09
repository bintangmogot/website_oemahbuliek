<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'id_akun', 'nama_lengkap', 'jabatan', 'tgl_masuk', 'no_hp', 'alamat'
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
    ];

    // Relasi dengan AkunUser
    public function relationtoUser()
    {
        return $this->belongsTo(User::class, 'id_akun', 'email');
    }

}