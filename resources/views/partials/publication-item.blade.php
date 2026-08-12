@php $compact = $compact ?? false; @endphp
<article class="group relative flex flex-col items-start gap-4 rounded-2xl border border-slate-200/80 bg-white transition-all duration-300 hover:-translate-y-1 hover:border-gundar-primary/30 hover:shadow-md {{ $compact ? 'p-4 sm:p-4' : 'p-5 shadow-sm sm:p-6' }}">
    <div class="flex w-full shrink-0 flex-row items-center gap-3 pt-1 text-left sm:w-28 sm:flex-col sm:items-start sm:gap-0">
        <div>
            <p class="text-sm font-black text-gundar-primary">{{ $pub->year ?? 'N/A' }}</p>
            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $pub->type_label ?? 'Jurnal' }}</p>
        </div>

        <div class="ml-auto flex gap-2 sm:ml-0 sm:mt-3 sm:flex-col">
            @if(($pub->views_count ?? 0) >= 50 || $pub->files->sum('downloads_count') >= 20)
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200/50 bg-gradient-to-r from-amber-50 to-orange-50 px-2 py-1.5 text-[10px] font-black uppercase tracking-widest text-amber-600 shadow-sm">
                    <svg class="h-3 w-3 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0013 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                    </svg>
                    Populer
                </span>
            @endif
        </div>
    </div>

    <div class="min-w-0 flex-1">
        <h3 class="pr-10 {{ $compact ? 'text-lg font-bold' : 'text-xl font-black' }} leading-snug text-gundar-dark transition group-hover:text-gundar-primary">
            <a href="{{ route('publications.show', $pub->id) }}" class="before:absolute before:inset-0">
                {{ $pub->title }}
            </a>
        </h3>

        <p class="mt-2 {{ $compact ? 'text-xs font-semibold' : 'text-sm font-semibold' }} text-slate-700">
            {{ $pub->author }} <span class="mx-2 text-slate-300">|</span> <span class="font-normal text-slate-500">{{ $pub->container ? $pub->container->name : 'Universitas Gunadarma' }}</span>
        </p>

        @php
            $abstractPreview = \Illuminate\Support\Str::limit(
                strip_tags($pub->highlighted_abstract ?? $pub->abstract ?? 'Abstrak tidak tersedia untuk dokumen ini.'),
                160
            );
        @endphp

        @unless($compact)
            <p class="mt-3 text-sm leading-6 text-slate-600 line-clamp-2">
                {{ $abstractPreview }}
            </p>

            <div class="relative z-10 mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4">
                <div class="flex flex-wrap items-center gap-4">
                    @if($pub->topics->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($pub->topics->take(3) as $topic)
                                <a href="{{ route('search', ['topic' => $topic->slug]) }}" class="rounded-full border border-gundar-primary/10 bg-gundar-primary/5 px-3 py-1 text-[10px] font-bold text-gundar-primary transition-colors hover:border-gundar-primary hover:bg-gundar-primary hover:text-white">
                                    #{{ $topic->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($pub->research_method)
                        <a href="{{ route('search', ['method' => $pub->research_method]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1.5 text-[10px] font-bold text-slate-500 transition hover:bg-gundar-primary/10 hover:text-gundar-primary" title="Filter berdasarkan metode ini">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0m-3.75 0H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 10-3 0m-3.75 0H7.5" /></svg>
                            {{ $pub->research_method }}
                        </a>
                    @endif

                    @if($pub->files->count() > 0)
                        <div class="flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $pub->files->contains(fn ($file) => $file->isRestricted()) ? 'border-amber-100 bg-amber-50 text-amber-600' : 'border-emerald-100 bg-emerald-50 text-emerald-600' }}">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ $pub->files->contains(fn ($file) => $file->isRestricted()) ? 'Login Diperlukan' : 'Akses Publik' }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
                    <span title="Jumlah dilihat" class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ number_format($pub->views_count ?? 0) }}
                    </span>
                    <span title="Jumlah diunduh" class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        {{ number_format($pub->files->sum('downloads_count') ?? 0) }}
                    </span>
                </div>
            </div>
        @endunless
    </div>

    @unless($compact)
        <div class="absolute right-4 top-4 z-20 sm:right-6 sm:top-6">
            @if(isset($isBookmarkPage) && $isBookmarkPage)
                @auth
                    <form action="{{ route('bookmarks.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="publication_id" value="{{ $pub->id }}">
                    <button type="submit" class="group relative z-30 flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-500 shadow-sm transition-all hover:scale-110 hover:bg-rose-500 hover:text-white active:scale-95" title="Hapus dari koleksi">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </form>
                @endauth
            @else
                @php
                    $isAuthenticated = auth()->check();
                    $isBookmarked = $isAuthenticated ? $pub->bookmarks()->where('user_id', auth()->id())->exists() : false;
                    $bookmarkAction = $isAuthenticated ? route('bookmarks.toggle') : route('login');
                    $bookmarkTitle = $isAuthenticated ? ($isBookmarked ? '★ Tersimpan' : '☆ Simpan') : 'Login untuk menyimpan bookmark';
                @endphp

                @if($isAuthenticated)
                    <form action="{{ $bookmarkAction }}" method="POST">
                        @csrf
                        <input type="hidden" name="publication_id" value="{{ $pub->id }}">
                        <button type="submit" class="group relative z-30 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/90 px-3 py-2 text-[11px] font-bold text-slate-600 shadow-sm transition-all duration-300 hover:scale-105 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600" title="Simpan ke koleksi">
                            <span class="absolute inset-0 rounded-full bg-amber-100 opacity-0 transition-opacity duration-300 group-hover:opacity-50"></span>

                            <svg class="relative z-10 h-4 w-4 transition-colors duration-300 {{ $isBookmarked ? 'fill-amber-400 text-amber-400' : 'fill-none text-slate-400 group-hover:text-amber-400' }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                            </svg>
                            <span class="relative z-10">{{ $isBookmarked ? '★ Tersimpan' : '☆ Simpan' }}</span>
                        </button>
                    </form>
                @else
                    <a href="{{ $bookmarkAction }}" class="group relative z-30 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/90 px-3 py-2 text-[11px] font-bold text-slate-600 shadow-sm transition-all duration-300 hover:scale-105 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600" title="Login untuk menyimpan bookmark">
                        <span class="absolute inset-0 rounded-full bg-amber-100 opacity-0 transition-opacity duration-300 group-hover:opacity-50"></span>
                        <svg class="relative z-10 h-4 w-4 text-slate-400 transition-colors duration-300 group-hover:text-amber-400" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                        <span class="relative z-10">{{ $bookmarkTitle }}</span>
                    </a>
                @endif
            @endif
        </div>
    @endunless
</article>