<div class="max-w-4xl mx-auto p-6">
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center text-[#106c21] hover:text-[#2f76aa] transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Powrót do strony głównej
        </a>
    </div>

    <article class="bg-gradient-to-br from-[#112b50] to-[#2f76aa] rounded-xl border-2 border-[#106c21] shadow-2xl overflow-hidden">
        @if($post->image)
            <div class="w-full aspect-video overflow-hidden">
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-8">
            <h1 class="text-4xl font-bold text-white mb-4">{{ $post->title }}</h1>

            <div class="flex items-center gap-4 text-sm text-neutral-300 mb-8 pb-6 border-b border-neutral-600">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ $post->author->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $post->published_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>

            <div class="prose prose-invert prose-lg max-w-none">
                <div class="text-neutral-100 leading-relaxed" style="line-height: 1.8; font-size: 1.1rem;">
                    {!! $post->content !!}
                </div>
            </div>
        </div>
    </article>

    <div class="mt-8 text-center">
        <a href="{{ route('home') }}" class="inline-block bg-[#106c21] text-white px-8 py-3 rounded-lg font-semibold hover:bg-[#2f76aa] transition shadow-lg">
            Zobacz więcej postów
        </a>
    </div>
</div>


