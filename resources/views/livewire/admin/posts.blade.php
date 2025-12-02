<div class="flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-6 shadow">

    <div class="mb-6 flex flex-wrap items-center gap-4 justify-between">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Szukaj po tytule, treści lub autorze..."
            class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 shadow-sm focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100"
        />

        <select wire:model.live="is_published" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie posty —</option>
            <option value="1">Opublikowane</option>
            <option value="0">Nieopublikowane</option>
        </select>

        <select wire:model.live="reactionFilter" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie reakcje —</option>
            <option value="liked">Lubiane</option>
            <option value="disliked">Nielubiane</option>
            <option value="no_reactions">Bez reakcji</option>
        </select>

        <select wire:model.live="commentFilter" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie komentarze —</option>
            <option value="commented">Komentowane</option>
            <option value="no_comments">Bez komentarzy</option>
        </select>

        <button wire:click="showCreateModal" class="rounded bg-[#880000] text-white px-4 py-2 font-semibold hover:bg-red-900 transition">
            Dodaj post
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4 overflow-x-auto">
        @forelse($posts as $post)
            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors min-w-0">
                <div class="flex gap-3 min-w-0">
                    @if($post->image)
                        <div class="flex-shrink-0 w-20 h-20 overflow-hidden rounded-lg">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'w-20 h-20 bg-neutral-200 dark:bg-neutral-700 rounded-lg flex items-center justify-center\'><svg class=\'w-8 h-8 text-neutral-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg></div>'">
                        </div>
                    @else
                        <div class="flex-shrink-0 w-20 h-20 bg-neutral-200 dark:bg-neutral-700 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ $post->title }}</h3>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mb-3 line-clamp-2">
                                    {!! Str::limit(strip_tags($post->content), 150) !!}
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-neutral-500 dark:text-neutral-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $post->author->name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $post->created_at->format('d.m.Y H:i') }}
                                    </span>
                                    @if($post->published_at)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Opublikowano: {{ $post->published_at->format('d.m.Y H:i') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1 text-green-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                        </svg>
                                        {{ $post->likes()->count() }}
                                    </span>
                                    <span class="flex items-center gap-1 text-red-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                        </svg>
                                        {{ $post->dislikes()->count() }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        {{ $post->comments()->count() }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                <span class="px-3 py-1 text-xs rounded-full {{ $post->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $post->is_published ? '✓ Opublikowany' : '○ Nieopublikowany' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button wire:click="editPost({{ $post->id }})" class="text-blue-600 hover:underline text-sm font-medium">Edytuj</button>
                            <button wire:click="togglePublish({{ $post->id }})" class="text-green-600 hover:underline text-sm font-medium">
                                {{ $post->is_published ? 'Ukryj' : 'Opublikuj' }}
                            </button>
                            <button
                                onclick="if(confirm('Czy na pewno chcesz usunąć ten post?')) { @this.deletePost({{ $post->id }}) }"
                                class="text-[#880000] hover:text-red-700 font-semibold text-sm">Usuń</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-neutral-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-medium">Brak postów</p>
                <p class="text-sm mt-1">Dodaj pierwszy post klikając przycisk powyżej</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-70 backdrop-blur-sm">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-neutral-900 border border-neutral-700 rounded-2xl w-full max-w-2xl max-h-[70vh] shadow-2xl transform transition-all my-8 overflow-hidden flex flex-col">
                    <!-- Header - Sticky -->
                    <div class="sticky top-0 bg-gradient-to-r from-neutral-900 via-neutral-800 to-neutral-900 px-6 py-4 border-b border-neutral-700 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">
                            {{ $editingPost['id'] ? '✏️ Edytuj post' : '✨ Dodaj nowy post' }}
                        </h3>
                        <button wire:click="closeModal" type="button" class="text-neutral-400 hover:text-white hover:bg-neutral-800 rounded-lg p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content - Scrollable -->
                    <div class="overflow-y-auto flex-1 px-6 py-5">
                        <form wire:submit.prevent="savePost" class="space-y-5" id="postForm">
                    <!-- Tytuł -->
                    <div>
                        <label for="title" class="block mb-1.5 text-sm font-medium text-neutral-300">
                            Tytuł <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            type="text"
                            wire:model.defer="editingPost.title"
                            placeholder="Wprowadź tytuł postu..."
                            class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 text-sm text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                        />
                        @error('editingPost.title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Treść -->
                    <div wire:ignore>
                        <label for="content" class="block mb-1.5 text-sm font-medium text-neutral-300">
                            Treść <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="content"
                            wire:model.defer="editingPost.content"
                            class="tinymce-editor"
                        ></textarea>
                        @error('editingPost.content')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Separator -->
                    <div class="border-t border-neutral-700"></div>

                    <!-- Obrazek -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Obrazek miniaturki
                        </h4>

                                <div class="flex gap-4 items-start">
                                    @if($editingPost['image'] && !$image)
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . $editingPost['image']) }}" alt="Current" class="w-24 h-24 object-cover rounded-lg border-2 border-neutral-600">
                                            <p class="text-xs text-neutral-500 mt-1 text-center">Aktualny</p>
                                        </div>
                                    @endif

                                    @if($image)
                                        <div class="flex-shrink-0">
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-[#880000]">
                                            <p class="text-xs text-[#880000] mt-1 text-center font-medium">Nowy</p>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <input
                                            id="image"
                                            type="file"
                                            wire:model="image"
                                            accept="image/*"
                                            class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 text-sm text-neutral-100 file:mr-3 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-[#880000] file:text-white file:cursor-pointer hover:file:bg-red-900 transition"
                                        />
                                        <p class="text-xs text-neutral-500 mt-2 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="leading-tight">Maksymalny rozmiar: 2MB (JPG, PNG, GIF)</span>
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
                            <div class="border-t border-neutral-700"></div>

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
                                        <label class="block mb-2 text-sm font-medium text-neutral-300">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="flex items-center gap-2.5 p-3 bg-neutral-800 border-2 border-neutral-700 rounded-lg cursor-pointer transition hover:border-green-600 has-[:checked]:border-green-600 has-[:checked]:bg-green-950/30">
                                                <input
                                                    type="radio"
                                                    wire:model.defer="editingPost.is_published"
                                                    value="1"
                                                    class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                                />
                                                <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                    Opublikowany
                                                </span>
                                            </label>
                                            <label class="flex items-center gap-2.5 p-3 bg-neutral-800 border-2 border-neutral-700 rounded-lg cursor-pointer transition hover:border-yellow-600 has-[:checked]:border-yellow-600 has-[:checked]:bg-yellow-950/30">
                                                <input
                                                    type="radio"
                                                    wire:model.defer="editingPost.is_published"
                                                    value="0"
                                                    class="w-4 h-4 text-yellow-600 focus:ring-yellow-500 focus:ring-2"
                                                />
                                                <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                                    Szkic
                                                </span>
                                            </label>
                                        </div>
                                        @error('editingPost.is_published')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                                    </div>

                                    <!-- Data publikacji -->
                                    <div>
                                        <label for="published_at" class="block mb-2 text-sm font-medium text-neutral-300">
                                            Data publikacji
                                        </label>
                                        <input
                                            id="published_at"
                                            type="datetime-local"
                                            wire:model.defer="editingPost.published_at"
                                            class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-2.5 text-sm text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                                        />
                                        <p class="text-xs text-neutral-500 mt-1.5">Zostaw puste dla automatycznej daty publikacji</p>
                                        @error('editingPost.published_at')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer - Sticky -->
                    <div class="sticky bottom-0 bg-neutral-900 px-6 py-4 border-t border-neutral-700 flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-5 py-2.5 rounded-lg border-2 border-neutral-600 bg-neutral-800 text-neutral-300 text-sm font-medium hover:bg-neutral-700 hover:border-neutral-500 transition-all"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            form="postForm"
                            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-[#880000] to-red-900 hover:from-red-900 hover:to-[#880000] text-white text-sm font-semibold shadow-lg hover:shadow-xl transition-all transform hover:scale-105 flex items-center gap-2"
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

<!-- TinyMCE Local -->
<script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

<script>
    let editorInitialized = false;

    document.addEventListener('livewire:init', () => {
        // Nasłuchuj na zamknięcie modala z Livewire
        Livewire.on('modalClosed', () => {
            cleanupEditor();
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

