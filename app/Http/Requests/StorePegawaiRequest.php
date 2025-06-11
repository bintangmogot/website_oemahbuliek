<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StorePegawaiRequest extends FormRequest
{
    public function rules()
{
    return [
        'id_akun'       => ['required','email','unique:users,email','unique:pegawai,id_akun'],
        'password' => 'required|min:6', 
        'nama_lengkap'  => ['required','string','max:255'],
        'jabatan'       => ['required','string','max:50'],
        'tgl_masuk'     => ['required','date'],
        'no_hp'         => ['required','string','max:15'],
        'alamat'        => ['nullable','string'],
    ];
    // // Tambahkan hanya saat create (bukan update)
    // if ($this->isMethod('post')) {
    //     $rules['password'] = 'required|min:6';
    // }
}

public function authorize()
{
    return true;
}
}
