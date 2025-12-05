<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @if($course)
            <!-- Filtry jednostek -->
            <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <!-- Rząd 1: Wyszukiwanie -->
                <div>
                        <input type="text" wire:model.live="search" class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]" placeholder="Szukaj po: nazwa, opis, materiały..." />
                    </div>

                    <!-- Rząd 2: Filtry i przyciski -->
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-4">
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" wire:model.live="filterRequiredOnly" class="rounded text-[#880000] focus:ring-[#880000]" />
                                <span class="text-neutral-900 dark:text-neutral-100">Tylko wymagane</span>
                            </label>

                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" wire:model.live="filterHasMaterials" class="rounded text-[#880000] focus:ring-[#880000]" />
                                <span class="text-neutral-900 dark:text-neutral-100">Z materiałami</span>
                            </label>

                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" wire:model.live="filterWithoutMaterials" class="rounded text-[#880000] focus:ring-[#880000]" />
                                <span class="text-neutral-900 dark:text-neutral-100">Bez materiałów</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-start justify-between gap-4 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">{{ $course ? $course->name : '' }}</h2>
                        @if($course && $course->description)
                            <p class="text-sm text-neutral-600 dark:text-neutral-300 max-w-3xl">{{ $course->description }}</p>
                        @endif
                    </div>
                    <button
                        wire:click="editCourse"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all shadow-md
                               bg-white text-neutral-900 border-2 border-neutral-300 hover:border-neutral-400 hover:shadow-lg
                               focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-400
                               dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:shadow-lg dark:hover:bg-neutral-700"
                        title="Edytuj kurs"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edytuj
                    </button>
                </div>

                <!-- DODAJ BLOK Header -->
                <div class="border-t border-neutral-200 dark:border-neutral-700 px-4 py-3 flex items-center justify-end bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 pr-4">
                    <button
                        wire:click="startCreateBlock"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors shadow-sm
                               bg-neutral-900 text-white border border-neutral-900 hover:bg-neutral-800
                               focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-800
                               dark:bg-white dark:text-neutral-900 dark:border-white dark:hover:bg-neutral-200"
                        title="Dodaj blok"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Dodaj blok
                    </button>
                </div>

                <!-- Struktura bloków i zagadnień -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden space-y-0">
                    @forelse($blocks as $block)
                        <div class="overflow-hidden border-b border-neutral-200 dark:border-neutral-700 last:border-b-0">
                            <!-- Blok nagłówek -->
                            <div class="px-4 py-3 flex items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                                <!-- Kolumna 1: Ikona wymagalności -->
                                <div class="w-5 flex justify-center flex-shrink-0">
                                    @if($block->is_required)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#880000] text-white" title="Wymagany">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#880000] text-white" title="Opcjonalny">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" /></svg>
                                        </span>
                                    @endif
                                </div>

                                <!-- Kolumna 2: Nazwa bloku + opis + czas -->
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="font-semibold text-neutral-900 dark:text-white truncate uppercase">{{ $block->title }}</div>
                                    @if($block->description)
                                        <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ $block->description }}</div>
                                    @endif
                                    @php($totalMinutes = $block->children->sum('duration_minutes'))
                                    @if($totalMinutes > 0)
                                        <div class="text-xs text-neutral-600 dark:text-neutral-400">ŁĄCZNY CZAS: {{ $totalMinutes }} min</div>
                                    @endif
                                </div>

                                <!-- Kolumna 3: Przyciski akcji -->
                                <div class="flex items-center gap-2 flex-shrink-0 px-3">
                                    <button wire:click="editUnit({{ $block->id }})" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Edytuj">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                    </button>
                                    <button wire:click="deleteUnit({{ $block->id }})" wire:confirm="Czy na pewno usunąć ten blok?" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Usuń">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>

                                <!-- Kolumna 4: Strzałki (odsunięte od krawędzi) -->
                                <div class="flex flex-col gap-0.5 w-5 flex justify-center pr-4">
                                    @php($isFirstBlock = $loop->first)
                                    @php($isLastBlock = $loop->last)
                                    <button wire:click="moveUnit({{ $block->id }}, 'up')" {{ $isFirstBlock ? 'disabled' : '' }} class="inline-flex items-center justify-center h-3 {{ $isFirstBlock ? 'invisible' : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }}" title="Przesuń w górę">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l7 7H3l7-7z" /></svg>
                                    </button>
                                    <button wire:click="moveUnit({{ $block->id }}, 'down')" {{ $isLastBlock ? 'disabled' : '' }} class="inline-flex items-center justify-center h-3 {{ $isLastBlock ? 'invisible' : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }}" title="Przesuń w dół">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 17l-7-7h14l-7 7z" /></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Zagadnienia Header - DODAJ ZAGADNIENIE -->
                            <div class="border-t border-neutral-200 dark:border-neutral-700 px-4 py-3 flex items-center justify-end bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 pr-4">
                                <button
                                    wire:click="startCreateTopic({{ $block->id }})"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors shadow-sm
                                           bg-neutral-900 text-white border border-neutral-900 hover:bg-neutral-800
                                           focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-800
                                           dark:bg-white dark:text-neutral-900 dark:border-white dark:hover:bg-neutral-200"
                                    title="Dodaj zagadnienie"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Dodaj zagadnienie
                                </button>
                            </div>

                            <!-- Zagadnienia -->
                            @php($topics = $block->children)
                            @if($topics->count())
                                <div class="border-t border-neutral-200 dark:border-neutral-700">
                                    @foreach($topics as $topic)
                                        <div class="py-2.5 flex items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors border-b border-neutral-200 dark:border-neutral-700 last:border-b-0 px-4">
                                            <!-- Kolumna 1: Wcięcie przed ikoną -->
                                            <div class="w-12"></div>

                                            <!-- Kolumna 2: Ikona wymagalności -->
                                            <div class="w-5 flex justify-center flex-shrink-0">
                                                @if($topic->is_required)
                                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#880000] text-white" title="Wymagany">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#880000] text-white" title="Opcjonalny">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" /></svg>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Kolumna 3: Zawartość zagadnienia -->
                                            <div class="ml-3 flex-1 min-w-0">
                                                <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $topic->title }}</div>
                                                @if($topic->description)
                                                    <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ $topic->description }}</div>
                                                @endif
                                                @if($topic->duration_minutes)
                                                    <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ $topic->duration_minutes }} min</div>
                                                @endif
                                            </div>

                                            <!-- Kolumna 4: Przyciski akcji -->
                                            <div class="flex items-center gap-2 flex-shrink-0 px-3">
                                                <button wire:click="editUnit({{ $topic->id }})" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Edytuj">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                                </button>
                                                <button wire:click="deleteUnit({{ $topic->id }})" wire:confirm="Czy na pewno usuńąć to zagadnienie?" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Usuń">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                </button>
                                            </div>

                                            <!-- Kolumna 5: Strzałki (odsunięte od krawędzi) -->
                                            <div class="flex flex-col gap-0.5 w-5 flex justify-center pr-4">
                                                @php($isFirstTopic = $loop->first)
                                                @php($isLastTopic = $loop->last)
                                                <button wire:click="moveUnit({{ $topic->id }}, 'up')" {{ $isFirstTopic ? 'disabled' : '' }} class="inline-flex items-center justify-center h-3 {{ $isFirstTopic ? 'invisible' : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }}" title="Przesuń w górę">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l7 7H3l7-7z" /></svg>
                                                </button>
                                                <button wire:click="moveUnit({{ $topic->id }}, 'down')" {{ $isLastTopic ? 'disabled' : '' }} class="inline-flex items-center justify-center h-3 {{ $isLastTopic ? 'invisible' : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }}" title="Przesuń w dół">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 17l-7-7h14l-7 7z" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 bg-white dark:bg-neutral-900">Brak zagadnień w tym bloku.</div>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-3 text-neutral-500 dark:text-neutral-400">Brak bloków spełniających kryteria.</div>
                    @endforelse
                </div>

                <!-- Legenda -->
                <div class="mt-6 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-3">Legenda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#880000] text-white">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </span>
                            <span class="text-neutral-900 dark:text-neutral-100">Element wymagany</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#880000] text-white">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" /></svg>
                            </span>
                            <span class="text-neutral-900 dark:text-neutral-100">Element opcjonalny</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex flex-col gap-0.5">
                                <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3l7 7H3l7-7z" /></svg>
                                <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 17l-7-7h14l-7 7z" /></svg>
                            </div>
                            <span class="text-neutral-900 dark:text-neutral-100">Zmiana pozycji bloku lub zagadnienia</span>
                        </div>
                    </div>
                </div>
        @else
            <div class="text-center py-12 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <div class="text-neutral-500 dark:text-neutral-400">Kurs "OPW z Dronem" nie znaleziony. Utwórz go poprzez seeder lub ręcznie.</div>
            </div>
        @endif

        <!-- Modal edycji jednostki (blok/zagadnienie) -->
        @if($editingUnitId !== null || $editingParentId !== null)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="resetUnitEditor">
                <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-2xl transform transition-all max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between sticky top-0 bg-white dark:bg-neutral-900">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $editingParentId ? 'EDYCJA ZAGADNIENIA: ' : 'EDYCJA BLOKU: ' }}{{ $unitTitle }}</h2>
                        <button wire:click="resetUnitEditor" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Tytuł</label>
                            <input type="text" wire:model.live="unitTitle" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Opis</label>
                            <textarea wire:model.live="unitDescription" rows="3" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"></textarea>
                        </div>

                        @if($editingParentId === null)
                            <!-- Info o łącznym czasie bloku -->
                            @if($editingUnitId)
                                @php($editedBlock = \App\Models\CourseUnit::find($editingUnitId))
                                @php($totalBlockMinutes = $editedBlock?->children->sum('duration_minutes') ?? 0)
                                @if($totalBlockMinutes > 0)
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="text-sm font-semibold text-blue-800 dark:text-blue-100">Łączny czas realizacji treści w bloku: {{ $totalBlockMinutes }} minut</div>
                                    </div>
                                @endif
                            @endif
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" wire:model.live="unitIsRequired" class="rounded text-blue-600 focus:ring-blue-600" />
                                <span class="text-neutral-800 dark:text-neutral-200">Wymagany</span>
                            </label>
                            @if($editingParentId !== null)
                                <!-- Tylko dla zagadnień: czas -->
                                <div>
                                    <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Czas (minuty)</label>
                                    <input type="number" min="0" wire:model.live="unitDurationMinutes" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" />
                                </div>
                            @endif
                        </div>

                        <!-- Sekcja materiałów (read-only) -->
                        @if($editingUnitId && $editingUnitId > 0)
                            <div class="border-t border-neutral-200 dark:border-neutral-800 pt-4 mt-4">
                                <h3 class="text-sm font-semibold text-neutral-800 dark:text-neutral-100 mb-3">Materiały do {{ $editingParentId ? 'zagadnienia' : 'bloku' }}</h3>

                                <!-- Lista materiałów (tylko wyświetlanie) -->
                                @if(count($unitMaterials) > 0)
                                    <div class="space-y-2 mb-4 max-h-48 overflow-y-auto bg-neutral-50 dark:bg-neutral-800/50 rounded p-3 border border-neutral-200 dark:border-neutral-700">
                                        @foreach($unitMaterials as $material)
                                            <div class="flex items-center justify-between text-xs bg-white dark:bg-neutral-800 p-2 rounded border border-neutral-200 dark:border-neutral-700">
                                                <div class="flex-1">
                                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ $material['title'] }}</div>
                                                    @if(!empty($material['description']))
                                                        <div class="text-neutral-600 dark:text-neutral-300 mt-1">{{ $material['description'] }}</div>
                                                    @endif
                                                    <div class="text-neutral-500 dark:text-neutral-400 mt-1">{{ ucfirst(str_replace('_', ' ', $material['type'])) }} • {{ $material['uploaded_by'] }}</div>
                                                    @if(!$material['is_approved'])
                                                        <div class="text-yellow-600 dark:text-yellow-400">Czeka na zatwierdzenie</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400 italic mb-3">Brak materiałów</div>
                                @endif

                                <!-- Info -->
                                <div class="text-xs text-blue-300 italic">Zarządzanie materiałami dostępne w sekcji "Materiały do zajęć" w menu.</div>
                            </div>
                        @else
                            <div class="border-t border-neutral-200 dark:border-neutral-800 pt-4 mt-4">
                                <div class="text-xs text-yellow-700 dark:text-yellow-400 italic">Materiały można przeglądać po zapisaniu bloku/zagadnienia.</div>
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 flex items-center justify-end gap-2 sticky bottom-0 bg-white dark:bg-neutral-900">
                        <button wire:click="resetUnitEditor" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium border border-neutral-300 text-neutral-700 bg-white rounded hover:bg-neutral-50 transition dark:border-neutral-600 dark:text-neutral-300 dark:bg-neutral-800 dark:hover:bg-neutral-700">
                            Anuluj
                        </button>
                        <button
                            wire:click="saveUnit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded shadow-sm transition
                                   bg-neutral-900 text-white border border-neutral-900 hover:bg-neutral-800
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-800
                                   dark:bg-white dark:text-neutral-900 dark:border-white dark:hover:bg-neutral-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Zapisz jednostkę
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal edycji kursu -->
        @if($showCourseEditor)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="closeCourseEditor">
                <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-2xl transform transition-all max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between sticky top-0 bg-white dark:bg-neutral-900">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Edytuj kurs</h2>
                        <button wire:click="closeCourseEditor" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Nazwa kursu</label>
                            <input type="text" wire:model.live="courseTitle" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" placeholder="Nazwa kursu" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">Opis kursu</label>
                            <textarea wire:model.live="courseDescription" rows="4" class="w-full px-4 py-2 rounded border border-neutral-300 bg-white text-neutral-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" placeholder="Krótki opis kursu"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-2 sticky bottom-0 bg-white dark:bg-neutral-900">
                        <button wire:click="closeCourseEditor" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium border border-neutral-300 text-neutral-700 bg-white rounded hover:bg-neutral-50 transition dark:border-neutral-600 dark:text-neutral-300 dark:bg-neutral-800 dark:hover:bg-neutral-700">
                            Anuluj
                        </button>
                        <button
                            wire:click="saveCourse"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded shadow-sm transition
                                   bg-neutral-900 text-white border border-neutral-900 hover:bg-neutral-800
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-800
                                   dark:bg-white dark:text-neutral-900 dark:border-white dark:hover:bg-neutral-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Zapisz kurs
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>
