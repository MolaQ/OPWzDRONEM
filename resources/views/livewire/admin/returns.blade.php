<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Zwroty</h1>
        </div>

        <!-- Barcode Input -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="relative">
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                    Zeskanuj kod ucznia lub sprzętu
                </label>
                <input
                    type="text"
                    wire:model.live="barcode"
                    wire:keydown.enter="scanForReturn"
                    placeholder="Wpisz lub zeskanuj kod..."
                    autofocus
                    class="w-full px-4 py-3 rounded-lg border-2 border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white text-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >

                <!-- Suggestions Dropdown -->
                @if($showSuggestions && count($suggestions) > 0)
                    <div class="absolute z-10 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        @foreach($suggestions as $suggestion)
                            <button
                                type="button"
                                wire:click="selectSuggestion('{{ $suggestion['barcode'] }}', '{{ $suggestion['type'] }}', {{ $suggestion['id'] }}, '{{ addslashes($suggestion['name']) }}')"
                                class="w-full px-4 py-3 text-left hover:bg-neutral-100 dark:hover:bg-neutral-700 border-b border-neutral-100 dark:border-neutral-700 last:border-b-0"
                            >
                                <div class="flex items-center gap-3">
                                    @if($suggestion['type'] === 'student')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            Uczeń
                                        </span>
                                    @elseif($suggestion['type'] === 'equipment')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                            Sprzęt
                                        </span>
                                    @elseif($suggestion['type'] === 'equipment_set')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                            Zestaw
                                        </span>
                                    @endif
                                    <span class="font-medium text-neutral-900 dark:text-white">{{ $suggestion['name'] }}</span>
                                    <span class="text-sm text-neutral-500">{{ $suggestion['barcode'] }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-4 flex gap-2">
                <button
                    wire:click="scanForReturn"
                    class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition shadow-sm"
                >
                    Szukaj
                </button>
                <button
                    wire:click="resetForm"
                    class="px-6 py-2.5 bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-600 text-neutral-900 dark:text-white font-semibold rounded-lg transition"
                >
                    Wyczyść
                </button>
            </div>

            @if($error)
                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-800 dark:text-red-200">
                    {{ $error }}
                </div>
            @endif

            @if(session('success'))
                <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <!-- Student Rentals List -->
        @if(count($studentRentals) > 0)
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">
                    Wypożyczenia ucznia: {{ $result['name'] ?? '' }}
                </h2>

                <div class="space-y-3">
                    @foreach($studentRentals as $rental)
                        <div class="flex items-center justify-between p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition cursor-pointer"
                             wire:click="selectRentalItem('{{ $rental['barcode'] }}')">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded {{ $rental['type'] === 'equipment' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' }}">
                                        {{ $rental['type'] === 'equipment' ? 'Sprzęt' : 'Zestaw' }}
                                    </span>
                                    <span class="font-medium text-neutral-900 dark:text-white">
                                        {{ $rental['name'] }}
                                    </span>
                                    <span class="text-sm text-neutral-500">
                                        {{ $rental['barcode'] }}
                                    </span>
                                </div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Wypożyczone: {{ $rental['rented_at'] }}
                                </div>
                                @if($rental['notes'])
                                    <div class="text-sm text-neutral-500 dark:text-neutral-500 mt-1">
                                        Uwagi: {{ $rental['notes'] }}
                                    </div>
                                @endif
                            </div>
                            <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Return Form -->
        @if($activeRental)
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Potwierdź zwrot</h2>

                <!-- Rental Details -->
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg mb-4">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-2 py-1 text-xs font-medium rounded {{ $activeRental['type'] === 'equipment' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' }}">
                            {{ $activeRental['type'] === 'equipment' ? 'Sprzęt' : 'Zestaw' }}
                        </span>
                        <span class="font-semibold text-neutral-900 dark:text-white">
                            {{ $activeRental['name'] }}
                        </span>
                        <span class="text-sm text-neutral-500">
                            {{ $activeRental['barcode'] }}
                        </span>
                    </div>
                    <div class="text-sm text-neutral-700 dark:text-neutral-300">
                        <strong>Uczniowie:</strong> {{ $activeRental['students'] }}
                    </div>
                    <div class="text-sm text-neutral-700 dark:text-neutral-300">
                        <strong>Wypożyczone:</strong> {{ $activeRental['rented_at'] }}
                    </div>
                    @if($activeRental['notes'])
                        <div class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">
                            <strong>Uwagi przy wypożyczeniu:</strong> {{ $activeRental['notes'] }}
                        </div>
                    @endif
                </div>

                <!-- Return Notes -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            Typ uwagi
                        </label>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                wire:click="$set('noteType', 'info')"
                                class="px-4 py-2 rounded-lg font-medium transition {{ $noteType === 'info' ? 'bg-blue-600 text-white' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300' }}"
                            >
                                Info
                            </button>
                            <button
                                type="button"
                                wire:click="$set('noteType', 'warning')"
                                class="px-4 py-2 rounded-lg font-medium transition {{ $noteType === 'warning' ? 'bg-yellow-600 text-white' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300' }}"
                            >
                                Ostrzeżenie
                            </button>
                            <button
                                type="button"
                                wire:click="$set('noteType', 'damage')"
                                class="px-4 py-2 rounded-lg font-medium transition {{ $noteType === 'damage' ? 'bg-red-600 text-white' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300' }}"
                            >
                                Uszkodzenie
                            </button>
                            <button
                                type="button"
                                wire:click="$set('noteType', 'maintenance')"
                                class="px-4 py-2 rounded-lg font-medium transition {{ $noteType === 'maintenance' ? 'bg-purple-600 text-white' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300' }}"
                            >
                                Konserwacja
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            Uwagi przy zwrocie (opcjonalne)
                        </label>
                        <textarea
                            wire:model="returnNotes"
                            rows="3"
                            placeholder="Dodatkowe informacje o stanie sprzętu..."
                            class="w-full px-4 py-3 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500"
                        ></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button
                            wire:click="processReturn"
                            class="px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition shadow-sm"
                        >
                            Potwierdź zwrot
                        </button>
                        <button
                            wire:click="resetForm"
                            class="px-6 py-3 bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-600 text-neutral-900 dark:text-white font-semibold rounded-lg transition"
                        >
                            Anuluj
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>
