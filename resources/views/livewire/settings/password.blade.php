<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Settings') }}</h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Manage your account settings') }}</p>
            </div>
        </div>

        <!-- Taby nawigacyjne -->
        <div class="border-b border-neutral-200 dark:border-neutral-700">
            <div class="flex gap-1">
                <a href="{{ route('profile.edit') }}" wire:navigate class="px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('profile.edit') ? 'border-neutral-900 dark:border-white text-neutral-900 dark:text-white' : 'border-transparent text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }} transition-colors">
                    {{ __('Profile') }}
                </a>
                <a href="{{ route('user-password.edit') }}" wire:navigate class="px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('user-password.edit') ? 'border-neutral-900 dark:border-white text-neutral-900 dark:text-white' : 'border-transparent text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }} transition-colors">
                    {{ __('Password') }}
                </a>
                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <a href="{{ route('two-factor.show') }}" wire:navigate class="px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('two-factor.show') ? 'border-neutral-900 dark:border-white text-neutral-900 dark:text-white' : 'border-transparent text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }} transition-colors">
                        {{ __('Two-Factor Auth') }}
                    </a>
                @endif
                <a href="{{ route('appearance.edit') }}" wire:navigate class="px-4 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('appearance.edit') ? 'border-neutral-900 dark:border-white text-neutral-900 dark:text-white' : 'border-transparent text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white' }} transition-colors">
                    {{ __('Appearance') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6 max-w-2xl">
            <form method="POST" wire:submit="updatePassword" class="space-y-6">
            <flux:input
                wire:model="current_password"
                :label="__('Current password')"
                type="password"
                required
                autocomplete="current-password"
            />
            <flux:input
                wire:model="password"
                :label="__('New password')"
                type="password"
                required
                autocomplete="new-password"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            </form>
        </div>
    </div>
</flux:main>
