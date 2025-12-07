<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        @if($groups->isNotEmpty())
            <!-- Selektor grupy na górze -->
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700 space-y-3">
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
                            class="px-4 py-2 rounded-lg font-medium transition-all border-2 text-sm
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
                <!-- Przełącznik widoku -->
                <div class="bg-white dark:bg-neutral-900 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Widok</h3>
                    </div>
                    
                    <div class="flex gap-2 mt-3">
                        <button 
                            wire:click="setViewMode('all')"
                            class="px-4 py-2 rounded-lg font-medium transition-all border-2 text-sm
                            @if($viewMode === 'all')
                                bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 border-neutral-900 dark:border-white
                            @else
                                bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white border-neutral-300 dark:border-neutral-600 hover:border-neutral-900 dark:hover:border-white
                            @endif
                            ">
                            📊 Wyniki całej grupy
                        </button>
                        <button 
                            wire:click="setViewMode('corrections')"
                            class="px-4 py-2 rounded-lg font-medium transition-all border-2 text-sm
                            @if($viewMode === 'corrections')
                                bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 border-neutral-900 dark:border-white
                            @else
                                bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white border-neutral-300 dark:border-neutral-600 hover:border-neutral-900 dark:hover:border-white
                            @endif
                            ">
                            🔧 Poprawki
                        </button>
                    </div>
                </div>

                <!-- Statystyki klasy ze przełącznikiem -->
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition"
                         wire:click="toggleStats">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Statystyki klasy</h3>
                        </div>
                        <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300 transition-transform" 
                             :class="showStats ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>

                    @if($showStats)
                    <div class="border-t border-neutral-200 dark:border-neutral-700 p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Liczba uczniów</div>
                                <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">{{ $classStats['totalStudents'] }}</div>
                            </div>

                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Łącznie osiągnięć</div>
                                <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">{{ $classStats['totalAchievements'] }}</div>
                                <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                    {{ $classStats['avgAchievementsPerStudent'] }} na ucznia
                                </div>
                            </div>

                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">% uczniów z osiągnięciami</div>
                                <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">{{ $classStats['achievementRate'] }}%</div>
                            </div>

                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Złote gwiazdy</div>
                                <div class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-2">{{ $classStats['goldsCount'] }}</div>
                            </div>

                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Srebrne gwiazdy</div>
                                <div class="text-3xl font-bold text-gray-600 dark:text-gray-400 mt-2">{{ $classStats['silversCount'] }}</div>
                            </div>

                            <div class="p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Brązowe gwiazdy</div>
                                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-2">{{ $classStats['bronzesCount'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Widok szczegółowy ucznia -->
                @if($selectedStudentId && $studentDetail)
                    <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <button 
                                    wire:click="backToList"
                                    class="p-2 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition"
                                >
                                    <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </button>
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-300 dark:bg-neutral-600 text-neutral-900 dark:text-white font-semibold">
                                    {{ $studentDetail['student']->initials() }}
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-neutral-900 dark:text-white">{{ $studentDetail['student']->name }}</h2>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $studentDetail['student']->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Indeks zaliczeń -->
                        <div class="space-y-4">
                            @foreach($studentDetail['blocks'] as $block)
                                <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg overflow-hidden">
                                    <div class="bg-neutral-100 dark:bg-neutral-800 px-4 py-3">
                                        <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $block->title }}</h3>
                                    </div>
                                    <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                        @foreach($block->children as $topic)
                                            @php($achievement = $topic->achievements->first())
                                            @php($currentStar = $achievement?->star_type)
                                            <div class="px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition">
                                                <div class="flex items-center justify-between gap-4">
                                                    <div class="flex-1">
                                                        <p class="font-medium text-neutral-900 dark:text-white">{{ $topic->title }}</p>
                                                    </div>
                                                    <div class="flex-shrink-0 flex items-center gap-1">
                                                        <!-- Gold Star -->
                                                        <button 
                                                            wire:click="assignStar({{ $studentDetail['student']->id }}, {{ $topic->id }}, 'gold')"
                                                            class="p-2 rounded transition-all {{ $currentStar === 'gold' ? 'bg-amber-100 dark:bg-amber-900/30' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                                            title="Złoto (90-100%)"
                                                        >
                                                            <svg class="{{ $currentStar === 'gold' ? 'w-6 h-6' : 'w-5 h-5' }} text-amber-500 dark:text-amber-400" fill="{{ $currentStar === 'gold' ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ $currentStar === 'gold' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        </button>

                                                        <!-- Silver Star -->
                                                        <button 
                                                            wire:click="assignStar({{ $studentDetail['student']->id }}, {{ $topic->id }}, 'silver')"
                                                            class="p-2 rounded transition-all {{ $currentStar === 'silver' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                                            title="Srebro (70-89%)"
                                                        >
                                                            <svg class="{{ $currentStar === 'silver' ? 'w-6 h-6' : 'w-5 h-5' }} text-gray-500 dark:text-gray-400" fill="{{ $currentStar === 'silver' ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ $currentStar === 'silver' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        </button>

                                                        <!-- Bronze Star -->
                                                        <button 
                                                            wire:click="assignStar({{ $studentDetail['student']->id }}, {{ $topic->id }}, 'bronze')"
                                                            class="p-2 rounded transition-all {{ $currentStar === 'bronze' ? 'bg-orange-100 dark:bg-orange-900/30' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                                            title="Brąz (50-69%)"
                                                        >
                                                            <svg class="{{ $currentStar === 'bronze' ? 'w-6 h-6' : 'w-5 h-5' }} text-orange-600 dark:text-orange-500" fill="{{ $currentStar === 'bronze' ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ $currentStar === 'bronze' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        </button>

                                                        <!-- Gray Star -->
                                                        <button 
                                                            wire:click="assignStar({{ $studentDetail['student']->id }}, {{ $topic->id }}, 'failed')"
                                                            class="p-2 rounded transition-all {{ $currentStar === 'failed' ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                                            title="Szary (<50%)"
                                                        >
                                                            <svg class="{{ $currentStar === 'failed' ? 'w-6 h-6' : 'w-5 h-5' }} text-gray-400 dark:text-gray-500" fill="{{ $currentStar === 'failed' ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="{{ $currentStar === 'failed' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        </button>

                                                        <!-- Remove Star -->
                                                        @if($currentStar)
                                                            <button 
                                                                wire:click="removeStar({{ $studentDetail['student']->id }}, {{ $topic->id }})"
                                                                class="p-2 rounded transition-all hover:bg-red-100 dark:hover:bg-red-900/30 ml-1"
                                                                title="Usuń"
                                                            >
                                                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($viewMode === 'corrections')
                    <!-- Widok poprawek -->
                    @if($correctionsData->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($correctionsData as $blockData)
                                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                                    <div class="bg-red-50 dark:bg-red-900/20 px-4 py-3 border-b border-red-200 dark:border-red-800">
                                        <h3 class="font-semibold text-neutral-900 dark:text-white">📚 {{ $blockData['block']->title }}</h3>
                                    </div>
                                    <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                        @foreach($blockData['topics'] as $topicData)
                                            <div class="p-4">
                                                <h4 class="font-medium text-neutral-900 dark:text-white mb-3">{{ $topicData['topic']->title }}</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                    @foreach($topicData['students'] as $achievement)
                                                        <button 
                                                            wire:click="selectStudent({{ $achievement->user->id }})"
                                                            class="flex items-center gap-2 p-2 rounded-lg bg-neutral-50 dark:bg-neutral-800 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition text-left"
                                                        >
                                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-neutral-300 dark:bg-neutral-600 text-neutral-900 dark:text-white font-semibold text-xs">
                                                                {{ $achievement->user->initials() }}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ $achievement->user->name }}</p>
                                                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                                                    @if($achievement->star_type === 'failed') Szary ⭐
                                                                    @else Brąz ⭐
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                            <svg class="w-16 h-16 text-green-500 dark:text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-neutral-600 dark:text-neutral-400 font-medium">Brak uczniów wymagających poprawek! 🎉</p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-500 mt-1">Wszyscy uczniowie mają zaliczone zagadnienia na poziomie co najmniej brązowym.</p>
                        </div>
                    @endif
                @else
                    <!-- Widok wyników całej grupy -->

                <!-- Filtry i wyszukiwanie -->
                <div class="bg-white dark:bg-neutral-900 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-neutral-900 dark:text-white">🔍 Wyszukaj ucznia</label>
                        <div class="flex gap-2">
                            <input 
                                wire:model.live="searchStudent" 
                                type="text" 
                                placeholder="Wpisz imię lub nazwisko..."
                                class="flex-1 px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-neutral-400 focus:border-neutral-900 dark:focus:border-white focus:outline-none focus:ring-2 focus:ring-neutral-400 dark:focus:ring-neutral-500"
                            >
                            @if($searchStudent)
                                <button 
                                    wire:click="clearSearch"
                                    class="px-4 py-2 bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition text-sm font-medium"
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
                                class="px-3 py-1 rounded-full text-xs font-medium transition-all border
                                @if($filterStar === 'gold')
                                    bg-amber-100 dark:bg-amber-900/30 border-amber-500 text-amber-700 dark:text-amber-200
                                @else
                                    bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-amber-500
                                @endif
                                ">
                                <svg class="w-3 h-3 inline mr-1 text-amber-500 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                Złoto
                            </button>
                            <button 
                                wire:click="setFilterStar('silver')"
                                class="px-3 py-1 rounded-full text-xs font-medium transition-all border
                                @if($filterStar === 'silver')
                                    bg-gray-100 dark:bg-gray-900/30 border-gray-500 text-gray-700 dark:text-gray-200
                                @else
                                    bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-gray-500
                                @endif
                                ">
                                <svg class="w-3 h-3 inline mr-1 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                Srebro
                            </button>
                            <button 
                                wire:click="setFilterStar('bronze')"
                                class="px-3 py-1 rounded-full text-xs font-medium transition-all border
                                @if($filterStar === 'bronze')
                                    bg-orange-100 dark:bg-orange-900/30 border-orange-500 text-orange-700 dark:text-orange-200
                                @else
                                    bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:border-orange-500
                                @endif
                                ">
                                <svg class="w-3 h-3 inline mr-1 text-orange-600 dark:text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                Brąz
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
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
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
                                    <tr 
                                        wire:click="selectStudent({{ $student->id }})"
                                        class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors cursor-pointer"
                                    >
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
                                            <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 rounded-full font-semibold text-sm">
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
                                                        <svg class="w-3 h-3 text-amber-500 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                        {{ $goldCount }}
                                                    </span>
                                                @endif
                                                @if($silverCount > 0)
                                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-medium bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-200 rounded">
                                                        <svg class="w-3 h-3 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                        {{ $silverCount }}
                                                    </span>
                                                @endif
                                                @if($bronzeCount > 0)
                                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-xs font-medium bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-200 rounded">
                                                        <svg class="w-3 h-3 text-orange-600 dark:text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
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
                </div>

                <!-- Koniec widoku wyników całej grupy -->
                @endif

                <!-- Poprawki do uzupełnienia (<50% uczniów) - tylko w widoku 'all' -->
                @if($viewMode === 'all' && !$selectedStudentId && $topicsWithoutAchievements->isNotEmpty())
                <div class="space-y-3 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Poprawki (&lt;50% uczniów)</h3>
                    </div>
                    
                    <div class="space-y-2">
                        @foreach($topicsWithoutAchievements->take(5) as $item)
                            @php($topic = $item['topic'])
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-neutral-900 dark:text-white">{{ $topic->title }}</h4>
                                        @if($topic->description)
                                            <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ Str::limit($topic->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $item['percentageCompleted'] }}%</div>
                                        <div class="text-xs text-red-600 dark:text-red-400">{{ $item['studentsWithAchievement'] }}/{{ $classStats['totalStudents'] }}</div>
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
</flux:main>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('star-assigned', (event) => {
            const starTypes = {
                'gold': { icon: '🌟', text: 'Złotą gwiazdę', color: '#f59e0b' },
                'silver': { icon: '⭐', text: 'Srebrną gwiazdę', color: '#9ca3af' },
                'bronze': { icon: '🥉', text: 'Brązową gwiazdę', color: '#ea580c' },
                'gray': { icon: '⚫', text: 'Szarą gwiazdę', color: '#737373' }
            };
            
            const star = starTypes[event[0].starType] || starTypes['gold'];
            
            Swal.fire({
                title: 'Sukces!',
                html: `<div style="font-size: 3rem; margin: 20px 0;">${star.icon}</div><div style="font-size: 1.1rem;">Przyznano <strong>${star.text}</strong></div>`,
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#262626' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            });
        });
        
        Livewire.on('star-removed', () => {
            Swal.fire({
                title: 'Usunięto',
                text: 'Gwiazdka została usunięta',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#262626' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            });
        });
        
        Livewire.on('star-error', (event) => {
            Swal.fire({
                title: 'Błąd',
                text: event[0].message || 'Nie udało się przydzielić gwiazdki',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
                background: document.documentElement.classList.contains('dark') ? '#262626' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            });
        });
    });
</script>
