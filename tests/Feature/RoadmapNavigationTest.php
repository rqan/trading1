<?php

use App\Models\Casting;
use App\Models\CollectionItem;
use App\Models\Listing;
use App\Models\ListingItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->casting = Casting::create([
        'name' => 'Nissan Skyline GT-R (R34)',
        'slug' => 'nissan-skyline-gt-r-r34',
        'year' => 2026,
        'rarity_tier' => 'mainline',
        'base_value' => 100,
    ]);
});

it('renders main navigation links for authenticated user', function () {
    actingAs($this->user);

    // Verify all new/updated navigation routes return 200 OK
    get(route('clubs.index'))->assertOk();
    get(route('values'))->assertOk();
    get(route('my.listings'))->assertOk();
    get(route('my.trades'))->assertOk();
    get(route('my.journals'))->assertOk();
    get(route('dashboard'))->assertOk();
});

it('renders dynamic links correctly in listing browser', function () {
    actingAs($this->user);

    $collectionItem = CollectionItem::create([
        'user_id' => $this->otherUser->id,
        'casting_id' => $this->casting->id,
        'condition' => 'Mint Loose',
        'status' => 'for_trade',
        'photos' => [],
    ]);

    $listing = Listing::create([
        'user_id' => $this->otherUser->id,
        'status' => 'active',
        'title' => 'WTT R34',
    ]);

    ListingItem::create([
        'listing_id' => $listing->id,
        'casting_id' => $this->casting->id,
        'side' => 'offered',
        'condition' => 'mint',
    ]);

    $response = get(route('listings'));
    $response->assertOk();

    // Verify user profile link exists in the response
    $response->assertSee(route('user.profile', $this->otherUser->id));

    // Verify casting detail link exists in the response
    $response->assertSee(route('casting.detail', $this->casting->slug));
});

it('renders wishlist and collection tabs in dashboard', function () {
    actingAs($this->user);

    $response = get(route('dashboard'));
    $response->assertOk();

    // Check if the tabs exist by looking for Livewire components
    $response->assertSeeLivewire('collection-manager');
    $response->assertSeeLivewire('wishlist-manager');
});

it('can open trade proposal modal and submit', function () {
    actingAs($this->user);

    // Create item belonging to other user
    $otherCollectionItem = CollectionItem::create([
        'user_id' => $this->otherUser->id,
        'casting_id' => $this->casting->id,
        'condition' => 'Sealed',
        'status' => 'for_trade',
        'photos' => [],
    ]);

    $listing = Listing::create([
        'user_id' => $this->otherUser->id,
        'status' => 'active',
        'title' => 'WTT R34 Sealed',
    ]);

    ListingItem::create([
        'listing_id' => $listing->id,
        'casting_id' => $this->casting->id,
        'side' => 'offered',
        'condition' => 'mint',
    ]);

    // Create item belonging to current user
    $myCollectionItem = CollectionItem::create([
        'user_id' => $this->user->id,
        'casting_id' => $this->casting->id,
        'condition' => 'Mint Loose',
        'status' => 'available',
        'photos' => [],
    ]);

    Livewire::test('trade-proposal', ['listingId' => $listing->id])
        ->call('openModal')
        ->assertSet('showModal', true)
        ->set('selectedItems', [$myCollectionItem->id])
        ->set('note', 'Tukar dengan ini ya gan!')
        ->call('reviewProposal')
        ->assertSet('showConfirmation', true)
        ->call('propose')
        ->assertRedirect();

    $this->assertDatabaseHas('trades', [
        'initiator_id' => $this->user->id,
        'receiver_id' => $this->otherUser->id,
        'status' => 'proposed',
    ]);

    $this->assertDatabaseHas('trade_messages', [
        'user_id' => $this->user->id,
        'message' => 'Tukar dengan ini ya gan!',
    ]);
});
