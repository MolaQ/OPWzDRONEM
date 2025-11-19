<x-layouts.user :title="__('Dashboard')" class="bg-neutral-900 text-white">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-8 py-8 min-h-screen">
        {{-- Kolumna 1 --}}
        <div class="rounded-xl bg-neutral-800 border border-neutral-700 p-6 flex flex-col justify-center items-center shadow-lg" style="background-image: repeating-linear-gradient(135deg,#444 0 2px,transparent 2px 8px);">
            {{-- Umieść np. wiadomości, statystyki --}}
        </div>
        {{-- Kolumna 2 --}}
        <div class="rounded-xl bg-neutral-800 border border-neutral-700 p-6 flex flex-col justify-center items-center shadow-lg" style="background-image: repeating-linear-gradient(135deg,#444 0 2px,transparent 2px 8px);">
            {{-- Konkursy, najpopularniejsze zadania --}}
        </div>
        {{-- Kolumna 3 --}}
        <div class="rounded-xl bg-neutral-800 border border-neutral-700 p-6 flex flex-col justify-center items-center shadow-lg" style="background-image: repeating-linear-gradient(135deg,#444 0 2px,transparent 2px 8px);">
            {{-- Użytkownicy online, reklama, tagi --}}
        </div>
        {{-- Rząd na dole (rozpięty na całą szerokość) --}}
        <div class="rounded-xl bg-neutral-800 border border-neutral-700 p-6 col-span-1 md:col-span-3 shadow-lg mt-4" style="background-image: repeating-linear-gradient(135deg,#444 0 2px,transparent 2px 8px); min-height:320px">
            {{-- Najnowsze rozwiązania, chmura tagów, statystyki globalne --}}
        </div>
    </div>
</x-layouts.user>
