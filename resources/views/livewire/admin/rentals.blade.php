<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Wypożyczenia</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Zarządzaj aktywnymi i historycznymi wypożyczeniami</p>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        wire:click="openNewRentalModal"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#880000] text-white rounded-lg font-medium transition hover:bg-[#660000]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nowe wypożyczenie
                    </button>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 flex gap-4">
                        <span>Grupy: <strong class="text-neutral-900 dark:text-white">{{ $totalGroups }}</strong></span>
                        <span>Przedmioty: <strong class="text-neutral-900 dark:text-white">{{ $totalItems }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex gap-2">
                    <button
                        wire:click="$set('filter', 'active')"
                        class="px-4 py-2 rounded-lg font-medium transition text-sm {{ $filter === 'active' ? 'bg-[#880000] text-white' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                    >
                        Aktywne
                    </button>
                    <button
                        wire:click="$set('filter', 'all')"
                        class="px-4 py-2 rounded-lg font-medium transition text-sm {{ $filter === 'all' ? 'bg-[#880000] text-white' : 'bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                    >
                        Wszystkie
                    </button>
                </div>

                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Szukaj po uczniu, sprzęcie lub kodzie..."
                        class="w-full px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                    >
                </div>
            </div>
        </div>

        <!-- Rentals List -->
        <div class="space-y-3">
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

                                    @if($rental->returned_at)
                                        <div class="text-sm text-green-600 dark:text-green-400 mt-1">
                                            ✓ Zwrócone: {{ $rental->returned_at->format('d.m.Y H:i') }}
                                            @if($rental->return_notes)
                                                <span class="text-neutral-600 dark:text-neutral-400">
                                                    - {{ $rental->return_notes }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if(!$rental->returned_at)
                                    <button
                                        wire:click="forceReturn({{ $rental->id }})"
                                        wire:confirm="Czy na pewno chcesz wymusić zwrot tego sprzętu?"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm font-medium text-orange-600 hover:text-orange-700 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded transition"
                                        title="Wymuś zwrot"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                        </svg>
                                        Wymuś zwrot
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="text-neutral-500 dark:text-neutral-400 text-lg">
                        @if($search)
                            Nie znaleziono wypożyczeń pasujących do wyszukiwania
                        @elseif($filter === 'active')
                            Brak aktywnych wypożyczeń
                        @else
                            Brak wypożyczeń
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($rentalGroups->hasPages())
            <div class="mt-4">
                {{ $rentalGroups->links() }}
            </div>
        @endif
    </div>

    <!-- New Rental Modal -->
    @if($showNewRentalModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeNewRentalModal">
            <div class="flex items-start justify-center min-h-full px-4">
                <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between rounded-t-2xl">
                        <h3 class="text-xl font-bold text-neutral-900 dark:text-white">
                            Nowe wypożyczenie
                        </h3>
                        <button wire:click="closeNewRentalModal" type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 rounded-lg p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-5 space-y-5">
                        <!-- Barcode Scanner Input -->
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                            <label class="block text-sm font-semibold text-amber-900 dark:text-amber-200 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Skanuj kody kreskowe
                            </label>
                            <input
                                type="text"
                                wire:model="barcodeInput"
                                wire:keydown.enter="handleBarcode"
                                placeholder="Skanuj kod ucznia, sprzętu lub zestawu..."
                                autocomplete="off"
                                class="w-full px-4 py-2 rounded-lg border border-amber-300 dark:border-amber-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                autofocus
                            >
                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-2">
                                Wpisz kod i naciśnij Enter, lub wyszukaj ręcznie poniżej
                            </p>
                        </div>

                        <!-- Student Search -->
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                Wyszukaj ucznia
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="studentSearch"
                                placeholder="Imię, email lub kod kreskowy..."
                                class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                            >

                            @if(!empty($searchResults))
                                <div class="mt-2 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 max-h-48 overflow-y-auto">
                                    @foreach($searchResults as $student)
                                        <button
                                            type="button"
                                            wire:click="addStudent({{ $student['id'] }})"
                                            class="w-full px-4 py-2 text-left hover:bg-neutral-100 dark:hover:bg-neutral-700 border-b border-neutral-200 dark:border-neutral-700 last:border-0 transition"
                                        >
                                            <div class="font-medium text-neutral-900 dark:text-white">{{ $student['name'] }}</div>
                                            <div class="text-xs text-neutral-500">{{ $student['email'] }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Selected Students -->
                        @if(!empty($selectedStudents))
                            <div>
                                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                    Wybrani uczniowie
                                </label>
                                <div class="space-y-1">
                                    @foreach($selectedStudents as $studentId)
                                        @php
                                            $student = \App\Models\User::find($studentId);
                                        @endphp
                                        @if($student)
                                            <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 px-3 py-2 rounded-lg">
                                                <span class="text-sm text-neutral-900 dark:text-white">{{ $student->name }}</span>
                                                <button
                                                    type="button"
                                                    wire:click="removeStudent({{ $studentId }})"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Equipment Search -->
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                Wyszukaj sprzęt
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="equipmentSearch"
                                placeholder="Nazwa lub kod kreskowy..."
                                class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                            >

                            @if(!empty($availableEquipment))
                                <div class="mt-2 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 max-h-48 overflow-y-auto">
                                    @foreach($availableEquipment as $equip)
                                        <button
                                            type="button"
                                            wire:click="addEquipment({{ $equip['id'] }})"
                                            class="w-full px-4 py-2 text-left hover:bg-neutral-100 dark:hover:bg-neutral-700 border-b border-neutral-200 dark:border-neutral-700 last:border-0 transition"
                                        >
                                            <div class="font-medium text-neutral-900 dark:text-white">{{ $equip['name'] }}</div>
                                            <div class="text-xs text-neutral-500">{{ $equip['barcode'] }} - {{ $equip['model'] }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Selected Equipment -->
                        @if(!empty($selectedEquipment))
                            <div>
                                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                    Wybrany sprzęt
                                </label>
                                <div class="space-y-1">
                                    @foreach($selectedEquipment as $equipmentId)
                                        @php
                                            $equip = \App\Models\Equipment::find($equipmentId);
                                        @endphp
                                        @if($equip)
                                            <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 px-3 py-2 rounded-lg">
                                                <span class="text-sm text-neutral-900 dark:text-white">{{ $equip->name }} ({{ $equip->barcode }})</span>
                                                <button
                                                    type="button"
                                                    wire:click="removeEquipment({{ $equipmentId }})"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                                Uwagi (opcjonalne)
                            </label>
                            <textarea
                                wire:model="rentalNotes"
                                placeholder="Dodaj notatki dotyczące tego wypożyczenia..."
                                class="w-full px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 rounded-b-2xl flex items-center justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeNewRentalModal"
                            class="px-4 py-2 rounded-lg text-neutral-900 dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-700 transition"
                        >
                            Anuluj
                        </button>
                        <button
                            type="button"
                            wire:click="createRental"
                            class="px-4 py-2 rounded-lg bg-[#880000] text-white hover:bg-[#660000] transition font-medium"
                        >
                            Wypożycz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</flux:main>
