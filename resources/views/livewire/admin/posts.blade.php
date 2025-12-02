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

        <button wire:click="showCreateModal" class="rounded bg-[#880000] text-white px-4 py-2 font-semibold hover:bg-red-900 transition">
            Dodaj post
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($posts as $post)
            <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                <div class="flex gap-4">
                    @if($post->image)
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-32 h-32 object-cover rounded-lg">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
            <div class="bg-neutral-900 border border-neutral-700 rounded-2xl p-8 w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-white">
                        {{ $editingPost['id'] ? 'Edytuj post' : 'Dodaj nowy post' }}
                    </h3>
                    <button wire:click="closeModal" class="text-neutral-400 hover:text-neutral-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePost" class="space-y-6">
                    <!-- Tytuł -->
                    <div>
                        <label for="title" class="block mb-2 text-sm font-semibold text-neutral-300">
                            Tytuł <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            type="text"
                            wire:model.defer="editingPost.title"
                            placeholder="Wprowadź tytuł postu..."
                            class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                        />
                        @error('editingPost.title')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Treść -->
                    <div wire:ignore>
                        <label for="content" class="block mb-2 text-sm font-semibold text-neutral-300">
                            Treść <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="content"
                            wire:model.defer="editingPost.content"
                            class="tinymce-editor"
                        ></textarea>
                        @error('editingPost.content')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <!-- Obrazek -->
                    <div>
                        <label for="image" class="block mb-2 text-sm font-semibold text-neutral-300">
                            Obrazek
                        </label>

                        @if($editingPost['image'] && !$image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $editingPost['image']) }}" alt="Current" class="w-48 h-48 object-cover rounded-lg border border-neutral-700">
                                <p class="text-xs text-neutral-400 mt-2">Aktualny obrazek</p>
                            </div>
                        @endif

                        @if($image)
                            <div class="mb-3">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-48 h-48 object-cover rounded-lg border border-neutral-700">
                                <p class="text-xs text-neutral-400 mt-2">Nowy obrazek (podgląd)</p>
                            </div>
                        @endif

                        <input
                            id="image"
                            type="file"
                            wire:model="image"
                            accept="image/*"
                            class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#880000] file:text-white file:cursor-pointer hover:file:bg-red-900 transition"
                        />
                        <p class="text-xs text-neutral-400 mt-2">Maksymalny rozmiar: 2MB</p>
                        @error('image')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status publikacji -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-neutral-300">
                                Status publikacji <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-4 p-4 bg-neutral-800 rounded-lg border border-neutral-700">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
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
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingPost.is_published"
                                        value="0"
                                        class="w-4 h-4 text-yellow-600 focus:ring-yellow-500 focus:ring-2"
                                    />
                                    <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                        Nieopublikowany
                                    </span>
                                </label>
                            </div>
                            @error('editingPost.is_published')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Data publikacji -->
                        <div>
                            <label for="published_at" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Data publikacji
                            </label>
                            <input
                                id="published_at"
                                type="datetime-local"
                                wire:model.defer="editingPost.published_at"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            />
                            <p class="text-xs text-neutral-400 mt-2">Pozostaw puste dla automatycznej daty</p>
                            @error('editingPost.published_at')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Przyciski akcji -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-neutral-700">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-6 py-2.5 rounded-lg border border-neutral-600 bg-neutral-800 text-neutral-300 font-medium hover:bg-neutral-700 transition"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-lg bg-[#880000] hover:bg-red-900 text-white font-semibold shadow-lg hover:shadow-xl transition transform hover:scale-105"
                        >
                            {{ $editingPost['id'] ? '💾 Zapisz zmiany' : '➕ Dodaj post' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

</div>

<!-- TinyMCE Local -->
<script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

<script>
    let editorInitialized = false;

    document.addEventListener('livewire:init', () => {
        Livewire.on('closeModal', () => {
            if (typeof tinymce !== 'undefined') {
                tinymce.remove();
                editorInitialized = false;
            }
        });
    });

    // Obserwator zmian DOM dla inicjalizacji edytora
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        const editor = node.querySelector('.tinymce-editor');
                        if (editor && !editorInitialized) {
                            editorInitialized = true;
                            initTinyMCE();
                        }
                    }
                });
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    function initTinyMCE() {
        // Usuń poprzednie instancje
        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
            tinymce.remove('#content');
        }

        // Poczekaj aż TinyMCE się załaduje
        const checkTinyMCE = setInterval(() => {
            if (typeof tinymce !== 'undefined') {
                clearInterval(checkTinyMCE);

                setTimeout(() => {
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
                            });
                        }
                    }).then(() => {
                        console.log('TinyMCE initialized successfully');
                    }).catch((error) => {
                        console.error('TinyMCE initialization error:', error);
                    });
                }, 200);
            }
        }, 50);
    }
</script>

