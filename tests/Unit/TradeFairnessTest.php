<?php

use App\Models\CollectionItem;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\User;
use App\Services\TradeFairnessCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('trade is fair if value difference is within 15 percent', function () {
    $initiator = User::factory()->create();
    $receiver = User::factory()->create();
    $casting = \App\Models\Casting::create(['name' => 'Car', 'year' => 2026]);

    $trade = Trade::create([
        'initiator_id' => $initiator->id,
        'receiver_id' => $receiver->id,
    ]);

    $itemA = CollectionItem::create(['user_id' => $initiator->id, 'casting_id' => $casting->id, 'condition' => 'Mint', 'status' => 'available', 'photos' => []]);
    $itemA->computed_value = 100;
    $itemA->saveQuietly();
    TradeItem::create(['trade_id' => $trade->id, 'user_id' => $initiator->id, 'collection_item_id' => $itemA->id]);

    $itemB = CollectionItem::create(['user_id' => $receiver->id, 'casting_id' => $casting->id, 'condition' => 'Mint', 'status' => 'available', 'photos' => []]);
    $itemB->computed_value = 85;
    $itemB->saveQuietly();
    TradeItem::create(['trade_id' => $trade->id, 'user_id' => $receiver->id, 'collection_item_id' => $itemB->id]);

    $calc = new TradeFairnessCalculator();
    $result = $calc->calculate($trade);

    $this->assertTrue($result['is_fair']);
    $this->assertEquals(15.0, $result['percentage_diff']);
    $this->assertNull($result['needs_kicker_from']);
});

test('trade is unfair if value difference exceeds 15 percent', function () {
    $initiator = User::factory()->create();
    $receiver = User::factory()->create();
    $casting = \App\Models\Casting::create(['name' => 'Car2', 'year' => 2026]);

    $trade = Trade::create([
        'initiator_id' => $initiator->id,
        'receiver_id' => $receiver->id,
    ]);

    $itemA = CollectionItem::create(['user_id' => $initiator->id, 'casting_id' => $casting->id, 'condition' => 'Mint', 'status' => 'available', 'photos' => []]);
    $itemA->computed_value = 100;
    $itemA->saveQuietly();
    TradeItem::create(['trade_id' => $trade->id, 'user_id' => $initiator->id, 'collection_item_id' => $itemA->id]);

    $itemB = CollectionItem::create(['user_id' => $receiver->id, 'casting_id' => $casting->id, 'condition' => 'Mint', 'status' => 'available', 'photos' => []]);
    $itemB->computed_value = 80;
    $itemB->saveQuietly();
    TradeItem::create(['trade_id' => $trade->id, 'user_id' => $receiver->id, 'collection_item_id' => $itemB->id]);

    $calc = new TradeFairnessCalculator();
    $result = $calc->calculate($trade);

    $this->assertFalse($result['is_fair']);
    $this->assertEquals(20.0, $result['percentage_diff']);
    $this->assertEquals($receiver->id, $result['needs_kicker_from']);
    $this->assertEquals(5.0, $result['kicker_value_needed']);
});
