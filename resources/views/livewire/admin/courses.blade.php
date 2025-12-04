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

                <!-- Tytuł kursu z edycją -->
                <div class="flex items-center justify-between p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">{{ $course ? $course->name : '' }}</h2>
                    <button wire:click="editCourse" class="inline-flex items-center gap-2 px-3 py-1.5 bg-black hover:bg-neutral-800 text-[#880000] text-xs font-bold rounded transition-colors" title="Edytuj tytuł">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-[#880000] flex-shrink-0">
                            <span class="text-sm">+</span>
                        </span>
                        EDYTUJ TYTUŁ
                    </button>
                </div>

                <!-- DODAJ BLOK Header -->
                <div class="border-t border-neutral-200 dark:border-neutral-700 px-4 py-3 flex items-center justify-end bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 pr-4">
                    <!-- DODAJ BLOK z ikoną + -->
                    <button wire:click="startCreateBlock" class="inline-flex items-center gap-2 px-3 py-1.5 bg-black hover:bg-neutral-800 text-[#880000] text-xs font-bold rounded transition-colors" title="Dodaj blok">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-[#880000] flex-shrink-0">
                            <span class="text-sm">+</span>
                        </span>
                        DODAJ BLOK
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
                                <div class="flex items-center gap-3 flex-shrink-0 px-3">
                                    <button wire:click="editUnit({{ $block->id }})" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Edytuj blok">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button wire:click="deleteUnit({{ $block->id }})" wire:confirm="Czy na pewno usunąć ten blok?" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Usuń blok">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
                                <!-- DODAJ ZAGADNIENIE z ikoną + -->
                                <button wire:click="startCreateTopic({{ $block->id }})" class="inline-flex items-center gap-2 px-3 py-1.5 bg-black hover:bg-neutral-800 text-[#880000] text-xs font-bold rounded transition-colors" title="Dodaj zagadnienie">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-[#880000] flex-shrink-0">
                                        <span class="text-sm">+</span>
                                    </span>
                                    DODAJ ZAGADNIENIE
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
                                            <div class="flex items-center gap-3 flex-shrink-0 px-3">
                                                <button wire:click="editUnit({{ $topic->id }})" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Edytuj zagadnienie">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </button>
                                                <button wire:click="deleteUnit({{ $topic->id }})" wire:confirm="Czy na pewno usunąć to zagadnienie?" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Usuń zagadnienie">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
                <div class="bg-neutral-900 border border-neutral-700 rounded-2xl shadow-2xl transform transition-all max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-800 flex items-center justify-between sticky top-0 bg-neutral-900">
                        <h2 class="text-lg font-semibold text-white">{{ $editingParentId ? 'EDYCJA ZAGADNIENIA: ' : 'EDYCJA BLOKU: ' }}{{ $unitTitle }}</h2>
                        <button wire:click="resetUnitEditor" class="text-neutral-400 hover:text-neutral-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-neutral-100">Tytuł</label>
                            <input type="text" wire:model.live="unitTitle" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-neutral-100">Opis</label>
                            <textarea wire:model.live="unitDescription" rows="3" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]"></textarea>
                        </div>

                        @if($editingParentId === null)
                            <!-- Info o łącznym czasie bloku -->
                            @if($editingUnitId)
                                @php($editedBlock = \App\Models\CourseUnit::find($editingUnitId))
                                @php($totalBlockMinutes = $editedBlock?->children->sum('duration_minutes') ?? 0)
                                @if($totalBlockMinutes > 0)
                                    <div class="p-3 bg-blue-900/30 rounded-lg border border-blue-800">
                                        <div class="text-sm font-semibold text-blue-100">Łączny czas realizacji treści w bloku: {{ $totalBlockMinutes }} minut</div>
                                    </div>
                                @endif
                            @endif
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" wire:model.live="unitIsRequired" class="rounded text-[#880000] focus:ring-[#880000]" />
                                <span class="text-neutral-100">Wymagany</span>
                            </label>
                            @if($editingParentId !== null)
                                <!-- Tylko dla zagadnień: czas -->
                                <div>
                                    <label class="text-sm font-medium text-neutral-100">Czas (minuty)</label>
                                    <input type="number" min="0" wire:model.live="unitDurationMinutes" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]" />
                                </div>
                            @endif
                        </div>

                        <!-- Sekcja materiałów (read-only) -->
                        @if($editingUnitId && $editingUnitId > 0)
                            <div class="border-t border-neutral-800 pt-4 mt-4">
                                <h3 class="text-sm font-semibold text-neutral-100 mb-3">Materiały do {{ $editingParentId ? 'zagadnienia' : 'bloku' }}</h3>

                                <!-- Lista materiałów (tylko wyświetlanie) -->
                                @if(count($unitMaterials) > 0)
                                    <div class="space-y-2 mb-4 max-h-48 overflow-y-auto bg-neutral-800/50 rounded p-3 border border-neutral-700">
                                        @foreach($unitMaterials as $material)
                                            <div class="flex items-center justify-between text-xs bg-neutral-800 p-2 rounded border border-neutral-700">
                                                <div class="flex-1">
                                                    <div class="font-medium text-neutral-100">{{ $material['title'] }}</div>
                                                    @if(!empty($material['description']))
                                                        <div class="text-neutral-300 mt-1">{{ $material['description'] }}</div>
                                                    @endif
                                                    <div class="text-neutral-400 mt-1">{{ ucfirst(str_replace('_', ' ', $material['type'])) }} • {{ $material['uploaded_by'] }}</div>
                                                    @if(!$material['is_approved'])
                                                        <div class="text-yellow-400">Czeka na zatwierdzenie</div>
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
                            <div class="border-t border-neutral-800 pt-4 mt-4">
                                <div class="text-xs text-yellow-400 italic">Materiały można przeglądać po zapisaniu bloku/zagadnienia.</div>
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-800 flex items-center justify-end gap-2 sticky bottom-0 bg-neutral-900">
                        <button wire:click="resetUnitEditor" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-neutral-700 hover:bg-neutral-600 text-neutral-100 rounded transition">
                            Anuluj
                        </button>
                        <button wire:click="saveUnit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition">
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
                <div class="bg-neutral-900 border border-neutral-700 rounded-2xl shadow-2xl transform transition-all max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-800 flex items-center justify-between sticky top-0 bg-neutral-900">
                        <h2 class="text-lg font-semibold text-white">Edytuj kurs</h2>
                        <button wire:click="closeCourseEditor" class="text-neutral-400 hover:text-neutral-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-neutral-100">Nazwa kursu</label>
                            <input type="text" wire:model.live="courseTitle" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]" placeholder="Nazwa kursu" />
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-800 flex items-center justify-end gap-2 sticky bottom-0 bg-neutral-900">
                        <button wire:click="closeCourseEditor" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-neutral-700 hover:bg-neutral-600 text-neutral-100 rounded transition">
                            Anuluj
                        </button>
                        <button wire:click="saveCourse" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Zapisz kurs
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>
