<x-layouts.user :title="__('Dashboard')">
    <div class="p-6 bg-[#000000] min-h-screen">
        <div class="flex h-full w-full flex-1 flex-col gap-4">
            <!-- Three Column Grid -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Card 1 -->
                <div class="relative aspect-video overflow-hidden rounded-xl border-2 border-[#106c21] bg-gradient-to-br from-[#112b50] to-[#2f76aa] shadow-2xl">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center p-6">
                            <svg class="w-16 h-16 mx-auto mb-4 text-[#106c21]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <h3 class="text-white text-xl font-bold">Aktualności</h3>
                            <p class="text-neutral-300 mt-2">Najnowsze informacje</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="relative aspect-video overflow-hidden rounded-xl border-2 border-[#2f76aa] bg-gradient-to-br from-[#106c21] to-[#112b50] shadow-2xl">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center p-6">
                            <svg class="w-16 h-16 mx-auto mb-4 text-[#2f76aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="text-white text-xl font-bold">Materiały</h3>
                            <p class="text-neutral-300 mt-2">Zasoby edukacyjne</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3 -->
                <div class="relative aspect-video overflow-hidden rounded-xl border-2 border-[#4d3809] bg-gradient-to-br from-[#2f76aa] to-[#106c21] shadow-2xl">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center p-6">
                            <svg class="w-16 h-16 mx-auto mb-4 text-[#4d3809]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-white text-xl font-bold">Ćwiczenia</h3>
                            <p class="text-neutral-300 mt-2">Zadania praktyczne</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Large Bottom Card -->
            <div class="relative h-full flex-1 min-h-[400px] overflow-hidden rounded-xl border-2 border-[#106c21] bg-gradient-to-br from-[#112b50] via-[#2f76aa] to-[#106c21] shadow-2xl">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center p-8">
                        <svg class="w-24 h-24 mx-auto mb-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h2 class="text-white text-3xl font-bold mb-4">Witaj na platformie OPW z Dronem</h2>
                        <p class="text-neutral-200 text-lg max-w-2xl mx-auto">
                            Doskonalenie umiejętności operatorów dronów poprzez interaktywne materiały, 
                            ćwiczenia praktyczne i najnowsze informacje ze świata bezzałogowych statków powietrznych.
                        </p>
                        @guest
                        <div class="mt-8">
                            <a href="{{ route('login') }}" class="inline-block bg-[#106c21] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#2f76aa] transition shadow-lg">
                                Rozpocznij naukę
                            </a>
                        </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.user>
