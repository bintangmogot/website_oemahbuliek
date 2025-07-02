<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Logika ini akan dijalankan setelah otentikasi selesai
        if (Auth::check()) {
            $user = Auth::user();
            // Bagikan variabel 'unreadNotificationsCount' ke semua view
            View::share('unreadNotificationsCount', $user->unreadNotifications->count());
        } else {
            View::share('unreadNotificationsCount', 0);
        }

        // Lanjutkan request ke langkah berikutnya (controller, dll)
        return $next($request);
    }
}
