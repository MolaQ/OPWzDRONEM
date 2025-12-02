<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostReaction;
use App\Models\CommentReaction;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.user')]
class PostView extends Component
{
    public Post $post;
    public $newComment = '';
    public $userReaction = null;
    public $likesCount = 0;
    public $dislikesCount = 0;

    public function mount($id)
    {
        $this->post = Post::with(['author', 'comments.user', 'comments.reactions'])
            ->where('is_published', true)
            ->findOrFail($id);

        $this->loadReactionCounts();

        if (Auth::check()) {
            $reaction = PostReaction::where('user_id', Auth::id())
                ->where('post_id', $this->post->id)
                ->first();
            $this->userReaction = $reaction?->type;
        }
    }

    public function loadReactionCounts()
    {
        $this->likesCount = $this->post->likes()->count();
        $this->dislikesCount = $this->post->dislikes()->count();
    }

    public function react($type)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $existing = PostReaction::where('user_id', Auth::id())
            ->where('post_id', $this->post->id)
            ->first();

        if ($existing) {
            if ($existing->type === $type) {
                // Remove reaction if clicking same button
                $existing->delete();
                $this->userReaction = null;
            } else {
                // Change reaction
                $existing->update(['type' => $type]);
                $this->userReaction = $type;
            }
        } else {
            // Create new reaction
            PostReaction::create([
                'user_id' => Auth::id(),
                'post_id' => $this->post->id,
                'type' => $type,
            ]);
            $this->userReaction = $type;
        }

        $this->loadReactionCounts();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $this->post->id,
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->post->refresh();
        $this->post->load(['comments.user', 'comments.reactions']);
    }

    public function reactToComment($commentId, $type)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $existing = CommentReaction::where('user_id', Auth::id())
            ->where('comment_id', $commentId)
            ->first();

        if ($existing) {
            if ($existing->type === $type) {
                $existing->delete();
            } else {
                $existing->update(['type' => $type]);
            }
        } else {
            CommentReaction::create([
                'user_id' => Auth::id(),
                'comment_id' => $commentId,
                'type' => $type,
            ]);
        }

        $this->post->refresh();
        $this->post->load(['comments.user', 'comments.reactions']);
    }

    public function render()
    {
        return view('livewire.post-view');
    }
}
