<?php

use App\Models\InviteCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('registration requires valid invite code when beta mode is enabled', function () {
    config(['wflf.beta.require_invite' => true]);

    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('invite_code', 'INVALID')
        ->call('register');

    $component->assertHasErrors(['invite_code']);
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

test('registration works with valid invite code and marks it as used', function () {
    config(['wflf.beta.require_invite' => true]);
    
    $code = InviteCode::create(['code' => 'VALIDCODE']);

    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test2@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('invite_code', 'VALIDCODE')
        ->call('register');

    $component->assertHasNoErrors();
    $this->assertDatabaseHas('users', ['email' => 'test2@example.com']);
    
    $code->refresh();
    $this->assertNotNull($code->used_at);
    $this->assertNotNull($code->used_by);
});
