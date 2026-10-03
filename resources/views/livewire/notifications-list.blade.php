<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Notifikasi
        @if($unreadCount > 0)
            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                {{ $unreadCount }} belum dibaca
            </span>
        @endif
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button
                    wire:click="$set('filterType', '')"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors
                           {{ $filterType === '' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua
                </button>
                <button
                    wire:click="$set('filterType', 'unread')"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors
                           {{ $filterType === 'unread' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Belum Dibaca
                </button>
            </div>

            @if($unreadCount > 0)
                <button
                    wire:click="markAllRead"
                    class="text-sm text-orange-600 hover:underline">
                    ✓ Tandai semua dibaca
                </button>
            @endif
        </div>

        {{-- List --}}
        @if($notifications->isEmpty())
            <div class="bg-white shadow rounded-xl p-12 text-center text-slate-400">
                <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="font-medium text-slate-600">Tidak ada notifikasi</p>
                <p class="text-sm mt-1">Semua bersih! Kamu akan diberitahu saat ada aktivitas di trade kamu.</p>
            </div>
        @else
            <div class="bg-white shadow rounded-xl overflow-hidden divide-y divide-slate-100">
                @foreach($notifications as $notif)
                    @php
                        $data    = $notif->data;
                        $isUnread = is_null($notif->read_at);
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-4
                                {{ $isUnread ? 'bg-orange-50/50' : '' }}
                                hover:bg-slate-50 transition-colors group">

                        {{-- Ikon --}}
                        <span class="text-2xl shrink-0 mt-0.5">{{ $data['icon'] ?? '🔔' }}</span>

                        {{-- Konten --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 {{ $isUnread ? 'font-semibold' : '' }} leading-snug">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            @if(!empty($data['action_url']))
                                <a href="{{ $data['action_url'] }}"
                                   class="text-xs text-orange-600 hover:underline mt-1 inline-block">
                                    Lihat detail →
                                </a>
                            @endif
                        </div>

                        {{-- Aksi --}}
                        <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($isUnread)
                                <button
                                    wire:click="markRead('{{ $notif->id }}')"
                                    title="Tandai dibaca"
                                    class="p-1.5 rounded-md text-slate-400 hover:text-green-600 hover:bg-green-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @endif
                            <button
                                wire:click="deleteNotification('{{ $notif->id }}')"
                                wire:confirm="Hapus notifikasi ini?"
                                title="Hapus"
                                class="p-1.5 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Dot belum dibaca --}}
                        @if($isUnread)
                            <span class="mt-2 h-2 w-2 rounded-full bg-orange-500 shrink-0"></span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
