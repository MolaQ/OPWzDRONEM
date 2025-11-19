
<div class="flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-6 shadow">

    <div class="mb-6 flex flex-wrap items-center gap-4 justify-between">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Szukaj po nazwie lub opisie..."
            class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 shadow-sm focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100"
        />

        <select wire:model.live="active" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie statusy —</option>
            <option value="1">Aktywna</option>
            <option value="0">Nieaktywna</option>
        </select>

        <button wire:click="showCreateModal" class="rounded bg-[#880000] text-white px-4 py-2 font-semibold hover:bg-red-900 transition">
            Dodaj grupę
        </button>
    </div>

    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 rounded-lg overflow-hidden">
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
                    <td class="py-2 px-3 flex gap-2 justify-center">
                        <button wire:click="editGroup({{ $group->id }})" class="text-blue-600 hover:underline">Edytuj</button>
                        <button
                            onclick="if(confirm('Czy na pewno chcesz usunąć tę grupę?')) { @this.deleteGroup({{ $group->id }}) }"
                            class="text-[#880000] hover:text-red-700 font-semibold">Usuń</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-neutral-500">Brak grup.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">{{ $groups->links() }}</div>

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

