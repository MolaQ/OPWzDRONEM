<!-- Return Mode -->
<div class="space-y-4">
    @if(!$activeRental && empty($studentRentals))
        <!-- Step 1: Scan Item or Student -->
        <div class="relative overflow-hidden rounded-xl border border-orange-200 dark:border-orange-700 bg-white dark:bg-neutral-900">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">Zwrot sprzętu</h2>
                <p class="text-neutral-600 dark:text-neutral-400 mb-4">Zeskanuj kod ucznia (aby zobaczyć wszystkie wypożyczenia) lub kod sprzętu/zestawu</p>

                <div class="flex gap-3">
                    <div class="flex-1">
                        <input
                            type="text"
                            wire:model.live="barcode"
                            wire:keydown.enter="scanForReturn"
                            autofocus
                            placeholder="Zeskanuj kod (S - uczeń, E - sprzęt, Z - zestaw)"
                            class="w-full rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 px-4 py-3 text-lg font-mono"
                            x-data
                            x-init="setTimeout(()=>{$el.focus()},50)"
                        />
                    </div>
                    <button
                        wire:click="scanForReturn"
                        class="px-6 py-3 rounded-lg bg-orange-600 text-white font-semibold hover:bg-orange-700"
                    >
                        Szukaj
                    </button>
                </div>

                @if($showSuggestions)
                    <div class="mt-3 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 max-h-72 overflow-y-auto shadow">
                        @foreach($suggestions as $i => $s)
                            <button
                                type="button"
                                wire:click="selectSuggestion('{{ $s['barcode'] }}')"
                                class="w-full text-left px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center gap-3"
                                wire:key="suggestion-return-{{ $s['type'] }}-{{ $s['id'] }}-{{ $i }}"
                            >
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded {{ $s['type'] === 'student' ? 'bg-blue-500' : ($s['type'] === 'set' ? 'bg-orange-500' : 'bg-purple-500') }} text-white text-xs font-bold">
                                    {{ strtoupper(substr($s['type'],0,1)) }}
                                </span>
                                <span class="flex-1 truncate">{{ $s['name'] }}</span>
                                <span class="font-mono text-xs text-neutral-500 dark:text-neutral-400">{{ $s['barcode'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                @if($error)
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-800 dark:text-red-200">
                        {{ $error }}
                    </div>
                @endif
            </div>
        </div>
    @elseif(!empty($studentRentals))
        <!-- Student Rentals List -->
        <div class="relative overflow-hidden rounded-xl border border-blue-200 dark:border-blue-700 bg-white dark:bg-neutral-900">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Aktywne wypożyczenia</h2>
                    <button
                        wire:click="cancelReturn"
                        class="px-4 py-2 rounded-lg border-2 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800"
                    >
                        Anuluj
                    </button>
                </div>
                <p class="text-neutral-600 dark:text-neutral-400 mb-4">Wybierz przedmiot do zwrotu ({{ count($studentRentals) }})</p>

                <div class="space-y-3">
                    @foreach($studentRentals as $rental)
                        <button
                            wire:click="selectRentalForReturn({{ $rental['rental_id'] }})"
                            class="w-full text-left p-4 bg-orange-50 dark:bg-orange-900/20 border-2 border-orange-200 dark:border-orange-700 rounded-lg hover:border-orange-400 dark:hover:border-orange-500 transition"
                            wire:key="rental-{{ $rental['rental_id'] }}"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded {{ $rental['item_type'] === 'set' ? 'bg-orange-500' : 'bg-purple-500' }} text-white text-xs font-bold">
                                            {{ $rental['item_type'] === 'set' ? 'Z' : 'E' }}
                                        </span>
                                        <div>
                                            <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $rental['item_name'] }}</p>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400 font-mono">{{ $rental['item_barcode'] }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm mt-2">
                                        <div>
                                            <span class="text-neutral-500 dark:text-neutral-400">Wypożyczono:</span>
                                            <span class="text-neutral-900 dark:text-neutral-100">{{ $rental['rented_at']->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-neutral-500 dark:text-neutral-400">Czas:</span>
                                            <span class="text-neutral-900 dark:text-neutral-100">{{ $rental['duration'] }}</span>
                                        </div>
                                    </div>
                                    @if($rental['notes'])
                                        <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">
                                            <strong>Uwagi:</strong> {{ $rental['notes'] }}
                                        </p>
                                    @endif
                                </div>
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- Step 2: Rental Found - Confirm Return -->
        <div class="relative overflow-hidden rounded-xl border border-blue-200 dark:border-blue-700 bg-white dark:bg-neutral-900 p-6">
            <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100 mb-4">Informacje o wypożyczeniu</h3>

            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Przedmiot</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $activeRental['item_name'] }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-mono">{{ $activeRental['item_barcode'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Wypożyczyli (wszyscy zostaną rozliczeni)</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $activeRental['rented_by'] }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Zwrot zostanie przypisany całej grupie
                    </p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Data wypożyczenia</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $activeRental['rented_at']->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Czas wypożyczenia</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $activeRental['duration'] }}</p>
                </div>
                @if($activeRental['notes'])
                    <div class="col-span-2">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Uwagi przy wypożyczeniu</p>
                        <p class="text-neutral-900 dark:text-neutral-100">{{ $activeRental['notes'] }}</p>
                    </div>
                @endif
            </div>

            <div class="space-y-4 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                <div>
                    <label class="block font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Stan przedmiotu</label>
                    <select
                        wire:model="noteType"
                        class="w-full rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 px-4 py-2"
                    >
                        <option value="info">Bez uwag - zwrot standardowy</option>
                        <option value="warning">Drobne uwagi</option>
                        <option value="damage">Uszkodzenie</option>
                        <option value="maintenance">Wymaga konserwacji</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Uwagi przy zwrocie (opcjonalnie)</label>
                    <textarea
                        wire:model="returnNotes"
                        rows="3"
                        placeholder="Opisz stan przedmiotu, zauważone problemy..."
                        class="w-full rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 px-4 py-2"
                    ></textarea>
                </div>
            </div>

            <div class="flex gap-3 justify-end mt-6">
                <button
                    wire:click="cancelReturn"
                    class="px-6 py-3 rounded-lg border-2 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800"
                >
                    Anuluj
                </button>
                <button
                    wire:click="confirmReturn"
                    class="px-6 py-3 rounded-lg bg-orange-600 text-white font-semibold hover:bg-orange-700"
                >
                    Potwierdź zwrot
                </button>
            </div>
        </div>
    @endif
</div>
