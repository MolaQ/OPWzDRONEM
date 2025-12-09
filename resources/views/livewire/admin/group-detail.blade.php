<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <!-- Header -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="{{ route('admin.groups') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Wróć do grup
                        </a>
                    </div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ $groupName }}</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Szczegóły grupy</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Nazwa grupy</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $groupName }}</p>
                    </div>

                    @if($groupDescription)
                        <div>
                            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Opis</h3>
                            <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $groupDescription }}</p>
                        </div>
                    @endif

                    <!-- Wychowawcy -->
                    @if(count($supervisors) > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-green-700 dark:text-green-400">Wychowawcy</h3>
                            <div class="mt-2 space-y-1">
                                @foreach($supervisors as $supervisor)
                                    <a href="{{ route('admin.instructor.detail', $supervisor['id']) }}" 
                                       class="block text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                        {{ $supervisor['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Instruktorzy -->
                    @if(count($instructors) > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-purple-700 dark:text-purple-400">Instruktorzy</h3>
                            <div class="mt-2 space-y-1">
                                @foreach($instructors as $instructor)
                                    <a href="{{ route('admin.instructor.detail', $instructor['id']) }}" 
                                       class="block text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                        {{ $instructor['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($groupActive)
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Aktywna</span>
                            @else
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Nieaktywna</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Liczba studentów</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ count($students) }}</p>
                    </div>
                </div>
            </div>

            <!-- Students Section -->
            @if(count($students) > 0)
                <div class="mt-8 pt-8 border-t border-neutral-200 dark:border-neutral-700">
                    <h2 class="text-lg font-bold text-neutral-900 dark:text-white mb-4">Studenci w grupie ({{ count($students) }})</h2>
                    <div class="space-y-2">
                        @foreach($students as $student)
                            <a href="{{ route('admin.member.detail', $student['id']) }}" 
                               class="block p-3 bg-neutral-50 dark:bg-neutral-800 rounded border border-neutral-200 dark:border-neutral-700 hover:border-blue-500 dark:hover:border-blue-400 transition">
                                <p class="font-medium text-neutral-900 dark:text-white">{{ $student['name'] }}</p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $student['email'] }}</p>
                                @if($student['barcode'])
                                    <p class="text-xs font-mono text-neutral-500 dark:text-neutral-500 mt-1">{{ $student['barcode'] }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</flux:main>
