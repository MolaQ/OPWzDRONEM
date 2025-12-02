<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class News extends Component
{
    use WithPagination;

    public function render()
    {
        $posts = Post::with('author')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(6);

        return view('livewire.news', [
            'posts' => $posts,
        ]);
    }
}
