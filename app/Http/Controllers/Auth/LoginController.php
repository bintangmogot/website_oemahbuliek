<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        $user = Auth::user();

        // Redirect berdasarkan role tanpa operator ternary
        if ($user->isAdmin) {
            return redirect('/admin');
        } else {
            return redirect('/pegawai');
        }
    }

    return back()->withErrors([
    'email' => 'Email tidak ditemukan.',
    'password' => 'atau kata sandi salah.',
])->withInput();

}

public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}

}