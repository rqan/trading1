<?php

use App\Livewire\ListingBrowser;
use App\Livewire\WishlistManager;
use App\Models\Casting;
use App\Models\CollectionItem;
use App\Models\Listing;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TradeMatchNotification;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('user can add casting to wishlist and only once per casting', function () {
    $user = User::factory()->create();
    $casting = Casting::create(['name' => 'RX-7', 'year' => 2026]);

    Livewire::actingAs($user)
        ->test(WishlistManager::class)
        ->set('selectedCasting', $casting->id)
        ->call('add')
        ->assertHasNoErrors()
        ->assertSee('Casting added to wishlist');

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $user->id,
        'casting_id' => $casting->id,
    ]);

    // Try adding again
    Livewire::actingAs($user)
        ->test(WishlistManager::class)
        ->set('selectedCasting', $casting->id)
        ->call('add')
        ->assertSee('already in your wishlist');

    $this->assertDatabaseCount('wishlists', 1);
});

test('auto match notification triggers when listing matches wishlist', function () {
    Notification::fake();

    $userA = User::factory()->create();
    $userB = User::factory()->create();
    
    $casting = Casting::create(['name' => 'Civic Type R', 'year' => 2026]);

    // User A adds to wishlist
    Wishlist::create([
        'user_id' => $userA->id,
        'casting_id' => $casting->id,
    ]);

    // User B creates a collection item
    $collectionItem = CollectionItem::create([
        'user_id' => $userB->id,
        'casting_id' => $casting->id,
        'condition' => 'Mint Loose',
        'photos' => [],
        'status' => 'available',
    ]);

    // User B lists it for trade
    Listing::create([
        'user_id' => $userB->id,
        'collection_item_id' => $collectionItem->id,
        'type' => 'for_trade',
    ]);

    // User A should be notified
    Notification::assertSentTo(
        [$userA], TradeMatchNotification::class
    );
});

test('auto match notification triggers when wishlist matches existing listing', function () {
    Notification::fake();

    $userA = User::factory()->create();
    $userB = User::factory()->create();
    
    $casting = Casting::create(['name' => 'NSX', 'year' => 2026]);

    // User B has a collection item and lists it for trade
    $collectionItem = CollectionItem::create([
        'user_id' => $userB->id,
        'casting_id' => $casting->id,
        'condition' => 'Mint Loose',
        'photos' => [],
        'status' => 'available',
    ]);

    Listing::create([
        'user_id' => $userB->id,
        'collection_item_id' => $collectionItem->id,
        'type' => 'for_trade',
    ]);

    // User A adds to wishlist
    Wishlist::create([
        'user_id' => $userA->id,
        'casting_id' => $casting->id,
    ]);

    // User A should be notified
    Notification::assertSentTo(
        [$userA], TradeMatchNotification::class
    );
});

test('listing browser can search and filter', function () {
    $user = User::factory()->create();
    
    $castingA = Casting::create(['name' => 'Batmobile', 'year' => 2026, 'rarity_tier' => 'mainline']);
    $castingB = Casting::create(['name' => 'Delorean', 'year' => 2026, 'rarity_tier' => 'premium']);

    Listing::create([
        'user_id' => $user->id,
        'casting_id' => $castingA->id,
        'type' => 'looking_for',
    ]);

    $collectionItem = CollectionItem::create([
        'user_id' => $user->id,
        'casting_id' => $castingB->id,
        'condition' => 'Mint Loose',
        'photos' => [],
        'status' => 'available',
    ]);

    Listing::create([
        'user_id' => $user->id,
        'collection_item_id' => $collectionItem->id,
        'type' => 'for_trade',
    ]);

    Livewire::test(ListingBrowser::class)
        ->assertSee('Batmobile')
        ->assertSee('Delorean')
        ->set('search', 'Batmo')
        ->assertSee('Batmobile')
        ->assertDontSee('Delorean')
        ->set('search', '')
        ->set('filterType', 'for_trade')
        ->assertSee('Delorean')
        ->assertDontSee('Batmobile')
        ->set('filterType', '')
        ->set('filterRarity', 'premium')
        ->assertSee('Delorean')
        ->assertDontSee('Batmobile');
});
