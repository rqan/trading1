<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    /**
     * Tandai semua notifikasi sebagai sudah dibaca sekaligus.
     */
    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.notification-bell', [
            'unreadCount' => $user->unreadNotifications->count(),
            'notifications' => $user->notifications()->latest()->limit(6)->get(),
        ]);
    }
}
