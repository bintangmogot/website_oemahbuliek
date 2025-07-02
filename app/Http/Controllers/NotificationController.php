<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <-- 1. Pastikan Request di-import
// use Illuminate\Support\Facades\Auth; // <-- 2. Baris ini tidak lagi diperlukan

class NotificationController extends Controller
{
    // 3. Tambahkan Request $request di dalam parameter method
    public function index(Request $request)
    {
        // 4. Ganti Auth::user() menjadi $request->user()
        $user = $request->user();
        
        $notifications = $user->notifications()->paginate(15);
        
        // Tandai semua notifikasi yang belum dibaca sebagai sudah dibaca
        $user->unreadNotifications->markAsRead();

        return view('dashboard.notifications.index', compact('notifications'));
    }
}