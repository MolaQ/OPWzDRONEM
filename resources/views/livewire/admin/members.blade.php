
<div class="flex-1 overflow-auto rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-6 shadow">

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-[#880000] text-white rounded font-semibold text-center">
            {{ session('message') }}
        </div>
    @endif

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
                    <td class="py-2 px-3 capitalize">{{ $user->role }}</td>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-neutral-900 border border-neutral-700 rounded-xl p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-2xl font-bold mb-6 text-white">
                    {{ $editingUser['id'] ? 'Edytuj użytkownika' : 'Dodaj użytkownika' }}
                </h3>

                <form wire:submit.prevent="saveUser" class="space-y-5" autocomplete="off">
                    <div>
                        <label for="name" class="block mb-2 font-medium text-neutral-300">Imię i nazwisko</label>
                        <input id="name" type="text" wire:model.defer="editingUser.name"
                               class="w-full rounded bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100" />
                        @error('editingUser.name')<p class="mt-1 text-sm text-[#880000]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block mb-2 font-medium text-neutral-300">Email</label>
                        <input id="email" type="email" wire:model.defer="editingUser.email"
                               class="w-full rounded bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100" />
                        @error('editingUser.email')<p class="mt-1 text-sm text-[#880000]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="role" class="block mb-2 font-medium text-neutral-300">Rola</label>
                        <select id="role" wire:model.defer="editingUser.role" class="w-full rounded bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100">
                            <option value="user">Użytkownik</option>
                            <option value="admin">Administrator</option>
                            <option value="instructor">Instruktor</option>
                        </select>
                        @error('editingUser.role')<p class="mt-1 text-sm text-[#880000]">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="group_id" class="block mb-2 font-medium text-neutral-300">Grupa</label>
                        <select id="group_id" wire:model.defer="editingUser.group_id" class="w-full rounded bg-neutral-800 border border-neutral-700 px-4 py-3 text-neutral-100">
                            <option value="">Brak przypisania</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                        @error('editingUser.group_id')<p class="mt-1 text-sm text-[#880000]">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="submit" class="bg-[#880000] hover:bg-red-900 text-white font-semibold px-6 py-2 rounded-lg transition">Zapisz</button>
                        <button type="button" wire:click="closeModal" class="px-6 py-2 rounded-lg border border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700">Anuluj</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
