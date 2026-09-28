<?php

use App\Models\Casting;
use App\Models\CollectionItem;
use App\Models\Listing;
use App\Models\MarketSignal;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\MarketMultiplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('market multiplier service recalculates target accurately', function () {
    $casting = Casting::create(['name' => 'Test Casting', 'year' => 2026, 'rarity_tier' => 'mainline']);
    $user = User::factory()->create();

    // 3 wishlists
    Wishlist::create(['user_id' => $user->id, 'casting_id' => $casting->id]);
    Wishlist::create(['user_id' => User::factory()->create()->id, 'casting_id' => $casting->id]);
    Wishlist::create(['user_id' => User::factory()->create()->id, 'casting_id' => $casting->id]);

    // 1 listing (active)
    $collectionItem = CollectionItem::create([
        'user_id' => $user->id,
        'casting_id' => $casting->id,
        'condition' => 'Mint Loose',
        'photos' => [],
        'status' => 'for_trade',
    ]);
    Listing::create(['user_id' => $user->id, 'collection_item_id' => $collectionItem->id, 'type' => 'for_trade']);

    $service = new MarketMultiplierService();
    $service->recalculateAllTargetMultipliers();

    $signal = MarketSignal::where('casting_id', $casting->id)->first();
    // ratio = (3+1) / (1+1) = 2
    // raw = 1 + ln(2) * 0.25 (mainline sensitivity)
    // raw = 1 + 0.6931 * 0.25 = 1 + 0.173 = 1.173
    $this->assertNotNull($signal);
    $this->assertEqualsWithDelta(1.173, $signal->target_multiplier, 0.01);
});

test('market multiplier service applies smoothing', function () {
    $casting = Casting::create(['name' => 'Smooth Casting', 'year' => 2026]);
    $signal = MarketSignal::create([
        'casting_id' => $casting->id,
        'current_multiplier' => 1.0,
        'target_multiplier' => 2.0,
    ]);

    $service = new MarketMultiplierService();
    $service->applyHourlySmoothing();

    $signal->refresh();
    // new = 1.0 + (2.0 - 1.0) * 0.2 = 1.2
    $this->assertEqualsWithDelta(1.2, $signal->current_multiplier, 0.001);

    // check history created
    $this->assertDatabaseHas('market_multiplier_histories', [
        'casting_id' => $casting->id,
        'multiplier' => 1.2
    ]);
});

test('micro adjustments are applied on wishlist add and listing add', function () {
    $casting = Casting::create(['name' => 'Micro Casting', 'year' => 2026]);
    $user = User::factory()->create();

    MarketSignal::create([
        'casting_id' => $casting->id,
        'current_multiplier' => 1.0,
        'target_multiplier' => 1.0,
    ]);

    // Wishlist added +0.5%
    Wishlist::create(['user_id' => $user->id, 'casting_id' => $casting->id]);
    
    $signal = MarketSignal::where('casting_id', $casting->id)->first();
    $this->assertEqualsWithDelta(1.005, $signal->target_multiplier, 0.001);

    // Listing added -0.3%
    $collectionItem = CollectionItem::create([
        'user_id' => $user->id,
        'casting_id' => $casting->id,
        'condition' => 'Mint Loose',
        'photos' => [],
        'status' => 'available',
    ]);
    Listing::create(['user_id' => $user->id, 'collection_item_id' => $collectionItem->id, 'type' => 'for_trade']);

    $signal->refresh();
    $this->assertEqualsWithDelta(1.005 * (1 - 0.003), $signal->target_multiplier, 0.001);
});

test('condition service calculates and caches computed value', function () {
    $casting = Casting::create(['name' => 'Value Casting', 'year' => 2026, 'base_value' => 10]);
    $user = User::factory()->create();

    MarketSignal::create([
        'casting_id' => $casting->id,
        'current_multiplier' => 2.0, // Market multiplier is 2.0
    ]);

    $item = CollectionItem::create([
        'user_id' => $user->id,
        'casting_id' => $casting->id,
        'condition' => 'Sealed/MOC', // 1.5
        'card_condition' => 'Creased', // 0.9
        'photos' => [],
        'status' => 'available',
    ]);

    // Computed = Base (10) * Market (2.0) * Car (1.5) * Card (0.9)
    // 10 * 2.0 * 1.5 * 0.9 = 27
    
    $item->refresh();
    $this->assertEquals(27, $item->computed_value);
});
