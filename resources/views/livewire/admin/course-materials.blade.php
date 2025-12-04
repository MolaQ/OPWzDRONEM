<flux:main>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Materiały do zajęć</h1>
            <button wire:click="openMaterialEditor(null, null)" class="inline-flex items-center gap-2 px-4 py-2 bg-[#880000] hover:bg-red-900 text-white text-sm font-bold rounded transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                DODAJ MATERIAŁ
            </button>
        </div>

        <!-- Course info -->
        @if($course)
            <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $course->name }}</h2>
                @if($course->description)
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">{{ $course->description }}</p>
                @endif
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1 block">Szukaj</label>
                    <input type="text" wire:model.live="search" class="w-full px-3 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-[#880000]" placeholder="Szukaj po tytule..." />
                </div>
                <div>
                    <label class="text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1 block">Typ materiału</label>
                    <select wire:model.live="filterType" class="w-full px-3 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-[#880000]">
                        <option value="">Wszystkie typy</option>
                        <option value="pdf">Pliki</option>
                        <option value="video_link">Wideo</option>
                        <option value="external_link">Linki</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1 block">Blok/Zagadnienie</label>
                    <select wire:model.live="filterUnit" class="w-full px-3 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 text-sm focus:ring-2 focus:ring-[#880000]">
                        <option value="">Wszystkie</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}">{{ $block->title }} (BLOK)</option>
                            @php($blockTopics = $units->where('parent_id', $block->id))
                            @foreach($blockTopics as $topic)
                                <option value="{{ $topic->id }}">• {{ $topic->title }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Blocks and materials list -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            @forelse($blocks as $block)
                <div class="border-b border-neutral-200 dark:border-neutral-700 last:border-b-0">
                    <!-- Block header -->
                    <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-800">
                        <div class="font-semibold text-neutral-900 dark:text-white uppercase">{{ $block->title }}</div>
                        <div class="text-xs text-neutral-600 dark:text-neutral-400 mt-1">{{ $block->description }}</div>
                    </div>

                    <!-- Block materials -->
                    @if($block->materials->count() > 0)
                        <div class="px-4 py-2 bg-neutral-50/50 dark:bg-neutral-800/50">
                            <div class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Materiały bloku:</div>
                            @foreach($block->materials as $material)
                                <div class="flex items-center justify-between py-2 px-3 mb-1 bg-white dark:bg-neutral-900 rounded border border-neutral-200 dark:border-neutral-700">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $material->title }}</div>
                                        @if($material->description)
                                            <div class="text-xs text-neutral-600 dark:text-neutral-300 mt-1">{{ $material->description }}</div>
                                        @endif
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                            {{ ucfirst(str_replace('_', ' ', $material->type)) }} • 
                                            {{ $material->uploadedBy->name ?? 'Nieznany' }}
                                            @if(!$material->is_approved)
                                                <span class="text-yellow-500">• Czeka na zatwierdzenie</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="openMaterialEditor({{ $material->id }})" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Edytuj">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button wire:click="deleteMaterial({{ $material->id }})" wire:confirm="Usunąć ten materiał?" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Usuń">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Topics -->
                    @php($topics = $units->where('parent_id', $block->id))
                    @if($topics->count() > 0)
                        <div class="px-4 py-2">
                            @foreach($topics as $topic)
                                <div class="mb-3 pl-8">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-white mb-1">{{ $topic->title }}</div>
                                    
                                    <!-- Topic materials -->
                                    @if($topic->materials->count() > 0)
                                        @foreach($topic->materials as $material)
                                            <div class="flex items-center justify-between py-2 px-3 mb-1 bg-neutral-50 dark:bg-neutral-800 rounded border border-neutral-200 dark:border-neutral-700">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $material->title }}</div>
                                                    @if($material->description)
                                                        <div class="text-xs text-neutral-600 dark:text-neutral-300 mt-1">{{ $material->description }}</div>
                                                    @endif
                                                    <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                                        {{ ucfirst(str_replace('_', ' ', $material->type)) }} • 
                                                        {{ $material->uploadedBy->name ?? 'Nieznany' }}
                                                        @if(!$material->is_approved)
                                                            <span class="text-yellow-500">• Czeka na zatwierdzenie</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button wire:click="openMaterialEditor({{ $material->id }})" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Edytuj">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </button>
                                                    <button wire:click="deleteMaterial({{ $material->id }})" wire:confirm="Usunąć ten materiał?" class="inline-flex items-center justify-center w-6 h-6 bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition-colors" title="Usuń">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400 italic py-2 px-3">Brak materiałów</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">Brak bloków w kursie.</div>
            @endforelse
        </div>

        <!-- Material editor modal -->
        @if($showMaterialEditor)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click="closeMaterialEditor">
                <div class="bg-neutral-900 border border-neutral-700 rounded-2xl shadow-2xl transform transition-all max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                    <div class="px-6 py-4 border-b border-neutral-800 flex items-center justify-between sticky top-0 bg-neutral-900">
                        <h2 class="text-lg font-semibold text-white">{{ $editingMaterialId ? 'EDYTUJ MATERIAŁ' : 'DODAJ MATERIAŁ' }}</h2>
                        <button wire:click="closeMaterialEditor" class="text-neutral-400 hover:text-neutral-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Unit selector -->
                        <div>
                            <label class="text-sm font-medium text-neutral-100">Przypisz do</label>
                            <select wire:model="selectedUnitId" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]">
                                <option value="">Wybierz blok lub zagadnienie...</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}">{{ $block->title }} (BLOK)</option>
                                    @php($blockTopics = $units->where('parent_id', $block->id))
                                    @foreach($blockTopics as $topic)
                                        <option value="{{ $topic->id }}">• {{ $topic->title }} (zagadnienie)</option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('selectedUnitId') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-neutral-100">Tytuł materiału</label>
                            <input type="text" wire:model="materialTitle" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]" placeholder="np. Instrukcja montażu" />
                            @error('materialTitle') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-neutral-100">Opis (opcjonalny)</label>
                            <textarea wire:model="materialDescription" rows="3" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]" placeholder="Krótki opis materiału..."></textarea>
                            @error('materialDescription') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-neutral-100">Typ materiału</label>
                            <select wire:model.live="materialType" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]">
                                <option value="pdf">Plik PDF</option>
                                <option value="video_link">Link do wideo</option>
                                <option value="external_link">Zewnętrzny link</option>
                            </select>
                        </div>

                        @if($materialType === 'pdf')
                            <div>
                                <label class="text-sm font-medium text-neutral-100">Plik PDF (max 50MB)</label>
                                <input type="file" wire:model="materialFile" accept=".pdf" class="w-full px-3 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100" />
                                @error('materialFile') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div>
                                <label class="text-sm font-medium text-neutral-100">URL materiału</label>
                                <input type="url" wire:model="materialUrl" class="w-full px-4 py-2 rounded border border-neutral-700 bg-neutral-800 text-neutral-100 focus:ring-2 focus:ring-[#880000]" placeholder="https://..." />
                                @error('materialUrl') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 border-t border-neutral-800 flex items-center justify-end gap-2 sticky bottom-0 bg-neutral-900">
                        <button wire:click="closeMaterialEditor" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-neutral-700 hover:bg-neutral-600 text-neutral-100 rounded transition">
                            Anuluj
                        </button>
                        <button wire:click="saveMaterial" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-[#880000] hover:bg-red-900 text-white rounded border-2 border-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Zapisz materiał
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:main>

