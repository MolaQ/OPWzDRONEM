<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Post;

#[Layout('components.layouts.user')]
class NewsPage extends Component
{
    use WithPagination;

    public function render()
    {
        $posts = Post::with('author')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('livewire.news-page', [
            'posts' => $posts,
        ]);
    }
}
