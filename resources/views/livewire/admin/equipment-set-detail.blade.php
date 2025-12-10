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
                <div class="flex gap-2">
                    <button
                        wire:click="openReservationModal"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
                    >
                        Zarezerwuj zestaw
                    </button>
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

    <!-- Reservation Modal -->
    @if($showReservationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeReservationModal">
            <div class="flex items-start justify-center min-h-full px-4">
                <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between rounded-t-2xl">
                        <h3 class="text-xl font-bold text-neutral-900 dark:text-white">Rezerwacja zestawu</h3>
                        <button wire:click="closeReservationModal" type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 rounded-lg p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5">
                        <livewire:admin.equipment-set-reservation-form :equipmentSet="$equipmentSet" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</flux:main>
