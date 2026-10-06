<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notifikasi')]
class NotificationsList extends Component
{
    use WithPagination;

    public string $filterType = '';

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        $notif = Auth::user()->notifications()->where('id', $id)->first();
        $notif?->markAsRead();
    }

    public function deleteNotification(string $id): void
    {
        Auth::user()->notifications()->where('id', $id)->delete();
    }

    public function render()
    {
        $query = Auth::user()->notifications();

        if ($this->filterType === 'unread') {
            $query = Auth::user()->unreadNotifications();
        }

        $notifications = $query->latest()->paginate(15);
        $unreadCount = Auth::user()->unreadNotifications->count();

        return view('livewire.notifications-list', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
