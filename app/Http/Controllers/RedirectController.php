<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            return redirect('/admin');
        } else {
            return redirect('/pegawai');
        }
    }
}

