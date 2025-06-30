<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
{
    public function authorize()
    {
    return true; // Biarkan middleware yang handle

    }

    public function rules()
    {
        return [
            'nama_shift'       => ['required','string','max:50','unique:shift,nama_shift'],
            'is_shift_lembur' => 'required|in:0,1',
            'jam_mulai'        => ['required','date_format:H:i'],
            'jam_selesai'      => ['required','date_format:H:i','after:jam_mulai'],
            'toleransi_terlambat'  => ['required','integer','min:0'],
            'batas_lembur_min'     => ['required','integer','min:0'],
            'status' => 'required|in:0,1',
        ];
    }
}
