<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Sprzęt</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Zarządzaj pojedynczymi przedmiotami sprzętu</p>
                </div>
                <button
                    wire:click="create"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-black hover:bg-neutral-800 text-[#880000] text-xs font-bold rounded transition-colors"
                >
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-[#880000] flex-shrink-0">
                        <span class="text-sm">+</span>
                    </span>
                    DODAJ SPRZĘT
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Szukaj sprzętu po nazwie, kodzie, modelu lub kategorii..."
                    class="w-full px-4 py-2 pl-10 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                >
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Equipment Table -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-100 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Sprzęt
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Kod
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Model / Kategoria
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Akcje
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($equipments as $equipment)
                            <tr wire:key="equipment-{{ $equipment->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                                {{ $equipment->name }}
                                            </div>
                                            @if($equipment->description)
                                                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ Str::limit($equipment->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <canvas class="barcode-canvas" data-barcode="{{ $equipment->barcode }}" style="height: 50px;"></canvas>
                                        <span class="font-mono text-xs text-neutral-500 dark:text-neutral-400 text-center">{{ $equipment->barcode }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-neutral-900 dark:text-neutral-100">
                                        @if($equipment->model)
                                            <div class="font-medium">{{ $equipment->model }}</div>
                                        @endif
                                        @if($equipment->category)
                                            <div class="text-neutral-500 dark:text-neutral-400">{{ $equipment->category }}</div>
                                        @endif
                                        @if(!$equipment->model && !$equipment->category)
                                            <span class="text-neutral-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'rented' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                            'maintenance' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'retired' => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300',
                                        ];
                                        $statusLabels = [
                                            'available' => 'Dostępny',
                                            'rented' => 'Wypożyczony',
                                            'maintenance' => 'W naprawie',
                                            'retired' => 'Wycofany',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$equipment->status] ?? '' }}">
                                        {{ $statusLabels[$equipment->status] ?? $equipment->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @php
                                            $isRented = $equipment->rentals()->whereNull('returned_at')->exists();
                                        @endphp

                                        <button
                                            type="button"
                                            wire:click="edit({{ $equipment->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 {{ $isRented ? 'bg-neutral-300 dark:bg-neutral-600 cursor-not-allowed' : 'bg-[#880000] hover:bg-red-900' }} text-white rounded border-2 border-white transition-colors"
                                            title="{{ $isRented ? 'Nie można edytować wypożyczonego sprzętu' : 'Edytuj' }}"
                                            @if($isRented) disabled @endif
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete({{ $equipment->id }})"
                                            wire:confirm="Czy na pewno chcesz usunąć ten sprzęt?"
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
                                    <p class="text-lg font-medium">Brak sprzętu</p>
                                    <p class="mt-1 text-sm">Dodaj pierwszy przedmiot, aby rozpocząć</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($equipments->hasPages())
                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                    {{ $equipments->links() }}
                </div>
            @endif
        </div>

        <!-- Create/Edit Modal -->
        @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeModal">
            <div class="flex items-start justify-center min-h-full px-4">
                <div class="bg-neutral-900 border border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-neutral-900 via-neutral-800 to-neutral-900 px-6 py-4 border-b border-neutral-700 flex items-center justify-between rounded-t-2xl">
                        <h3 class="text-xl font-bold text-white">
                            {{ $editingEquipmentId ? '✏️ Edytuj sprzęt' : '✨ Dodaj nowy sprzęt' }}
                        </h3>
                        <button wire:click="closeModal" type="button" class="text-neutral-400 hover:text-white hover:bg-neutral-800 rounded-lg p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-5">
                        <form wire:submit.prevent="save" class="space-y-5">
                            <!-- Nazwa -->
                            <div>
                                <label for="name" class="block mb-1.5 text-sm font-medium text-neutral-300">
                                    Nazwa <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    wire:model="name"
                                    placeholder="Wprowadź nazwę sprzętu..."
                                    class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 text-sm text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                                />
                                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <!-- Model -->
                            <div>
                                <label for="model" class="block mb-1.5 text-sm font-medium text-neutral-300">
                                    Model
                                </label>
                                <input
                                    id="model"
                                    type="text"
                                    wire:model="model"
                                    placeholder="Wprowadź model..."
                                    class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 text-sm text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                                />
                                @error('model')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <!-- Kategoria -->
                            <div>
                                <label for="category" class="block mb-1.5 text-sm font-medium text-neutral-300">
                                    Kategoria
                                </label>
                                <input
                                    id="category"
                                    type="text"
                                    wire:model="category"
                                    placeholder="Wprowadź kategorię..."
                                    class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 text-sm text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                                />
                                @error('category')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-neutral-300">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-2.5 p-3 bg-neutral-800 border-2 border-neutral-700 rounded-lg cursor-pointer transition hover:border-green-600 has-[:checked]:border-green-600 has-[:checked]:bg-green-950/30">
                                        <input
                                            type="radio"
                                            wire:model="status"
                                            value="available"
                                            class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                        />
                                        <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                            Dostępny
                                        </span>
                                    </label>
                                    <label class="flex items-center gap-2.5 p-3 bg-neutral-800 border-2 border-neutral-700 rounded-lg cursor-pointer transition hover:border-yellow-600 has-[:checked]:border-yellow-600 has-[:checked]:bg-yellow-950/30">
                                        <input
                                            type="radio"
                                            wire:model="status"
                                            value="maintenance"
                                            class="w-4 h-4 text-yellow-600 focus:ring-yellow-500 focus:ring-2"
                                        />
                                        <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                            W naprawie
                                        </span>
                                    </label>
                                    <label class="flex items-center gap-2.5 p-3 bg-neutral-800 border-2 border-neutral-700 rounded-lg cursor-pointer transition hover:border-neutral-500 has-[:checked]:border-neutral-500 has-[:checked]:bg-neutral-800/50">
                                        <input
                                            type="radio"
                                            wire:model="status"
                                            value="retired"
                                            class="w-4 h-4 text-neutral-600 focus:ring-neutral-500 focus:ring-2"
                                        />
                                        <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                            <span class="w-2 h-2 rounded-full bg-neutral-500"></span>
                                            Wycofany
                                        </span>
                                    </label>
                                </div>
                                @error('status')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <!-- Opis -->
                            <div>
                                <label for="description" class="block mb-1.5 text-sm font-medium text-neutral-300">
                                    Opis
                                </label>
                                <textarea
                                    id="description"
                                    wire:model="description"
                                    rows="3"
                                    placeholder="Wprowadź opis..."
                                    class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 text-sm text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                                ></textarea>
                                @error('description')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="bg-neutral-900 px-6 py-4 border-t border-neutral-700 flex justify-end gap-3 rounded-b-2xl">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-5 py-2.5 rounded-lg border-2 border-neutral-600 bg-neutral-800 text-neutral-300 text-sm font-medium hover:bg-neutral-700 hover:border-neutral-500 transition-all"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            wire:click="save"
                            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-[#880000] to-red-900 hover:from-red-900 hover:to-[#880000] text-white text-sm font-semibold shadow-lg hover:shadow-xl transition-all transform hover:scale-105 flex items-center gap-2"
                        >
                            @if($editingEquipmentId)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Zapisz zmiany
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Dodaj sprzęt
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</flux:main>

<!-- JsBarcode Script -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', function() {
        initBarcodes();
    });

    document.addEventListener('DOMContentLoaded', function() {
        initBarcodes();
    });

    // Re-render barcodes after Livewire updates
    Livewire.hook('morph.updated', () => {
        setTimeout(initBarcodes, 100);
    });

    function initBarcodes() {
        document.querySelectorAll('.barcode-canvas').forEach(canvas => {
            const barcode = canvas.dataset.barcode;
            if (barcode && typeof JsBarcode !== 'undefined') {
                try {
                    JsBarcode(canvas, barcode, {
                        format: 'CODE128',
                        width: 2,
                        height: 50,
                        displayValue: false,
                        margin: 6,
                        lineColor: '#000000',
                        background: 'transparent'
                    });
                } catch(e) {
                    console.error('Barcode generation error:', e);
                }
            }
        });
    }
</script>
