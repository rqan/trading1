<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('trades.{tradeId}', function ($user, $tradeId) {
    $trade = \App\Models\Trade::find($tradeId);
    return $trade && ($user->id === $trade->initiator_id || $user->id === $trade->receiver_id);
});
