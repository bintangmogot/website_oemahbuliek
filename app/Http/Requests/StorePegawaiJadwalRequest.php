<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiJadwalRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'jadwal_shift_id' => ['required','exists:jadwal_shift,id'],
            'users_id'         => ['required','exists:users,id'],
        ];
    }
}
