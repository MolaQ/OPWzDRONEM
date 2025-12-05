<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Wyposażenie</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Zarządzaj przedmiotami wyposażenia pracowni</p>
                </div>
                <button
                    wire:click="create"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm border border-blue-500/70 dark:border-blue-500"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj wyposażenie
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Szukaj wyposażenia po nazwie, kodzie, modelu..."
                        class="w-full px-4 py-2 pl-10 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-[#880000] focus:border-transparent"
                    >
                    <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select wire:model.live="statusFilter" class="rounded-lg border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                    <option value="">— Wszystkie statusy —</option>
                    <option value="available">✓ Dostępny</option>
                    <option value="rented">✗ Wypożyczony</option>
                    <option value="maintenance">🔧 W naprawie</option>
                    <option value="under_service">🛠️ Konserwacja</option>
                    <option value="damaged">⚠️ Uszkodzony</option>
                    <option value="retired">⊘ Wycofany</option>
                </select>
            </div>
        </div>

        <!-- Equipment Table -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-100 dark:bg-neutral-800 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">
                                Wyposażenie
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
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($equipments as $equipment)
                            <tr wire:key="equipment-{{ $equipment->id }}" class="hover:bg-orange-50 dark:hover:bg-neutral-800 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
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
                                    <div class="flex flex-col gap-1 items-center">
                                        <div class="bg-white p-1 rounded border border-neutral-200 dark:border-neutral-600">
                                            <canvas class="barcode-canvas" data-barcode="{{ $equipment->barcode }}" style="height: 40px; display: block;"></canvas>
                                        </div>
                                        <span class="font-mono text-xs text-neutral-500 dark:text-neutral-400">{{ $equipment->barcode }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-neutral-900 dark:text-neutral-100">
                                        @if($equipment->model)
                                            <div class="font-medium">{{ $equipment->model }}</div>
                                        @endif
                                        @if($equipment->category)
                                            <div class="text-neutral-500 dark:text-neutral-400 text-xs">{{ $equipment->category }}</div>
                                        @endif
                                        @if(!$equipment->model && !$equipment->category)
                                            <span class="text-neutral-400">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusIcons = [
                                            'available' => '✓',
                                            'rented' => '✗',
                                            'maintenance' => '🔧',
                                            'under_service' => '🛠️',
                                            'damaged' => '⚠️',
                                            'retired' => '⊘',
                                        ];
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'rented' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                            'maintenance' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                            'under_service' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'damaged' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'retired' => 'bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300',
                                        ];
                                        $statusLabels = [
                                            'available' => 'Dostępny',
                                            'rented' => 'Wypożyczony',
                                            'maintenance' => 'W naprawie',
                                            'under_service' => 'Konserwacja',
                                            'damaged' => 'Uszkodzony',
                                            'retired' => 'Wycofany',
                                        ];
                                    @endphp
                                    <div class="flex items-center">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$equipment->status] ?? '' }}">
                                            {{ $statusLabels[$equipment->status] ?? $equipment->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @php
                                            $isRented = $equipment->rentals()->whereNull('returned_at')->exists();
                                        @endphp

                                        <button
                                            type="button"
                                            wire:click="edit({{ $equipment->id }})"
                                            class="inline-flex items-center justify-center w-7 h-7 {{ $isRented ? 'text-neutral-400 dark:text-neutral-600 cursor-not-allowed' : 'text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20' }} rounded transition-colors"
                                            title="{{ $isRented ? 'Nie można edytować wypożyczonego wyposażenia' : 'Edytuj' }}"
                                            @if($isRented) disabled @endif
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="delete({{ $equipment->id }})"
                                            wire:confirm="Czy na pewno chcesz usunąć to wyposażenie?"
                                            class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                            title="Usuń"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
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
                                    <p class="text-lg font-medium">Brak wyposażenia</p>
                                    <p class="mt-1 text-sm">Dodaj pierwsze wyposażenie, aby rozpocząć</p>
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
        <div class="fixed inset-0 z-50 overflow-y-auto bg-neutral-500 bg-opacity-75 dark:bg-neutral-900 dark:bg-opacity-75" wire:click="closeModal">
            <div class="flex items-start justify-center min-h-full px-4 py-8">
                <div class="bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl transform transition-all" wire:click.stop>
                    <!-- Header -->
                    <div class="sticky top-0 bg-white dark:bg-neutral-800 pb-4 mb-4 border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                                {{ $editingEquipmentId ? '✏️ Edytuj wyposażenie' : '✨ Nowe wyposażenie' }}
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

                    <!-- Content -->
                    <form wire:submit.prevent="save" class="px-6 pb-6 space-y-4">
                        <!-- Nazwa -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                Nazwa wyposażenia <span class="text-red-600 dark:text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="np. DJI Mavic 3"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Model -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                Model
                            </label>
                            <input
                                type="text"
                                wire:model="model"
                                class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="np. Mavic 3 Classic"
                            >
                            @error('model')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategoria -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                Kategoria
                            </label>
                            <input
                                type="text"
                                wire:model="category"
                                class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="np. Drona"
                            >
                            @error('category')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">
                                Status <span class="text-red-600 dark:text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-600 rounded-lg cursor-pointer hover:border-green-500 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50 dark:has-[:checked]:bg-green-950/20">
                                    <input type="radio" wire:model="status" value="available" class="w-4 h-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border border-green-400 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-200 dark:border-green-600">Dostępny</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-600 rounded-lg cursor-pointer hover:border-cyan-500 transition has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-50 dark:has-[:checked]:bg-cyan-950/20">
                                    <input type="radio" wire:model="status" value="rented" class="w-4 h-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border border-cyan-400 bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-200 dark:border-cyan-600">Wypożyczony</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-600 rounded-lg cursor-pointer hover:border-orange-500 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 dark:has-[:checked]:bg-orange-950/20">
                                    <input type="radio" wire:model="status" value="maintenance" class="w-4 h-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border border-orange-400 bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-200 dark:border-orange-600">W naprawie</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-600 rounded-lg cursor-pointer hover:border-yellow-500 transition has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50 dark:has-[:checked]:bg-yellow-950/20">
                                    <input type="radio" wire:model="status" value="under_service" class="w-4 h-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border border-yellow-400 bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200 dark:border-yellow-600">Konserwacja</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-600 rounded-lg cursor-pointer hover:border-red-500 transition has-[:checked]:border-red-500 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-950/20">
                                    <input type="radio" wire:model="status" value="damaged" class="w-4 h-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border border-red-400 bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-200 dark:border-red-600">Uszkodzony</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 border-neutral-200 dark:border-neutral-600 rounded-lg cursor-pointer hover:border-neutral-500 transition has-[:checked]:border-neutral-500 has-[:checked]:bg-neutral-50 dark:has-[:checked]:bg-neutral-700/20">
                                    <input type="radio" wire:model="status" value="retired" class="w-4 h-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border border-neutral-400 bg-neutral-100 text-neutral-700 dark:bg-neutral-700/40 dark:text-neutral-200 dark:border-neutral-600">Wycofany</span>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Opis -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                Opis
                            </label>
                            <textarea
                                wire:model="description"
                                rows="3"
                                class="w-full px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="Opcjonalny opis wyposażenia..."
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="sticky bottom-0 bg-white dark:bg-neutral-800 border-t border-neutral-200 dark:border-neutral-700 px-6 py-4 flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            wire:click="save"
                            class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition border border-orange-500/70 dark:border-orange-500"
                        >
                            @if($editingEquipmentId)
                                Zapisz zmiany
                            @else
                                Dodaj wyposażenie
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
                        height: 40,
                        displayValue: false,
                        margin: 4,
                        lineColor: '#000000',
                        background: '#ffffff'
                    });
                } catch(e) {
                    console.error('Barcode generation error:', e);
                }
            }
        });
    }
</script>
