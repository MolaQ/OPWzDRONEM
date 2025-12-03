<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Scanner Input -->
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-1">Skaner kodów kreskowych</h1>
            <p class="text-neutral-600 dark:text-neutral-400 mb-6">Zeskanuj kod ucznia (S), sprzętu (E) lub zestawu (Z)</p>

            <div class="flex gap-3">
                    <div class="flex-1">
                    <label for="barcode" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Kod kreskowy
                    </label>
                    <input
                        type="text"
                        id="barcode"
                        wire:model.live.debounce.500ms="barcode"
                        autofocus
                        placeholder="Zeskanuj lub wpisz kod (np. S0000000001, E0000000001, Z0000000001)"
                        class="w-full rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 px-4 py-3 text-lg font-mono text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000] focus:border-transparent transition"
                        x-data
                        x-init="setTimeout(()=>{$el.focus()},50)"
                        @scanned.window="setTimeout(() => $el.focus(), 100)"
                    />
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                        Format: <span class="font-mono">S</span> (student), <span class="font-mono">E</span> (equipment), <span class="font-mono">Z</span> (zestaw) + 10 cyfr
                    </p>

                    @if($showSuggestions)
                    <div class="mt-3 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 max-h-72 overflow-y-auto shadow">
                        @foreach($suggestions as $i => $s)
                            <button
                                type="button"
                                wire:click="selectSuggestion('{{ $s['barcode'] }}')"
                                class="w-full text-left px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center gap-3"
                                wire:key="suggestion-{{ $s['type'] }}-{{ $s['id'] }}-{{ $i }}"
                            >
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded {{ $s['type'] === 'student' ? 'bg-blue-500' : ($s['type'] === 'set' ? 'bg-orange-500' : 'bg-purple-500') }} text-white text-xs font-bold">
                                    {{ strtoupper(substr($s['type'],0,1)) }}
                                </span>
                                <span class="flex-1 truncate">{{ $s['name'] }}</span>
                                <span class="font-mono text-xs text-neutral-500 dark:text-neutral-400">{{ $s['barcode'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    @endif
                    </div>
                    <div class="flex items-end">
                        <button
                            wire:click="scan"
                            class="px-6 py-3 rounded-lg bg-[#880000] text-white font-semibold hover:bg-red-900 transition flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Skanuj
                    </button>
                </div>
            </div>

            @if($error)
                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $error }}</p>
                    </div>
                    <button wire:click="clear" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400">Kody uczniów</p>
                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100">Format: S##########</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-purple-600 dark:text-purple-400">Kody sprzętu</p>
                    <p class="text-lg font-bold text-purple-900 dark:text-purple-100">Format: E##########</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400">Kody zestawów</p>
                    <p class="text-lg font-bold text-green-900 dark:text-green-100">Format: Z##########</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results (if present) -->
    @if($result)
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 flex items-center gap-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h2 class="text-xl font-bold text-white">Znaleziono!</h2>
                    <p class="text-green-100 text-sm">
                        Typ: {{ $result['type'] === 'student' ? 'Uczeń' : ($result['type'] === 'set' ? 'Zestaw' : 'Sprzęt') }}
                    </p>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto">
                @if($result['type'] === 'student')
                    <!-- Student Details -->
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                                {{ $result['entity']->initials() }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                                    {{ $result['entity']->name }}
                                </h3>
                                <p class="text-neutral-600 dark:text-neutral-400 font-mono">{{ $result['entity']->barcode }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full {{ $result['entity']->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $result['entity']->active ? 'Aktywny' : 'Nieaktywny' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Email</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $result['entity']->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Grupa</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $result['entity']->group?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Licencja pilota</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100 font-mono">{{ $result['entity']->pilot_license ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Licencja operatora</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100 font-mono">{{ $result['entity']->operator_license ?? '—' }}</p>
                            </div>
                            @if($result['entity']->license_expiry_date)
                            <div class="col-span-2">
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Ważność licencji</p>
                                <p class="font-medium {{ $result['entity']->license_expiry_date->isPast() ? 'text-red-600 dark:text-red-400' : 'text-neutral-900 dark:text-neutral-100' }}">
                                    {{ $result['entity']->license_expiry_date->format('d.m.Y') }}
                                    @if($result['entity']->license_expiry_date->isPast())
                                        <span class="text-xs">(wygasła)</span>
                                    @else
                                        <span class="text-xs">({{ $result['entity']->license_expiry_date->diffForHumans() }})</span>
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                @elseif($result['type'] === 'set')
                    <!-- Equipment Set Details -->
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white flex-shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                                    {{ $result['entity']->name }}
                                </h3>
                                <p class="text-neutral-600 dark:text-neutral-400 font-mono">{{ $result['entity']->barcode }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full {{ $result['entity']->isAvailable() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $result['entity']->isAvailable() ? 'Dostępny' : 'Niekompletny' }}
                            </span>
                        </div>

                        @if($result['entity']->description)
                        <div class="pt-2">
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">Opis</p>
                            <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $result['entity']->description }}</p>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-700">
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-3">Skład zestawu ({{ $result['entity']->equipments->count() }} szt.):</p>
                            <div class="space-y-2">
                                @foreach($result['entity']->equipments as $eq)
                                    <div class="flex items-center justify-between p-2 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                        <div class="flex items-center gap-2 flex-1">
                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $eq->name }}</span>
                                            @if($eq->model)
                                                <span class="text-xs text-neutral-500 dark:text-neutral-400">({{ $eq->model }})</span>
                                            @endif
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $eq->status_color }}-100 text-{{ $eq->status_color }}-800 dark:bg-{{ $eq->status_color }}-900 dark:text-{{ $eq->status_color }}-200">
                                            {{ $eq->status_label }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Equipment Details -->
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white flex-shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                                    {{ $result['entity']->name }}
                                </h3>
                                <p class="text-neutral-600 dark:text-neutral-400 font-mono">{{ $result['entity']->barcode }}</p>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full bg-{{ $result['entity']->status_color }}-100 text-{{ $result['entity']->status_color }}-800 dark:bg-{{ $result['entity']->status_color }}-900 dark:text-{{ $result['entity']->status_color }}-200">
                                {{ $result['entity']->status_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                            @if($result['entity']->model)
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Model</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $result['entity']->model }}</p>
                            </div>
                            @endif
                            @if($result['entity']->category)
                            <div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Kategoria</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100 capitalize">{{ $result['entity']->category }}</p>
                            </div>
                            @endif
                            @if($result['entity']->description)
                            <div class="col-span-2">
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Opis</p>
                                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $result['entity']->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-6 pt-4 border-t border-neutral-200 dark:border-neutral-700 flex gap-3">
                    <button
                        wire:click="clear"
                        class="px-4 py-2 rounded-lg border-2 border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 font-medium hover:bg-neutral-50 dark:hover:bg-neutral-700 transition"
                    >
                        Nowe skanowanie
                    </button>
                    @if($result['type'] === 'student')
                        <a
                            href="{{ route('admin.members') }}"
                            wire:navigate
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Zobacz profil
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
    </div>
</flux:main>
