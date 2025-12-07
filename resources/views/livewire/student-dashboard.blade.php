<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
            <span class="mr-3">🎓</span>Moje Osiągnięcia
        </h1>
        <p class="text-neutral-600 dark:text-neutral-400">
            Przegląd Twoich przyznanych gwiazd i postępu w kursie {{ $course?->name ?? 'kursu' }}
        </p>
    </div>

    @if($course && $blocks->isNotEmpty())
        <!-- Legenda ocen -->
        <div class="p-4 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="flex flex-wrap items-center gap-6">
                <span class="text-xs font-semibold text-neutral-900 dark:text-white uppercase tracking-wider">Legenda ocen:</span>
                
                <div class="flex items-center gap-1.5">
                    <svg class="w-5 h-5" fill="rgb(234, 179, 8)" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs text-neutral-700 dark:text-neutral-300"><strong>Złoto</strong> 90-100%</span>
                </div>

                <div class="flex items-center gap-1.5">
                    <svg class="w-5 h-5" fill="rgb(156, 163, 175)" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs text-neutral-700 dark:text-neutral-300"><strong>Srebro</strong> 70-89%</span>
                </div>

                <div class="flex items-center gap-1.5">
                    <svg class="w-5 h-5" fill="rgb(180, 83, 9)" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs text-neutral-700 dark:text-neutral-300"><strong>Brąz</strong> 50-69%</span>
                </div>

                <div class="flex items-center gap-1.5">
                    <svg class="w-5 h-5" fill="rgb(107, 114, 128)" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs text-neutral-700 dark:text-neutral-300"><strong>Szary</strong> &lt;50%</span>
                </div>
            </div>
        </div>

        <!-- Bloki z osiągnięciami -->
        <div class="space-y-4">
            @foreach($blocks as $block)
                <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <!-- Nagłówek bloku z postępem -->
                    <div class="p-4 bg-gradient-to-r from-neutral-50 to-neutral-100 dark:from-neutral-800 dark:to-neutral-700 border-b border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h2 class="text-lg font-bold text-neutral-900 dark:text-white">{{ $block->title }}</h2>
                                @if($block->description)
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">{{ $block->description }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-2xl font-bold text-neutral-900 dark:text-white">
                                    {{ $blockProgress[$block->id]['percentage'] ?? 0 }}%
                                </div>
                                <div class="text-xs text-neutral-600 dark:text-neutral-400">
                                    {{ $blockProgress[$block->id]['completed'] ?? 0 }}/{{ $blockProgress[$block->id]['total'] ?? 0 }} zagadnień
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress bar -->
                        <div class="mt-3 h-2 bg-neutral-300 dark:bg-neutral-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500" style="width: {{ $blockProgress[$block->id]['percentage'] ?? 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- Zagadnienia w bloku -->
                    <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @php($blockTopics = $block->children()->orderBy('position')->get())
                        @forelse($blockTopics as $topic)
                            @php($achievement = $achievements->firstWhere('course_unit_id', $topic->id))
                            <div class="p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                <div class="flex items-start gap-4">
                                    <!-- Gwiazda lub placeholder -->
                                    <div class="flex-shrink-0 pt-1">
                                        @if($achievement)
                                            @php($starColors = [
                                                'gold' => 'rgb(234, 179, 8)',
                                                'silver' => 'rgb(156, 163, 175)',
                                                'bronze' => 'rgb(180, 83, 9)',
                                                'failed' => 'rgb(107, 114, 128)',
                                            ])
                                            <svg class="w-8 h-8" fill="{{ $starColors[$achievement->star_type] ?? 'rgb(107, 114, 128)' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Informacje o zagadnieniu -->
                                    <div class="flex-1">
                                        <h3 class="font-medium text-neutral-900 dark:text-white">{{ $topic->title }}</h3>
                                        @if($topic->description)
                                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">{{ $topic->description }}</p>
                                        @endif
                                        @if($topic->duration_minutes)
                                            <div class="text-xs text-neutral-500 dark:text-neutral-500 mt-2">⏱️ {{ $topic->duration_minutes }} min</div>
                                        @endif
                                    </div>

                                    <!-- Status -->
                                    @if($achievement)
                                        <div class="text-right flex-shrink-0">
                                            <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">
                                                ✓ Zaliczone
                                            </span>
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                                                {{ $achievement->assigned_at->format('d.m.Y') }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-right flex-shrink-0">
                                            <span class="inline-block px-3 py-1 bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-xs font-medium rounded-full">
                                                — Do zrobienia
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-neutral-500 dark:text-neutral-400">
                                Brak zagadnień w tym bloku
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Ogólne statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php(
                $totalTopics = $blocks->sum(fn($b) => $b->children()->count());
                $totalAchievements = $achievements->count();
                $goldCount = $achievements->where('star_type', 'gold')->count();
                $silverCount = $achievements->where('star_type', 'silver')->count();
                $bronzeCount = $achievements->where('star_type', 'bronze')->count();
            )
            
            <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <div class="text-sm text-neutral-600 dark:text-neutral-400">Całkowity postęp</div>
                <div class="text-3xl font-bold text-neutral-900 dark:text-white mt-2">
                    {{ $totalTopics > 0 ? round(($totalAchievements / $totalTopics) * 100) : 0 }}%
                </div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ $totalAchievements }}/{{ $totalTopics }} zagadnień zaliczonych
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <div class="text-sm text-neutral-600 dark:text-neutral-400">Gwiazdy łącznie</div>
                <div class="flex items-end gap-2 mt-2">
                    <svg class="w-8 h-8" fill="rgb(234, 179, 8)" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <div>
                        <div class="font-bold text-neutral-900 dark:text-white">{{ $totalAchievements }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">przyznanych</div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <div class="text-sm text-neutral-600 dark:text-neutral-400">Rozkład gwiazd</div>
                <div class="flex gap-2 mt-2">
                    @if($goldCount > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-200 rounded">
                            <svg class="w-3 h-3" fill="rgb(234, 179, 8)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $goldCount }}
                        </span>
                    @endif
                    @if($silverCount > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-200 rounded">
                            <svg class="w-3 h-3" fill="rgb(156, 163, 175)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $silverCount }}
                        </span>
                    @endif
                    @if($bronzeCount > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-200 rounded">
                            <svg class="w-3 h-3" fill="rgb(180, 83, 9)" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $bronzeCount }}
                        </span>
                    @endif
                    @if($goldCount === 0 && $silverCount === 0 && $bronzeCount === 0)
                        <span class="text-xs text-neutral-500 dark:text-neutral-400 italic">Brak przyznanych gwiazd</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <p class="text-neutral-600 dark:text-neutral-400">Brak dostępnych kursów lub bloków.</p>
        </div>
    @endif
</div>
