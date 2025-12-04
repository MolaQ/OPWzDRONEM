<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Posts extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $is_published = '';
    public $reactionFilter = ''; // all, liked, disliked, no_reactions
    public $commentFilter = ''; // all, commented, no_comments
    public $showModal = false;
    public $image;
    public $editingPost = [
        'id' => null,
        'title' => '',
        'content' => '',
        'is_published' => false,
        'published_at' => null,
        'image' => null,
    ];

    protected $updatesQueryString = [
        'search', 'is_published', 'reactionFilter', 'commentFilter', 'page'
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingIsPublished() { $this->resetPage(); }
    public function updatingReactionFilter() { $this->resetPage(); }
    public function updatingCommentFilter() { $this->resetPage(); }

    public function showCreateModal()
    {
        $this->editingPost = [
            'id' => null,
            'title' => '',
            'content' => '',
            'is_published' => false,
            'published_at' => null,
            'image' => null,
        ];
        $this->image = null;
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function editPost($id)
    {
        $post = Post::findOrFail($id);
        $this->editingPost = [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'is_published' => $post->is_published,
            'published_at' => $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : null,
            'image' => $post->image,
        ];
        $this->image = null;
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function savePost()
    {
        $rules = [
            'editingPost.title' => 'required|string|max:255',
            'editingPost.content' => 'required|string',
            'editingPost.is_published' => 'required|boolean',
            'editingPost.published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048', // 2MB max
        ];

        $this->validate($rules);

        $data = $this->editingPost;

        // Handle image upload
        if ($this->image) {
            // Delete old image if exists
            if ($this->editingPost['id']) {
                $oldPost = Post::find($this->editingPost['id']);
                if ($oldPost && $oldPost->image) {
                    Storage::disk('public')->delete($oldPost->image);
                }
            }

            $data['image'] = $this->image->store('posts', 'public');
        }

        // Set published_at automatically if publishing
        if ($data['is_published'] && !$data['published_at']) {
            $data['published_at'] = now();
        }

        if ($this->editingPost['id']) {
            $post = Post::find($this->editingPost['id']);
            $post->update($data);
            $msg = 'Post został zaktualizowany!';
        } else {
            $data['author_id'] = Auth::id();
            Post::create($data);
            $msg = 'Post został dodany!';
        }

        $this->dispatch('notify', type: 'success', message: $msg);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->image = null;
        $this->editingPost = [
            'id' => null,
            'title' => '',
            'content' => '',
            'is_published' => false,
            'published_at' => null,
            'image' => null,
        ];
        $this->resetValidation();
        $this->dispatch('modalClosed');
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        // Delete image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
        $this->dispatch('notify', type: 'success', message: 'Post został usunięty!');
    }

    public function togglePublish($id)
    {
        $post = Post::findOrFail($id);
        $post->is_published = !$post->is_published;

        if ($post->is_published && !$post->published_at) {
            $post->published_at = now();
        }

        $post->save();

        $status = $post->is_published ? 'opublikowany' : 'ukryty';
        $this->dispatch('notify', type: 'success', message: "Post został {$status}!");
    }

    public function render()
    {
        $posts = Post::with(['author', 'reactions'])
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                      ->orWhere('content', 'like', "%{$this->search}%")
                      ->orWhereHas('author', function($authorQuery) {
                          $authorQuery->where('name', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->is_published !== '', fn($q) => $q->where('is_published', $this->is_published))
            ->when($this->reactionFilter === 'liked', function($q) {
                $q->whereHas('reactions', function($query) {
                    $query->where('type', 'like');
                });
            })
            ->when($this->reactionFilter === 'disliked', function($q) {
                $q->whereHas('reactions', function($query) {
                    $query->where('type', 'dislike');
                });
            })
            ->when($this->reactionFilter === 'no_reactions', function($q) {
                $q->whereDoesntHave('reactions');
            })
            ->when($this->commentFilter === 'commented', function($q) {
                $q->whereHas('comments');
            })
            ->when($this->commentFilter === 'no_comments', function($q) {
                $q->whereDoesntHave('comments');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.posts', [
            'posts' => $posts,
        ]);
    }
}
