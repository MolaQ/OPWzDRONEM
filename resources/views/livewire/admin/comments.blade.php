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

    <div class="grid grid-cols-1 gap-4 overflow-x-auto">
        @forelse($comments as $comment)
            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors min-w-0">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-sm font-bold">
                            {{ $comment->user->initials() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $comment->user->name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 flex flex-wrap items-center gap-1.5">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $comment->created_at->format('d.m.Y H:i') }}
                                </span>
                                <span>·</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Post: <a href="{{ route('post.view', $comment->post->id) }}" target="_blank" class="text-[#880000] hover:underline font-medium">{{ Str::limit($comment->post->title, 40) }}</a>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-neutral-700 dark:text-neutral-200 mb-3 pl-13">{{ $comment->content }}</p>

                <div class="flex items-center justify-between gap-4 pl-13">
                    <div class="flex flex-wrap items-center gap-3 text-xs text-neutral-500 dark:text-neutral-400">
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                            {{ $comment->likes()->count() }}
                        </span>
                        <span class="flex items-center gap-1 text-red-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                            </svg>
                            {{ $comment->dislikes()->count() }}
                        </span>
                    </div>

                    <button
                        onclick="if(confirm('Czy na pewno chcesz usunąć ten komentarz?')) { @this.deleteComment({{ $comment->id }}) }"
                        class="text-[#880000] hover:text-red-700 font-semibold text-sm"
                    >
                        Usuń
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-neutral-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <p class="text-lg font-medium">Brak komentarzy</p>
                <p class="text-sm mt-1">Komentarze pojawią się tutaj, gdy użytkownicy zaczną komentować posty</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $comments->links() }}</div>
</div>
