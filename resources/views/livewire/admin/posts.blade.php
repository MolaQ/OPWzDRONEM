<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Filtry i wyszukiwanie -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <!-- Rząd 1: Wyszukiwanie -->
            <div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Szukaj po tytule, treści lub autorze..."
                    class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]"
                />
            </div>

            <!-- Rząd 2: Filtry i przyciski -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <select wire:model.live="is_published" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie posty —</option>
                        <option value="1">Opublikowane</option>
                        <option value="0">Nieopublikowane</option>
                    </select>

                    <select wire:model.live="reactionFilter" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie reakcje —</option>
                        <option value="liked">Lubiane</option>
                        <option value="disliked">Nielubiane</option>
                        <option value="no_reactions">Bez reakcji</option>
                    </select>

                    <select wire:model.live="commentFilter" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie komentarze —</option>
                        <option value="commented">Komentowane</option>
                        <option value="no_comments">Bez komentarzy</option>
                    </select>
                </div>

                <button wire:click="showCreateModal" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm border border-blue-500/70 dark:border-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj post
                </button>
            </div>
        </div>

        <!-- Lista postów -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
            @forelse($posts as $post)
            <div wire:key="post-{{ $post->id }}"
            <div wire:key="post-{{ $post->id }}" class="overflow-hidden border-b border-neutral-200 dark:border-neutral-700 last:border-b-0">
                    <div class="px-4 py-3 flex items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors gap-3">
                        <!-- Kolumna 1: Status publikacji -->
                        <div class="w-5 flex justify-center flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $post->is_published ? 'bg-green-600' : 'bg-orange-500' }} text-white text-xs font-bold" title="{{ $post->is_published ? 'Opublikowany' : 'Szkic' }}">
                                {{ $post->is_published ? '✓' : '○' }}
                            </span>
                        </div>

                        <!-- Kolumna 2: Informacje o poście -->
                        <div class="ml-2 flex-1 min-w-0">
                            <div class="font-semibold text-neutral-900 dark:text-white truncate uppercase">{{ $post->title }}</div>
                            @if($post->content)
                                <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1 line-clamp-1">{{ strip_tags(Str::limit(strip_tags($post->content), 100)) }}</div>
                            @endif
                            <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">
                                {{ $post->author->name }} • {{ $post->created_at->format('d.m.Y H:i') }}
                                @if($post->published_at)
                                    • Opublikowano: {{ $post->published_at->format('d.m.Y H:i') }}
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                                <span class="flex items-center gap-1 text-green-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    {{ $post->likes()->count() }}
                                </span>
                                <span class="flex items-center gap-1 text-red-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                    </svg>
                                    {{ $post->dislikes()->count() }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    {{ $post->comments()->count() }}
                                </span>
                            </div>
                        </div>

                        <!-- Kolumna 3: Akcje -->
                        <div class="flex items-center gap-2 flex-shrink-0 px-3">
                            <button type="button" wire:click="editPost({{ $post->id }})" class="inline-flex items-center justify-center w-7 h-7 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors" title="Edytuj">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                            </button>
                            <button type="button" wire:click="deletePost({{ $post->id }})" wire:confirm="Czy na pewno usunąć ten post?" class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Usuń">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-neutral-500 px-6">
                    <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg font-medium">Brak postów</p>
                    <p class="text-sm mt-1">Dodaj pierwszy post klikając przycisk powyżej</p>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        <div class="mt-6">{{ $posts->links() }}</div>

        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeModal">
                <div class="flex items-start justify-center min-h-full px-4">
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 flex items-center justify-between rounded-t-2xl">
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-white">
                                {{ $editingPost['id'] ? '✏️ Edytuj post' : '✨ Dodaj nowy post' }}
                            </h3>
                            <button wire:click="closeModal" type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 rounded-lg p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="px-6 py-5">
                        <form wire:submit.prevent="savePost" class="space-y-5" id="postForm">
                    <!-- Tytuł -->
                    <div>
                        <label for="title" class="block mb-1.5 text-sm font-medium text-neutral-700 dark:text-neutral-200">
                            Tytuł <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            type="text"
                            wire:model="editingPost.title"
                            placeholder="Wprowadź tytuł postu..."
                            class="w-full rounded-lg bg-white border border-neutral-300 px-3 py-2 text-sm text-neutral-900 placeholder-neutral-400 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-100"
                        />
                        @error('editingPost.title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Treść -->
                    <div wire:ignore>
                        <label for="content" class="block mb-1.5 text-sm font-medium text-neutral-700 dark:text-neutral-200">
                            Treść <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="content"
                            wire:model="editingPost.content"
                            class="tinymce-editor"
                        ></textarea>
                        @error('editingPost.content')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Separator -->
                    <div class="border-t border-neutral-200 dark:border-neutral-700"></div>

                    <!-- Obrazek -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Obrazek miniaturki
                        </h4>

                                <div class="flex gap-4 items-start">
                                    @if($editingPost['image'] && !$image)
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . $editingPost['image']) }}" alt="Current" class="w-24 h-24 object-cover rounded-lg border-2 border-neutral-200 dark:border-neutral-600">
                                            <p class="text-xs text-neutral-500 mt-1 text-center">Aktualny</p>
                                        </div>
                                    @endif

                                    @if($image)
                                        <div class="flex-shrink-0">
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-blue-500">
                                            <p class="text-xs text-blue-600 dark:text-blue-300 mt-1 text-center font-medium">Nowy</p>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <input
                                            id="image"
                                            type="file"
                                            wire:model="image"
                                            accept="image/*"
                                            class="w-full rounded-lg bg-white border border-neutral-300 px-3 py-2 text-sm text-neutral-900 file:mr-3 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-600 file:text-white file:cursor-pointer hover:file:bg-blue-700 transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-100"
                                        />
                                        <p class="text-xs text-neutral-500 mt-2 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="leading-tight text-neutral-600 dark:text-neutral-300">Maksymalny rozmiar: 2MB (JPG, PNG, GIF)</span>
                                        </p>
                                        @error('image')<p class="mt-1.5 text-xs text-red-400 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="leading-tight">{{ $message }}</span>
                                        </p>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Separator -->
                            <div class="border-t border-neutral-200 dark:border-neutral-700"></div>

                            <!-- Sekcja: Ustawienia publikacji -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Ustawienia publikacji
                                </h4>

                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Status publikacji -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-200">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="flex items-center gap-2.5 p-3 bg-neutral-50 border-2 border-neutral-200 rounded-lg cursor-pointer transition hover:border-green-600 has-[:checked]:border-green-600 has-[:checked]:bg-green-50 dark:bg-neutral-800 dark:border-neutral-700 dark:has-[:checked]:bg-green-950/30">
                                                <input
                                                    type="radio"
                                                    wire:model="editingPost.is_published"
                                                    value="1"
                                                    class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                                />
                                                <span class="flex items-center gap-2 text-sm font-semibold text-neutral-800 dark:text-neutral-300">
                                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                    Opublikowany
                                                </span>
                                            </label>
                                            <label class="flex items-center gap-2.5 p-3 bg-neutral-50 border-2 border-neutral-200 rounded-lg cursor-pointer transition hover:border-yellow-600 has-[:checked]:border-yellow-600 has-[:checked]:bg-yellow-50 dark:bg-neutral-800 dark:border-neutral-700 dark:has-[:checked]:bg-yellow-950/30">
                                                <input
                                                    type="radio"
                                                    wire:model="editingPost.is_published"
                                                    value="0"
                                                    class="w-4 h-4 text-yellow-600 focus:ring-yellow-500 focus:ring-2"
                                                />
                                                <span class="flex items-center gap-2 text-sm font-semibold text-neutral-800 dark:text-neutral-300">
                                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                                    Szkic
                                                </span>
                                            </label>
                                        </div>
                                        @error('editingPost.is_published')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                                    </div>

                                    <!-- Data publikacji -->
                                    <div>
                                        <label for="published_at" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-200">
                                            Data publikacji
                                        </label>
                                        <input
                                            id="published_at"
                                            type="datetime-local"
                                            wire:model="editingPost.published_at"
                                            class="w-full rounded-lg bg-white border border-neutral-300 px-4 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-100"
                                        />
                                        <p class="text-xs text-neutral-500 mt-1.5">Zostaw puste dla automatycznej daty publikacji</p>
                                        @error('editingPost.published_at')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>

                        <!-- Footer -->
                        <div class="bg-white dark:bg-neutral-900 px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-3 rounded-b-2xl">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-5 py-2.5 rounded-lg border border-neutral-300 bg-white text-neutral-700 text-sm font-medium hover:bg-neutral-50 transition dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            form="postForm"
                            class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm border border-blue-500/70 dark:border-blue-500 transition flex items-center gap-2"
                        >
                            @if($editingPost['id'])
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Zapisz zmiany
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Dodaj post
                            @endif
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>

<!-- TinyMCE Local -->
<script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

<script>
    let editorInitialized = false;

    document.addEventListener('livewire:init', () => {
        // Nasłuchuj na zamknięcie modala z Livewire
        Livewire.on('modalClosed', () => {
            cleanupEditor();
        });

        // Nasłuchuj na otwarcie modala
        Livewire.on('open-modal', () => {
            console.log('Modal opening event received');
            // Poczekaj na renderowanie modala
            setTimeout(() => {
                const editor = document.querySelector('.tinymce-editor');
                if (editor && !editorInitialized) {
                    console.log('Initializing TinyMCE after modal open');
                    cleanupEditor();
                    setTimeout(() => {
                        initTinyMCE();
                    }, 150);
                }
            }, 100);
        });
    });

    function cleanupEditor() {
        if (typeof tinymce !== 'undefined') {
            tinymce.remove('.tinymce-editor');
            editorInitialized = false;
            console.log('TinyMCE cleaned up');
        }
    }

    // Obsługa otwierania modala
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) {
                            const editor = node.querySelector('.tinymce-editor');
                            if (editor && !editorInitialized) {
                                editorInitialized = true;
                                // Wyczyść poprzednie instancje
                                cleanupEditor();
                                setTimeout(() => {
                                    initTinyMCE();
                                }, 150);
                            }
                        }
                    });
                }

                // Wykryj usunięcie modala
                if (mutation.removedNodes.length) {
                    mutation.removedNodes.forEach((node) => {
                        if (node.nodeType === 1 && (node.classList?.contains('fixed') || node.querySelector?.('.tinymce-editor'))) {
                            cleanupEditor();
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    function initTinyMCE() {
        // Poczekaj aż TinyMCE się załaduje
        const checkTinyMCE = setInterval(() => {
            if (typeof tinymce !== 'undefined') {
                clearInterval(checkTinyMCE);

                tinymce.init({
                        selector: '.tinymce-editor',
                        license_key: 'gpl',
                        promotion: false,
                        skin: 'oxide-dark',
                        content_css: 'dark',
                        height: 400,
                        menubar: false,
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                            'insertdatetime', 'media', 'table', 'help', 'wordcount'
                        ],
                        toolbar: 'undo redo | blocks | bold italic forecolor | ' +
                            'alignleft aligncenter alignright alignjustify | ' +
                            'bullist numlist outdent indent | removeformat | help',
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; background-color: #262626; color: #e5e5e5; }',
                        branding: false,
                        setup: function(editor) {
                            editor.on('change keyup', function() {
                                editor.save();
                                const content = editor.getContent();
                                @this.set('editingPost.content', content);
                            });

                            editor.on('init', function() {
                                // Ustaw wartość początkową
                                const content = @this.get('editingPost.content');
                                if (content) {
                                    editor.setContent(content);
                                }
                                console.log('TinyMCE initialized successfully');
                            });
                        }
                    }).catch((error) => {
                        console.error('TinyMCE initialization error:', error);
                        editorInitialized = false;
                    });
            }
        }, 50);
    }
</script>

