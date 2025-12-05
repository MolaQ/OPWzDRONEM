<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Zestawy sprzętu</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Zarządzaj zestawami sprzętu dostępnymi do wypożyczenia</p>
                </div>
                <button
                    wire:click="create"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-black hover:bg-neutral-800 text-[#880000] text-xs font-bold rounded transition-colors"
                >
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-[#880000] flex-shrink-0">
                        <span class="text-sm">+</span>
                    </span>
                    DODAJ ZESTAW
                </button>
            </div>
        </div>

    <!-- Flash Messages - Usuń bo używamy SweetAlert -->
    
    <!-- Search -->
    <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Szukaj zestawów po nazwie, kodzie lub opisie..."
                class="w-full px-4 py-2 pl-10 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
            >
            <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>
    <!-- Sets Table -->
    <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
        <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-100 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Zestaw
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Kod
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Sprzęty
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Akcje
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($sets as $set)
                            <tr wire:key="set-{{ $set->id }}" class="hover:bg-orange-100 dark:hover:bg-neutral-700 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                                {{ $set->name }}
                                            </div>
                                            @if($set->description)
                                                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ Str::limit($set->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <canvas class="barcode-canvas" data-barcode="{{ $set->barcode }}" style="height: 50px;"></canvas>
                                        <span class="font-mono text-xs text-neutral-500 dark:text-neutral-400 text-center">{{ $set->barcode }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-neutral-900 dark:text-neutral-100">
                                            {{ $set->equipments_count }} szt.
                                        </span>
                                        @if(!$set->isComplete())
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400">(niekompletny)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        @if($set->active)
                                            @php
                                                $setStatus = $set->status;
                                                $statusConfig = [
                                                    'available' => ['label' => 'Dostępny', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'],
                                                    'rented' => ['label' => 'Wypożyczony', 'color' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200'],
                                                    'incomplete' => ['label' => 'Niekompletny', 'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'],
                                                    'maintenance' => ['label' => 'W konserwacji', 'color' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'],
                                                    'damaged' => ['label' => 'Uszkodzony', 'color' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'],
                                                    'unavailable' => ['label' => 'Niedostępny', 'color' => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300'],
                                                ];
                                                $config = $statusConfig[$setStatus] ?? $statusConfig['unavailable'];
                                            @endphp
                                            <div class="flex items-center">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $config['color'] }}">
                                                    {{ $config['label'] }}
                                                </span>
                                            </div>
                                            @if($setStatus !== 'available' && $setStatus !== 'rented')
                                                @php
                                                    $problematicEquipment = $set->missingEquipment();
                                                @endphp
                                                @if($problematicEquipment->count() > 0)
                                                    <div class="text-xs text-neutral-600 dark:text-neutral-400 ml-1 mt-1">
                                                        @foreach($problematicEquipment->take(3) as $equip)
                                                            @php
                                                                $equipStatusLabels = [
                                                                    'rented' => 'Wypożyczony',
                                                                    'maintenance' => 'W naprawie',
                                                                    'under_service' => 'Konserwacja',
                                                                    'damaged' => 'Uszkodzony',
                                                                    'retired' => 'Wycofany',
                                                                ];
                                                                $equipStatusLabel = $equipStatusLabels[$equip->status] ?? ucfirst($equip->status);
                                                            @endphp
                                                            <div class="truncate">• {{ $equip->name }} ({{ $equipStatusLabel }})</div>
                                                        @endforeach
                                                        @if($problematicEquipment->count() > 3)
                                                            <div class="text-xs italic">+{{ $problematicEquipment->count() - 3 }} więcej...</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <div class="flex items-center">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                                    Nieaktywny
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @php
                                            $isRented = $set->rentals()->whereNull('returned_at')->exists();
                                        @endphp

                                        <button
                                            type="button"
                                            wire:click="edit({{ $set->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 {{ $isRented ? 'bg-neutral-300 dark:bg-neutral-600 cursor-not-allowed' : 'bg-[#880000] hover:bg-red-900' }} text-white rounded border-2 border-white transition-colors"
                                            title="{{ $isRented ? 'Nie można edytować wypożyczonego zestawu' : 'Edytuj' }}"
                                            @if($isRented) disabled @endif
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="toggleActive({{ $set->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors cursor-pointer"
                                            title="{{ $set->active ? 'Dezaktywuj' : 'Aktywuj' }}"
                                        >
                                            @if($set->active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            @endif
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete({{ $set->id }})"
                                            wire:confirm="Czy na pewno chcesz usunąć ten zestaw?"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors cursor-pointer"
                                            title="Usuń"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-lg font-medium">Brak zestawów</p>
                                    <p class="mt-1 text-sm">Dodaj pierwszy zestaw, aby rozpocząć</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sets->hasPages())
                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                    {{ $sets->links() }}
                </div>
            @endif
        </div>

        <!-- Create/Edit Modal -->
        <div x-data="{ show: @entangle('showModal') }" x-show="show" @click.away="show = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-neutral-500 bg-opacity-75 dark:bg-neutral-900 dark:bg-opacity-75"
                @click="show = false"
            ></div>

            <!-- Modal -->
            <div
                x-show="show"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 my-8 text-left align-middle transition-all transform bg-white dark:bg-neutral-800 shadow-xl rounded-2xl z-50"
            >
                <div class="sticky top-0 bg-white dark:bg-neutral-800 pb-4 mb-4 border-b border-neutral-200 dark:border-neutral-700 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $editingSetId ? 'Edytuj zestaw' : 'Nowy zestaw' }}
                        </h3>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                    Nazwa zestawu *
                                </label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    placeholder="np. DJI Mavic 3 - Zestaw kompletny"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                    Opis
                                </label>
                                <textarea
                                    wire:model="description"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    placeholder="Opcjonalny opis zestawu..."
                                ></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Equipment Selection -->
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                    Sprzęty w zestawie *
                                </label>

                                <!-- Equipment Search/Scan -->
                                <div class="mb-3 relative">
                                    <input
                                        type="text"
                                        wire:model.live="equipmentSearch"
                                        placeholder="Wpisz kod lub nazwę sprzętu (E##########)"
                                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent font-mono text-sm"
                                    >

                                    @if($showEquipmentSuggestions)
                                        <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                            @foreach($equipmentSuggestions as $eq)
                                                <button
                                                    type="button"
                                                    wire:click="selectEquipmentFromSuggestion({{ $eq['id'] }})"
                                                    class="w-full text-left px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-700 flex items-center justify-between gap-2 text-sm"
                                                    wire:key="suggestion-{{ $eq['id'] }}"
                                                >
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $eq['name'] }}</div>
                                                        @if($eq['model'])
                                                            <div class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $eq['model'] }}</div>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs font-mono text-neutral-500 dark:text-neutral-400 flex-shrink-0">{{ $eq['barcode'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Selected Equipment List -->
                                @if(!empty($selectedEquipment))
                                    <div class="mb-3 space-y-1.5 max-h-40 overflow-y-auto border border-neutral-300 dark:border-neutral-600 rounded-lg p-2">
                                        @foreach($selectedEquipment as $eqId)
                                            @php
                                                $eq = $availableEquipment->firstWhere('id', $eqId);
                                                if ($eq) {
                                                    $statusLabel = [
                                                        'available' => 'Dostępny',
                                                        'rented' => 'Wypożyczony',
                                                        'maintenance' => 'W naprawie',
                                                        'under_service' => 'Konserwacja',
                                                        'damaged' => 'Uszkodzony',
                                                        'retired' => 'Wycofany'
                                                    ][$eq->status] ?? ucfirst($eq->status);
                                                    $statusColorClass = [
                                                        'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'rented' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                                        'maintenance' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                                        'under_service' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                        'damaged' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                        'retired' => 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300'
                                                    ][$eq->status] ?? 'bg-neutral-100 text-neutral-800';
                                                }
                                            @endphp
                                            @if($eq)
                                                <div wire:key="selected-{{ $eq->id }}" class="flex items-center justify-between p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-sm">
                                                    <div class="flex-1 min-w-0 mr-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $eq->name }}</span>
                                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColorClass }} flex-shrink-0">
                                                                {{ $statusLabel }}
                                                            </span>
                                                        </div>
                                                        <span class="text-xs font-mono text-neutral-500 dark:text-neutral-400">{{ $eq->barcode }}</span>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        wire:click="removeEquipment({{ $eq->id }})"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 flex-shrink-0"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <!-- All Available Equipment (Collapsed) -->
                                <details class="border border-neutral-300 dark:border-neutral-600 rounded-lg text-sm">
                                    <summary class="px-3 py-2 cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-700 font-medium text-neutral-700 dark:text-neutral-300">
                                        Przeglądaj wszystkie sprzęty ({{ $availableEquipment->count() }})
                                    </summary>
                                    <div class="max-h-48 overflow-y-auto p-2 space-y-1">
                                        @foreach($availableEquipment as $equipment)
                                            @php
                                                $statusLabel = [
                                                    'available' => 'Dostępny',
                                                    'rented' => 'Wypożyczony',
                                                    'maintenance' => 'W naprawie',
                                                    'under_service' => 'Konserwacja',
                                                    'damaged' => 'Uszkodzony',
                                                    'retired' => 'Wycofany'
                                                ][$equipment->status] ?? ucfirst($equipment->status);
                                                $statusColorClass = [
                                                    'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'rented' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                                    'maintenance' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                                    'under_service' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'damaged' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'retired' => 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300'
                                                ][$equipment->status] ?? 'bg-neutral-100 text-neutral-800';
                                            @endphp
                                            <label wire:key="equipment-{{ $equipment->id }}" class="flex items-center gap-2 p-2 hover:bg-neutral-50 dark:hover:bg-neutral-700 rounded-lg cursor-pointer transition">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="selectedEquipment"
                                                    value="{{ $equipment->id }}"
                                                    class="w-4 h-4 text-orange-600 border-neutral-300 rounded focus:ring-orange-500"
                                                >
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $equipment->name }}</span>
                                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColorClass }} flex-shrink-0">
                                                            {{ $statusLabel }}
                                                        </span>
                                                    </div>
                                                    @if($equipment->model)
                                                        <div class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $equipment->model }}</div>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-mono text-neutral-500 dark:text-neutral-400 flex-shrink-0">{{ $equipment->barcode }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </details>

                                @error('selectedEquipment')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    Wybrano: {{ count($selectedEquipment) }} szt.
                                </p>
                            </div>

                            <!-- Active Toggle -->
                            <div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="active"
                                        class="w-4 h-4 text-orange-600 border-neutral-300 rounded focus:ring-orange-500"
                                    >
                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                        Zestaw aktywny (dostępny do wypożyczenia)
                                    </span>
                                </label>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="flex-1 px-4 py-2 border-2 border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-700 dark:text-neutral-300 font-medium hover:bg-neutral-50 dark:hover:bg-neutral-700 transition text-sm"
                                >
                                    Anuluj
                                </button>
                                <button
                                    type="submit"
                                    class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition text-sm"
                                >
                                    {{ $editingSetId ? 'Zapisz' : 'Utwórz' }}
                                </button>
                            </div>
                        </form>
                </div>
            </div>
        </div>

        @script
        <script>
            // Generate barcodes for all canvas elements
            document.addEventListener('livewire:navigated', function() {
                generateBarcodes();
            });

            $wire.on('set-saved', () => {
                setTimeout(() => generateBarcodes(), 100);
            });

            function generateBarcodes() {
                document.querySelectorAll('.barcode-canvas').forEach(canvas => {
                    const barcode = canvas.dataset.barcode;
                    if (barcode && typeof JsBarcode !== 'undefined') {
                        try {
                            JsBarcode(canvas, barcode, {
                                format: 'CODE128',
                                width: 2,
                                height: 50,
                                displayValue: false,
                                margin: 0
                            });
                        } catch (e) {
                            console.error('Barcode generation failed:', e);
                        }
                    }
                });
            }

            generateBarcodes();
        </script>
        @endscript
    </div>
</flux:main>
