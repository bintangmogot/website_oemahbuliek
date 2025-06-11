<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        $id = $this->route('pegawai')->id;

        return [
            'nama_lengkap'  => ['required','string','max:255'],
            'jabatan'       => ['required','string','max:50'],
            'tgl_masuk'     => ['required','date'],
            'no_hp'         => ['required','string','max:15'],
            'alamat'        => ['nullable','string'],
        ];
    }
}
