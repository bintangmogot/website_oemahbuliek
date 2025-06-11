<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $users = DB::table('users')->where('email', session('email'))->get();
        
        // Cek apakah role user sesuai
        if ($users[0]->role !== $role) {
            // Redirect ke dashboard yang sesuai dengan role user
            if ($users[0]->role === 'admin' || $users[0]->role === 'pegawai') {
                return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } 
            // elseif ($users[0]->role === 'pegawai') {
            //     return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            // }
            
            // Jika role tidak dikenali, logout dan redirect ke login
            auth()->logout();
            return redirect()->route('login')->with('error', 'Role tidak valid.');
        }

        return $next($request);
    }
}