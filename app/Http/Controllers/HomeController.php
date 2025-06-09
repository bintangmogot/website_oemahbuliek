<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $users = DB::table('users')->where('email', session('email'))->get();
            // Cek role user
            if ($users[0]->role == 'admin') {
                return redirect('/admin');
            } else {
                return redirect('/pegawai');
            }
        }


        // Kalau belum login, tampilkan homepage biasa
        return view('index'); // atau landing.blade.php sesuai milikmu
    }
}
