<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJadwalShiftRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        $id = $this->route('jadwal_shift')->id;
        return [
            'shift_id'        => ['required','exists:shift,id'],
            'nama_periode'    => ['required','string','max:100'],
            'mulai_berlaku'   => ['required','date'],
            'berakhir_berlaku'=> ['nullable','date','after_or_equal:mulai_berlaku'],
            'hari_kerja'      => ['required','array','min:1'],
            'hari_kerja.*'    => ['in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
        ];
    }

    public function messages()
    {
        return [
            'nama_periode.required'     => 'Nama periode wajib diisi.',
            'mulai_berlaku.required'    => 'Tanggal mulai wajib diisi.',
            'berakhir_berlaku.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
            'hari_kerja.required'       => 'Pilih minimal satu hari kerja.',
        ];
    }
}