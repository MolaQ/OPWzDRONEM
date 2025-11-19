<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen flex flex-col bg-white dark:bg-zinc-800">

    {{-- Sticky Navbar --}}
    <flux:header class="sticky top-0 z-40 w-full bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar>
            <flux:navbar.item icon="home" href="#" current>Wiadomości</flux:navbar.item>
            <flux:navbar.item icon="document-text" href="#">Documents</flux:navbar.item>
            <flux:navbar.item icon="calendar" href="#">Calendar</flux:navbar.item>
            <flux:navbar.item icon="information-circle" href="#">Help</flux:navbar.item>
        </flux:navbar>
                        @auth
        <flux:spacer />
        <flux:dropdown position="top" align="end">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />
            <flux:menu>
                <flux:menu.item icon="cog">Settings</flux:menu.item>
                <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>

            </flux:menu>
        </flux:dropdown>
                        @endauth
                                                @guest


        <flux:spacer />
        <flux:navbar.item icon="arrow-right-end-on-rectangle" href="{{ route('login') }}">{{ __('Login') }}</flux:navbar.item>
                        @endguest
    </flux:header>

    {{-- Main content --}}
    <main class="flex-1 flex flex-col">
        <div class="flex-1 p-4">
            {{ $slot }}
        </div>

        {{-- Sticky Footer --}}
        <flux:footer class="sticky bottom-0 z-40 w-full bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700 px-4 py-2 flex items-center justify-between">
            <span>&copy; {{ date('Y') }} Twoja aplikacja</span>
            <div>
                <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="text-sm text-zinc-500">Repozytorium</a>
            </div>
        </flux:footer>
    </main>

    @fluxScripts
</body>
</html>
