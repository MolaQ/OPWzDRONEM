<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="{{ route('admin.equipment') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Wróć do wyposażenia
                        </a>
                    </div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ $equipment->name }}</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">{{ $equipment->barcode }}</p>
                </div>
                <div class="flex gap-2">
                    <button
                        wire:click="openReservationModal"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition"
                    >
                        Zarezerwuj
                    </button>
                    @can('equipment.maintenance')
                    <button
                        wire:click="openMaintenanceModal"
                        class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition"
                    >
                        Zaloguj serwis
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Status and Key Info -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <h3 class="text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase">Status</h3>
                <p class="text-lg font-bold text-neutral-900 dark:text-white mt-2">
                    <span class="inline-block px-2 py-1 rounded text-xs font-medium
                        @if($equipment->status === 'available') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                        @elseif($equipment->status === 'rented') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                        @elseif($equipment->status === 'maintenance') bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200
                        @elseif($equipment->status === 'damaged') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        @endif
                    ">
                        {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                    </span>
                </p>
            </div>

            <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <h3 class="text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase">Model</h3>
                <p class="text-base text-neutral-900 dark:text-white mt-2">{{ $equipment->model ?? 'N/A' }}</p>
            </div>

            <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <h3 class="text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase">Kategoria</h3>
                <p class="text-base text-neutral-900 dark:text-white mt-2">{{ $equipment->category ?? 'N/A' }}</p>
            </div>

            <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <h3 class="text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase">Kondycja</h3>
                <p class="text-base text-neutral-900 dark:text-white mt-2">{{ $equipment->condition_status ?? 'Nieznana' }}</p>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-bold text-neutral-900 dark:text-white mb-4">Informacje ogólne</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Numer seryjny</h3>
                        <p class="text-base font-mono text-neutral-900 dark:text-white mt-1">{{ $equipment->serial_number ?? 'Brak' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Data zakupu</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($equipment->purchase_date)
                                {{ $equipment->purchase_date->format('d.m.Y') }}
                            @else
                                Nie podano
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Data wygaśnięcia gwarancji</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($equipment->warranty_expiry_date)
                                {{ $equipment->warranty_expiry_date->format('d.m.Y') }}
                            @else
                                Nie podano
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Koszt</h3>
                        <p class="text-base font-semibold text-neutral-900 dark:text-white mt-1">{{ $equipment->cost ? number_format($equipment->cost, 2, '.', ' ') . ' zł' : 'Nie podano' }}</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Lokalizacja</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipment->location ?? 'Nie podano' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Ostatni serwis</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($equipment->last_maintenance_date)
                                {{ $equipment->last_maintenance_date->format('d.m.Y H:i') }}
                            @else
                                Nie serwisowany
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Następny serwis</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($equipment->next_maintenance_due)
                                <span @class([
                                    'font-semibold',
                                    'text-red-600 dark:text-red-400' => now()->isAfter($equipment->next_maintenance_due),
                                    'text-amber-600 dark:text-amber-400' => now()->diffInDays($equipment->next_maintenance_due) <= 7,
                                    'text-green-600 dark:text-green-400' => now()->diffInDays($equipment->next_maintenance_due) > 7,
                                ])>
                                    {{ $equipment->next_maintenance_due->format('d.m.Y H:i') }}
                                </span>
                            @else
                                Nie zaplanowano
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Opis</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $equipment->description ?? 'Brak opisu' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance History Section -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-bold text-neutral-900 dark:text-white mb-4">Historia serwisowania</h2>

            @if(count($maintenanceLogs) > 0)
                <div class="space-y-3">
                    @foreach($maintenanceLogs as $log)
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">
                                    @switch($log['type'])
                                        @case('preventive_maintenance')
                                            Przegląd konserwacyjny
                                        @break
                                        @case('repair')
                                            Naprawa
                                        @break
                                        @case('inspection')
                                            Inspekcja
                                        @break
                                        @case('calibration')
                                            Kalibracja
                                        @break
                                        @case('battery_replacement')
                                            Wymiana baterii
                                        @break
                                        @case('cleaning')
                                            Czyszczenie
                                        @break
                                        @case('software_update')
                                            Aktualizacja oprogramowania
                                        @break
                                        @default
                                            {{ ucfirst(str_replace('_', ' ', $log['type'])) }}
                                    @endswitch
                                </h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                    {{ \Carbon\Carbon::parse($log['performed_at'])->format('d.m.Y H:i') }}
                                    @if($log['performed_by'])
                                        - {{ $log['performed_by']['name'] ?? 'Nieznany' }}
                                    @endif
                                </p>
                            </div>
                            @if($log['cost'])
                            <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ number_format($log['cost'], 2, '.', ' ') }} zł</p>
                            @endif
                        </div>

                        @if($log['description'])
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2"><strong>Opis:</strong> {{ $log['description'] }}</p>
                        @endif

                        @if($log['findings'])
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2"><strong>Ustalenia:</strong> {{ $log['findings'] }}</p>
                        @endif

                        @if($log['actions_taken'])
                        <p class="text-sm text-neutral-600 dark:text-neutral-400"><strong>Podjęte działania:</strong> {{ $log['actions_taken'] }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500 dark:text-neutral-400 italic">Brak wpisów historii serwisowania</p>
            @endif
        </div>

        <!-- Reservations History Section -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-bold text-neutral-900 dark:text-white mb-4">Historia rezerwacji</h2>

            @if(count($reservations) > 0)
                <div class="space-y-3">
                    @foreach($reservations as $reservation)
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">
                                    {{ $reservation['user']['name'] ?? 'Nieznany użytkownik' }}
                                </h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                    {{ \Carbon\Carbon::parse($reservation['reserved_from'])->format('d.m.Y H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($reservation['reserved_until'])->format('d.m.Y H:i') }}
                                </p>
                                @if($reservation['group'])
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">Grupa: {{ $reservation['group']['name'] ?? 'N/A' }}</p>
                                @endif
                            </div>
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium
                                @if($reservation['status'] === 'pending') bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200
                                @elseif($reservation['status'] === 'confirmed') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                @elseif($reservation['status'] === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                @elseif($reservation['status'] === 'completed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                @elseif($reservation['status'] === 'cancelled') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $reservation['status'])) }}
                            </span>
                        </div>

                        @if($reservation['reason'])
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2"><strong>Powód:</strong> {{ $reservation['reason'] }}</p>
                        @endif

                        @if($reservation['notes'])
                        <p class="text-sm text-neutral-600 dark:text-neutral-400"><strong>Notatki:</strong> {{ $reservation['notes'] }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500 dark:text-neutral-400 italic">Brak historii rezerwacji</p>
            @endif
        </div>
    </div>

    <!-- Reservation Modal -->
    @if($showReservationModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <h2 class="text-xl font-bold text-neutral-900 dark:text-white mb-4">Zarezerwuj sprzęt</h2>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">{{ $equipment->name }}</p>

            <livewire:admin.equipment-reservation-form :equipment="$equipment" @close-modal="closeReservationModal" @refresh-data="refreshData" />
        </div>
    </div>
    @endif

    <!-- Maintenance Modal -->
    @if($showMaintenanceModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
            <h2 class="text-xl font-bold text-neutral-900 dark:text-white mb-4">Zaloguj serwis</h2>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">{{ $equipment->name }}</p>

            <livewire:admin.equipment-maintenance-log-form :equipment="$equipment" @close-modal="closeMaintenanceModal" @refresh-data="refreshData" />
        </div>
    </div>
    @endif
</flux:main>
