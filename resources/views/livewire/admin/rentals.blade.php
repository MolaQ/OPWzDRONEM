<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Wypożyczenia</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Zarządzaj aktywnymi i historycznymi wypożyczeniami</p>
                </div>
                <div class="text-sm text-neutral-600 dark:text-neutral-400 flex gap-4">
                    <span>Grupy: <strong class="text-neutral-900 dark:text-white">{{ $totalGroups }}</strong></span>
                    <span>Przedmioty: <strong class="text-neutral-900 dark:text-white">{{ $totalItems }}</strong></span>
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
</flux:main>
