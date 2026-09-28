<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('listings', \App\Livewire\ListingBrowser::class)
    ->middleware(['auth', 'verified'])
    ->name('listings');

Route::get('trades/{trade}', \App\Livewire\TradeRoom::class)
    ->middleware(['auth', 'verified'])
    ->name('trade.room');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
