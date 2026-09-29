<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CommunityFeed extends Component
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
        ]);
        
        $this->reset('content');
    }

    public function addComment($postId)
    {
        if (empty(trim($this->commentContent[$postId] ?? ''))) return;

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
        $query = Post::with(['user', 'comments.user', 'collectionItem.casting'])->latest();

        if ($this->filter === 'following') {
            $followedIds = Auth::user()->following()->pluck('followed_id');
            $query->whereIn('user_id', $followedIds);
        }

        // Query Optimization: eager load with counts/aggregates if needed
        $posts = $query->paginate(20);
        $followingIds = Auth::user()->following()->pluck('followed_id')->toArray();

        return view('livewire.community-feed', [
            'posts' => $posts,
            'followingIds' => $followingIds,
        ]);
    }
}
