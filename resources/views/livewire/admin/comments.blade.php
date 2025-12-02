<div class="flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-6 shadow">

    <div class="mb-6 flex flex-wrap items-center gap-4 justify-between">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Szukaj po treści, autorze lub poście..."
            class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 shadow-sm focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100"
        />

        <select wire:model.live="sortBy" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="recent">Najnowsze</option>
            <option value="popular">Najpopularniejsze (polubienia)</option>
            <option value="controversial">Kontrowersyjne (nielubienia)</option>
        </select>
    </div>

    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4 border border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $comment->user->name }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ $comment->created_at->format('d.m.Y H:i') }} ·
                            Post: <a href="{{ route('post.view', $comment->post->id) }}" target="_blank" class="text-[#106c21] hover:underline">{{ Str::limit($comment->post->title, 40) }}</a>
                        </p>
                    </div>
                    <button
                        onclick="if(confirm('Czy na pewno chcesz usunąć ten komentarz?')) { @this.deleteComment({{ $comment->id }}) }"
                        class="text-red-600 hover:text-red-700 font-semibold text-sm"
                    >
                        Usuń
                    </button>
                </div>

                <p class="text-neutral-700 dark:text-neutral-200 mb-3">{{ $comment->content }}</p>

                <div class="flex items-center gap-4 text-sm text-neutral-600 dark:text-neutral-400">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        <span>{{ $comment->likes()->count() }} polubień</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                        </svg>
                        <span>{{ $comment->dislikes()->count() }} nielubień</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                Brak komentarzy.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $comments->links() }}
    </div>
</div>
