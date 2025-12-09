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
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ $member->name }}</h1>
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
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $member->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Email</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $member->email }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Kod kreskowy</h3>
                        <p class="text-base font-mono text-neutral-900 dark:text-white mt-1">{{ $member->barcode ?? 'Brak' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Grupa</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $member->group?->name ?? 'Brak przypisania' }}</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($member->active)
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Aktywny</span>
                            @else
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Nieaktywny</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Role</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">
                            @if($member->getRoleNames()->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($member->getRoleNames() as $role)
                                        <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">{{ $role }}</span>
                                    @endforeach
                                </div>
                            @else
                                Brak ról
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Data dołączenia</h3>
                        <p class="text-base text-neutral-900 dark:text-white mt-1">{{ $member->created_at->format('d.m.Y H:i') }}</p>
                    </div>
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
