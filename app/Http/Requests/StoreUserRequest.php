<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize() { return auth()->user()->role === 'admin'; }

    public function rules()
    {
        return [
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role'     => ['required','in:admin,pegawai'],
            'nama_lengkap'  => ['required','string','max:255'],
            'jabatan'       => ['required','string','max:50'],
            'tgl_masuk'     => ['required','date'],
            'no_hp'         => ['required','string','max:15'],
            'alamat'        => ['nullable','string'],
            'foto_profil'   => ['nullable','image','max:2048'],
        ];
    }
}
