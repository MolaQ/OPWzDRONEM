<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Comment;

class Comments extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'recent'; // recent, popular, controversial

    protected $updatesQueryString = ['search', 'sortBy', 'page'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function deleteComment($id)
    {
        Comment::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Komentarz został usunięty!');
    }

    public function render()
    {
        $query = Comment::with(['user', 'post', 'reactions'])
            ->when($this->search, function ($q) {
                $q->where('content', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('post', function ($postQuery) {
                        $postQuery->where('title', 'like', "%{$this->search}%");
                    });
            });

        // Apply sorting
        switch ($this->sortBy) {
            case 'popular':
                $query->withCount(['reactions as likes_count' => function ($q) {
                    $q->where('type', 'like');
                }])->orderByDesc('likes_count');
                break;
            case 'controversial':
                $query->withCount(['reactions as dislikes_count' => function ($q) {
                    $q->where('type', 'dislike');
                }])->orderByDesc('dislikes_count');
                break;
            default: // recent
                $query->orderByDesc('created_at');
                break;
        }

        $comments = $query->paginate(20);

        return view('livewire.admin.comments', [
            'comments' => $comments,
        ]);
    }
}
