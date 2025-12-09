<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="{{ route('admin.members') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Wróć do użytkowników
                        </a>
                    </div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ $name }}</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Szczegóły użytkownika</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Imię i nazwisko</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Email</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $email }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Kod kreskowy</h3>
                        @if($barcode)
                            <div class="mt-3 p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 flex flex-col items-center" wire:ignore>
                                <div class="bg-white dark:bg-neutral-900 p-3 rounded border border-neutral-200 dark:border-neutral-700 mb-3">
                                    <svg id="barcode-{{ $memberId }}" style="height: 60px; display: block;"></svg>
                                </div>
                                <p class="text-xs font-mono text-neutral-600 dark:text-neutral-400">{{ $barcode }}</p>
                            </div>
                        @else
                            <p class="text-base text-neutral-600 dark:text-neutral-400 mt-1">Brak</p>
                        @endif
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($active)
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Aktywny</span>
                            @else
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Nieaktywny</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Data dołączenia</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $createdAt }}</p>
                    </div>

                    <!-- Dla wychowawców -->
                    @if($isSupervisor)
                        <div>
                            <h3 class="text-sm font-semibold text-green-700 dark:text-green-400 mb-2">Grupy - Wychowawca</h3>
                            <div class="space-y-2">
                                @forelse($supervisedGroups as $group)
                                    <details class="border border-green-200 dark:border-green-800 rounded p-2 bg-green-50 dark:bg-green-900/20">
                                        <summary class="cursor-pointer font-medium text-neutral-900 dark:text-white hover:text-green-700 dark:hover:text-green-400 text-sm">
                                            {{ $group['name'] }} ({{ count($group['students']) }} studentów)
                                        </summary>
                                        <div class="mt-2 ml-2 space-y-1 border-l border-green-300 dark:border-green-700 pl-3">
                                            @foreach($group['students'] as $student)
                                                <a href="{{ route('admin.member.detail', $student['id']) }}" class="block text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $student['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @empty
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Brak przypisanych grup</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Dla instruktorów -->
                    @if($isInstructor)
                        <div>
                            <h3 class="text-sm font-semibold text-purple-700 dark:text-purple-400 mb-2">Grupy - Instruktor</h3>
                            <div class="space-y-2">
                                @forelse($instructedGroups as $group)
                                    <details class="border border-purple-200 dark:border-purple-800 rounded p-2 bg-purple-50 dark:bg-purple-900/20">
                                        <summary class="cursor-pointer font-medium text-neutral-900 dark:text-white hover:text-purple-700 dark:hover:text-purple-400 text-sm">
                                            {{ $group['name'] }} ({{ count($group['students']) }} studentów)
                                        </summary>
                                        <div class="mt-2 ml-2 space-y-1 border-l border-purple-300 dark:border-purple-700 pl-3">
                                            @foreach($group['students'] as $student)
                                                <a href="{{ route('admin.member.detail', $student['id']) }}" class="block text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $student['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @empty
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Brak przypisanych grup</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Dla studentów - wyświetl informacje o grupie -->
                    @if($isStudent && $groupName)
                        <div>
                            <h3 class="text-sm font-semibold text-blue-700 dark:text-blue-400 mb-2">Grupa - Student</h3>
                            <div class="space-y-3 border border-blue-200 dark:border-blue-800 rounded p-3 bg-blue-50 dark:bg-blue-900/20">
                                <div>
                                    <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Nazwa grupy</p>
                                    <a href="{{ route('admin.group.detail', $groupId) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline mt-1 block">
                                        {{ $groupName }}
                                    </a>
                                </div>

                                @if(count($supervisors) > 0)
                                    <div class="border-t border-blue-200 dark:border-blue-700 pt-2">
                                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Wychowawca</p>
                                        <div class="mt-1 space-y-1">
                                            @foreach($supervisors as $supervisor)
                                                <a href="{{ route('admin.instructor.detail', $supervisor['id']) }}" 
                                                   class="block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $supervisor['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(count($instructors) > 0)
                                    <div class="border-t border-blue-200 dark:border-blue-700 pt-2">
                                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Instruktor</p>
                                        <div class="mt-1 space-y-1">
                                            @foreach($instructors as $instructor)
                                                <a href="{{ route('admin.instructor.detail', $instructor['id']) }}" 
                                                   class="block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $instructor['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Dla studentów bez grupy - wyświetl role -->
                    @if($isStudent && !$groupName)
                        <div>
                            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Role</h3>
                            <p class="text-base text-neutral-900 dark:text-white mt-1">
                                @if(count($roles) > 0)
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($roles as $role)
                                            <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">{{ $role }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    Brak ról
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Full Width -->
            <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div>
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Szczegóły dodatkowe</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">Tutaj mogą być wyświetlane dodatkowe szczegóły dotyczące tego użytkownika.</p>
                </div>
            </div>
        </div>
    </div>
</flux:main>

<!-- JsBarcode Script Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

@if($barcode)
<script>
    (function() {
        const renderBarcode = () => {
            const barcodeElement = document.getElementById('barcode-{{ $memberId }}');
            if (barcodeElement && typeof JsBarcode !== 'undefined') {
                const isDark = document.documentElement.classList.contains('dark');
                try {
                    // Wyczyść element przed renderowaniem
                    barcodeElement.innerHTML = '';
                    
                    // Użyj elementu DOM zamiast selektora
                    JsBarcode(barcodeElement, '{{ $barcode }}', {
                        format: 'CODE128',
                        width: 2,
                        height: 50,
                        displayValue: false,
                        margin: 10,
                        lineColor: isDark ? '#ffffff' : '#000000',
                        background: 'transparent'
                    });
                    console.log('Barcode rendered successfully for member: {{ $memberId }}');
                } catch (e) {
                    console.error('Barcode generation error:', e);
                }
            } else {
                if (!barcodeElement) {
                    console.log('Barcode element not found: barcode-{{ $memberId }}');
                }
                if (typeof JsBarcode === 'undefined') {
                    console.log('JsBarcode not loaded yet');
                }
                setTimeout(renderBarcode, 100);
            }
        };

        // Try immediately and after delay
        setTimeout(renderBarcode, 100);
        setTimeout(renderBarcode, 300);
        setTimeout(renderBarcode, 500);
    })();
</script>
@endif
