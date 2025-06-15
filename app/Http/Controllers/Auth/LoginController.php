<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Print_;

class LoginController extends Controller
{

    // FUNGSI UNTUK MENAMPILKAN FORM LOGIN
    public function showLoginForm()
    {
        if (Auth::check()) {
        return redirect('/dashboard');
    }
        return view('auth.login');
    }

    // FUNGSI UNTUK PROSES LOGIN
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
        $email = $credentials['email'];
        // Mengambil data dengan kondisi tertentu (WHERE)
        $user = DB::table('users')->where('email', $email)->first();
                
        // Set session
        session([
            'email'     => $user->email,
            'role'      => $user->role,
            'nama_user' => $user->nama_lengkap,  // selalu pakai nama_lengkap
        ]);
         // Jika role=pegawai, ambil juga jabatan
        if ($user->role === 'pegawai') {
            session(['jabatan' => $user->jabatan]);
        }
        
        return redirect('/dashboard');
    }

        return back()->withErrors([
        'email'    => 'Email tidak ditemukan.',
        'password' => 'atau kata sandi salah.',
    ])->withInput();

}

// FUNGSI UNTUK LOGOUT
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