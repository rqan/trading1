<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Mencari Club</h1>
                <p class="text-sm text-slate-500 mt-0.5">Temukan dan bergabunglah dengan Club kolektor favoritmu.</p>
            </div>
            <div>
                <!-- Nanti tombol Create Club jika diperlukan -->
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama club..." class="w-full md:w-1/3 pl-3 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($clubs as $club)
                <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-start gap-4 mb-3">
                            <div class="h-12 w-12 rounded bg-slate-100 flex items-center justify-center text-2xl shrink-0">
                                {{ $club->image_url ? '' : '🏁' }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 line-clamp-1">
                                    <a href="{{ route('clubs.show', $club) }}" class="hover:text-orange-600">{{ $club->name }}</a>
                                </h3>
                                <p class="text-xs text-slate-500">{{ $club->members_count }} Anggota</p>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 line-clamp-2 mb-4">{{ $club->description ?? 'Tidak ada deskripsi.' }}</p>
                    </div>

                    @auth
                        @if(in_array($club->id, $joinedClubIds))
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Tergabung</span>
                                <button wire:click="leaveClub({{ $club->id }})" class="text-xs text-red-500 hover:underline">Keluar</button>
                            </div>
                        @else
                            <button wire:click="joinClub({{ $club->id }})" class="w-full px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition-colors">
                                Bergabung ke Club
                            </button>
                        @endif
                    @endauth
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <p class="text-3xl mb-2">🔍</p>
                    <p>Tidak ada club yang ditemukan.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $clubs->links() }}
        </div>
    </div>
</div>
