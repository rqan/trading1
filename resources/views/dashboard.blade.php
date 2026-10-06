<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $stats = Cache::remember('dashboard_stats', 60, function () {
                        return [
                            'total_users' => \App\Models\User::count(),
                            'active_listings' => \App\Models\Listing::count(),
                            'completed_trades' => \App\Models\Trade::where('status', 'completed')->count(),
                        ];
                    });
                @endphp
                <div class="bg-white p-6 shadow sm:rounded-lg text-center">
                    <h3 class="text-gray-500 text-sm">Total Collectors</h3>
                    <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg text-center">
                    <h3 class="text-gray-500 text-sm">Active Listings</h3>
                    <p class="text-3xl font-bold">{{ $stats['active_listings'] }}</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg text-center">
                    <h3 class="text-gray-500 text-sm">Completed Trades</h3>
                    <p class="text-3xl font-bold">{{ $stats['completed_trades'] }}</p>
                </div>
            </div>

            {{-- Tab System: Collection & Wishlist --}}
            <div x-data="{ tab: new URLSearchParams(window.location.search).get('tab') === 'wishlist' ? 'wishlist' : 'collection' }">
                {{-- Tab Headers --}}
                <div class="flex border-b border-slate-200 mb-0">
                    <button @click="tab = 'collection'"
                            :class="tab === 'collection' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors focus:outline-none">
                        📦 My Collection
                    </button>
                    <button @click="tab = 'wishlist'"
                            :class="tab === 'wishlist' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors focus:outline-none">
                        ❤️ My Wishlist
                    </button>
                </div>

                {{-- Tab Content: Collection --}}
                <div x-show="tab === 'collection'" x-cloak>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="font-bold text-lg mb-4">My Collection</h3>
                            @livewire('collection-manager')
                        </div>
                    </div>
                </div>

                {{-- Tab Content: Wishlist --}}
                <div x-show="tab === 'wishlist'" x-cloak>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg">
                        <div class="p-6 text-gray-900">
                            @livewire('wishlist-manager')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
