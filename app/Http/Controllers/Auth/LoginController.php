<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    // Fungsi untuk menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Fungsi untuk menangani login
    public function login(Request $request)
{
    // Validasi input dari form login
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);
  // Menggunakan Auth::attempt untuk memverifikasi kredensial  
    // Jika berhasil, maka session akan di-regenerate
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
    // Jika login gagal, kembalikan ke halaman login dengan pesan error
    return back()->withErrors([
    'email' => 'Email tidak ditemukan.',
    'password' => 'atau kata sandi salah.',
])->withInput();

}

public function logout(Request $request)
{
    //diberi transaction agar jika ada error saat logout, tidak mengganggu proses lainnya
    DB::transaction(function() use ($request) {
        Auth::logout();

        // Ini memicu update remember_token di model:
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    });
    return redirect('/login');
}

}