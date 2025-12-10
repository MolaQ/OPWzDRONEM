<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Użytkownik</label>
            <select wire:model="user_id" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">-- Wybierz użytkownika --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Grupa (opcjonalnie)</label>
            <select wire:model="group_id" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">-- Bez grupy --</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Od</label>
            <input type="datetime-local" wire:model="reserved_from" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('reserved_from') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Do</label>
            <input type="datetime-local" wire:model="reserved_until" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('reserved_until') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Powód (opcjonalnie)</label>
        <input type="text" wire:model="reason" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Powód rezerwacji">
        @error('reason') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Notatki (opcjonalnie)</label>
        <textarea wire:model="notes" rows="3" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Dodatkowe informacje"></textarea>
        @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    @if($showErrors)
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            Sprawdź dane rezerwacji – zestaw jest już zajęty w tym terminie.
        </div>
    @endif

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" wire:click="$dispatch('close-modal')" class="px-4 py-2 rounded-lg border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800">Anuluj</button>
        <button type="button" wire:click="save" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">Zapisz rezerwację</button>
    </div>
</div>
