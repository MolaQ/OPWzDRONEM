<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header removed per request -->

        <!-- Filter Bar at Top -->
        <div class="p-4 bg-gradient-to-r from-neutral-50 to-neutral-100 dark:from-neutral-800 dark:to-neutral-700 rounded-lg border-2 border-neutral-200 dark:border-neutral-600">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Group Buttons -->
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-3 uppercase tracking-wider">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                        </svg>
                        Grupa
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @forelse($groups as $group)
                            <button
                                wire:click="$set('selectedGroupId', {{ $group->id }})"
                                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap {{ $selectedGroupId === $group->id ? 'bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 shadow-lg border-2 border-neutral-900 dark:border-white' : 'bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 border-2 border-neutral-300 dark:border-neutral-600 hover:border-neutral-400 dark:hover:border-neutral-500' }}"
                            >
                                {{ $group->name }}
                            </button>
                        @empty
                            <span class="text-sm text-neutral-500 dark:text-neutral-400 italic">Brak grup</span>
                        @endforelse
                    </div>
                </div>

                <!-- Block Buttons -->
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-3 uppercase tracking-wider">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM15 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zM5 13a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5z"></path>
                        </svg>
                        Blok
                    </label>
                    @if($selectedGroupId)
                        <div class="flex flex-wrap gap-2">
                            @forelse($blocks as $block)
                                <button
                                    wire:click="$set('selectedBlockId', {{ $block->id }})"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap {{ $selectedBlockId === $block->id ? 'bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 shadow-lg border-2 border-neutral-900 dark:border-white' : 'bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 border-2 border-neutral-300 dark:border-neutral-600 hover:border-neutral-400 dark:hover:border-neutral-500' }}"
                                >
                                    {{ $block->title }}
                                </button>
                            @empty
                                <span class="text-sm text-neutral-500 dark:text-neutral-400 italic">Brak bloków</span>
                            @endforelse
                        </div>
                    @else
                        <div class="px-3 py-2 text-sm text-neutral-500 dark:text-neutral-400 italic">Wybierz grupę</div>
                    @endif
                </div>

                <!-- Topic Buttons -->
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-3 uppercase tracking-wider">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 100-2 4 4 0 00-4 4v10a4 4 0 004 4h12a4 4 0 004-4V5a4 4 0 00-4-4 1 1 0 100 2 2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"></path>
                        </svg>
                        Zagadnienie
                    </label>
                    @if($selectedGroupId && $selectedBlockId)
                        <div class="flex flex-wrap gap-2">
                            @forelse($topics as $topic)
                                <button
                                    wire:click="$set('selectedTopicId', {{ $topic->id }})"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 whitespace-nowrap {{ $selectedTopicId === $topic->id ? 'bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 shadow-lg border-2 border-neutral-900 dark:border-white' : 'bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 border-2 border-neutral-300 dark:border-neutral-600 hover:border-neutral-400 dark:hover:border-neutral-500' }}"
                                >
                                    {{ $topic->title }}
                                </button>
                            @empty
                                <span class="text-sm text-neutral-500 dark:text-neutral-400 italic">Brak zagadnień</span>
                            @endforelse
                        </div>
                    @else
                        <div class="px-3 py-2 text-sm text-neutral-500 dark:text-neutral-400 italic">Wybierz blok</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search Input (only if topic selected) -->
        @if($selectedTopicId)
            <div>
                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-2 uppercase tracking-wider">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                    Szukaj
                </label>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Imię lub email..."
                    class="w-full px-4 py-2 bg-white dark:bg-neutral-700 border-2 border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 focus:ring-2 focus:ring-offset-0 focus:ring-neutral-400 dark:focus:ring-neutral-500 transition-colors"
                >
            </div>
        @endif

        <!-- Students Table -->
        @if($selectedTopicId && $students->isNotEmpty())
            <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
                <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-100 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-900 dark:text-white">Uczeń</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-neutral-900 dark:text-white">Ocena</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold text-neutral-900 dark:text-white">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($students as $student)
                            @php
                                $achievement = $achievements->where('user_id', $student->id)->first();
                                $currentStar = $achievement?->star_type;
                            @endphp
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                <td class="px-6 py-4 text-neutral-900 dark:text-white">
                                    <div class="font-medium">{{ $student->name }}</div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $student->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-1">
                                        <!-- Gold Star -->
                                        <button
                                            wire:click="assignStar({{ $student->id }}, 'gold')"
                                            class="p-2 rounded transition-all {{ $currentStar === 'gold' ? 'bg-amber-100 dark:bg-amber-900' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                            title="Złoto (90-100%)"
                                        >
                                            <svg class="{{ $currentStar === 'gold' ? 'w-8 h-8' : 'w-5 h-5' }}" fill="{{ $currentStar === 'gold' ? 'rgb(234, 179, 8)' : 'none' }}" stroke="{{ $currentStar === 'gold' ? 'none' : 'rgb(234, 179, 8)' }}" stroke-width="{{ $currentStar === 'gold' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>

                                        <!-- Silver Star -->
                                        <button
                                            wire:click="assignStar({{ $student->id }}, 'silver')"
                                            class="p-2 rounded transition-all {{ $currentStar === 'silver' ? 'bg-gray-200 dark:bg-gray-700' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                            title="Srebro (70-89%)"
                                        >
                                            <svg class="{{ $currentStar === 'silver' ? 'w-8 h-8' : 'w-5 h-5' }}" fill="{{ $currentStar === 'silver' ? 'rgb(156, 163, 175)' : 'none' }}" stroke="{{ $currentStar === 'silver' ? 'none' : 'rgb(156, 163, 175)' }}" stroke-width="{{ $currentStar === 'silver' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>

                                        <!-- Bronze Star -->
                                        <button
                                            wire:click="assignStar({{ $student->id }}, 'bronze')"
                                            class="p-2 rounded transition-all {{ $currentStar === 'bronze' ? 'bg-orange-100 dark:bg-orange-900' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800' }}"
                                            title="Brąz (50-69%)"
                                        >
                                            <svg class="{{ $currentStar === 'bronze' ? 'w-8 h-8' : 'w-5 h-5' }}" fill="{{ $currentStar === 'bronze' ? 'rgb(180, 83, 9)' : 'none' }}" stroke="{{ $currentStar === 'bronze' ? 'none' : 'rgb(180, 83, 9)' }}" stroke-width="{{ $currentStar === 'bronze' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>

                                        <!-- Gray Star -->
                                        <button
                                            wire:click="assignStar({{ $student->id }}, 'failed')"
                                            class="p-2 rounded transition-all hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                            title="Szary (<50%)"
                                        >
                                            <svg class="{{ $currentStar === 'failed' ? 'w-8 h-8' : 'w-5 h-5' }}" fill="{{ $currentStar === 'failed' ? 'rgb(107, 114, 128)' : 'none' }}" stroke="{{ $currentStar === 'failed' ? 'none' : 'rgb(107, 114, 128)' }}" stroke-width="{{ $currentStar === 'failed' ? '0' : '1.5' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($currentStar)
                                        <button
                                            type="button"
                                            wire:click="removeStar({{ $student->id }})"
                                            class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 border-2 border-neutral-300 hover:border-neutral-400 hover:text-neutral-700 rounded transition-all dark:text-neutral-400 dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:text-neutral-300"
                                            title="Usuń"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Legend Below Table - Single Line -->
            <div class="p-3 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-xs font-semibold text-neutral-900 dark:text-white uppercase tracking-wider">Legenda:</span>

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
        @elseif($selectedTopicId && $students->isEmpty())
            <div class="text-center py-12">
                <p class="text-neutral-500 dark:text-neutral-400">Brak uczniów spełniających kryteria wyszukiwania.</p>
            </div>
        @elseif(!$selectedTopicId && $selectedBlockId)
            <div class="text-center py-12">
                <p class="text-neutral-500 dark:text-neutral-400">Wybierz zagadnienie, aby wyświetlić uczniów.</p>
            </div>
        @elseif(!$selectedBlockId && $selectedGroupId)
            <div class="text-center py-12">
                <p class="text-neutral-500 dark:text-neutral-400">Wybierz blok, aby wyświetlić zagadnienia.</p>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-neutral-500 dark:text-neutral-400">Wybierz grupę, blok i zagadnienie, aby wyświetlić uczniów.</p>
            </div>
        @endif
    </div>
</flux:main>

<script>
document.addEventListener('livewire:navigated', function() {
    Livewire.on('star-awarded', (data) => {
        const { studentName, starLabel } = data;
        Swal.fire({
            icon: 'success',
            title: 'Gwiazdka przyznana! ⭐',
            html: `<p><strong>${studentName}</strong></p><p>przyznano ${starLabel}</p>`,
            toast: true,
            position: 'top-end',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    });
});
</script>
