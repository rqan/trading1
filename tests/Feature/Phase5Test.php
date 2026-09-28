<?php

use App\Models\Casting;
use App\Models\MarketMultiplierHistory;
use App\Models\MarketSignal;
use App\Models\Rating;
use App\Models\Trade;
use App\Models\User;
use App\Services\MarketMultiplierService;
use App\Services\TrustScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('trust score recalculates correctly', function () {
    $user = User::factory()->create([
        'name' => 'Focarix',
        'email' => 'test@example.com',
        'trust_score' => 100 // default
    ]);

    $service = new TrustScoreService();
    
    // Base 50 + 10 (profile) = 60
    $service->recalculate($user);
    $user->refresh();
    $this->assertEquals(60, $user->trust_score);

    // 1 completed trade = +2
    $trade = Trade::create(['initiator_id' => $user->id, 'receiver_id' => User::factory()->create()->id, 'status' => 'completed']);
    
    // 5-star rating = +30
    Rating::create(['trade_id' => $trade->id, 'reviewer_id' => $trade->receiver_id, 'reviewee_id' => $user->id, 'score' => 5]);

    $service->recalculate($user);
    $user->refresh();
    // 60 + 2 + 30 = 92
    $this->assertEquals(92, $user->trust_score);
});

test('anomaly detection ignores signals from low trust accounts', function () {
    $user = User::factory()->create(['trust_score' => 50, 'created_at' => now()->subDays(10)]);
    $casting = Casting::create(['name' => 'Car', 'year' => 2026]);

    MarketSignal::create([
        'casting_id' => $casting->id,
        'current_multiplier' => 1.0,
        'target_multiplier' => 1.0,
    ]);

    $service = new MarketMultiplierService();
    $service->applyMicroAdjustment($casting->id, 'wishlist_added', $user);

    // Because trust score is 50 (<60), it should be ignored
    $signal = MarketSignal::where('casting_id', $casting->id)->first();
    $this->assertEquals(1.0, $signal->target_multiplier);
});

test('anomaly detection ignores signals from new accounts', function () {
    $user = User::factory()->create(['trust_score' => 100, 'created_at' => now()->subDays(2)]);
    $casting = Casting::create(['name' => 'Car', 'year' => 2026]);

    MarketSignal::create([
        'casting_id' => $casting->id,
        'current_multiplier' => 1.0,
        'target_multiplier' => 1.0,
    ]);

    $service = new MarketMultiplierService();
    $service->applyMicroAdjustment($casting->id, 'wishlist_added', $user);

    // Because account is < 7 days old, it should be ignored
    $signal = MarketSignal::where('casting_id', $casting->id)->first();
    $this->assertEquals(1.0, $signal->target_multiplier);
});

test('anomaly detection caps daily movement to 10 percent', function () {
    $casting = Casting::create(['name' => 'Car', 'year' => 2026]);

    // Start of day multiplier is 1.0
    MarketMultiplierHistory::create([
        'casting_id' => $casting->id,
        'multiplier' => 1.0,
        'recorded_at' => now()->startOfDay(),
    ]);

    // Target is very high (3.0)
    $signal = MarketSignal::create([
        'casting_id' => $casting->id,
        'current_multiplier' => 1.0,
        'target_multiplier' => 3.0,
    ]);

    $service = new MarketMultiplierService();
    $service->applyHourlySmoothing(); // would normally move to 1.4 (1.0 + (3.0-1.0)*0.2)
    
    // But it's capped at +10% of start of day (1.1)
    $signal->refresh();
    $this->assertEqualsWithDelta(1.1, $signal->current_multiplier, 0.001);
});
