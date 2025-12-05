<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Filtry i wyszukiwanie -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <!-- Rząd 1: Wyszukiwanie -->
            <div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Szukaj po tytule, opisie lub autorze..."
                    class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]"
                />
            </div>

            <!-- Rząd 2: Filtry i przyciski -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <select wire:model.live="filterType" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie typy —</option>
                        <option value="pdf">Pliki PDF</option>
                        <option value="video_link">Wideo</option>
                        <option value="external_link">Linki</option>
                    </select>

                    <select wire:model.live="filterUnit" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie bloki —</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}">{{ $block->title }}</option>
                            @php($blockTopics = $units->where('parent_id', $block->id))
                            @foreach($blockTopics as $topic)
                                <option value="{{ $topic->id }}">  • {{ $topic->title }}</option>
                            @endforeach
                        @endforeach
                    </select>

                    <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" wire:model.live="filterPending" class="rounded text-[#880000] focus:ring-[#880000]" />
                        <span class="text-neutral-900 dark:text-neutral-100">Czeka na zatwierdzenie</span>
                    </label>
                </div>

                <button
                    wire:click="openMaterialEditor(null, null)"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all shadow-md
                           bg-white text-neutral-900 hover:border-neutral-400 hover:shadow-lg border-2 border-neutral-300
                           focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                           dark:bg-neutral-800 dark:text-white dark:hover:border-neutral-500 dark:hover:shadow-lg dark:border-neutral-600"
                    title="Dodaj materiał"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj materiał
                </button>
            </div>
        </div>

        <!-- Tytuł kursu -->
        <div class="flex items-start justify-between gap-4 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">{{ $course ? $course->name : 'Materiały do zajęć' }}</h2>
                @if($course && $course->description)
                    <p class="text-sm text-neutral-600 dark:text-neutral-300 max-w-3xl">{{ $course->description }}</p>
                @endif
            </div>
        </div>

        <!-- Struktura bloków i materiałów -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden space-y-0">
            @forelse($blocks as $block)
                <div class="overflow-hidden border-b border-neutral-200 dark:border-neutral-700 last:border-b-0">
                    <!-- Blok nagłówek -->
                    <div class="px-4 py-3 flex items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                        <!-- Kolumna 1: Ikona bloku -->
                        <div class="w-5 flex justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM15 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zM5 13a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>

                        <!-- Kolumna 2: Nazwa bloku + opis -->
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="font-semibold text-neutral-900 dark:text-white truncate uppercase">{{ $block->title }}</div>
                            @if($block->description)
                                <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ $block->description }}</div>
                            @endif
                        </div>

                        <!-- Kolumna 3: Liczba materiałów -->
                        <div class="flex-shrink-0 px-3 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ $block->materials->count() }} materiałów
                        </div>
                    </div>

                    <!-- Materiały bloku i zagadnienia -->
                    @php($blockMaterials = $block->materials)
                    @php($blockTopics = $units->where('parent_id', $block->id))

                    @if($blockMaterials->count() > 0)
                        <div class="px-4 py-2 bg-neutral-50 dark:bg-neutral-800 border-t border-neutral-200 dark:border-neutral-700">
                            <div class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-2">MATERIAŁY BLOKU:</div>
                            @foreach($blockMaterials as $material)
                                <div class="flex items-center hover:bg-neutral-100 dark:hover:bg-neutral-700 py-2 px-3 mb-1 rounded transition-colors group">
                                    <!-- Ikona typu -->
                                    <div class="w-5 flex justify-center flex-shrink-0">
                                        @if($material->type === 'pdf')
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" /></svg>
                                        @elseif($material->type === 'video_link')
                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm13.707-2.707a1 1 0 00-1.414 1.414L14.586 7l-1.293 1.293a1 1 0 101.414 1.414L16 8.414l1.293 1.293a1 1 0 001.414-1.414L17.414 8l1.293-1.293a1 1 0 00-1.414-1.414L16 6.586l-1.293-1.293z" /></svg>
                                        @else
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        @endif
                                    </div>

                                    <!-- Informacje materiału -->
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ $material->title }}</div>
                                        @if($material->description)
                                            <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">{{ $material->description }}</div>
                                        @endif
                                        <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $material->type)) }} •
                                            {{ $material->uploadedBy->name ?? 'Nieznany' }}
                                            @if(!$material->is_approved)
                                                <span class="text-yellow-500 font-semibold">• Czeka na zatwierdzenie</span>
                                            @else
                                                <span class="text-green-500">• Zatwierdzone</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Przyciski akcji -->
                                    <div class="flex items-center gap-2 flex-shrink-0 px-3">
                                        <button wire:click="openMaterialEditor({{ $material->id }})" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Edytuj">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                        </button>
                                        <button wire:click="deleteMaterial({{ $material->id }})" wire:confirm="Usuńąć ten materiał?" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Usuń">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Zagadnienia -->
                    @if($blockTopics->count() > 0)
                        <div class="px-4 py-2">
                            @foreach($blockTopics as $topic)
                                <div class="pl-6 mb-3">
                                    <!-- Nagłówek zagadnienia -->
                                    <div class="flex items-center py-2 px-3 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded transition-colors group">
                                        <!-- Ikona zagadnienia -->
                                        <div class="w-5 flex justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a1 1 0 011-1h3a1 1 0 011 1v1h10a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1v-1H3a1 1 0 01-1-1V6z" />
                                            </svg>
                                        </div>

                                        <!-- Nazwa zagadnienia -->
                                        <div class="ml-3 flex-1 min-w-0">
                                            <div class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ $topic->title }}</div>
                                        </div>

                                        <!-- Liczba materiałów -->
                                        <div class="flex-shrink-0 px-3 text-xs text-neutral-600 dark:text-neutral-400">
                                            {{ $topic->materials->count() }} materiałów
                                        </div>
                                    </div>

                                    <!-- Materiały zagadnienia -->
                                    @if($topic->materials->count() > 0)
                                        <div class="mt-1 ml-3">
                                            @foreach($topic->materials as $material)
                                                <div class="flex items-center hover:bg-neutral-100 dark:hover:bg-neutral-700 py-2 px-3 mb-1 rounded transition-colors group">
                                                    <!-- Ikona typu -->
                                                    <div class="w-5 flex justify-center flex-shrink-0">
                                                        @if($material->type === 'pdf')
                                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" /></svg>
                                                        @elseif($material->type === 'video_link')
                                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm13.707-2.707a1 1 0 00-1.414 1.414L14.586 7l-1.293 1.293a1 1 0 101.414 1.414L16 8.414l1.293 1.293a1 1 0 001.414-1.414L17.414 8l1.293-1.293a1 1 0 00-1.414-1.414L16 6.586l-1.293-1.293z" /></svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                                        @endif
                                                    </div>

                                                    <!-- Informacje materiału -->
                                                    <div class="ml-3 flex-1 min-w-0">
                                                        <div class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ $material->title }}</div>
                                                        @if($material->description)
                                                            <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">{{ $material->description }}</div>
                                                        @endif
                                                        <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">
                                                            {{ ucfirst(str_replace('_', ' ', $material->type)) }} •
                                                            {{ $material->uploadedBy->name ?? 'Nieznany' }}
                                                            @if(!$material->is_approved)
                                                                <span class="text-yellow-500 font-semibold">• Czeka na zatwierdzenie</span>
                                                            @else
                                                                <span class="text-green-500">• Zatwierdzone</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Przyciski akcji -->
                                                    <div class="flex items-center gap-2 flex-shrink-0 px-3">
                                                        <button wire:click="openMaterialEditor({{ $material->id }})" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Edytuj">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                                        </button>
                                                        <button wire:click="deleteMaterial({{ $material->id }})" wire:confirm="Usunąć ten materiał?" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Usuń">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400 italic py-2 px-3 ml-3">Brak materiałów</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">Brak bloków w kursie.</div>
            @endforelse
        </div>

        <!-- Material editor modal -->
        @if($showMaterialEditor)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="closeMaterialEditor">
                <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-2xl transform transition-all max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 flex items-center justify-between sticky top-0 bg-white dark:bg-neutral-900">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $editingMaterialId ? 'EDYTUJ MATERIAŁ' : 'DODAJ MATERIAŁ' }}</h2>
                        <button wire:click="closeMaterialEditor" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Unit selector -->
                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Przypisz do</label>
                            <select wire:model="selectedUnitId" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100">
                                <option value="">Wybierz blok lub zagadnienie...</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}">{{ $block->title }} (BLOK)</option>
                                    @php($blockTopics = $units->where('parent_id', $block->id))
                                    @foreach($blockTopics as $topic)
                                        <option value="{{ $topic->id }}">• {{ $topic->title }} (zagadnienie)</option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('selectedUnitId') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Tytuł materiału</label>
                            <input type="text" wire:model="materialTitle" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" placeholder="np. Instrukcja montażu" />
                            @error('materialTitle') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Opis (opcjonalny)</label>
                            <textarea wire:model="materialDescription" rows="3" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" placeholder="Krótki opis materiału..."></textarea>
                            @error('materialDescription') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Typ materiału</label>
                            <select wire:model.live="materialType" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100">
                                <option value="pdf">Plik PDF</option>
                                <option value="video_link">Link do wideo</option>
                                <option value="external_link">Zewnętrzny link</option>
                            </select>
                        </div>

                        @if($materialType === 'pdf')
                            <div>
                                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Plik PDF (max 50MB)</label>
                                <input type="file" wire:model="materialFile" accept=".pdf" class="w-full px-3 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" />
                                @error('materialFile') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div>
                                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">URL materiału</label>
                                <input type="url" wire:model="materialUrl" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" placeholder="https://..." />
                                @error('materialUrl') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 flex items-center justify-end gap-2 sticky bottom-0 bg-white dark:bg-neutral-900">
                        <button wire:click="closeMaterialEditor" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium border border-neutral-300 text-neutral-700 bg-white rounded hover:bg-neutral-50 transition dark:border-neutral-600 dark:text-neutral-300 dark:bg-neutral-800 dark:hover:bg-neutral-700">
                            Anuluj
                        </button>
                        <button
                            wire:click="saveMaterial"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded shadow-md transition-all
                                   bg-white text-neutral-900 hover:border-neutral-400 hover:shadow-lg border-2 border-neutral-300
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                   dark:bg-neutral-800 dark:text-white dark:hover:border-neutral-500 dark:hover:shadow-lg dark:border-neutral-600"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Zapisz materiał
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>

