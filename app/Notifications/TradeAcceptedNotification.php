<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TradeAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(public Trade $trade) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'trade_id' => $this->trade->id,
            'icon' => '✅',
            'message' => "Trade #{$this->trade->id} telah disetujui kedua pihak! Saatnya kirim paket.",
            'action_url' => route('trade.room', $this->trade->id),
        ];
    }
}
