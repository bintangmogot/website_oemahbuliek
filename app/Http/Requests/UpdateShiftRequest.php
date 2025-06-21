<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShiftRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        $id = $this->route('shift')->id;

        return [
            'nama_shift'            => ['required','string','max:50',"unique:shift,nama_shift,{$id}"],
            'jam_mulai'             => ['required','date_format:H:i:s'],
            'jam_selesai'           => ['required','date_format:H:i:s','after:jam_mulai'],
            'toleransi_terlambat'   => ['required','integer','min:0'],
            'batas_lembur_min'      => ['required','integer','min:0'],
        ];
    }
}
