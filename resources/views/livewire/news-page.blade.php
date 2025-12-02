<div class="max-w-7xl mx-auto p-3">
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center text-[#106c21] hover:text-[#2f76aa] transition mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Powrót do strony głównej
        </a>
        <h1 class="text-4xl font-bold text-neutral-900 dark:text-white mb-2">⚡ Aktualności</h1>
        <p class="text-neutral-600 dark:text-neutral-400">Najnowsze informacje i ogłoszenia</p>
    </div>

    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($posts as $post)
                <a href="{{ route('post.view', $post->id) }}" wire:navigate class="bg-gradient-to-br from-[#112b50] to-[#2f76aa] rounded-xl border-2 border-[#106c21] shadow-xl overflow-hidden hover:scale-105 transition-transform duration-300 block">
                    @if($post->image)
                        <div class="aspect-video w-full overflow-hidden">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'aspect-video w-full bg-gradient-to-br from-[#106c21] to-[#2f76aa] flex items-center justify-center\'><svg class=\'w-20 h-20 text-white/30\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z\' /></svg></div>'">
                        </div>
                    @else
                        <div class="aspect-video w-full bg-gradient-to-br from-[#106c21] to-[#2f76aa] flex items-center justify-center">
                            <svg class="w-20 h-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white mb-3 line-clamp-2">{{ $post->title }}</h3>

                        <div class="text-neutral-300 text-sm mb-4 line-clamp-3">
                            {!! Str::limit(strip_tags($post->content), 120) !!}
                        </div>

                        <div class="flex items-center justify-between text-xs text-neutral-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $post->author->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $post->published_at->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="flex justify-center">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-gradient-to-br from-[#112b50] to-[#2f76aa] rounded-xl border-2 border-[#106c21]">
            <svg class="w-24 h-24 mx-auto mb-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <p class="text-white text-xl font-semibold mb-2">Brak aktualności</p>
            <p class="text-neutral-400">Nowe posty pojawią się tutaj wkrótce</p>
        </div>
    @endif
</div>
