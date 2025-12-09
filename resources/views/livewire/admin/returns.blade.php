<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Zwroty</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Zarządzaj zwrotami sprzętu i materiałów</p>
                </div>
            </div>
        </div>

        <!-- Barcode Scanner & Search -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex flex-col gap-4">
                <!-- Barcode Scanner -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Zeskanuj kod ucznia lub sprzętu
                    </label>
                    <input
                        type="text"
                        wire:model.live="barcode"
                        wire:keydown.enter="scanForReturn"
                        placeholder="Wpisz lub zeskanuj kod..."
                        autofocus
                        class="w-full px-4 py-3 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                    >

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

                <!-- Search Bar -->
                <div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Szukaj po uczniu, sprzęcie lub kodzie..."
                        class="w-full px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                    >
                </div>

                <!-- Error and Success Messages -->
                @if($error)
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-800 dark:text-red-200">
                        {{ $error }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Rentals List (from barcode scan) -->
        @if(count($studentRentals) > 0)
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">
                    Wypożyczenia ucznia: {{ $result['name'] ?? '' }}
                </h2>

                <div class="space-y-3">
                    @foreach($studentRentals as $rental)
                        <div class="flex items-center justify-between p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
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
                            </div>
                            <button
                                type="button"
                                wire:click="openReturnModal({{ $rental['id'] }})"
                                class="ml-4 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition"
                            >
                                Zwróć
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Active Rentals List -->
        <div class="space-y-3">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white px-4">Wszystkie aktywne wypożyczenia</h2>

            @forelse($rentalGroups as $group)
                <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-4">
                    <!-- Group Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="font-semibold text-neutral-900 dark:text-white">
                                @if($group->users->count() === 1)
                                    {{ $group->users->first()->name }}
                                @else
                                    {{ $group->users->count() }} uczniów
                                @endif
                            </div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">
                                Wypożyczone: {{ $group->created_at->format('d.m.Y H:i') }}
                                @if($group->rentedByUser)
                                    przez {{ $group->rentedByUser->name }}
                                @endif
                            </div>
                            @if($group->rental_notes)
                                <div class="text-sm text-neutral-500 dark:text-neutral-500 mt-1">
                                    <strong>Uwagi:</strong> {{ $group->rental_notes }}
                                </div>
                            @endif
                        </div>

                        @if($group->users->count() > 1)
                            <details class="text-sm">
                                <summary class="cursor-pointer text-blue-600 hover:text-blue-700">
                                    Zobacz uczniów
                                </summary>
                                <ul class="mt-2 space-y-1">
                                    @foreach($group->users as $user)
                                        <li class="text-neutral-700 dark:text-neutral-300">
                                            {{ $user->name }} ({{ $user->barcode }})
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </div>

                    <!-- Rentals in Group -->
                    <div class="space-y-2">
                        @foreach($group->rentals as $rental)
                            @if(!$rental->returned_at)
                                <div class="flex items-center justify-between p-3 bg-neutral-50 dark:bg-neutral-900 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            @if($rental->equipment)
                                                <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                    Sprzęt
                                                </span>
                                                <span class="font-medium text-neutral-900 dark:text-white">
                                                    {{ $rental->equipment->name }}
                                                </span>
                                                <span class="text-sm text-neutral-500">
                                                    {{ $rental->equipment->barcode }}
                                                </span>
                                            @elseif($rental->equipmentSet)
                                                <span class="px-2 py-1 text-xs font-medium rounded bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                                    Zestaw
                                                </span>
                                                <span class="font-medium text-neutral-900 dark:text-white">
                                                    {{ $rental->equipmentSet->name }}
                                                </span>
                                                <span class="text-sm text-neutral-500">
                                                    {{ $rental->equipmentSet->barcode }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="openReturnModal({{ $rental->id }})"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm font-medium text-orange-600 hover:text-orange-700 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded transition"
                                        title="Zwróć sprzęt"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                        </svg>
                                        Zwróć
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="text-neutral-500 dark:text-neutral-400 text-lg">
                        Brak aktywnych wypożyczeń
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($rentalGroups->hasPages())
                <div class="mt-4">
                    {{ $rentalGroups->links() }}
                </div>
            @endif
        </div>

        <!-- Return Modal -->
        @if($showReturnModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeReturnModal">
                <div class="flex items-start justify-center min-h-full px-4">
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between rounded-t-2xl">
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-white">
                                Zwrot sprzętu
                            </h3>
                            <button wire:click="closeReturnModal" type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 rounded-lg p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="px-6 py-5 space-y-5">
                            <!-- Return Notes -->
                            <div>
                                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                    Typ uwagi
                                </label>
                                <div class="flex gap-2 flex-wrap">
                                    <button
                                        type="button"
                                        wire:click="$set('noteType', 'info')"
                                        class="px-4 py-2 rounded-lg font-medium transition text-sm {{ $noteType === 'info' ? 'bg-blue-600 text-white border border-blue-600' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                                    >
                                        Info
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$set('noteType', 'warning')"
                                        class="px-4 py-2 rounded-lg font-medium transition text-sm {{ $noteType === 'warning' ? 'bg-yellow-600 text-white border border-yellow-600' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                                    >
                                        Ostrzeżenie
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$set('noteType', 'damage')"
                                        class="px-4 py-2 rounded-lg font-medium transition text-sm {{ $noteType === 'damage' ? 'bg-red-600 text-white border border-red-600' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                                    >
                                        Uszkodzenie
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$set('noteType', 'maintenance')"
                                        class="px-4 py-2 rounded-lg font-medium transition text-sm {{ $noteType === 'maintenance' ? 'bg-purple-600 text-white border border-purple-600' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                                    >
                                        Konserwacja
                                    </button>
                                </div>
                            </div>

                            <!-- Notes Textarea -->
                            <div>
                                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                    Uwagi przy zwrocie (opcjonalne)
                                </label>
                                <textarea
                                    wire:model="returnNotes"
                                    placeholder="Dodatkowe informacje o stanie sprzętu..."
                                    class="w-full px-4 py-3 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 rounded-b-2xl flex items-center justify-end gap-2">
                            <button
                                type="button"
                                wire:click="closeReturnModal"
                                class="px-4 py-2 rounded-lg text-neutral-900 dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-700 transition"
                            >
                                Anuluj
                            </button>
                            <button
                                type="button"
                                wire:click="processReturn"
                                class="px-4 py-2 rounded-lg bg-orange-600 text-white hover:bg-orange-700 transition font-medium"
                            >
                                Potwierdź zwrot
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>