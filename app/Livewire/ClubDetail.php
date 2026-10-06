<?php

namespace App\Livewire;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ClubDetail extends Component
{
    use WithPagination;

    public Club $club;

    public $content = '';

    public $commentContent = [];

    public function mount(Club $club)
    {
        $this->club = $club->loadCount('members');
    }

    public function getTitle()
    {
        return $this->club->name.' - Club';
    }

    public function createPost()
    {
        $this->validate(['content' => 'required|max:1000']);
        if (! $this->isMember()) {
            return;
        }

        Post::create([
            'user_id' => Auth::id(),
            'club_id' => $this->club->id,
            'content' => $this->content,
        ]);

        $this->reset('content');
    }

    public function addComment($postId)
    {
        if (empty(trim($this->commentContent[$postId] ?? ''))) {
            return;
        }
        if (! $this->isMember()) {
            return;
        }

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $this->commentContent[$postId],
        ]);

        $this->commentContent[$postId] = '';
    }

    public function isMember()
    {
        if (! Auth::check()) {
            return false;
        }

        return $this->club->members()->where('user_id', Auth::id())->exists();
    }

    public function getMyRole()
    {
        if (! Auth::check()) {
            return null;
        }
        $member = ClubMember::where('club_id', $this->club->id)->where('user_id', Auth::id())->first();

        return $member ? $member->club_role : null;
    }

    public function render()
    {
        $posts = Post::with(['user', 'comments.user', 'collectionItem.casting'])
            ->where('club_id', $this->club->id)
            ->latest()
            ->paginate(15);

        return view('livewire.club-detail', [
            'posts' => $posts,
            'isMember' => $this->isMember(),
            'myRole' => $this->getMyRole(),
        ]);
    }
}
