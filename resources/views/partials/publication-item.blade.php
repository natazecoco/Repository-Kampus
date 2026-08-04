<article class="group relative flex flex-col items-start gap-4 rounded-[32px] bg-white/80 backdrop-blur-xl p-5 sm:p-6 border border-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:-translate-y-1.5 hover:border-gundar-primary/30 hover:shadow-[0_12px_40px_rgba(118,58,151,0.08)] sm:flex-row sm:gap-6">
    
    <!-- Meta Kiri (Tahun & Tipe & Metode Riset & Badge Populer) -->
    <div class="w-full shrink-0 sm:w-28 pt-1 text-left flex flex-row sm:flex-col items-center sm:items-start gap-3 sm:gap-0">
        <div>
            <p class="text-sm font-black text-gundar-primary">{{ $pub->year ?? 'N/A' }}</p>
            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $pub->type_label ?? 'Jurnal' }}</p>
        </div>
        
        <div class="flex sm:flex-col gap-2 mt-0 sm:mt-3 ml-auto sm:ml-0">
            <!-- Badge Metode Riset -->
            @if($pub->research_method)
                <a href="{{ route('home', ['method' => $pub->research_method]) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-50/50 px-2 py-1.5 text-[10px] font-bold text-slate-500 border border-slate-200 hover:bg-gundar-primary hover:text-white hover:border-gundar-primary transition-colors shadow-sm" title="Filter berdasarkan metode ini">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                    {{ $pub->research_method }}
                </a>
            @endif

            <!-- Badge Populer -->
            @if(($pub->views_count ?? 0) >= 50 || $pub->files->sum('downloads_count') >= 20)
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-amber-50 to-orange-50 px-2 py-1.5 text-[10px] font-black uppercase tracking-widest text-amber-600 border border-amber-200/50 shadow-sm">
                    <svg class="w-3 h-3 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0013 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                    Populer
                </span>
            @endif
        </div>
    </div>

    <!-- Konten Tengah -->
    <div class="flex-1 min-w-0">
        <h3 class="text-xl font-black leading-snug text-gundar-dark transition group-hover:text-gundar-primary pr-10">
            <a href="{{ route('publications.show', $pub->id) }}" class="before:absolute before:inset-0">
                {{ $pub->title }}
            </a>
        </h3>
        
        <p class="mt-2 text-sm font-semibold text-slate-700">
            {{ $pub->author }} <span class="text-slate-300 mx-2">|</span> <span class="text-slate-500 font-normal">{{ $pub->container ? $pub->container->name : 'Universitas Gunadarma' }}</span>
        </p>

        <!-- Abstrak -->
        <p class="mt-3 text-sm leading-relaxed text-slate-500 line-clamp-2">
            {!! $pub->highlighted_abstract ?? $pub->abstract ?? 'Abstrak tidak tersedia untuk dokumen ini.' !!}
        </p>

        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 relative z-10 border-t border-slate-100/60 pt-4">
            
            <div class="flex flex-wrap items-center gap-4">
                <!-- Topik Tags -->
                @if($pub->topics->isNotEmpty())
                    <div class="flex gap-2">
                        @foreach($pub->topics->take(2) as $topic)
                            <a href="{{ route('topic.show', $topic->slug) }}" class="rounded-full bg-gundar-primary/5 px-3 py-1 text-[10px] font-bold text-gundar-primary hover:bg-gundar-primary hover:text-white transition-colors border border-gundar-primary/10 hover:border-gundar-primary">
                                #{{ $topic->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
                
                <!-- Akses File -->
                @if($pub->files->count() > 0)
                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF
                    </div>
                @endif
            </div>

            <!-- Statistik Mini -->
            <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
                <span title="Jumlah dilihat" class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    {{ number_format($pub->views_count ?? 0) }}
                </span>
                <span title="Jumlah diunduh" class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ number_format($pub->files->sum('downloads_count') ?? 0) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Aksi Kanan (Bookmark / Hapus) -->
    <div class="absolute right-4 top-4 z-20 sm:top-6 sm:right-6">
        @auth
            @if(isset($isBookmarkPage) && $isBookmarkPage)
                <form action="{{ route('bookmarks.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="publication_id" value="{{ $pub->id }}">
                    <button type="submit" class="group flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 text-rose-500 transition-all hover:bg-rose-500 hover:text-white hover:scale-110 active:scale-95 shadow-sm relative z-30" title="Hapus dari koleksi">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    </button>
                </form>
            @else
                @php
                    $isBookmarked = $pub->bookmarks()->where('user_id', auth()->id())->exists();
                @endphp
                <form action="{{ route('bookmarks.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="publication_id" value="{{ $pub->id }}">
                    <button type="submit" class="group relative z-30 flex items-center justify-center w-10 h-10 rounded-full transition-transform duration-300 hover:scale-110 active:scale-90" title="Simpan ke koleksi">
                        <span class="absolute inset-0 rounded-full bg-amber-100 opacity-0 group-hover:opacity-50 transition-opacity duration-300"></span>
                        
                        <svg class="w-7 h-7 relative z-10 transition-colors duration-300 {{ $isBookmarked ? 'text-amber-400 fill-amber-400 drop-shadow-[0_2px_8px_rgba(251,191,36,0.5)]' : 'text-slate-300 fill-none group-hover:text-amber-400' }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                    </button>
                </form>
            @endif
        @endauth
    </div>
</article>