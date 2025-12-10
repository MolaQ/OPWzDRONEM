<form wire:submit="save" class="space-y-4">
    <!-- User Selection -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Użytkownik
        </label>
        <select
            wire:model="user_id"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
            <option value="">Wybierz użytkownika</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
        @error('user_id')
            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Group Selection -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Grupa (opcjonalnie)
        </label>
        <select
            wire:model="group_id"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
            <option value="">Brak grupy</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Date Range -->
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                Od
            </label>
            <input
                type="datetime-local"
                wire:model="reserved_from"
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            @error('reserved_from')
                <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
                Do
            </label>
            <input
                type="datetime-local"
                wire:model="reserved_until"
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            @error('reserved_until')
                <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Reason -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Powód rezerwacji (opcjonalnie)
        </label>
        <input
            type="text"
            wire:model="reason"
            placeholder="np. Zajęcia praktyczne, projekt, nauka..."
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
    </div>

    <!-- Notes -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Notatki (opcjonalnie)
        </label>
        <textarea
            wire:model="notes"
            placeholder="Dodatkowe informacje dotyczące rezerwacji..."
            rows="3"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        ></textarea>
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-4">
        <button
            type="button"
            wire:click="$dispatch('close-modal')"
            class="flex-1 px-4 py-2 bg-neutral-300 dark:bg-neutral-700 text-neutral-900 dark:text-white rounded-lg font-medium hover:bg-neutral-400 dark:hover:bg-neutral-600 transition"
        >
            Anuluj
        </button>
        <button
            type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
        >
            Zarezerwuj
        </button>
    </div>
</form>
