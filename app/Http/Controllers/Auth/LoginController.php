<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Print_;

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
        $email = $credentials['email'];
        // Mengambil data dengan kondisi tertentu (WHERE)
        $users = DB::table('users')->where('email', $email)->get();
      
        // $nama = $users->nama_lengkap;
          
        // Redirect berdasarkan role tanpa operator ternary
        if ($users[0]->role == 'admin') {
            session(['email' => $users[0]->email]);
            session(['nama_user' => 'Admin']);
            return redirect('/admin');
        } else {
        $users = DB::table('pegawai')
      ->leftJoin('users', 'pegawai.id_akun', '=', 'users.email') // JOIN users ON pegawai.id_akun = users.email
      ->select('pegawai.*', 'users.*') // pilih kolom yang diinginkan
      ->where('users.email', $email) // filter berdasarkan email
      ->get();
            session(['email' => $users[0]->email]);
            session(['nama_user' => $users[0]->nama_lengkap]);
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