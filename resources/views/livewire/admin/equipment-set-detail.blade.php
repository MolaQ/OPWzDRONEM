<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="{{ route('admin.equipment-sets') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Wróć do zestawów
                        </a>
                    </div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ $equipmentSet->name }}</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Szczegóły zestawu wyposażenia</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Nazwa</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipmentSet->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Opis</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipmentSet->description ?? 'Brak opisu' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipmentSet->status ?? 'Brak danych' }}</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Liczba elementów</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipmentSet->equipments()->count() ?? 'Brak danych' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Data dodania</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipmentSet->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Full Width -->
            <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div>
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Szczegóły dodatkowe</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">Tutaj mogą być wyświetlane dodatkowe szczegóły dotyczące tego zestawu wyposażenia.</p>
                </div>
            </div>
        </div>
    </div>
</flux:main>
