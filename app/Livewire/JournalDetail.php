<?php

namespace App\Livewire;

use App\Models\Journal;
use App\Models\JournalComment;
use App\Models\JournalReaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class JournalDetail extends Component
{
    public Journal $journal;

    // Reaksi
    public const EMOJIS = ['👍', '❤️', '🔥', '😮', '🤩'];

    public ?string $myReaction = null;

    // Komentar
    public string $newComment = '';

    public function mount(Journal $journal): void
    {
        // Akses kontrol
        if ($journal->visibility === 'private' && ! $journal->isOwnedBy(Auth::id())) {
            abort(403, 'Jurnal ini bersifat pribadi.');
        }

        $this->journal = $journal;
        $this->myReaction = JournalReaction::where('journal_id', $journal->id)
            ->where('user_id', Auth::id())
            ->value('emoji');
    }

    // ── Reaksi ───────────────────────────────────────────────

    /**
     * Toggle reaksi: jika emoji sama → cabut, jika beda → ganti, jika belum ada → tambah.
     */
    public function react(string $emoji): void
    {
        if (! in_array($emoji, self::EMOJIS, true)) {
            return;
        }

        $existing = JournalReaction::where('journal_id', $this->journal->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            if ($existing->emoji === $emoji) {
                // Cabut reaksi
                $existing->delete();
                $this->myReaction = null;
            } else {
                // Ganti reaksi
                $existing->update(['emoji' => $emoji]);
                $this->myReaction = $emoji;
            }
        } else {
            // Tambah reaksi baru
            JournalReaction::create([
                'journal_id' => $this->journal->id,
                'user_id' => Auth::id(),
                'emoji' => $emoji,
            ]);
            $this->myReaction = $emoji;
        }

        // Muat ulang jurnal agar counter reaksi ikut diperbarui
        $this->journal->load('reactions');
    }

    // ── Komentar ─────────────────────────────────────────────

    public function postComment(): void
    {
        $this->validate(['newComment' => 'required|string|max:1000']);

        JournalComment::create([
            'journal_id' => $this->journal->id,
            'user_id' => Auth::id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->journal->load('comments.user');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = JournalComment::find($commentId);

        if (! $comment) {
            return;
        }

        // Hanya pemilik komentar atau pemilik jurnal yang boleh hapus
        if ($comment->user_id !== Auth::id() && ! $this->journal->isOwnedBy(Auth::id())) {
            return;
        }

        $comment->delete();
        $this->journal->load('comments.user');
    }

    // ── Hapus jurnal ─────────────────────────────────────────

    public function delete(): void
    {
        if (! $this->journal->isOwnedBy(Auth::id())) {
            abort(403);
        }

        // Hapus semua foto dari storage
        foreach ($this->journal->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $this->journal->delete();

        session()->flash('message', 'Jurnal berhasil dihapus.');
        $this->redirect(route('my.journals'), navigate: true);
    }

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        $this->journal->loadMissing(['user', 'casting', 'photos', 'reactions', 'comments.user']);

        // Hitung reaksi per emoji
        $reactionCounts = collect(self::EMOJIS)->mapWithKeys(function ($emoji) {
            return [$emoji => $this->journal->reactions->where('emoji', $emoji)->count()];
        });

        return view('livewire.journal-detail', [
            'reactionCounts' => $reactionCounts,
            'isOwner' => $this->journal->isOwnedBy(Auth::id()),
        ]);
    }
}
