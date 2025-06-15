<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class RedirectController extends Controller
{
    public function index()
    {
        // $email = session('email');
        // // $email = "mantap@gmail.com";
        // // Mengambil data dengan kondisi tertentu (WHERE)
        // $users = DB::table('users')->where('email', $email)->get();
        // if ($users[0]->role == 'admin') {
        //     return redirect('/admin');
        // } else {
        //     return redirect('/pegawai');
        // }
    }
}

