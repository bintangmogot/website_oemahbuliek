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
        ];
    }
}
