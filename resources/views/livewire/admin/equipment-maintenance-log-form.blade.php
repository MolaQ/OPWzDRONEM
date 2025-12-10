<form wire:submit="save" class="space-y-4">
    <!-- Type Selection -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Typ serwisu
        </label>
        <select
            wire:model="type"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
            <option value="">Wybierz typ</option>
            <option value="preventive_maintenance">Przegląd konserwacyjny</option>
            <option value="repair">Naprawa</option>
            <option value="inspection">Inspekcja</option>
            <option value="calibration">Kalibracja</option>
            <option value="battery_replacement">Wymiana baterii</option>
            <option value="cleaning">Czyszczenie</option>
            <option value="software_update">Aktualizacja oprogramowania</option>
            <option value="other">Inne</option>
        </select>
        @error('type')
            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Opis (opcjonalnie)
        </label>
        <textarea
            wire:model="description"
            placeholder="Co zostało zrobione?..."
            rows="2"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        ></textarea>
    </div>

    <!-- Findings -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Ustalenia (opcjonalnie)
        </label>
        <textarea
            wire:model="findings"
            placeholder="Co zostało znalezione podczas serwisu?..."
            rows="2"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        ></textarea>
    </div>

    <!-- Actions Taken -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Podjęte działania (opcjonalnie)
        </label>
        <textarea
            wire:model="actions_taken"
            placeholder="Jakie działania zostały podjęte?..."
            rows="2"
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        ></textarea>
    </div>

    <!-- Cost -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Koszt (opcjonalnie)
        </label>
        <div class="relative">
            <input
                type="number"
                step="0.01"
                wire:model="cost"
                placeholder="0.00"
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <span class="absolute right-3 top-2.5 text-neutral-600 dark:text-neutral-400">zł</span>
        </div>
        @error('cost')
            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Next Maintenance Recommended -->
    <div>
        <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">
            Następny serwis zalecany (opcjonalnie)
        </label>
        <input
            type="text"
            wire:model="next_maintenance_recommended"
            placeholder="np. Za 50 godzin lotu, za 3 miesiące, itp."
            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
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
            class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium transition"
        >
            Zapisz serwis
        </button>
    </div>
</form>
