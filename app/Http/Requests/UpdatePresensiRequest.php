<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePresensiRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'jadwal_shift_id' => ['required','exists:jadwal_shift,id'],
            'user_id'         => ['required','exists:users,id'],
            'tgl_presensi'    => ['required','date'],
            'shift_ke'        => ['required','integer','min:1'],
            'jam_masuk'       => ['nullable','date_format:H:i:s'],
            'jam_keluar'      => ['nullable','date_format:H:i:s','after_or_equal:jam_masuk'],
            'status_kehadiran'=> ['required','string','max:50'],
            'menit_terlambat' => ['nullable','integer','min:0'],
            'menit_lembur'    => ['nullable','integer','min:0'],
            'upah_lembur'     => ['nullable','integer','min:0'],
            'potongan_terlambat'=> ['nullable','integer','min:0'],
            'keterangan'      => ['nullable','string'],
        ];
    }
}
