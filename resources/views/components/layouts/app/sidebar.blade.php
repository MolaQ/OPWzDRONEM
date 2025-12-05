<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('admin.dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="Panel główny" class="grid">
                    <flux:navlist.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>Pulpit</flux:navlist.item>
                </flux:navlist.group>

                {{-- Sekcja Treści --}}
                <flux:navlist.group heading="Treści" class="grid">
                    <flux:navlist.item icon="academic-cap" :href="route('admin.courses')" :current="request()->routeIs('admin.courses')" wire:navigate>Program kursu</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.course-materials')" :current="request()->routeIs('admin.course-materials')" wire:navigate>Materiały do zajęć</flux:navlist.item>
                    <flux:navlist.item icon="newspaper" :href="route('admin.posts')" :current="request()->routeIs('admin.posts')" wire:navigate>Posty</flux:navlist.item>
                    <flux:navlist.item icon="chat-bubble-left-right" :href="route('admin.comments')" :current="request()->routeIs('admin.comments')" wire:navigate>Komentarze</flux:navlist.item>
                </flux:navlist.group>

                {{-- Sekcja Zarządzania --}}
                @canany(['users.view', 'groups.view'])
                <flux:navlist.group heading="Zarządzanie" class="grid">
                    <flux:navlist.item icon="users" :href="route('admin.members')" :current="request()->routeIs('admin.members')" wire:navigate>Użytkownicy</flux:navlist.item>
                    <flux:navlist.item icon="user-group" :href="route('admin.groups')" :current="request()->routeIs('admin.groups')" wire:navigate>Grupy</flux:navlist.item>
                </flux:navlist.group>
                @endcanany

                {{-- Sekcja Wyposażenie Pracowni --}}
                @canany(['equipment.view', 'equipment-sets.view', 'rentals.view'])
                <flux:navlist.group heading="Wyposażenie Pracowni" class="grid">
                    <flux:navlist.item icon="shopping-cart" :href="route('admin.rentals')" :current="request()->routeIs('admin.rentals')" wire:navigate>Wypożyczenia</flux:navlist.item>
                    <flux:navlist.item icon="arrow-uturn-left" :href="route('admin.returns')" :current="request()->routeIs('admin.returns')" wire:navigate>Zwroty</flux:navlist.item>
                    <flux:navlist.item icon="cube" :href="route('admin.equipment')" :current="request()->routeIs('admin.equipment')" wire:navigate>Wyposażenie</flux:navlist.item>
                    <flux:navlist.item icon="cube" :href="route('admin.equipment-sets')" :current="request()->routeIs('admin.equipment-sets')" wire:navigate>Zestawy</flux:navlist.item>
                </flux:navlist.group>
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
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
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
        </flux:sidebar>

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

        {{ $slot }}

        <!-- Global JsBarcode library -->
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        @fluxScripts
    </body>
</html>
