<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 space-y-6">
        
        {{-- Club Header --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="h-32 bg-slate-800"></div>
            <div class="px-6 pb-6 relative">
                <div class="flex justify-between items-end -mt-10 mb-4">
                    <div class="h-20 w-20 bg-white rounded-xl border-4 border-white shadow-sm flex items-center justify-center text-4xl">
                        {{ $club->image_url ? '' : '🏁' }}
                    </div>
                    @if(in_array($myRole, ['founder', 'co_founder']))
                        <a href="{{ route('clubs.manage', $club) }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">Kelola Club</a>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $club->name }}</h1>
                <p class="text-sm text-slate-500">{{ $club->members_count }} Anggota &middot; {{ $club->is_private ? 'Privat' : 'Publik' }}</p>
                <p class="mt-4 text-slate-700 text-sm">{{ $club->description ?? 'Selamat datang di club ini.' }}</p>
                
                @if($club->rules)
                    <div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-lg">
                        <h3 class="text-xs font-bold text-orange-800 uppercase mb-1">Peraturan Club</h3>
                        <p class="text-sm text-orange-900 whitespace-pre-line">{{ $club->rules }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Club Feed --}}
        @if($isMember)
            <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 shadow-sm">
                <form wire:submit.prevent="createPost">
                    <textarea wire:model="content" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500" rows="3" placeholder="Bagikan sesuatu ke member club ini..."></textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition font-medium text-sm">Post di Club</button>
                    </div>
                    @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </form>
            </div>
        @else
            <div class="bg-slate-100 border border-slate-200 rounded-xl p-6 text-center text-slate-600">
                <p class="mb-4">Hanya anggota yang bisa melihat dan memposting di feed club ini.</p>
                {{-- Nanti ditambahkan tombol Join dari halaman ini juga --}}
            </div>
        @endif

        @if($isMember)
            <div class="space-y-6">
                @forelse ($posts as $post)
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <a href="{{ route('user.profile', $post->user->id) }}" class="font-bold text-slate-900 hover:text-orange-600">{{ $post->user->name }}</a>
                                <p class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <p class="text-slate-800 whitespace-pre-line mb-4">{{ $post->content }}</p>

                        @if($post->collectionItem)
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg border border-slate-100 flex gap-4">
                                <div class="text-3xl opacity-50">🚗</div>
                                <div>
                                    <p class="text-xs text-slate-500 font-semibold uppercase">Terkait Item</p>
                                    <p class="font-medium text-slate-900">{{ $post->collectionItem->casting->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="border-t border-slate-100 pt-4 mt-4">
                            <h4 class="text-sm font-bold text-slate-700 mb-3">Komentar ({{ $post->comments->count() }})</h4>
                            <div class="space-y-3 mb-4">
                                @foreach($post->comments as $comment)
                                    <div class="flex text-sm bg-slate-50 p-3 rounded-lg">
                                        <span class="font-bold text-slate-900 mr-2">{{ $comment->user->name }}:</span>
                                        <span class="text-slate-700">{{ $comment->content }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <form wire:submit.prevent="addComment({{ $post->id }})" class="flex gap-2">
                                <input type="text" wire:model="commentContent.{{ $post->id }}" placeholder="Tulis komentar..." class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
                                <button type="submit" class="bg-slate-800 text-white px-3 py-1.5 rounded-md hover:bg-slate-900 text-sm">Kirim</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-white rounded-xl border border-slate-200">
                        <p class="text-slate-500">Belum ada postingan di club ini.</p>
                    </div>
                @endforelse
                
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
