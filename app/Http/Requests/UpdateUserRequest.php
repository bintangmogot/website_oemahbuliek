<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        $userId = $this->user->id;
        return [
            'password'     => ['nullable','string','min:6'],
            'role'         => ['required','in:admin,pegawai'],
            'nama_lengkap' => ['required','string','max:255'],
            'jabatan'      => ['required','string','max:50'],
            'tgl_masuk'    => ['required','date'],
            'no_hp'        => ['required', 'string', 'max:15', Rule::unique('users')->ignore($userId)],
            'alamat'       => ['nullable','string'],
            'foto_profil'  => ['nullable','image','max:2048'],
        ];
    }

}