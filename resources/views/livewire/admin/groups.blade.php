<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Filtry i wyszukiwanie -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Szukaj po nazwie lub opisie..."
                    class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <select wire:model.live="active" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-600 dark:bg-neutral-800 dark:text-neutral-100">
                        <option value="">— Wszystkie statusy —</option>
                        <option value="1">Aktywna</option>
                        <option value="0">Nieaktywna</option>
                    </select>
                </div>

                <button
                    wire:click="showCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all shadow-md
                           bg-white text-neutral-900 border-2 border-neutral-300 hover:border-neutral-400 hover:shadow-lg
                           focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-400
                           dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:shadow-lg dark:hover:bg-neutral-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj grupę
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
                            <td class="py-2 px-3 font-semibold text-neutral-900 dark:text-white">{{ $group->name }}</td>
                            <td class="py-2 px-3 text-neutral-600 dark:text-neutral-400">{{ $group->description ?? '—' }}</td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-700">
                                    {{ $group->users_count }} {{ $group->users_count == 1 ? 'użytkownik' : 'użytkowników' }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $group->active ? 'border-green-500 text-green-700 bg-green-50 dark:bg-green-900/20 dark:text-green-200' : 'border-red-500 text-red-700 bg-red-50 dark:bg-red-900/20 dark:text-red-200' }}">
                                    {{ $group->active ? 'Aktywna' : 'Nieaktywna' }}
                                </span>
                            </td>
                            <td class="py-2 px-3 flex gap-2 justify-center">
                                <button type="button" wire:click="editGroup({{ $group->id }})" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Edytuj">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                </button>
                                <button type="button" wire:click="deleteGroup({{ $group->id }})" wire:confirm="Czy na pewno usuńąć tę grupę?" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 rounded transition-colors border border-neutral-300 dark:border-neutral-600" title="Usuń">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
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

            <div class="mt-6">{{ $groups->links() }}</div>
        </div>
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeModal">
                <div class="flex items-start justify-center min-h-full px-4">
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 flex items-center justify-between rounded-t-2xl">
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-white">
                                {{ $editingGroup['id'] ? 'Edytuj grupę' : 'Dodaj nową grupę' }}
                            </h3>
                            <button wire:click="closeModal" type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 rounded-lg p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <form wire:submit.prevent="saveGroup" class="p-6 space-y-6" autocomplete="off">
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Nazwa grupy -->
                                <div>
                                    <label for="name" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                        Nazwa grupy <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        type="text"
                                        wire:model.defer="editingGroup.name"
                                        placeholder="np. Klasa IVOPW 2025/2026"
                                        class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                    />
                                    @error('editingGroup.name')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                                </div>

                                <!-- Opis -->
                                <div>
                                    <label for="description" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                        Opis grupy
                                    </label>
                                    <textarea
                                        id="description"
                                        wire:model.defer="editingGroup.description"
                                        rows="3"
                                        placeholder="Opcjonalny opis grupy..."
                                        class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition resize-none"
                                    ></textarea>
                                    @error('editingGroup.description')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                                </div>

                                <!-- Status grupy -->
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                        Status grupy <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-4 p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                        <label class="flex items-center gap-3 cursor-pointer flex-1">
                                            <input
                                                type="radio"
                                                wire:model.defer="editingGroup.active"
                                                value="1"
                                                class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                            />
                                            <span class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1 rounded-full border border-green-500 text-green-700 bg-green-50 dark:bg-green-900/20 dark:text-green-200">
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
                                            <span class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1 rounded-full border border-red-500 text-red-700 bg-red-50 dark:bg-red-900/20 dark:text-red-200">
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                Nieaktywna
                                            </span>
                                        </label>
                                    </div>
                                    @error('editingGroup.active')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <!-- Przyciski akcji -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    class="px-6 py-2.5 rounded-lg border border-neutral-300 text-neutral-700 bg-white hover:bg-neutral-50 font-medium transition dark:border-neutral-600 dark:text-neutral-300 dark:bg-neutral-800 dark:hover:bg-neutral-700"
                                >
                                    Anuluj
                                </button>
                                <button
                                    type="submit"
                                    class="px-6 py-2.5 rounded-lg bg-white text-neutral-900 border-2 border-neutral-300 hover:border-neutral-400 hover:shadow-lg font-medium transition-all dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                                >
                                    {{ $editingGroup['id'] ? 'Zapisz zmiany' : 'Dodaj grupę' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>
