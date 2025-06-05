<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $user = auth()->user();
        
        // Cek apakah role user sesuai
        if ($user->role !== $role) {
            // Redirect ke dashboard yang sesuai dengan role user
            if ($user->role === 'admin') {
                return redirect('/admin')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } elseif ($user->role === 'pegawai') {
                return redirect('/pegawai')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            // Jika role tidak dikenali, logout dan redirect ke login
            auth()->logout();
            return redirect()->route('login')->with('error', 'Role tidak valid.');
        }

        return $next($request);
    }
}