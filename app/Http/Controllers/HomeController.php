<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin) {
                return redirect('/admin');
            } else {
                return redirect('/pegawai');
            }
        }

        // Kalau belum login, tampilkan homepage biasa
        return view('index'); // atau landing.blade.php sesuai milikmu
    }
}
