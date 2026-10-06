<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Club Feed') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Create Post -->
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <form wire:submit.prevent="createPost">
                <textarea wire:model="content" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" placeholder="What's on your mind?"></textarea>
                @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <div class="mt-2 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Post</button>
                </div>
            </form>
        </div>

        <!-- Filter -->
        <div class="flex gap-4">
            <button wire:click="$set('filter', 'all')" class="px-4 py-2 rounded-md {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">All Posts</button>
            <button wire:click="$set('filter', 'following')" class="px-4 py-2 rounded-md {{ $filter === 'following' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700' }}">Following</button>
        </div>

        {{-- Feed --}}
        <div class="space-y-4">
            @forelse($feedItems as $entry)

                @if ($entry['type'] === 'post')
                    @php $post = $entry['item']; @endphp
                    <div class="bg-white p-6 shadow sm:rounded-lg" wire:key="post-{{ $post->id }}">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500">
                                    {{ substr($post->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold">{{ $post->user->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if($post->user_id !== auth()->id())
                                <button wire:click="toggleFollow({{ $post->user_id }})" class="text-sm {{ in_array($post->user_id, $followingIds) ? 'text-gray-500' : 'text-blue-600 font-semibold' }}">
                                    {{ in_array($post->user_id, $followingIds) ? 'Following' : 'Follow' }}
                                </button>
                            @endif
                        </div>

                        <div class="mt-4">
                            <p class="whitespace-pre-wrap">{{ $post->content }}</p>
                        </div>

                        @if($post->collectionItem)
                            <div class="mt-4 p-4 border rounded-md bg-gray-50">
                                <p class="text-sm font-semibold text-gray-700">Showcasing:</p>
                                <p>{{ $post->collectionItem->casting->name }} ({{ $post->collectionItem->condition }})</p>
                            </div>
                        @endif

                        {{-- Comments --}}
                        <div class="mt-6 border-t pt-4">
                            <h5 class="text-sm font-semibold text-gray-700 mb-2">Comments</h5>
                            <div class="space-y-3 mb-3">
                                @foreach($post->comments as $comment)
                                    <div class="bg-gray-50 p-2 rounded text-sm">
                                        <span class="font-bold">{{ $comment->user->name }}:</span> {{ $comment->content }}
                                    </div>
                                @endforeach
                            </div>
                            <form wire:submit.prevent="addComment({{ $post->id }})" class="flex gap-2">
                                <input wire:model="commentContent.{{ $post->id }}" type="text" class="flex-1 text-sm border-gray-300 rounded-md" placeholder="Write a comment...">
                                <button type="submit" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm">Reply</button>
                            </form>
                        </div>
                    </div>

                @elseif ($entry['type'] === 'journal')
                    @php $journal = $entry['item']; @endphp
                    <a href="{{ route('journals.show', $journal) }}"
                       wire:navigate
                       wire:key="journal-{{ $journal->id }}"
                       class="block bg-white shadow sm:rounded-lg overflow-hidden hover:shadow-md transition">

                        {{-- Badge jurnal --}}
                        <div class="px-6 pt-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center font-bold text-red-600">
                                    {{ substr($journal->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-gray-800">{{ $journal->user->name }}</h4>
                                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">📔 Jurnal</span>
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $journal->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Cover photo --}}
                        @if ($journal->coverPhoto())
                            <img src="{{ $journal->coverPhoto()->url }}"
                                 alt="Foto jurnal {{ $journal->title }}"
                                 loading="lazy"
                                 decoding="async"
                                 width="800"
                                 height="192"
                                 class="mt-3 w-full h-48 object-cover">
                        @endif

                        <div class="p-6 pt-3 space-y-1">
                            <h3 class="font-semibold text-gray-900">{{ $journal->title }}</h3>
                            <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                                <span>📅 {{ $journal->acquired_at->translatedFormat('d M Y') }}</span>
                                <span>🛒 {{ $journal->acquisitionLabel() }}</span>
                                @if ($journal->place_name)
                                    <span>📍 {{ $journal->place_name }}</span>
                                @endif
                                @if ($journal->casting)
                                    <span>🚗 {{ $journal->casting->name }}</span>
                                @endif
                            </div>
                            @if ($journal->story)
                                <p class="text-sm text-gray-600 line-clamp-2 mt-1">{{ $journal->story }}</p>
                            @endif

                            {{-- Ringkasan reaksi --}}
                            @if ($journal->reactions->count())
                                <div class="flex gap-2 pt-1 text-sm">
                                    @foreach (\App\Livewire\JournalDetail::EMOJIS as $emoji)
                                        @php $count = $journal->reactions->where('emoji', $emoji)->count(); @endphp
                                        @if ($count > 0)
                                            <span class="text-gray-500">{{ $emoji }} {{ $count }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </a>
                @endif

            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-lg">Belum ada postingan atau jurnal di feed.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
