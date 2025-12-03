<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Wyszukiwarka globalna</h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Szukaj uczniów, sprzętu, zestawów, postów i komentarzy</p>
            </div>
        </div>

        <!-- Search Input -->
        <div class="relative max-w-3xl">
            <input
                type="text"
                wire:model.live.debounce.300ms="query"
                placeholder="Wpisz nazwę, kod kreskowy, email..."
                autofocus
                class="w-full px-4 py-3 pl-12 pr-12 rounded-lg border-2 border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white text-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
            <svg class="w-6 h-6 text-neutral-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            @if($query)
                <button
                    wire:click="clearSearch"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        <!-- Results -->
        @if($query && strlen($query) >= 2)
            <div class="space-y-6 max-w-5xl">
                <!-- Students -->
                @if(!empty($results['students']))
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Uczniowie ({{ count($results['students']) }})
                        </h2>

                        <div class="space-y-3">
                            @foreach($results['students'] as $student)
                                <div class="p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg">
                                    <div class="flex items-start justify-between gap-3">
                                        <button
                                            wire:click="showDetails('student', {{ $student['id'] }})"
                                            class="flex-1 text-left"
                                        >
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-semibold text-neutral-900 dark:text-white">{{ $student['name'] }}</span>
                                                <span class="px-2 py-1 text-xs font-mono bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded">
                                                    {{ $student['barcode'] }}
                                                </span>
                                                @if($student['group'])
                                                    <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                                        {{ $student['group'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                                {{ $student['email'] }}
                                            </div>

                                            @if($student['rentals_count'] > 0)
                                                <div class="mt-3 space-y-2">
                                                    <div class="text-sm font-medium text-orange-600 dark:text-orange-400">
                                                        Aktywne wypożyczenia ({{ $student['rentals_count'] }}):
                                                    </div>
                                                    @foreach($student['active_rentals'] as $rental)
                                                        <div class="flex items-center gap-2 text-sm pl-4">
                                                            <span class="px-2 py-0.5 text-xs rounded {{ $rental['type'] === 'Sprzęt' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' }}">
                                                                {{ $rental['type'] }}
                                                            </span>
                                                            <span class="text-neutral-700 dark:text-neutral-300">{{ $rental['name'] }}</span>
                                                            <span class="text-neutral-500">({{ $rental['barcode'] }})</span>
                                                            <span class="text-neutral-400 text-xs">od {{ $rental['rented_at'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                                                    ✓ Brak aktywnych wypożyczeń
                                                </div>
                                            @endif
                                            </div>
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.members') }}" class="px-3 py-2 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Uczniowie</a>
                                            <a href="{{ route('admin.rentals') }}" class="px-3 py-2 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Wypożyczenia</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Equipment -->
                @if(!empty($results['equipment']))
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            Sprzęt ({{ count($results['equipment']) }})
                        </h2>

                        <div class="space-y-3">
                            @foreach($results['equipment'] as $item)
                                <button
                                    wire:click="showDetails('equipment', {{ $item['id'] }})"
                                    class="w-full text-left p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition"
                                >
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-semibold text-neutral-900 dark:text-white">{{ $item['name'] }}</span>
                                                <span class="px-2 py-1 text-xs font-mono bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded">
                                                    {{ $item['barcode'] }}
                                                </span>
                                            </div>

                                            @if($item['description'])
                                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                                    {{ $item['description'] }}
                                                </div>
                                            @endif

                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $item['status_color'] === 'green' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($item['status_color'] === 'orange' ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-300') }}">
                                                    {{ $item['status'] }}
                                                </span>

                                                @if($item['rented_by'])
                                                    <div class="text-sm text-neutral-700 dark:text-neutral-300">
                                                        Wypożyczył(a): <strong>{{ implode(', ', $item['rented_by']['users']) }}</strong>
                                                        <span class="text-neutral-500 text-xs ml-2">{{ $item['rented_by']['rented_at'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Equipment Sets -->
                @if(!empty($results['equipment_sets']))
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Zestawy ({{ count($results['equipment_sets']) }})
                        </h2>

                        <div class="space-y-3">
                            @foreach($results['equipment_sets'] as $set)
                                <button
                                    wire:click="showDetails('equipment_set', {{ $set['id'] }})"
                                    class="w-full text-left p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition"
                                >
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-semibold text-neutral-900 dark:text-white">{{ $set['name'] }}</span>
                                                <span class="px-2 py-1 text-xs font-mono bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded">
                                                    {{ $set['barcode'] }}
                                                </span>
                                                <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                                    {{ $set['equipments_count'] }} szt.
                                                </span>
                                            </div>

                                            @if($set['description'])
                                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                                    {{ $set['description'] }}
                                                </div>
                                            @endif

                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $set['status_color'] === 'green' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($set['status_color'] === 'orange' ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-300') }}">
                                                    {{ $set['status'] }}
                                                </span>

                                                @if($set['rented_by'])
                                                    <div class="text-sm text-neutral-700 dark:text-neutral-300">
                                                        Wypożyczył(a): <strong>{{ implode(', ', $set['rented_by']['users']) }}</strong>
                                                        <span class="text-neutral-500 text-xs ml-2">{{ $set['rented_by']['rented_at'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Posts -->
                @if(!empty($results['posts']))
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            Posty ({{ count($results['posts']) }})
                        </h2>

                        <div class="space-y-3">
                            @foreach($results['posts'] as $post)
                                <a href="{{ route('post.view', $post['id']) }}" class="block p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    <div class="font-semibold text-neutral-900 dark:text-white">{{ $post['title'] }}</div>
                                    <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                        {{ $post['excerpt'] }}
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-neutral-500">
                                        @if($post['author'])
                                            <span>Autor: {{ $post['author'] }}</span>
                                        @endif
                                        @if($post['published_at'])
                                            <span>{{ $post['published_at'] }}</span>
                                        @endif
                                        <span>Wyświetlenia: {{ $post['views'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Comments -->
                @if(!empty($results['comments']))
                    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            Komentarze ({{ count($results['comments']) }})
                        </h2>

                        <div class="space-y-3">
                            @foreach($results['comments'] as $comment)
                                <a href="{{ route('post.view', $comment['post_id']) }}" class="block p-4 bg-neutral-50 dark:bg-neutral-900 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    <div class="text-sm text-neutral-700 dark:text-neutral-300">
                                        {{ $comment['content'] }}
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-neutral-500">
                                        <span>Autor: {{ $comment['author'] }}</span>
                                        <span>Post: {{ $comment['post_title'] }}</span>
                                        <span>{{ $comment['created_at'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- No Results -->
                @if(empty($results['students']) && empty($results['equipment']) && empty($results['equipment_sets']) && empty($results['posts']) && empty($results['comments']))
                    <div class="text-center py-12 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                        <svg class="w-16 h-16 text-neutral-300 dark:text-neutral-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-neutral-500 dark:text-neutral-400 text-lg">
                            Nie znaleziono wyników dla "<strong>{{ $query }}</strong>"
                        </div>
                        <div class="text-neutral-400 dark:text-neutral-500 text-sm mt-2">
                            Spróbuj wyszukać inną frazę lub kod kreskowy
                        </div>
                    </div>
                @endif
            </div>
        @elseif($query && strlen($query) < 2)
            <div class="text-center py-12 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <div class="text-neutral-500 dark:text-neutral-400">
                    Wpisz przynajmniej 2 znaki, aby rozpocząć wyszukiwanie
                </div>
            </div>
        @else
            <div class="text-center py-12 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <svg class="w-16 h-16 text-neutral-300 dark:text-neutral-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <div class="text-neutral-500 dark:text-neutral-400 text-lg">
                    Zacznij wpisywać, aby wyszukać
                </div>
                <div class="text-neutral-400 dark:text-neutral-500 text-sm mt-2">
                    Możesz szukać uczniów, sprzętu, zestawów, postów lub komentarzy
                </div>
            </div>
        @endif
    </div>

    <!-- Details Modal -->
    @if($showModal && $selectedItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="closeModal">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-neutral-900 dark:text-white">
                        @if($selectedType === 'student')
                            Szczegóły ucznia
                        @elseif($selectedType === 'equipment')
                            Szczegóły sprzętu
                        @elseif($selectedType === 'equipment_set')
                            Szczegóły zestawu
                        @endif
                    </h2>
                    <button wire:click="closeModal" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-4">
                    @if($selectedType === 'student')
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Imię i nazwisko</label>
                                <div class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $selectedItem['name'] }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Email</label>
                                    <div class="text-neutral-900 dark:text-white">{{ $selectedItem['email'] }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Kod kreskowy</label>
                                    <div class="font-mono text-neutral-900 dark:text-white">{{ $selectedItem['barcode'] }}</div>
                                </div>
                            </div>

                            @if($selectedItem['group'])
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Grupa</label>
                                    <div class="text-neutral-900 dark:text-white">{{ $selectedItem['group'] }}</div>
                                </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Data utworzenia</label>
                                <div class="text-neutral-900 dark:text-white">{{ $selectedItem['created_at'] }}</div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-700">
                            <a href="{{ route('admin.members') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Zarządzaj uczniami
                            </a>
                        </div>

                    @elseif($selectedType === 'equipment')
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Nazwa</label>
                                <div class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $selectedItem['name'] }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Kod kreskowy</label>
                                    <div class="font-mono text-neutral-900 dark:text-white">{{ $selectedItem['barcode'] }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Model</label>
                                    <div class="text-neutral-900 dark:text-white">{{ $selectedItem['model'] ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Kategoria</label>
                                    <div class="text-neutral-900 dark:text-white">{{ $selectedItem['category'] ?? '-' }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Status</label>
                                    <div>
                                        @php
                                            $statusMap = [
                                                'dostepny' => ['label' => 'Dostępny', 'color' => 'green'],
                                                'wypozyczony' => ['label' => 'Wypożyczony', 'color' => 'orange'],
                                                'w_uzyciu' => ['label' => 'W użyciu', 'color' => 'blue'],
                                                'konserwacja' => ['label' => 'Konserwacja', 'color' => 'yellow'],
                                                'uszkodzony' => ['label' => 'Uszkodzony', 'color' => 'red'],
                                            ];
                                            $statusInfo = $statusMap[$selectedItem['status']] ?? ['label' => $selectedItem['status'], 'color' => 'gray'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusInfo['color'] === 'green' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($statusInfo['color'] === 'orange' ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : ($statusInfo['color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : ($statusInfo['color'] === 'yellow' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' : ($statusInfo['color'] === 'red' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-300')))) }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($selectedItem['description'])
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Opis</label>
                                    <div class="text-neutral-900 dark:text-white">{{ $selectedItem['description'] }}</div>
                                </div>
                            @endif

                            @if($selectedItem['active_rental'])
                                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                                    <div class="text-sm font-medium text-orange-900 dark:text-orange-200 mb-1">Obecnie wypożyczone</div>
                                    <div class="text-sm text-orange-800 dark:text-orange-300">
                                        <strong>{{ implode(', ', $selectedItem['active_rental']['users']) }}</strong>
                                        <div class="text-xs mt-1">od {{ $selectedItem['active_rental']['rented_at'] }}</div>
                                    </div>
                                </div>
                            @endif
                            <a href="{{ route('admin.returns') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Zwroty
                            </a>
                            <a href="{{ route('admin.rentals') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Wypożyczenia
                            </a>
                        </div>

                    @elseif($selectedType === 'equipment_set')
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Nazwa</label>
                                <div class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $selectedItem['name'] }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Kod kreskowy</label>
                                    <div class="font-mono text-neutral-900 dark:text-white">{{ $selectedItem['barcode'] }}</div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Status</label>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $selectedItem['active'] ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-300' }}">
                                            {{ $selectedItem['active'] ? 'Aktywny' : 'Nieaktywny' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($selectedItem['description'])
                                <div>
                                    <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Opis</label>
                                    <div class="text-neutral-900 dark:text-white">{{ $selectedItem['description'] }}</div>
                                </div>
                            @endif

                            @if($selectedItem['active_rental'])
                                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                                    <div class="text-sm font-medium text-orange-900 dark:text-orange-200 mb-1">Obecnie wypożyczone</div>
                                    <div class="text-sm text-orange-800 dark:text-orange-300">
                                        <strong>{{ implode(', ', $selectedItem['active_rental']['users']) }}</strong>
                                        <div class="text-xs mt-1">od {{ $selectedItem['active_rental']['rented_at'] }}</div>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-neutral-600 dark:text-neutral-400 mb-2 block">Sprzęt w zestawie ({{ count($selectedItem['equipments']) }})</label>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($selectedItem['equipments'] as $eq)
                                        <div class="flex items-center justify-between p-2 bg-neutral-50 dark:bg-neutral-900 rounded">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $eq['name'] }}</div>
                                                <div class="text-xs text-neutral-500 font-mono">{{ $eq['barcode'] }}</div>
                                            </div>
                                            <div>
                                                @php
                                                    $eqStatusMap = [
                                                        'dostepny' => ['label' => 'Dostępny', 'color' => 'green'],
                                                        'wypozyczony' => ['label' => 'Wypożyczony', 'color' => 'orange'],
                                                        'w_uzyciu' => ['label' => 'W użyciu', 'color' => 'blue'],
                                                        'konserwacja' => ['label' => 'Konserwacja', 'color' => 'yellow'],
                                                        'uszkodzony' => ['label' => 'Uszkodzony', 'color' => 'red'],
                                                    ];
                                                    $eqStatusInfo = $eqStatusMap[$eq['status']] ?? ['label' => $eq['status'], 'color' => 'gray'];
                                                @endphp
                                                <span class="px-2 py-0.5 text-xs rounded {{ $eqStatusInfo['color'] === 'green' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($eqStatusInfo['color'] === 'orange' ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' : ($eqStatusInfo['color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : ($eqStatusInfo['color'] === 'yellow' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' : ($eqStatusInfo['color'] === 'red' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-300')))) }}">
                                                    {{ $eqStatusInfo['label'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-700 flex gap-2">
                            <a href="{{ route('admin.equipment-sets') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Zarządzaj zestawami
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</flux:main>

