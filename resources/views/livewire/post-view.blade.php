<div class="max-w-4xl mx-auto p-3">
    <div class="mb-4">
        <a href="{{ route('home') }}" class="inline-flex items-center text-[#106c21] hover:text-[#2f76aa] transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Powrót do strony głównej
        </a>
    </div>

    <article class="bg-gradient-to-br from-[#112b50] to-[#2f76aa] rounded-xl border-2 border-[#106c21] shadow-2xl overflow-hidden">
        @if($post->image)
            <div class="w-full aspect-video overflow-hidden">
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'aspect-video bg-gradient-to-br from-[#106c21] to-[#2f76aa] flex items-center justify-center\'><svg class=\'w-20 h-20 text-white/30\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg></div>'">
            </div>
        @endif

        <div class="p-6">
            <h1 class="text-4xl font-bold text-white mb-4">{{ $post->title }}</h1>

            <div class="flex items-center gap-4 text-sm text-neutral-300 mb-6 pb-4 border-b border-neutral-600">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ $post->author->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $post->published_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>

            <div class="prose prose-invert prose-lg max-w-none mb-6">
                <div class="text-neutral-100 leading-relaxed" style="line-height: 1.8; font-size: 1.1rem;">
                    {!! $post->content !!}
                </div>
            </div>

            <!-- Reactions Section -->
            @auth
                <div class="flex items-center gap-4 py-4 border-t border-neutral-600">
                    <button
                        wire:click="react('like')"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition {{ $userReaction === 'like' ? 'bg-green-600 text-white' : 'bg-neutral-700 text-neutral-300 hover:bg-neutral-600' }}"
                    >
                        <svg class="w-5 h-5" fill="{{ $userReaction === 'like' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        <span class="font-semibold">{{ $likesCount }}</span>
                    </button>
                    <button
                        wire:click="react('dislike')"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition {{ $userReaction === 'dislike' ? 'bg-red-600 text-white' : 'bg-neutral-700 text-neutral-300 hover:bg-neutral-600' }}"
                    >
                        <svg class="w-5 h-5" fill="{{ $userReaction === 'dislike' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                        </svg>
                        <span class="font-semibold">{{ $dislikesCount }}</span>
                    </button>
                </div>
            @else
                <div class="flex items-center gap-4 py-4 border-t border-neutral-600">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-neutral-700 text-neutral-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        <span class="font-semibold">{{ $likesCount }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-neutral-700 text-neutral-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                        </svg>
                        <span class="font-semibold">{{ $dislikesCount }}</span>
                    </div>
                </div>
                <div class="py-3 px-4 bg-blue-900/30 border border-blue-700/50 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-blue-200 text-sm">
                        <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-100 underline font-medium">Zaloguj się</a>, aby polubić lub skomentować ten post.
                    </p>
                </div>
            @endauth
        </div>
    </article>

    <!-- Comments Section -->
    <div class="mt-6 bg-gradient-to-br from-[#112b50] to-[#2f76aa] rounded-xl border-2 border-[#106c21] shadow-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Komentarze ({{ $post->comments->count() }})</h2>

        @auth
            <form wire:submit.prevent="addComment" class="mb-6">
                <textarea
                    wire:model="newComment"
                    rows="3"
                    placeholder="Dodaj komentarz..."
                    class="w-full px-4 py-3 rounded-lg bg-neutral-800 text-white border border-neutral-600 focus:border-[#106c21] focus:ring-2 focus:ring-[#106c21] transition"
                ></textarea>
                @error('newComment') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                <button type="submit" class="mt-2 bg-[#106c21] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#2f76aa] transition">
                    Dodaj komentarz
                </button>
            </form>

            <div class="space-y-4">
            @forelse($post->comments as $comment)
                <div class="bg-neutral-800/50 rounded-lg p-4 border border-neutral-700">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-[#106c21] flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white font-semibold">{{ $comment->user->name }}</p>
                                <p class="text-xs text-neutral-400">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-neutral-200 mb-3">{{ $comment->content }}</p>

                    @auth
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="reactToComment({{ $comment->id }}, 'like')"
                                class="flex items-center gap-1 text-sm {{ $comment->reactions->where('user_id', auth()->id())->where('type', 'like')->count() ? 'text-green-400' : 'text-neutral-400 hover:text-green-400' }} transition"
                            >
                                <svg class="w-4 h-4" fill="{{ $comment->reactions->where('user_id', auth()->id())->where('type', 'like')->count() ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span>{{ $comment->likes()->count() }}</span>
                            </button>
                            <button
                                wire:click="reactToComment({{ $comment->id }}, 'dislike')"
                                class="flex items-center gap-1 text-sm {{ $comment->reactions->where('user_id', auth()->id())->where('type', 'dislike')->count() ? 'text-red-400' : 'text-neutral-400 hover:text-red-400' }} transition"
                            >
                                <svg class="w-4 h-4" fill="{{ $comment->reactions->where('user_id', auth()->id())->where('type', 'dislike')->count() ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                </svg>
                                <span>{{ $comment->dislikes()->count() }}</span>
                            </button>
                        </div>
                    @endauth
                </div>
            @empty
                <p class="text-neutral-400 text-center py-8">Brak komentarzy. Bądź pierwszy!</p>
            @endforelse
            </div>
        @else
            <div class="py-4 px-4 bg-blue-900/30 border border-blue-700/50 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-blue-200 text-sm">
                    <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-100 underline font-medium">Zaloguj się</a>, aby zobaczyć i dodać komentarze.
                </p>
            </div>
        @endauth
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" class="inline-block bg-[#106c21] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#2f76aa] transition shadow-lg">
            Zobacz więcej postów
        </a>
    </div>
</div>


