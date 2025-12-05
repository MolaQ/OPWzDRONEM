<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Filtry i wyszukiwanie -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <!-- Rząd 1: Wyszukiwanie -->
            <div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Szukaj po treści, autorze lub poście..."
                    class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]"
                />
            </div>

            <!-- Rząd 2: Filtry -->
            <div class="flex flex-wrap items-center gap-4">
                <select wire:model.live="sortBy" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                    <option value="recent">Najnowsze</option>
                    <option value="popular">Najpopularniejsze (polubienia)</option>
                    <option value="controversial">Kontrowersyjne (nielubienia)</option>
                </select>
            </div>
        </div>

        <!-- Lista komentarzy -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
        @forelse($comments as $comment)
                <div class="overflow-hidden border-b border-neutral-200 dark:border-neutral-700 last:border-b-0">
                    <div class="px-4 py-3 flex items-start hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors gap-3">
                        <!-- Awatar -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-full border border-neutral-300 bg-neutral-200 text-neutral-900 flex items-center justify-center text-sm font-bold dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                            {{ $comment->user->initials() }}
                        </div>

                        <!-- Treść komentarza -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $comment->user->name }}</span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">•</span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                            </div>

                            <a href="{{ route('post.view', $comment->post->id) }}" target="_blank" class="text-xs text-[#880000] hover:underline mb-2 block">
                                Post: {{ Str::limit($comment->post->title, 50) }}
                            </a>

                            <p class="text-sm text-neutral-700 dark:text-neutral-200 mb-2">{{ $comment->content }}</p>

                            <div class="flex items-center gap-4 text-xs">
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
                        </div>

                        <!-- Przycisk usuwania -->
                        <div class="flex-shrink-0">
                            <button
                                wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Czy na pewno chcesz usunąć ten komentarz?"
                                class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                title="Usuń"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-neutral-500 px-6">
                <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <p class="text-lg font-medium">Brak komentarzy</p>
                <p class="text-sm mt-1">Komentarze pojawią się tutaj, gdy użytkownicy zaczną komentować posty</p>
            </div>
        @endforelse
        </div>

        <!-- Paginacja -->
        <div class="mt-6">{{ $comments->links() }}</div>
    </div>
</flux:main>
