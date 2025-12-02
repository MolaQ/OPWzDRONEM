<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Post;

#[Layout('components.layouts.user')]
class PostView extends Component
{
    public Post $post;

    public function mount($id)
    {
        $this->post = Post::with('author')
            ->where('is_published', true)
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.post-view');
    }
}
