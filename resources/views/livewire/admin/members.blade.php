
<div class="flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-6 shadow">

    <div class="mb-6 flex flex-wrap items-center gap-4 justify-between">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Szukaj po nazwie, mailu lub grupie..."
            class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 shadow-sm focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100"
        />

        <select wire:model.live="role" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie role —</option>
            @foreach($roles as $r)
                <option value="{{ $r }}">{{ ucfirst($r) }}</option>
            @endforeach
        </select>

        <select wire:model.live="group_id" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie grupy —</option>
            @foreach($groups as $g)
                <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="active" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-900 dark:text-neutral-100">
            <option value="">— Wszystkie statusy —</option>
            <option value="1">Aktywny</option>
            <option value="0">Nieaktywny</option>
        </select>

        <button wire:click="showCreateModal" class="rounded bg-[#880000] text-white px-4 py-2 font-semibold hover:bg-red-900 transition">
            Dodaj użytkownika
        </button>
    </div>

    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 rounded-lg overflow-hidden">
        <thead class="bg-neutral-100 dark:bg-neutral-800">
            <tr>
                <th class="py-2 px-3">Imię i nazwisko</th>
                <th class="py-2 px-3">Email</th>
                <th class="py-2 px-3">Rola</th>
                <th class="py-2 px-3">Grupa</th>
                <th class="py-2 px-3">Status</th>
                <th class="py-2 px-3">Akcje</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse($users as $user)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                    <td class="py-2 px-3">{{ $user->name }}</td>
                    <td class="py-2 px-3">{{ $user->email }}</td>
                    <td class="py-2 px-3 capitalize">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                    <td class="py-2 px-3">{{ $user->group?->name ?? '—' }}</td>
                    <td class="py-2 px-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $user->active ? 'Aktywny' : 'Nieaktywny' }}
                        </span>
                    </td>
                    <td class="py-2 px-3 flex gap-2">
                        <button wire:click="editUser({{ $user->id }})" class="text-blue-600 hover:underline">Edytuj</button>
                        <button
                            onclick="if(confirm('Czy na pewno chcesz usunąć tego użytkownika?')) { @this.deleteUser({{ $user->id }}) }"
                            class="text-[#880000] hover:text-red-700 font-semibold">Usuń</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-neutral-500">Brak użytkowników.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">{{ $users->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">
            <div class="bg-neutral-900 border border-neutral-700 rounded-2xl p-8 w-full max-w-2xl shadow-2xl transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-white">
                        {{ $editingUser['id'] ? 'Edytuj użytkownika' : 'Dodaj nowego użytkownika' }}
                    </h3>
                    <button wire:click="closeModal" class="text-neutral-400 hover:text-neutral-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveUser" class="space-y-6" autocomplete="off">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Imię i nazwisko -->
                        <div class="md:col-span-2">
                            <label for="name" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Imię i nazwisko <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                wire:model.defer="editingUser.name"
                                placeholder="Jan Kowalski"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            />
                            @error('editingUser.name')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label for="email" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Adres email <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                type="email"
                                wire:model.defer="editingUser.email"
                                placeholder="jan.kowalski@example.com"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            />
                            @error('editingUser.email')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Rola -->
                        <div>
                            <label for="role" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Rola <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="role"
                                wire:model.defer="editingUser.role"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            >
                                <option value="student">👤 Student</option>
                                <option value="admin">⚡ Administrator</option>
                                <option value="instructor">🎓 Instruktor</option>
                                <option value="guest">👁️ Gość</option>
                            </select>
                            @error('editingUser.role')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Grupa -->
                        <div>
                            <label for="group_id" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Grupa
                            </label>
                            <select
                                id="group_id"
                                wire:model.defer="editingUser.group_id"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            >
                                <option value="">Brak przypisania</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('editingUser.group_id')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Hasło -->
                        <div class="md:col-span-2">
                            <label for="password" class="block mb-2 text-sm font-semibold text-neutral-300">
                                Hasło @if(!$editingUser['id'])<span class="text-red-500">*</span>@endif
                                @if($editingUser['id'])<span class="text-neutral-500 text-xs">(pozostaw puste, aby nie zmieniać)</span>@endif
                            </label>
                            <input
                                id="password"
                                type="password"
                                wire:model.defer="editingUser.password"
                                placeholder="{{ $editingUser['id'] ? 'Pozostaw puste, aby nie zmieniać hasła' : 'Minimum 8 znaków' }}"
                                class="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                            />
                            @error('editingUser.password')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Status konta -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-semibold text-neutral-300">
                                Status konta <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-4 p-4 bg-neutral-800 rounded-lg border border-neutral-700">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingUser.active"
                                        value="1"
                                        class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                    />
                                    <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Aktywne
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingUser.active"
                                        value="0"
                                        class="w-4 h-4 text-red-600 focus:ring-red-500 focus:ring-2"
                                    />
                                    <span class="flex items-center gap-2 text-sm font-medium text-neutral-300">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Nieaktywne
                                    </span>
                                </label>
                            </div>
                            @error('editingUser.active')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
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
                            {{ $editingUser['id'] ? '💾 Zapisz zmiany' : '➕ Dodaj użytkownika' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
