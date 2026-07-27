<article class="group relative flex flex-col items-start gap-2 border-b border-slate-100 py-8 transition hover:bg-slate-50/50 sm:flex-row sm:gap-6 -mx-4 px-4 rounded-2xl">
    
    <!-- Meta Kiri (Tahun & Tipe) -->
    <div class="w-full shrink-0 sm:w-24 pt-1 text-left">
        <p class="text-sm font-bold text-gundar-primary">{{ $pub->year ?? 'N/A' }}</p>
        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $pub->type ?? 'JURNAL' }}</p>
    </div>

    <!-- Konten Tengah -->
    <div class="flex-1 min-w-0">
        <h3 class="text-xl font-black leading-snug text-gundar-dark transition group-hover:text-gundar-primary">
            <a href="{{ route('publications.show', $pub->id) }}" class="before:absolute before:inset-0">
                {{ $pub->title }}
            </a>
        </h3>
        
        <p class="mt-1.5 text-sm font-medium text-slate-600">
            {{ $pub->author }} <span class="text-slate-300 mx-1">|</span> <span class="text-slate-500 font-normal">{{ $pub->container ? $pub->container->name : 'Universitas Gunadarma' }}</span>
        </p>

        <p class="mt-4 text-sm leading-relaxed text-slate-500 line-clamp-2">
            {!! $pub->highlighted_abstract ?? 'Abstrak tidak tersedia untuk dokumen ini.' !!}
        </p>

        <div class="mt-5 flex flex-wrap items-center gap-4 relative z-10">
            <!-- Topik Tags -->
            @if($pub->topics->isNotEmpty())
                <div class="flex gap-2">
                    @foreach($pub->topics->take(2) as $topic)
                        <a href="{{ route('topic.show', $topic->slug) }}" class="rounded bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600 hover:bg-gundar-primary hover:text-white transition shadow-sm">
                            {{ $topic->name }}
                        </a>
                    @endforeach
                </div>
            @endif
            
            <!-- Akses File -->
            @if($pub->files->count() > 0)
                <div class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-emerald-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </div>
            @endif
        </div>
    </div>

    <!-- Aksi Kanan (Bookmark) -->
    <div class="absolute right-4 top-8 z-10 sm:static sm:pt-1">
        @auth
            <form action="{{ route('bookmarks.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="publication_id" value="{{ $pub->id }}">
                <button type="submit" class="text-2xl text-slate-300 hover:text-gundar-accent transition" title="Simpan ke koleksi">
                    {{ $pub->bookmarks()->where('user_id', auth()->id())->exists() ? '★' : '☆' }}
                </button>
            </form>
        @endauth
    </div>
</article>