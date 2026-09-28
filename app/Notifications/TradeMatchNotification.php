<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Casting;

class TradeMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $casting;
    public $matchType;

    public function __construct(Casting $casting, $matchType)
    {
        $this->casting = $casting;
        $this->matchType = $matchType; // e.g. 'someone_has_your_wishlist', 'someone_wants_your_listing'
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Add 'mail' later if needed
    }

    public function toArray(object $notifiable): array
    {
        return [
            'casting_id' => $this->casting->id,
            'casting_name' => $this->casting->name,
            'match_type' => $this->matchType,
            'message' => $this->matchType === 'someone_has_your_wishlist'
                ? "Someone just listed {$this->casting->name} for trade!"
                : "Someone is looking for your {$this->casting->name}!",
        ];
    }
}
