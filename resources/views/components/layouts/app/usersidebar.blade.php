<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-[#000000] flex flex-col">

    <!-- Top Banner - Full Width -->
    <header class="bg-gradient-to-r from-[#106c21] via-[#112b50] to-[#2f76aa] text-white py-4 shadow-2xl">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between gap-4">
                <!-- Left Logo -->
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 bg-white rounded-lg shadow-xl overflow-hidden">
                        <img src="{{ asset('img/powiat.png') }}" alt="Logo Left" class="w-full h-full object-cover" onerror="this.src='{{ asset('img/powiat.svg') }}'">
                    </div>
                </div>

                <!-- Center Title -->
                <div class="flex-1 text-center px-4">
                    <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold tracking-tight">{{ config('app.name', 'OPW z Dronem') }}</h1>
                    <p class="mt-1 text-sm md:text-base lg:text-lg text-neutral-200">Zespół Szkół Technicznych w Pile</p>
                </div>

                <!-- Right Logos -->
                <div class="flex-shrink-0 flex gap-2 md:gap-4">
                    <div class="w-12 h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-white rounded-lg shadow-xl overflow-hidden">
                        <img src="{{ asset('img/opw.png') }}" alt="Logo Right 1" class="w-full h-full object-cover" onerror="this.src='{{ asset('img/wcr.svg') }}'">
                    </div>
                    <div class="w-12 h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-white rounded-lg shadow-xl overflow-hidden">
                        <img src="{{ asset('img/zst.png') }}" alt="Logo Right 2" class="w-full h-full object-cover" onerror="this.src='{{ asset('img/zst.svg') }}'">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-1 flex max-w-[100vw]">
        <!-- Left Sidebar -->
        <aside class="hidden lg:flex lg:flex-col w-64 bg-[#112b50] text-white border-r border-[#2f76aa]">
            <nav class="flex-1 flex flex-col p-6 space-y-2 overflow-y-auto">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-[#2f76aa] text-white' : 'text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="font-semibold">OPW z Dronem</span>
                </a>
                <a href="{{ route('news') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('news') ? 'bg-[#2f76aa] text-white' : 'text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span class="font-semibold">Aktualności</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="font-semibold">Education</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-semibold">Exercises</span>
                </a>

                <!-- Additional Links -->
                <div class="pt-6 mt-6 border-t border-[#2f76aa]">
                    <p class="px-4 text-xs uppercase text-neutral-400 font-semibold mb-3">Społeczność</p>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="font-medium">Użytkownicy</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span class="font-medium">Forum</span>
                    </a>
                </div>

                <!-- User Menu at Bottom -->
                <div class="mt-auto pt-6 border-t border-[#2f76aa]">
                    @auth
                    <flux:dropdown position="bottom" align="start">
                        <flux:button variant="ghost" class="w-full justify-start">
                            <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-[#2f76aa]/30 hover:bg-[#2f76aa]/50 cursor-pointer transition-all w-full">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#106c21] text-white font-semibold">
                                    {{ auth()->user()->initials() }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-neutral-300 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </flux:button>
                        <flux:menu class="w-[240px]">
                            <flux:menu.item :href="route('settings.all')" icon="cog" wire:navigate>{{ __('Ustawienia') }}</flux:menu.item>

                            @can('access admin panel')
                                <flux:menu.separator />
                                <flux:menu.item :href="route('admin.dashboard')" icon="shield-check" wire:navigate>{{ __('Panel Admin') }}</flux:menu.item>
                                <flux:menu.item :href="route('admin.posts')" icon="newspaper" wire:navigate>{{ __('Posty') }}</flux:menu.item>
                                <flux:menu.item :href="route('admin.members')" icon="users" wire:navigate>{{ __('Użytkownicy') }}</flux:menu.item>
                                <flux:menu.item :href="route('admin.groups')" icon="user-group" wire:navigate>{{ __('Grupy') }}</flux:menu.item>
                            @endcan

                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('Wyloguj') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                    @else
                    <a href="{{ route('login') }}" class="block w-full bg-[#106c21] hover:bg-[#2f76aa] text-white text-center font-semibold px-4 py-3 rounded-lg transition shadow-lg">
                        <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Zaloguj się
                    </a>
                    @endauth
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            {{ $slot }}
        </main>
    </div>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed bottom-4 right-4 z-50">
        <button onclick="document.getElementById('mobileMenu').classList.toggle('hidden')" class="bg-[#106c21] text-white p-4 rounded-full shadow-2xl hover:bg-[#2f76aa] transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden fixed inset-0 bg-black bg-opacity-75 z-40">
        <div class="bg-[#112b50] h-full w-64 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-white text-xl font-bold">Menu</h3>
                <button onclick="document.getElementById('mobileMenu').classList.add('hidden')" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-[#2f76aa] text-white' : 'text-neutral-300 hover:bg-[#2f76aa]/50' }}">
                    <span class="font-semibold">OPW z Dronem</span>
                </a>
                <a href="{{ route('news') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('news') ? 'bg-[#2f76aa] text-white' : 'text-neutral-300 hover:bg-[#2f76aa]/50' }}">
                    <span class="font-semibold">Aktualności</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                    <span class="font-semibold">Education</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                    <span class="font-semibold">Exercises</span>
                </a>

                @auth
                <div class="pt-6 mt-6 border-t border-[#2f76aa] space-y-1">
                    <p class="px-4 text-xs uppercase text-neutral-400 font-semibold mb-2">{{ __('Konto') }}</p>
                    <a href="{{ route('settings.all') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                        {{ __('Ustawienia') }}
                    </a>

                    @can('access admin panel')
                        <p class="px-4 pt-4 text-xs uppercase text-neutral-400 font-semibold">{{ __('Administracja') }}</p>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                            {{ __('Panel Admin') }}
                        </a>
                        <a href="{{ route('admin.posts') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                            {{ __('Posty') }}
                        </a>
                        <a href="{{ route('admin.members') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                            {{ __('Użytkownicy') }}
                        </a>
                        <a href="{{ route('admin.groups') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                            {{ __('Grupy') }}
                        </a>
                    @endcan

                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-[#106c21] hover:bg-[#2f76aa] transition">
                            {{ __('Wyloguj') }}
                        </button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}" class="block mt-4 bg-white text-[#106c21] px-4 py-2 rounded-lg text-center font-semibold">
                    Zaloguj się
                </a>
                @endauth
            </nav>
        </div>
    </div>

    <!-- Sticky Footer -->
    <footer class="bg-[#112b50] text-white border-t border-[#2f76aa] py-6 mt-auto">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="font-semibold text-lg">{{ config('app.name', 'OPW z Dronem') }}</p>
                    <p class="text-sm text-neutral-400 mt-1">Zespół Szkół Technicznych w Pile</p>
                </div>
                <div class="flex gap-6 text-sm text-neutral-400">
                    <a href="#" class="hover:text-white transition">Regulamin</a>
                    <a href="#" class="hover:text-white transition">Prywatność</a>
                    <a href="#" class="hover:text-white transition">Kontakt</a>
                </div>
                <div class="text-sm text-neutral-400">
                    &copy; {{ date('Y') }} Wszystkie prawa zastrzeżone
                </div>
            </div>
        </div>
    </footer>

    @fluxScripts
</body>
</html>
