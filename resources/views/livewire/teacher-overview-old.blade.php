<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
            <span class="mr-3">📊</span>Osiągnięcia Grupy
        </h1>
        <p class="text-neutral-600 dark:text-neutral-400">
            Przegląd osiągnięć i postępu uczniów w kursie
        </p>
    </div>

    @if($groups->isNotEmpty())
        <!-- Selektor grupy -->
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Wybierz grupę</h2>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @foreach($groups as $group)
                    <button 
                        wire:click="selectGroup({{ $group->id }})"
                        class="px-4 py-2 rounded-full font-medium transition-all border-2
                        @if($selectedGroupId === $group->id)
                            bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 border-neutral-900 dark:border-white
                        @else
                            bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white border-neutral-300 dark:border-neutral-600 hover:border-neutral-900 dark:hover:border-white
                        @endif
                        ">
                        {{ $group->name }}
                    </button>
                @endforeach
            </div>
        </div>

        @if($selectedGroupId && $students->isNotEmpty())
            <!-- Statystyki klasy -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="text-sm text-neutral-600 dark:text-neutral-400">Liczba uczniów</div>
                    <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">{{ $classStats['totalStudents'] }}</div>
                </div>

                <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="text-sm text-neutral-600 dark:text-neutral-400">Łącznie osiągnięć</div>
                    <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">{{ $classStats['totalAchievements'] }}</div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ $classStats['avgAchievementsPerStudent'] }} na ucznia
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="text-sm text-neutral-600 dark:text-neutral-400">% uczniów z osiągnięciami</div>
                    <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">{{ $classStats['achievementRate'] }}%</div>
                </div>

                <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="text-sm text-neutral-600 dark:text-neutral-400">Rozkład gwiazd</div>
                    <div class="flex gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-200 rounded">
                            <svg class="w-3 h-3" fill="rgb(234, 179, 8)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $classStats['goldsCount'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-200 rounded">
                            <svg class="w-3 h-3" fill="rgb(156, 163, 175)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $classStats['silversCount'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-200 rounded">
                            <svg class="w-3 h-3" fill="rgb(180, 83, 9)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $classStats['bronzesCount'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filtry i wyszukiwanie -->
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700 space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-neutral-900 dark:text-white">🔍 Wyszukaj ucznia</label>
                    <div class="flex gap-2">
                        <input 
                            wire:model.live="searchStudent" 
                            type="text" 
                            placeholder="Wpisz imię lub nazwisko..."
                            class="flex-1 px-4 py-2 bg-white dark:bg-neutral-800 border-2 border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-neutral-400 focus:border-neutral-900 dark:focus:border-white focus:outline-none focus:ring-2 focus:ring-neutral-400 dark:focus:ring-neutral-500"
                        >
                        @if($searchStudent)
                            <button 
                                wire:click="clearSearch"
                                class="px-4 py-2 bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition"
                            >
                                Wyczyść
                            </button>
                        @endif
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-neutral-900 dark:text-white">⭐ Filtruj po gwiazdach</label>
                    <div class="flex flex-wrap gap-2">
                        <button 
                            wire:click="setFilterStar('gold')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all border-2
                            @if($filterStar === 'gold')
                                bg-amber-100 dark:bg-amber-900/30 border-amber-500 text-amber-700 dark:text-amber-200
                            @else
                                bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-amber-500
                            @endif
                            ">
                            <svg class="w-3 h-3 inline mr-1" fill="rgb(234, 179, 8)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Złoto
                        </button>
                        <button 
                            wire:click="setFilterStar('silver')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all border-2
                            @if($filterStar === 'silver')
                                bg-gray-100 dark:bg-gray-900/30 border-gray-500 text-gray-700 dark:text-gray-200
                            @else
                                bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-gray-500
                            @endif
                            ">
                            <svg class="w-3 h-3 inline mr-1" fill="rgb(156, 163, 175)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Srebro
                        </button>
                        <button 
                            wire:click="setFilterStar('bronze')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all border-2
                            @if($filterStar === 'bronze')
                                bg-orange-100 dark:bg-orange-900/30 border-orange-500 text-orange-700 dark:text-orange-200
                            @else
                                bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-orange-500
                            @endif
                            ">
                            <svg class="w-3 h-3 inline mr-1" fill="rgb(180, 83, 9)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Brąz
                        </button>
                        <button 
                            wire:click="setFilterStar('failed')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all border-2
                            @if($filterStar === 'failed')
                                bg-gray-100 dark:bg-gray-900/30 border-gray-600 text-gray-700 dark:text-gray-200
                            @else
                                bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-gray-600
                            @endif
                            ">
                            <svg class="w-3 h-3 inline mr-1" fill="rgb(107, 114, 128)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Szary (&lt;50%)
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-neutral-900 dark:text-white">🔀 Sortuj po</label>
                    <div class="flex gap-2">
                        <button 
                            wire:click="setSortBy('name')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-all
                            @if($sortBy === 'name')
                                bg-neutral-900 dark:bg-white text-white dark:text-neutral-900
                            @else
                                bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700
                            @endif
                            ">
                            Imię (A-Z)
                        </button>
                        <button 
                            wire:click="setSortBy('name_desc')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-all
                            @if($sortBy === 'name_desc')
                                bg-neutral-900 dark:bg-white text-white dark:text-neutral-900
                            @else
                                bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700
                            @endif
                            ">
                            Imię (Z-A)
                        </button>
                        <button 
                            wire:click="setSortBy('progress')"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition-all
                            @if($sortBy === 'progress')
                                bg-neutral-900 dark:bg-white text-white dark:text-neutral-900
                            @else
                                bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700
                            @endif
                            ">
                            Postęp (malejąco)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabela uczniów z osiągnięciami -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-neutral-100 dark:bg-neutral-800 border-b border-neutral-300 dark:border-neutral-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-neutral-900 dark:text-white">Uczeń</th>
                            <th class="px-4 py-3 text-center font-semibold text-neutral-900 dark:text-white">Osiągnięcia</th>
                            <th class="px-4 py-3 text-center font-semibold text-neutral-900 dark:text-white">Rozkład</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($students as $student)
                            @php($studentAchievements = $achievements->where('user_id', $student->id))
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-3 text-neutral-900 dark:text-white font-medium">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-neutral-300 dark:bg-neutral-600 text-neutral-900 dark:text-white font-semibold text-xs">
                                            {{ $student->initials() }}
                                        </div>
                                        <div>
                                            <p>{{ $student->name }}</p>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 rounded-full font-semibold">
                                        {{ $studentAchievements->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        @php($goldCount = $studentAchievements->where('star_type', 'gold')->count())
                                        @php($silverCount = $studentAchievements->where('star_type', 'silver')->count())
                                        @php($bronzeCount = $studentAchievements->where('star_type', 'bronze')->count())
                                        
                                        @if($goldCount > 0)
                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-200 rounded">
                                                <svg class="w-3 h-3" fill="rgb(234, 179, 8)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                {{ $goldCount }}
                                            </span>
                                        @endif
                                        @if($silverCount > 0)
                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-medium bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-200 rounded">
                                                <svg class="w-3 h-3" fill="rgb(156, 163, 175)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                {{ $silverCount }}
                                            </span>
                                        @endif
                                        @if($bronzeCount > 0)
                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-medium bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-200 rounded">
                                                <svg class="w-3 h-3" fill="rgb(180, 83, 9)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                {{ $bronzeCount }}
                                            </span>
                                        @endif
                                        @if($goldCount === 0 && $silverCount === 0 && $bronzeCount === 0)
                                            <span class="text-xs text-neutral-400 dark:text-neutral-500 italic">brak</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                    @if($searchStudent)
                                        Brak wyników dla "{{ $searchStudent }}"
                                    @else
                                        Nie ma osiągnięć w wybranej grupie
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Zagadnienia słabo zaliczane -->
            @if($topicsWithoutAchievements->isNotEmpty())
            <div class="space-y-3 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 6v2M6 9a2 2 0 11-4 0 2 2 0 014 0zm0 12a2 2 0 11-4 0 2 2 0 014 0zm12-12a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Zagadnienia do uzupełnienia (&lt;50% uczniów)</h3>
                </div>
                
                <div class="space-y-2">
                    @foreach($topicsWithoutAchievements->take(5) as $item)
                        @php($topic = $item['topic'])
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h4 class="font-medium text-neutral-900 dark:text-white">{{ $topic->title }}</h4>
                                    @if($topic->description)
                                        <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ Str::limit($topic->description, 100) }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $item['percentageCompleted'] }}%</div>
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400">{{ $item['studentsWithAchievement'] }}/{{ $classStats['totalStudents'] }} uczniów</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        @else
            <div class="text-center py-12 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <svg class="w-16 h-16 text-neutral-300 dark:text-neutral-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <p class="text-neutral-600 dark:text-neutral-400 font-medium">Wybierz grupę, aby zobaczyć osiągnięcia uczniów</p>
            </div>
        @endif

    @else
        <div class="text-center py-12 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <p class="text-neutral-600 dark:text-neutral-400">Brak dostępnych grup.</p>
        </div>
    @endif
</div>
