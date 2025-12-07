<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="flex min-h-screen">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('admin.dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                {{-- Sekcja dla Studentów --}}
                @if(auth()->user()->hasRole('student'))
                <div x-data="{ open: true }" class="mb-2">
                    <button @click="open = !open" class="flex w-full items-center justify-between px-2 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19V7a2 2 0 012-2h9a2 2 0 012 2v12M3 19h14M3 19l4-4m10 4V7a2 2 0 012-2h0a2 2 0 012 2v12m-4 0h4"/></svg>
                            Moje Kursy
                        </span>
                        <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        <flux:navlist.item icon="star" :href="route('student.achievements')" :current="request()->routeIs('student.achievements')" wire:navigate>Moje Osiągnięcia</flux:navlist.item>
                        <flux:navlist.item icon="academic-cap" href="#" wire:navigate>Materiały</flux:navlist.item>
                    </div>
                </div>
                @endif

                {{-- Sekcja Struktura Kursu --}}
                @canany(['admin.panel.access', 'courses.view', 'achievements.view'])
                <div x-data="{ open: {{ request()->routeIs(['admin.dashboard', 'admin.courses', 'admin.course-materials', 'admin.awards']) ? 'true' : 'false' }} }" class="mb-2">
                    <button @click="open = !open" class="flex w-full items-center justify-between px-2 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6l7 4-7 4-7-4 7-4zm0 8v4m0-12V2"/></svg>
                            Struktura Kursu
                        </span>
                        <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        @can('admin.panel.access')
                        <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>Pulpit</flux:navlist.item>
                        @endcan
                        @can('courses.view')
                        <flux:navlist.item icon="academic-cap" :href="route('admin.courses')" :current="request()->routeIs('admin.courses')" wire:navigate>Program kursu</flux:navlist.item>
                        <flux:navlist.item icon="document-text" :href="route('admin.course-materials')" :current="request()->routeIs('admin.course-materials')" wire:navigate>Materiały do zajęć</flux:navlist.item>
                        @endcan
                        @can('achievements.view')
                        <flux:navlist.item icon="star" :href="route('admin.awards')" :current="request()->routeIs('admin.awards')" wire:navigate>Przyznaj Gwiazdy</flux:navlist.item>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- Sekcja Treści --}}
                @canany(['posts.view', 'comments.view'])
                <div x-data="{ open: {{ request()->routeIs(['admin.posts', 'admin.comments']) ? 'true' : 'false' }} }" class="mb-2">
                    <button @click="open = !open" class="flex w-full items-center justify-between px-2 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-8 8h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Treści
                        </span>
                        <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        @can('posts.view')
                        <flux:navlist.item icon="newspaper" :href="route('admin.posts')" :current="request()->routeIs('admin.posts')" wire:navigate>Posty</flux:navlist.item>
                        @endcan
                        @can('comments.view')
                        <flux:navlist.item icon="chat-bubble-left-right" :href="route('admin.comments')" :current="request()->routeIs('admin.comments')" wire:navigate>Komentarze</flux:navlist.item>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- Sekcja dla Wychowawców --}}
                @if(auth()->user()->hasRole('wychowawca') || auth()->user()->hasRole('koordynator') || auth()->user()->hasRole('admin'))
                <div x-data="{ open: {{ request()->routeIs('teacher.overview') ? 'true' : 'false' }} }" class="mb-2">
                    <button @click="open = !open" class="flex w-full items-center justify-between px-2 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4c1.657 0 3 1.343 3 3S13.657 10 12 10s-3-1.343-3-3 1.343-3 3-3zm-6 14a6 6 0 1112 0v1H6v-1z"/></svg>
                            Wychowawca
                        </span>
                        <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        <flux:navlist.item icon="chart-bar" :href="route('teacher.overview')" :current="request()->routeIs('teacher.overview')" wire:navigate>Przegląd klasy</flux:navlist.item>
                    </div>
                </div>
                @endif

                {{-- Sekcja Zarządzania --}}
                @canany(['users.view', 'groups.view', 'roles.view', 'permissions.view'])
                <div x-data="{ open: {{ request()->routeIs(['admin.members', 'admin.groups', 'admin.roles', 'admin.permissions']) ? 'true' : 'false' }} }" class="mb-2">
                    <button @click="open = !open" class="flex w-full items-center justify-between px-2 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            Zarządzanie
                        </span>
                        <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        @can('users.view')
                        <flux:navlist.item icon="users" :href="route('admin.members')" :current="request()->routeIs('admin.members')" wire:navigate>Użytkownicy</flux:navlist.item>
                        @endcan
                        @can('groups.view')
                        <flux:navlist.item icon="user-group" :href="route('admin.groups')" :current="request()->routeIs('admin.groups')" wire:navigate>Grupy</flux:navlist.item>
                        @endcan
                        @can('roles.view')
                        <flux:navlist.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>Role</flux:navlist.item>
                        @endcan
                        @can('permissions.view')
                        <flux:navlist.item icon="key" :href="route('admin.permissions')" :current="request()->routeIs('admin.permissions')" wire:navigate>Uprawnienia</flux:navlist.item>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- Sekcja Wyposażenie Pracowni --}}
                @canany(['equipment.view', 'equipment-sets.view', 'rentals.view'])
                <div x-data="{ open: {{ request()->routeIs(['admin.rentals', 'admin.returns', 'admin.equipment', 'admin.equipment-sets']) ? 'true' : 'false' }} }" class="mb-2">
                    <button @click="open = !open" class="flex w-full items-center justify-between px-2 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l2 10h10l2-10h2M9 7V4h6v3"/></svg>
                            Wyposażenie Pracowni
                        </span>
                        <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        @can('rentals.view')
                        <flux:navlist.item icon="shopping-cart" :href="route('admin.rentals')" :current="request()->routeIs('admin.rentals')" wire:navigate>Wypożyczenia</flux:navlist.item>
                        <flux:navlist.item icon="arrow-uturn-left" :href="route('admin.returns')" :current="request()->routeIs('admin.returns')" wire:navigate>Zwroty</flux:navlist.item>
                        @endcan
                        @can('equipment.view')
                        <flux:navlist.item icon="cube" :href="route('admin.equipment')" :current="request()->routeIs('admin.equipment')" wire:navigate>Wyposażenie</flux:navlist.item>
                        @endcan
                        @can('equipment-sets.view')
                        <flux:navlist.item icon="cube" :href="route('admin.equipment-sets')" :current="request()->routeIs('admin.equipment-sets')" wire:navigate>Zestawy</flux:navlist.item>
                        @endcan
                    </div>
                </div>
                @endcanany
            </flux:navlist>

            <flux:spacer />

            {{-- Wyszukiwarka przeniesiona na dół --}}
            @can('admin.panel.access')
            <flux:navlist variant="outline">
                <flux:navlist.item icon="magnifying-glass" :href="route('admin.search')" :current="request()->routeIs('admin.search')" wire:navigate>Wyszukiwarka</flux:navlist.item>
            </flux:navlist>
            @endcan

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:button variant="ghost" class="w-full justify-start text-neutral-900 dark:text-neutral-50">
                    <div class="flex items-center gap-3 px-2 py-2 rounded-lg w-full">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-neutral-200 text-neutral-900 font-semibold dark:bg-neutral-700 dark:text-white">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p class="text-sm font-semibold text-neutral-900 truncate dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-neutral-500 truncate dark:text-neutral-300">{{ auth()->user()->email }}</p>
                        </div>
                        <svg class="w-4 h-4 text-neutral-500 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </flux:button>

                <flux:menu class="w-[230px]">
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-2 py-2 text-start text-sm">
                            <span class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                >
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>

                    <flux:menu.separator />

                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Ustawienia</flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            Wyloguj
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-screen bg-white dark:bg-zinc-800">
            <!-- Mobile User Menu -->
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <flux:spacer />

                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                        >
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Ustawienia</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                Wyloguj
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

            <main class="flex-1 overflow-auto">
                {{ $slot }}
            </main>
        </div>
        </div>

        <!-- Global JsBarcode library -->
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            (() => {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = stored ?? (prefersDark ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
        @fluxScripts
    </body>
</html>
