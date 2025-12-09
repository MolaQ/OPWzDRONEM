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
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Szczegóły instruktora</p>
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
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Data dołączenia</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $createdAt }}</p>
                    </div>
                </div>
            </div>

            <!-- Groups Section -->
            <div class="mt-8 pt-8 border-t border-neutral-200 dark:border-neutral-700 space-y-6">
                <!-- Supervised Groups -->
                @if(count($supervisedGroups) > 0)
                    <div>
                        <h2 class="text-lg font-bold text-neutral-900 dark:text-white mb-4">Grupy, w których pełni funkcję wychowawcy</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($supervisedGroups as $group)
                                <a href="{{ route('admin.group.detail', $group['id']) }}" class="block p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 hover:border-blue-500 dark:hover:border-blue-400 transition">
                                    <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $group['name'] }}</h3>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                            {{ $group['studentCount'] }} studentów
                                        </span>
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Instructed Groups -->
                @if(count($instructedGroups) > 0)
                    <div>
                        <h2 class="text-lg font-bold text-neutral-900 dark:text-white mb-4">Grupy, w których pełni funkcję instruktora</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($instructedGroups as $group)
                                <a href="{{ route('admin.group.detail', $group['id']) }}" class="block p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 hover:border-blue-500 dark:hover:border-blue-400 transition">
                                    <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $group['name'] }}</h3>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                            {{ $group['studentCount'] }} studentów
                                        </span>
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- No Groups Message -->
                @if(count($supervisedGroups) === 0 && count($instructedGroups) === 0)
                    <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Ten instruktor nie jest przypisany do żadnej grupy.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</flux:main>
