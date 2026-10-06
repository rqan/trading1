<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Journal;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Club Feed')]
class ClubFeed extends Component
{
    public $content = '';

    public $commentContent = [];

    public $filter = 'all'; // all, following

    public function createPost()
    {
        $this->validate(['content' => 'required|max:1000']);

        Post::create([
            'user_id' => Auth::id(),
            'content' => $this->content,
            // default club_id null for global feed (can be modified later)
        ]);

        $this->reset('content');
    }

    public function addComment($postId)
    {
        if (empty(trim($this->commentContent[$postId] ?? ''))) {
            return;
        }

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $this->commentContent[$postId],
        ]);

        $this->commentContent[$postId] = '';
    }

    public function toggleFollow($userId)
    {
        $user = Auth::user();
        if ($user->following()->where('followed_id', $userId)->exists()) {
            $user->following()->detach($userId);
        } else {
            $user->following()->attach($userId);
        }
    }

    public function render()
    {
        $postQuery = Post::with(['user', 'comments.user', 'collectionItem.casting'])->latest();

        $journalQuery = Journal::with(['user', 'casting', 'photos', 'reactions'])
            ->inFeed();

        if ($this->filter === 'following') {
            $followedIds = Auth::user()->following()->pluck('followed_id');
            $postQuery->whereIn('user_id', $followedIds);
            $journalQuery->whereIn('user_id', $followedIds);
        }

        // Gabungkan posts dan jurnal lalu urutkan berdasarkan created_at terbaru.
        // Ambil 30 posts terbaru + 20 jurnal terbaru, merge, sort, ambil 30 teratas.
        $posts = $postQuery->limit(30)->get()->map(fn ($p) => ['type' => 'post', 'item' => $p, 'created_at' => $p->created_at]);
        $journals = $journalQuery->limit(20)->get()->map(fn ($j) => ['type' => 'journal', 'item' => $j, 'created_at' => $j->created_at]);

        $feedItems = $posts->concat($journals)
            ->sortByDesc('created_at')
            ->take(30)
            ->values();

        $followingIds = Auth::user()->following()->pluck('followed_id')->toArray();

        return view('livewire.club-feed', [
            'feedItems' => $feedItems,
            'followingIds' => $followingIds,
        ]);
    }
}
