<?php

use App\Livewire\CollectionManager;
use App\Models\Casting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('admin can access filament casting page', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get('/admin/castings')->assertStatus(403);
    $this->actingAs($admin)->get('/admin/castings')->assertStatus(200);
});

test('user can add casting to collection via livewire component', function () {
    $user = User::factory()->create();
    $casting = Casting::create([
        'name' => 'Datsun 510',
        'series' => 'JDM',
        'year' => 2026,
        'rarity_tier' => 'sth',
        'base_value' => 50,
    ]);

    Livewire::actingAs($user)
        ->test(CollectionManager::class)
        ->set('selectedCasting', $casting->id)
        ->set('condition', 'Mint Loose')
        ->set('cardCondition', 'None')
        ->set('status', 'for_trade')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('collection_items', [
        'user_id' => $user->id,
        'casting_id' => $casting->id,
        'condition' => 'Mint Loose',
        'status' => 'for_trade',
    ]);
});

test('user can delete item from collection', function () {
    $user = User::factory()->create();
    $casting = Casting::create([
        'name' => 'Skyline R34',
        'year' => 2026,
    ]);

    $item = \App\Models\CollectionItem::create([
        'user_id' => $user->id,
        'casting_id' => $casting->id,
        'condition' => 'Sealed/MOC',
        'photos' => [],
        'status' => 'available'
    ]);

    Livewire::actingAs($user)
        ->test(CollectionManager::class)
        ->call('deleteItem', $item->id);

    $this->assertDatabaseMissing('collection_items', [
        'id' => $item->id,
    ]);
});
