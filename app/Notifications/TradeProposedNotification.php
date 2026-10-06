<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TradeProposedNotification extends Notification
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
            'from_name' => $this->trade->initiator->name,
            'icon' => '🤝',
            'message' => "{$this->trade->initiator->name} mengajukan trade kepada kamu.",
            'action_url' => route('trade.room', $this->trade->id),
        ];
    }
}
