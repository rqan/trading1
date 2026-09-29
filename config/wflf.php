<?php

return [
    'multipliers' => [
        'min' => 0.5,
        'max' => 3.0,
        'smoothing_factor' => 0.2, // daily smoothing
        'daily_cap' => 0.10, // max 10% daily movement
    ],
    
    'micro_adjustments' => [
        'wishlist_added' => 0.005, // +0.5%
        'trade_completed' => 0.01, // +1%
        'listing_added' => -0.003, // -0.3%
    ],

    'trust_score' => [
        'base' => 50,
        'profile_bonus' => 10,
        'trade_bonus' => 2,
        'cancel_penalty' => 5,
        'min_required' => 60, // flag threshold
    ],

    'beta' => [
        'require_invite' => env('WFLF_REQUIRE_INVITE', true),
    ],
];
