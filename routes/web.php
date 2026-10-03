<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home');
Route::view('aturan', 'pages.aturan')->name('aturan');
Route::view('privasi', 'pages.privasi')->name('privasi');
Route::view('tentang', 'pages.tentang')->name('tentang');

// Halaman Values — publik, SEO-friendly
Route::get('values', \App\Livewire\ValuesList::class)->name('values');
Route::get('casting/{casting:slug}', \App\Livewire\CastingDetail::class)->name('casting.detail');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('listings', \App\Livewire\ListingBrowser::class)
    ->middleware(['auth', 'verified'])
    ->name('listings');

Route::get('club', \App\Livewire\ClubFeed::class)
    ->middleware(['auth', 'verified'])
    ->name('club.feed');

Route::get('clubs', \App\Livewire\ClubList::class)
    ->name('clubs.index');

Route::get('clubs/{club:slug}', \App\Livewire\ClubDetail::class)
    ->name('clubs.show');

Route::get('clubs/{club:slug}/manage', \App\Livewire\ClubManage::class)
    ->middleware(['auth', 'verified'])
    ->name('clubs.manage');

Route::get('trades/{trade}', \App\Livewire\TradeRoom::class)
    ->middleware(['auth', 'verified'])
    ->name('trade.room');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('my-trades', \App\Livewire\MyTrades::class)
    ->middleware(['auth', 'verified'])
    ->name('my.trades');

Route::get('my-listings', \App\Livewire\MyListings::class)
    ->middleware(['auth', 'verified'])
    ->name('my.listings');

Route::get('notifications', \App\Livewire\NotificationsList::class)
    ->middleware(['auth', 'verified'])
    ->name('notifications');

Route::get('users/{user}', \App\Livewire\PublicProfile::class)
    ->middleware(['auth'])
    ->name('user.profile');

// ── Jurnal Hot Wheels ────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('journals', \App\Livewire\MyJournals::class)->name('my.journals');
    Route::get('journals/create', \App\Livewire\JournalForm::class)->name('journals.create');
    Route::get('journals/{journal}', \App\Livewire\JournalDetail::class)->name('journals.show');
    Route::get('journals/{journal}/edit', \App\Livewire\JournalForm::class)->name('journals.edit');
});

require __DIR__.'/auth.php';
