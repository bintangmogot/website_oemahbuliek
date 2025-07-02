<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $unreadCount = Auth::user()->unreadNotifications->count();
            $view->with('unreadNotificationsCount', $unreadCount);
        } else {
            // Jika tidak ada user login, set ke 0
            $view->with('unreadNotificationsCount', 0);
        }
    }
}