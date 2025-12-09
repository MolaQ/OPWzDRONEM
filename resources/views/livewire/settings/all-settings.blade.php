<div class="max-w-4xl mx-auto p-3">
    <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-[#106c21] hover:text-[#2f76aa] transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Powrót do panelu admin
        </a>
    </div>

    <div class="bg-gradient-to-br from-[#112b50] to-[#2f76aa] rounded-xl border-2 border-[#106c21] shadow-2xl p-6 space-y-6">
        <h1 class="text-3xl font-bold text-white mb-4">Ustawienia konta</h1>

        <!-- Profile Section -->
        <div class="bg-neutral-800/50 rounded-lg p-6 border border-neutral-700">
            <h2 class="text-xl font-bold text-white mb-4">Profil</h2>
            <form wire:submit.prevent="updateProfile" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-neutral-300 mb-2">Imię i nazwisko</label>
                    <input
                        type="text"
                        value="{{ $name }}"
                        disabled
                        class="w-full px-4 py-3 rounded-lg bg-neutral-700 text-neutral-400 border border-neutral-600 cursor-not-allowed"
                    />
                    <p class="text-xs text-neutral-400 mt-1">Zmiana nazwy jest ograniczona do instruktorów i administratorów.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-300 mb-2">Email</label>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full px-4 py-3 rounded-lg bg-neutral-800 text-white border border-neutral-600 focus:border-[#106c21] focus:ring-2 focus:ring-[#106c21] transition"
                    />
                    @error('email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="bg-[#106c21] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#2f76aa] transition">
                    Zapisz profil
                </button>
            </form>
        </div>

        <!-- Password Section -->
        <div class="bg-neutral-800/50 rounded-lg p-6 border border-neutral-700">
            <h2 class="text-xl font-bold text-white mb-4">Zmiana hasła</h2>
            <form wire:submit.prevent="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-neutral-300 mb-2">Obecne hasło</label>
                    <input
                        type="password"
                        wire:model="current_password"
                        class="w-full px-4 py-3 rounded-lg bg-neutral-800 text-white border border-neutral-600 focus:border-[#106c21] focus:ring-2 focus:ring-[#106c21] transition"
                    />
                    @error('current_password') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-300 mb-2">Nowe hasło</label>
                    <input
                        type="password"
                        wire:model="password"
                        class="w-full px-4 py-3 rounded-lg bg-neutral-800 text-white border border-neutral-600 focus:border-[#106c21] focus:ring-2 focus:ring-[#106c21] transition"
                    />
                    @error('password') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-300 mb-2">Potwierdź nowe hasło</label>
                    <input
                        type="password"
                        wire:model="password_confirmation"
                        class="w-full px-4 py-3 rounded-lg bg-neutral-800 text-white border border-neutral-600 focus:border-[#106c21] focus:ring-2 focus:ring-[#106c21] transition"
                    />
                </div>

                <button type="submit" class="bg-[#106c21] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#2f76aa] transition">
                    Zmień hasło
                </button>
            </form>
        </div>

        <!-- Appearance Section -->
        <div class="bg-neutral-800/50 rounded-lg p-6 border border-neutral-700">
            <h2 class="text-xl font-bold text-white mb-4">Wygląd</h2>
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light" icon="sun">{{ __('Jasny') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Ciemny') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('Systemowy') }}</flux:radio>
            </flux:radio.group>
        </div>
    </div>
</div>
