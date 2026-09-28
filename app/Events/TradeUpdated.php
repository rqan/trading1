<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TradeUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tradeId;

    public function __construct($tradeId)
    {
        $this->tradeId = $tradeId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('trades.' . $this->tradeId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'TradeUpdated';
    }
}
