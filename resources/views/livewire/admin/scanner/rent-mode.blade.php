<!-- Rental Mode -->
<div class="space-y-4">
    <!-- Main Scan Input -->
    <div class="relative overflow-hidden rounded-xl border border-green-200 dark:border-green-700 bg-white dark:bg-neutral-900">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-2">Wypożyczanie sprzętu</h2>
            <p class="text-neutral-600 dark:text-neutral-400 mb-4">
                @if(empty($rentalGroup))
                    Zeskanuj kod ucznia (możesz dodać więcej uczniów)
                @else
                    Zeskanuj kody uczniów lub sprzętu/zestawów
                @endif
            </p>

            <div class="flex gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live="barcode"
                        wire:keydown.enter="scanForRental"
                        autofocus
                        placeholder="Zeskanuj kod (S - uczeń, E - sprzęt, Z - zestaw)"
                        class="w-full rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 px-4 py-3 text-lg font-mono"
                        x-data
                        x-init="setTimeout(()=>{$el.focus()},50)"
                    />
                </div>
                <button
                    wire:click="scanForRental"
                    class="px-6 py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Dodaj
                </button>
            </div>

            @if($showSuggestions)
                <div class="mt-3 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 max-h-72 overflow-y-auto shadow">
                    @foreach($suggestions as $i => $s)
                        <button
                            type="button"
                            wire:click="selectSuggestion('{{ $s['barcode'] }}')"
                            class="w-full text-left px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center gap-3"
                            wire:key="suggestion-rent-{{ $s['type'] }}-{{ $s['id'] }}-{{ $i }}"
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

    <!-- Rental Group and Cart -->
    @if(!empty($rentalGroup) || !empty($cart))
        <div class="grid md:grid-cols-2 gap-4">
            <!-- Rental Group (Students) -->
            <div class="relative overflow-hidden rounded-xl border border-blue-200 dark:border-blue-700 bg-white dark:bg-neutral-900 p-6">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-3">
                    Wypożyczający ({{ count($rentalGroup) }})
                </h3>
                @if(empty($rentalGroup))
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Zeskanuj uczniów</p>
                @else
                    <div class="space-y-2">
                        @foreach($rentalGroup as $student)
                            <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg" wire:key="student-{{ $student['id'] }}">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($student['name'], 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $student['name'] }}</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-mono">{{ $student['barcode'] }}</p>
                                    </div>
                                </div>
                                <button wire:click="removeFromGroup({{ $student['id'] }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Cart (Equipment/Sets) -->
            <div class="relative overflow-hidden rounded-xl border border-purple-200 dark:border-purple-700 bg-white dark:bg-neutral-900 p-6">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-3">
                    Przedmioty ({{ count($cart) }})
                </h3>
                @if(empty($cart))
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Zeskanuj sprzęt lub zestawy</p>
                @else
                    <div class="space-y-2">
                        @foreach($cart as $item)
                            <div class="flex items-center justify-between p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg" wire:key="cart-{{ $item['barcode'] }}">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded {{ $item['type'] === 'set' ? 'bg-orange-500' : 'bg-purple-500' }} text-white text-xs font-bold">
                                        {{ $item['type'] === 'set' ? 'Z' : 'E' }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $item['name'] }}</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-mono">{{ $item['barcode'] }}</p>
                                    </div>
                                </div>
                                <button wire:click="removeFromCart('{{ $item['barcode'] }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Available Equipment List -->
        @if(!empty($rentalGroup) && !empty($availableEquipment))
            <div class="relative overflow-hidden rounded-xl border border-green-200 dark:border-green-700 bg-white dark:bg-neutral-900 p-6">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-3">
                    Dostępny sprzęt ({{ count($availableEquipment) }})
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-96 overflow-y-auto">
                    @foreach($availableEquipment as $item)
                        @php
                            $inCart = collect($cart)->firstWhere('barcode', $item['barcode']);
                        @endphp
                        <button
                            wire:click="addToCartById('{{ $item['type'] }}', {{ $item['id'] }})"
                            @disabled($inCart)
                            class="text-left p-3 rounded-lg border-2 transition {{ $inCart ? 'bg-neutral-100 dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 opacity-50 cursor-not-allowed' : 'bg-white dark:bg-neutral-900 border-green-200 dark:border-green-700 hover:border-green-400 dark:hover:border-green-500' }}"
                            wire:key="available-{{ $item['type'] }}-{{ $item['id'] }}"
                        >
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded {{ $item['type'] === 'set' ? 'bg-orange-500' : 'bg-purple-500' }} text-white text-xs font-bold">
                                    {{ $item['type'] === 'set' ? 'Z' : 'E' }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $item['name'] }}</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-mono">{{ $item['barcode'] }}</p>
                                </div>
                                @if($inCart)
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Notes and Confirm -->
        @if(!empty($rentalGroup) && !empty($cart))
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-6">
                <label class="block font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Uwagi (opcjonalnie)</label>
                <textarea
                    wire:model="rentalNotes"
                    rows="2"
                    placeholder="Dodatkowe informacje o wypożyczeniu..."
                    class="w-full rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 px-4 py-2 mb-4"
                ></textarea>

                <div class="flex gap-3 justify-end">
                    <button
                        wire:click="switchMode('scan')"
                        class="px-6 py-3 rounded-lg border-2 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800"
                    >
                        Anuluj
                    </button>
                    <button
                        wire:click="confirmRental"
                        class="px-6 py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Potwierdź wypożyczenie ({{ count($rentalGroup) }} os., {{ count($cart) }} przedm.)
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>
