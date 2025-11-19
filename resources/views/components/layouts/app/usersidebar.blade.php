<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-[#000000] flex flex-col">

    <!-- Top Banner - Full Width -->
    <header class="bg-gradient-to-r from-[#106c21] via-[#112b50] to-[#2f76aa] text-white py-8 shadow-2xl">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-5xl font-bold tracking-tight">{{ config('app.name', 'OPW z Dronem') }}</h1>
                    <p class="mt-2 text-lg text-neutral-200">Zespół Szkół Technicznych w Pile</p>
                </div>

                @auth
                <div class="hidden lg:flex items-center gap-4">
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                    <flux:dropdown position="bottom" align="end">
                        <flux:profile
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                        />
                        <flux:menu class="w-[220px]">
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Ustawienia') }}</flux:menu.item>
                            @if(in_array(auth()->user()->role, ['admin', 'instructor']))
                            <flux:menu.item :href="route('admin.dashboard')" icon="shield-check" wire:navigate>{{ __('Panel Admin') }}</flux:menu.item>
                            @endif
                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('Wyloguj') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
                @else
                <a href="{{ route('login') }}" class="hidden lg:block bg-white text-[#106c21] px-6 py-2 rounded-lg font-semibold hover:bg-neutral-100 transition shadow-lg">
                    Zaloguj się
                </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-1 flex">
        <!-- Left Sidebar -->
        <aside class="hidden lg:block w-64 bg-[#112b50] text-white border-r border-[#2f76aa]">
            <nav class="p-6 space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-[#2f76aa] text-white' : 'text-neutral-300 hover:bg-[#2f76aa]/50 hover:text-white' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span class="font-semibold">News</span>
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
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-[#2f76aa] text-white">
                    <span class="font-semibold">News</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                    <span class="font-semibold">Education</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-neutral-300 hover:bg-[#2f76aa]/50">
                    <span class="font-semibold">Exercises</span>
                </a>

                @auth
                <div class="pt-6 mt-6 border-t border-[#2f76aa]">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-neutral-300">
                        Ustawienia
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-3 text-neutral-300">
                            Wyloguj
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
                    <p class="text-sm text-neutral-400 mt-1">Platforma edukacyjna operatorów dronów</p>
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
