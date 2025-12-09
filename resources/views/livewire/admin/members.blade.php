<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Filtry i wyszukiwanie -->
        <div class="space-y-3 p-4 bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700">
            <!-- Rząd 1: Wyszukiwanie -->
            <div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Szukaj po nazwie, mailu lub grupie..."
                    class="w-full px-4 py-2 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]"
                />
            </div>

            <!-- Rząd 2: Filtry i przyciski -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <select wire:model.live="role" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie role —</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="group_id" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie grupy —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="active" class="rounded border border-neutral-200 dark:border-neutral-700 px-4 py-2 focus:ring-2 focus:ring-[#880000] dark:bg-neutral-800 dark:text-neutral-100 text-sm">
                        <option value="">— Wszystkie statusy —</option>
                        <option value="1">Aktywny</option>
                        <option value="0">Nieaktywny</option>
                    </select>
                </div>

                <button
                    wire:click="showCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all shadow-md
                           bg-white text-neutral-900 border-2 border-neutral-300 hover:border-neutral-400 hover:shadow-lg
                           focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-400
                           dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:shadow-lg dark:hover:bg-neutral-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj użytkownika
                </button>
            </div>
        </div>

        <!-- Tabela użytkowników -->
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden flex-1 overflow-y-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="py-2 px-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Imię i nazwisko</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Email</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Rola</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Grupa</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                            <td class="py-2 px-3 text-neutral-900 dark:text-neutral-100">{{ $user->name }}</td>
                            <td class="py-2 px-3 text-neutral-900 dark:text-neutral-100">{{ $user->email }}</td>
                            <td class="py-2 px-3 text-neutral-900 dark:text-neutral-100 capitalize">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                            <td class="py-2 px-3 text-neutral-900 dark:text-neutral-100">{{ $user->group?->name ?? '—' }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $user->active ? 'Aktywny' : 'Nieaktywny' }}
                                </span>
                            </td>
                            <td class="py-2 px-3 flex gap-2">
                                <a href="{{ route('admin.member.detail', $user->id) }}" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded transition-all dark:text-neutral-400 dark:hover:text-neutral-100 dark:hover:bg-neutral-800" title="Szczegóły">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                </a>
                                <button type="button" wire:click="editUser({{ intval($user->id) }})" class="inline-flex items-center justify-center w-7 h-7 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded transition-all dark:text-neutral-400 dark:hover:text-neutral-100 dark:hover:bg-neutral-800" title="Edytuj">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </button>
                                <button type="button" wire:click="deleteUser({{ intval($user->id) }})" wire:confirm="Czy na pewno usunąć tego użytkownika?" class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-all dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-950" title="Usuń">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-neutral-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-neutral-300 dark:text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="text-lg font-medium">Brak użytkowników</p>
                                <p class="text-sm mt-1">Dodaj pierwszego użytkownika klikając przycisk powyżej</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginacja -->
        <div class="mt-6">{{ $users->links() }}</div>
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 py-6" wire:click="closeModal">
                <div class="flex items-start justify-center min-h-full px-4">
                    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl w-full max-w-2xl shadow-2xl transform transition-all my-8" wire:click.stop>
                        <!-- Modal Header with Barcode at the top -->
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 flex items-center justify-between gap-6 rounded-t-2xl">
                            <h3 class="text-xl font-bold text-neutral-900 dark:text-white flex-1">
                                {{ $editingUser['id'] ? 'Edytuj użytkownika' : 'Dodaj nowego użytkownika' }}
                            </h3>
                            @if($editingUser['id'] && $editingUser['barcode'])
                            <div
                                class="inline-block bg-white dark:bg-neutral-800 rounded-lg px-4 py-3 border border-neutral-200 dark:border-neutral-700"
                                x-data="{
                                    render() {
                                        const el = document.getElementById('barcode-top-{{ $editingUser['id'] }}');
                                        if (!el || typeof JsBarcode === 'undefined') {
                                            setTimeout(() => this.render(), 100);
                                            return;
                                        }
                                        const isDark = document.documentElement.classList.contains('dark');
                                        try {
                                            JsBarcode(el, '{{ $editingUser['barcode'] }}', {
                                                format: 'CODE128',
                                                width: 2,
                                                height: 50,
                                                displayValue: false,
                                                margin: 6,
                                                lineColor: isDark ? '#ffffff' : '#000000',
                                                background: 'transparent'
                                            });
                                        } catch(e) {
                                            console.error(e);
                                        }
                                    }
                                }"
                                x-init="$nextTick(() => render())"
                                @showModal.window="render()"
                            >
                                <div class="text-center" wire:ignore>
                                    <svg id="barcode-top-{{ $editingUser['id'] }}"></svg>
                                    <p class="mt-1 text-xs font-mono text-neutral-900 dark:text-neutral-100">{{ $editingUser['barcode'] }}</p>
                                </div>
                            </div>
                            @endif
                            <button wire:click="closeModal" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 transition rounded-lg p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body (scrollable if needed) -->
                        <form wire:submit.prevent="saveUser" class="space-y-6 p-6 max-h-[70vh] overflow-y-auto" autocomplete="off">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Imię i nazwisko -->
                        <div class="md:col-span-2">
                            <label for="name" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                Imię i nazwisko <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                wire:model.defer="editingUser.name"
                                placeholder="Jan Kowalski"
                                class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            />
                            @error('editingUser.name')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label for="email" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                Adres email <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                type="email"
                                wire:model.defer="editingUser.email"
                                placeholder="jan.kowalski@example.com"
                                class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            />
                            @error('editingUser.email')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Rola -->
                        <div>
                            <label for="role" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                Rola <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="role"
                                wire:model.defer="editingUser.role"
                                class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            >
                                <option value="student">👤 Student</option>
                                <option value="admin">⚡ Administrator</option>
                                <option value="instructor">🎓 Instruktor</option>
                                <option value="guest">👁️ Gość</option>
                            </select>
                            @error('editingUser.role')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Grupa -->
                        <div>
                            <label for="group_id" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                Grupa
                            </label>
                            @if($editingUser['role'] === 'student')
                                <select
                                    id="group_id"
                                    wire:model.defer="editingUser.group_id"
                                    class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                >
                                    <option value="">Brak przypisania</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="w-full rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 px-4 py-3 text-neutral-500 dark:text-neutral-400 text-sm">
                                    Tylko studenci mogą być przypisani do grupy
                                </div>
                            @endif
                            @error('editingUser.group_id')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Hasło -->
                        <div class="md:col-span-2">
                            <label for="password" class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                Hasło @if(!$editingUser['id'])<span class="text-red-500">*</span>@endif
                                @if($editingUser['id'])<span class="text-neutral-500 text-xs">(pozostaw puste, aby nie zmieniać)</span>@endif
                            </label>
                            <input
                                id="password"
                                type="password"
                                wire:model.defer="editingUser.password"
                                placeholder="{{ $editingUser['id'] ? 'Pozostaw puste, aby nie zmieniać hasła' : 'Minimum 8 znaków' }}"
                                class="w-full rounded-lg bg-white border border-neutral-300 dark:bg-neutral-800 dark:border-neutral-700 px-4 py-3 text-neutral-900 dark:text-neutral-100 placeholder-neutral-500 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            />
                            @error('editingUser.password')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <!-- Status konta -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                Status konta <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-4 p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingUser.active"
                                        value="1"
                                        class="w-4 h-4 text-green-600 focus:ring-green-500 focus:ring-2"
                                    />
                                    <span class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1 rounded-full border border-green-500 text-green-700 bg-green-50 dark:bg-green-900/20 dark:text-green-200">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Aktywne
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input
                                        type="radio"
                                        wire:model.defer="editingUser.active"
                                        value="0"
                                        class="w-4 h-4 text-red-600 focus:ring-red-500 focus:ring-2"
                                    />
                                    <span class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1 rounded-full border border-red-500 text-red-700 bg-red-50 dark:bg-red-900/20 dark:text-red-200">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Nieaktywne
                                    </span>
                                </label>
                            </div>
                            @error('editingUser.active')<p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        @if(!$editingUser['id'])
                        <div class="md:col-span-2">
                            <div class="p-4 bg-blue-900/20 rounded-lg border border-blue-800">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-blue-300">Automatyczne generowanie kodu</p>
                                        <p class="text-xs text-blue-400 mt-1">Kod kreskowy typu Code-128 zostanie automatycznie wygenerowany po utworzeniu użytkownika w formacie S##### (Student).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Przyciski akcji -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-6 py-2.5 rounded-lg border border-neutral-300 text-neutral-700 bg-white hover:bg-neutral-50 font-medium transition dark:border-neutral-600 dark:text-neutral-300 dark:bg-neutral-800 dark:hover:bg-neutral-700"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all shadow-md
                                   bg-white text-neutral-900 border-2 border-neutral-300 hover:border-neutral-400 hover:shadow-lg
                                   focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-400
                                   dark:bg-neutral-800 dark:text-white dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:shadow-lg dark:hover:bg-neutral-700"
                        >
                            {{ $editingUser['id'] ? 'Zapisz zmiany' : 'Dodaj użytkownika' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- JsBarcode Script -->
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        @if($editingUser['id'] && $editingUser['barcode'])
        <script>
            // Wait for modal and library to be fully loaded
            (function() {
                const renderBarcode = () => {
                    const barcodeElement = document.getElementById('barcode-{{ $editingUser['id'] }}');
                    if (barcodeElement && typeof JsBarcode !== 'undefined') {
                        const isDark = document.documentElement.classList.contains('dark');
                        try {
                            JsBarcode(barcodeElement, '{{ $editingUser['barcode'] }}', {
                                format: 'CODE128',
                                width: 2,
                                height: 80,
                                displayValue: false,
                                margin: 10,
                                lineColor: isDark ? '#ffffff' : '#000000',
                                background: 'transparent'
                            });
                            console.log('Barcode rendered successfully');
                        } catch (e) {
                            console.error('Barcode generation error:', e);
                        }
                    } else {
                        console.log('Retrying barcode render...');
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
    @endif

    </div>
</flux:main>
