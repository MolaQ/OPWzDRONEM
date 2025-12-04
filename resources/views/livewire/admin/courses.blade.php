<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kursy</h1>
        </div>

        <!-- Akcje -->
        <div class="flex items-center gap-2">
            <button wire:click="resetForm" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Dodaj szablon kursu</button>
            <button wire:click="startInstanceCreator" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Utwórz instancję kursu</button>
        </div>

        <!-- Szablony kursów -->
        <div class="mt-4">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white mb-2">Szablony kursów</h2>
            <div class="space-y-3">
                @forelse($templates as $template)
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-blue-300 dark:border-blue-700 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $template->name }}</div>
                                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">SZABLON</span>
                                </div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Jednostek: {{ $template->units_count }} • 
                                    Teoria: {{ number_format($template->calculated_theory_minutes / 60, 1) }}h • 
                                    Praktyka: {{ number_format($template->calculated_practice_flight_minutes / 60, 1) }}h • 
                                    Lab: {{ number_format($template->calculated_practice_lab_minutes / 60, 1) }}h • 
                                    Symulator: {{ number_format($template->calculated_simulator_minutes / 60, 1) }}h
                                </div>
                                @if($template->description)
                                    <div class="mt-1 text-sm text-neutral-700 dark:text-neutral-300">{{ $template->description }}</div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="recalculateHours({{ $template->id }})" class="px-3 py-1.5 text-xs bg-neutral-200 hover:bg-neutral-300 text-neutral-800 rounded">Przelicz godz.</button>
                                <button wire:click="editCourse({{ $template->id }})" class="px-3 py-1.5 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded">Edytuj</button>
                                <button wire:click="deleteCourse({{ $template->id }})" wire:confirm="Czy na pewno usunąć szablon?" class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded">Usuń</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                        <div class="text-neutral-500 dark:text-neutral-400">Brak szablonów — utwórz pierwszy powyżej.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Instancje kursów -->
        <div class="mt-4">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white mb-2">Instancje kursów (aktywne)</h2>
            <div class="space-y-3">
                @forelse($instances as $instance)
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $instance->name }}</div>
                                @if($instance->template)
                                    <div class="text-xs text-neutral-500">Oparte na: {{ $instance->template->name }}</div>
                                @endif
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Jednostek: {{ $instance->units_count }} • 
                                    Uczniów: {{ $instance->studentCourses()->count() }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="deleteCourse({{ $instance->id }})" wire:confirm="Czy na pewno usunąć instancję?" class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded">Usuń</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                        <div class="text-neutral-500 dark:text-neutral-400">Brak aktywnych instancji — utwórz nową powyżej.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Struktura jednostek szablonu -->
        <div class="mt-4">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white mb-2">Edycja struktury (pierwszy szablon)</h2>
            
            <!-- Filtry jednostek -->
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" wire:model.debounce.300ms="search" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" placeholder="Szukaj w blokach i zagadnieniach" />
                </div>
                <div>
                    <select wire:model.live="filterCategory" class="px-3 py-2 rounded border dark:bg-neutral-900">
                        <option value="">Wszystkie kategorie</option>
                        <option value="theory">Teoria</option>
                        <option value="practice_flight">Praktyka (lot)</option>
                        <option value="practice_lab">Laboratorium</option>
                        <option value="simulator">Symulator</option>
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="filterRequiredOnly" />
                    Tylko wymagane
                </label>
                <button wire:click="startCreateBlock" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm">+ Dodaj blok</button>
            </div>
        @if($selectedCourse)
            <div class="mt-4">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Struktura: {{ $selectedCourse->name }}</h2>
                <div class="mt-2 space-y-3">
                    @forelse($blocks as $block)
                        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                            <div class="px-4 py-3 flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="font-semibold text-neutral-900 dark:text-white">{{ $block->title }}</div>
                                    <div class="text-xs text-neutral-600 dark:text-neutral-400">Kategoria: {{ $block->type }} • {{ $block->is_required ? 'Wymagany' : 'Opcjonalny' }} • Pozycja: {{ $block->position ?? 0 }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="startCreateTopic({{ $block->id }})" class="px-2 py-1 text-xs bg-green-600 hover:bg-green-700 text-white rounded">+ Zagadnienie</button>
                                    <button wire:click="moveUnit({{ $block->id }}, 'up')" class="px-2 py-1 text-xs bg-neutral-200 hover:bg-neutral-300 rounded">↑</button>
                                    <button wire:click="moveUnit({{ $block->id }}, 'down')" class="px-2 py-1 text-xs bg-neutral-200 hover:bg-neutral-300 rounded">↓</button>
                                    <button wire:click="editUnit({{ $block->id }})" class="px-2 py-1 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded">Edytuj</button>
                                    <button wire:click="deleteUnit({{ $block->id }})" wire:confirm="Czy na pewno usunąć ten blok?" class="px-2 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded">Usuń</button>
                                </div>
                            </div>
                            @php($topics = $block->children)
        
            <!-- Modal edycji jednostki (blok/zagadnienie) -->
            @if($editingUnitId !== null || $editingParentId !== null || $unitTitle)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="resetUnitEditor">
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl max-w-2xl w-full" wire:click.stop>
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $editingParentId ? 'Zagadnienie' : 'Blok' }} — {{ $editingUnitId ? 'Edycja' : 'Nowa' }}</h2>
                            <button wire:click="resetUnitEditor" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Tytuł</label>
                                <input type="text" wire:model.live="unitTitle" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Opis</label>
                                <textarea wire:model.live="unitDescription" rows="3" class="w-full px-3 py-2 rounded border dark:bg-neutral-900"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Kategoria</label>
                                    <select wire:model.live="unitType" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" @if($editingParentId !== null) disabled @endif>
                                        <option value="theory">Teoria</option>
                                        <option value="practice_lab">Laboratorium</option>
                                        <option value="simulator">Symulator</option>
                                        <option value="practice_flight">Praktyka (lot)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Pozycja</label>
                                    <input type="number" min="0" wire:model.live="unitPosition" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" wire:model.live="unitIsRequired" />
                                    Wymagany
                                </label>
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Czas (minuty)</label>
                                    <input type="number" min="0" wire:model.live="unitDurationMinutes" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" />
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-2">
                            <button wire:click="resetUnitEditor" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 text-neutral-800 rounded">Anuluj</button>
                            <button wire:click="saveUnit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Zapisz jednostkę</button>
                        </div>
                    </div>
                </div>
            @endif
                            @if($topics->count())
                                <div class="border-t border-neutral-200 dark:border-neutral-700">
                                    @foreach($topics as $topic)
                                        <div class="px-4 py-2 flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm text-neutral-900 dark:text-white">{{ $topic->title }}</div>
                                                <div class="text-xs text-neutral-600 dark:text-neutral-400">{{ $topic->type }} • {{ $topic->is_required ? 'Wymagany' : 'Opcjonalny' }} • {{ $topic->duration_minutes ? $topic->duration_minutes.' min' : 'czas n/d' }} • Poz. {{ $topic->position ?? 0 }}</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button wire:click="moveUnit({{ $topic->id }}, 'up')" class="px-2 py-1 text-xs bg-neutral-200 hover:bg-neutral-300 rounded">↑</button>
                                                <button wire:click="moveUnit({{ $topic->id }}, 'down')" class="px-2 py-1 text-xs bg-neutral-200 hover:bg-neutral-300 rounded">↓</button>
                                                <button wire:click="editUnit({{ $topic->id }})" class="px-2 py-1 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded">Edytuj</button>
                                                <button wire:click="deleteUnit({{ $topic->id }})" wire:confirm="Czy na pewno usunąć to zagadnienie?" class="px-2 py-1 text-xs bg-red-600 hover:bg-red-700 text-white rounded">Usuń</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-4 py-2 text-sm text-neutral-500">Brak zagadnień w tym bloku.</div>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-3 text-neutral-500">Brak bloków spełniających kryteria.</div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Modal edycji/dodawania kursu -->
        @if($editingCourseId !== null || $name)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="resetForm">
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl max-w-2xl w-full" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $editingCourseId ? 'Edytuj kurs' : 'Nowy kurs' }}</h2>
                        <button wire:click="resetForm" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Nazwa</label>
                            <input type="text" wire:model.live="name" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" placeholder="OPW z Dronem" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Domyślne godziny lotów</label>
                                <input type="number" min="0" wire:model.live="default_flight_hours_required" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Domyślne godziny symulatora</label>
                                <input type="number" min="0" wire:model.live="default_sim_hours_required" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" />
                            </div>
                        </div>
                        <div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Opis</label>
                            <textarea wire:model.live="description" rows="3" class="w-full px-3 py-2 rounded border dark:bg-neutral-900" placeholder="Opis kursu..."></textarea>
                        </div>
                        <div>
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model.live="is_template" />
                                To jest szablon kursu
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model.live="require_lab" />
                                Wymagaj jednostek laboratoryjnych
                            </label>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-2">
                        <button wire:click="resetForm" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 text-neutral-800 rounded">Anuluj</button>
                        <button wire:click="saveCourse" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Zapisz</button>
                    </div>
                </div>
            </div>
        @endif
    </div>

<!-- Modal tworzenia instancji kursu -->
@if($showInstanceCreator)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="cancelInstanceCreator">
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl max-w-2xl w-full" wire:click.stop>
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Utwórz instancję kursu</h2>
                <button wire:click="cancelInstanceCreator" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Wybierz szablon kursu</label>
                    <select wire:model.live="selectedTemplateId" class="w-full px-3 py-2 rounded border dark:bg-neutral-900">
                        <option value="">-- Wybierz szablon --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Wybierz grupę (opcjonalnie)</label>
                    <select wire:model.live="selectedGroupId" class="w-full px-3 py-2 rounded border dark:bg-neutral-900">
                        <option value="">-- Brak / Ręczny wybór uczniów --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->users_count }} aktywnych)</option>
                        @endforeach
                    </select>
                </div>
                @if($selectedGroupId)
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 bg-blue-50 dark:bg-blue-900/20 p-3 rounded">
                        Wszyscy aktywni uczniowie z wybranej grupy zostaną automatycznie przypisani do kursu.
                    </div>
                @else
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-900/50 p-3 rounded">
                        Bez grupy — możesz później ręcznie dodać uczniów do kursu.
                    </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-2">
                <button wire:click="cancelInstanceCreator" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 text-neutral-800 rounded">Anuluj</button>
                <button wire:click="createCourseInstance" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded" @if(!$selectedTemplateId) disabled @endif>Utwórz instancję</button>
            </div>
        </div>
    </div>
@endif

</flux:main>
