<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Filtry i wyszukiwanie -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <!-- Rząd 1: Wyszukiwanie -->
            <div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Szukaj po nazwie lub opisie..."
                    class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]"
                />
            </div>

            <!-- Rząd 2: Filtry i przyciski -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <select wire:model.live="active" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie statusy —</option>
                        <option value="1">Aktywna</option>
                        <option value="0">Nieaktywna</option>
                    </select>
                </div>

                <button wire:click="showCreateModal" class="inline-flex items-center gap-2 px-3 py-1.5 bg-black hover:bg-neutral-800 text-[#880000] text-xs font-bold rounded transition-colors">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-[#880000] flex-shrink-0">
                        <span class="text-sm">+</span>
                    </span>
                    DODAJ GRUPĘ
                </button>
            </div>
        </div>

        <!-- Tabela grup -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
        <thead class="bg-neutral-100 dark:bg-neutral-800">
            <tr>
                <th class="py-2 px-3 text-left">Nazwa grupy</th>
                <th class="py-2 px-3 text-left">Opis</th>
                <th class="py-2 px-3 text-center">Liczba użytkowników</th>
                <th class="py-2 px-3 text-center">Status</th>
                <th class="py-2 px-3 text-center">Akcje</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse($groups as $group)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                    <td class="py-2 px-3 font-semibold">{{ $group->name }}</td>
                    <td class="py-2 px-3 text-neutral-600 dark:text-neutral-400">{{ $group->description ?? '—' }}</td>
                    <td class="py-2 px-3 text-center">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $group->users_count }} {{ $group->users_count == 1 ? 'użytkownik' : 'użytkowników' }}
                        </span>
                    </td>
                    <td class="py-2 px-3 text-center">
                        <span class="px-2 py-1 text-xs rounded-full {{ $group->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $group->active ? 'Aktywna' : 'Nieaktywna' }}
                        </span>
                    </td>
                    <td class="py-2 px-3 flex gap-3 justify-center">
                        <button type="button" wire:click="editGroup({{ $group->id }})" class="inline-flex items-center justify-center w-8 h-8 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors cursor-pointer" title="Edytuj grupę">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button type="button" wire:click="deleteGroup({{ $group->id }})" wire:confirm="Czy na pewno usunąć tę grupę?" class="inline-flex items-center justify-center w-8 h-8 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors cursor-pointer" title="Usuń grupę">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-neutral-500">Brak grup.</td>
                </tr>
            @endforelse
        </tbody>
        </table>

        <!-- Paginacja -->
        <div class="mt-6">{{ $groups->links() }}</div>
    </div>
</flux:main>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
            <div class="bg-neutral-900 border border-neutral-700 rounded-2xl p-8 w-full max-w-2xl shadow-2xl transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-white">
                        {{ $editingGroup['id'] ? 'Edytuj grupę' : 'Dodaj nową grupę' }}
                    </h3>
                    <button wire:click="closeModal" class="text-neutral-400 hover:text-neutral-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveGroup" class="space-y-6" autocomplete="off">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nazwa grupy -->
                        <div>
                            <label for="name" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Nazwa grupy <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                wire:model.defer="editingGroup.name"
                                placeholder="np. Klasa IVOPW 2025/2026"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            />
                            @error('editingGroup.name')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Opis -->
                        <div>
                            <label for="description" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Opis grupy
                            </label>
                            <textarea
                                id="description"
                                wire:model.defer="editingGroup.description"
                                placeholder="Opcjonalny opis grupy..."
                                rows="3"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition resize-none"
                            ></textarea>
                            @error('editingGroup.description')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Status grupy -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-neutral-300">
                                Status grupy <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-4 p-4 bg-neutral-800 rounded-lg border border-neutral-700">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingGroup.active"
                                        value="1"
                                        class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                    />
                                    <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Aktywna
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingGroup.active"
                                        value="0"
                                        class="w-4 h-4 text-red-600 focus:ring-red-500 focus:ring-2"
                                    />
                                    <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Nieaktywna
                                    </span>
                                </label>
                            </div>
                            @error('editingGroup.active')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Przyciski akcji -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-neutral-700">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-6 py-2.5 rounded-lg border border-neutral-600 bg-neutral-800 text-neutral-300 font-medium hover:bg-neutral-700 transition"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-lg bg-[#880000] hover:bg-red-900 text-white font-semibold shadow-lg hover:shadow-xl transition transform hover:scale-105"
                        >
                            {{ $editingGroup['id'] ? '💾 Zapisz zmiany' : '➕ Dodaj grupę' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

