<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Moje rezerwacje</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Historia i zarządzanie rezerwacjami sprzętu</p>
                </div>
                <a href="{{ route('admin.equipment') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    Zarezerwuj sprzęt
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Szukaj sprzętu lub użytkownika..."
                        class="w-full px-4 py-2 pl-10 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    wire:model.live="statusFilter"
                    class="rounded-lg border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-neutral-100 text-sm"
                >
                    <option value="">— Wszystkie statusy —</option>
                    <option value="pending">⏳ Oczekująca</option>
                    <option value="confirmed">✓ Potwierdzona</option>
                    <option value="active">▶ Aktywna</option>
                    <option value="completed">✓ Ukończona</option>
                    <option value="cancelled">✗ Anulowana</option>
                </select>

                <div class="flex gap-2">
                    <button
                        wire:click="setSortBy('reserved_from')"
                        @class([
                            'flex-1 px-3 py-2 rounded-lg text-sm font-medium transition border',
                            'bg-blue-600 dark:bg-blue-700 text-white border-blue-600 dark:border-blue-700' => $sortBy === 'reserved_from',
                            'bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700' => $sortBy !== 'reserved_from',
                        ])
                    >
                        {{ $sortBy === 'reserved_from' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }} Data
                    </button>
                    <button
                        wire:click="setSortBy('status')"
                        @class([
                            'flex-1 px-3 py-2 rounded-lg text-sm font-medium transition border',
                            'bg-blue-600 dark:bg-blue-700 text-white border-blue-600 dark:border-blue-700' => $sortBy === 'status',
                            'bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700' => $sortBy !== 'status',
                        ])
                    >
                        {{ $sortBy === 'status' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }} Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Reservations List -->
        <div class="space-y-3 flex-1 overflow-y-auto">
            @forelse($reservations as $reservation)
                @php
                    $item = $reservation->equipment ?? $reservation->equipmentSet;
                    $isSet = (bool) $reservation->equipment_set_id;
                @endphp
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start gap-4">
                        <!-- Equipment Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                                    {{ $item?->name }}
                                </h3>
                                <span class="text-xs font-mono text-neutral-500 dark:text-neutral-400">
                                    {{ $item?->barcode }}
                                </span>
                                @if($isSet)
                                    <span class="px-2 py-0.5 text-[11px] rounded bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">Zestaw</span>
                                @endif
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-center gap-3 mb-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                    @if($reservation->status === 'pending') bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200
                                    @elseif($reservation->status === 'confirmed') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                    @elseif($reservation->status === 'used') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                    @elseif($reservation->status === 'completed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                    @elseif($reservation->status === 'cancelled') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                    @endif
                                ">
                                    @switch($reservation->status)
                                        @case('pending')
                                            ⏳ Oczekująca
                                        @break
                                        @case('confirmed')
                                            ✓ Potwierdzona
                                        @break
                                        @case('used')
                                            ▶ Użyta
                                        @break
                                        @case('completed')
                                            ✓ Ukończona
                                        @break
                                        @case('cancelled')
                                            ✗ Anulowana
                                        @break
                                    @endswitch
                                </span>

                                @if($reservation->group)
                                <span class="text-xs text-neutral-600 dark:text-neutral-400">
                                    Grupa: <strong>{{ $reservation->group->name }}</strong>
                                </span>
                                @endif
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-neutral-600 dark:text-neutral-400">Od:</p>
                                    <p class="font-semibold text-neutral-900 dark:text-white">
                                        {{ $reservation->reserved_from->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-neutral-600 dark:text-neutral-400">Do:</p>
                                    <p class="font-semibold text-neutral-900 dark:text-white">
                                        {{ $reservation->reserved_until->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Reason and Notes -->
                            @if($reservation->reason || $reservation->notes)
                            <div class="mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                @if($reservation->reason)
                                <p class="text-sm text-neutral-700 dark:text-neutral-300">
                                    <strong>Powód:</strong> {{ $reservation->reason }}
                                </p>
                                @endif
                                @if($reservation->notes)
                                <p class="text-sm text-neutral-700 dark:text-neutral-300 mt-1">
                                    <strong>Notatki:</strong> {{ $reservation->notes }}
                                </p>
                                @endif
                            </div>
                            @endif

                            <!-- Confirmation Info -->
                            @if($reservation->confirmed_by && $reservation->confirmed_at)
                            <div class="mt-3 pt-3 border-t border-neutral-200 dark:border-neutral-700 text-xs text-neutral-600 dark:text-neutral-400">
                                Potwierdził: {{ $reservation->confirmedBy->name }} - {{ $reservation->confirmed_at->format('d.m.Y H:i') }}
                            </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-2">
                            <a
                                href="{{ $isSet ? route('admin.equipment-set.detail', $reservation->equipment_set_id) : route('admin.equipment.detail', $reservation->equipment->id) }}"
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition whitespace-nowrap text-center"
                            >
                                Szczegóły
                            </a>

                            @can('equipment.confirm-reservation')
                                @if($reservation->status === 'pending')
                                <button
                                    wire:click="confirmReservation({{ $reservation->id }})"
                                    class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition whitespace-nowrap"
                                >
                                    Potwierdź
                                </button>
                                @endif
                            @endcan

                            @can('rentals.create')
                                @if(in_array($reservation->status, ['pending', 'confirmed']) && (($reservation->equipment && $reservation->equipment->status === 'available') || $isSet))
                                <button
                                    wire:click="checkoutReservation({{ $reservation->id }})"
                                    wire:confirm="Czy wypożyczyć sprzęt teraz?"
                                    class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition whitespace-nowrap"
                                >
                                    Wypożycz
                                </button>
                                @endif
                            @endcan

                            @if($reservation->status === 'pending' && $reservation->reserved_from > now())
                            <button
                                wire:click="cancelReservation({{ $reservation->id }})"
                                onclick="return confirm('Czy na pewno chcesz anulować tę rezerwację?')"
                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition whitespace-nowrap"
                            >
                                Anuluj
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-neutral-400 dark:text-neutral-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-1">Brak rezerwacji</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        @if($statusFilter || $search)
                            Nie znaleziono rezerwacji spełniających kryteria wyszukiwania
                        @else
                            Nie masz jeszcze żadnych rezerwacji sprzętu
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reservations->hasPages())
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</flux:main>
