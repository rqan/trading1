<div class="relative" x-data="{ notifOpen: false }">
    {{-- Bell Button --}}
    <button
        @click="notifOpen = !notifOpen"
        @click.outside="notifOpen = false"
        class="relative flex items-center justify-center h-9 w-9 rounded-full text-slate-500 hover:bg-slate-100 hover:text-orange-600 transition-colors"
        aria-label="Notifikasi">
        {{-- Bell Icon --}}
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Badge jumlah belum dibaca --}}
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="notifOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden"
        style="display:none">

        {{-- Header dropdown --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-sm font-semibold text-slate-700">Notifikasi</span>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <button
                        wire:click="markAllRead"
                        class="text-xs text-orange-600 hover:underline">
                        Tandai semua dibaca
                    </button>
                @endif
                <a href="{{ route('notifications') }}"
                   class="text-xs text-slate-500 hover:text-orange-600">
                    Lihat semua →
                </a>
            </div>
        </div>

        {{-- List Notifikasi --}}
        @if($notifications->isEmpty())
            <div class="px-4 py-8 text-center text-slate-400 text-sm">
                <svg class="mx-auto h-8 w-8 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Tidak ada notifikasi
            </div>
        @else
            <ul class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                @foreach($notifications as $notif)
                    @php $data = $notif->data; $isUnread = is_null($notif->read_at); @endphp
                    <li>
                        <a href="{{ $data['action_url'] ?? route('notifications') }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-orange-50 transition-colors
                                  {{ $isUnread ? 'bg-orange-50/60' : '' }}">
                            <span class="text-xl shrink-0 mt-0.5">{{ $data['icon'] ?? '🔔' }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700 leading-snug {{ $isUnread ? 'font-medium' : '' }}">
                                    {{ $data['message'] ?? '' }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if($isUnread)
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-orange-500 shrink-0"></span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
