@extends('layouts.app')

@section('content')

    <!-- Kontainer Utama Detail Jurnal -->
    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali Minimalis -->
        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-bold text-slate-400 transition hover:text-gundar-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Katalog
        </a>

        <!-- HEADER ARTIKEL -->
        <article class="rounded-[24px] border border-slate-200 bg-white p-8 shadow-sm sm:p-12 mb-10 relative overflow-hidden">
            <!-- Aksen Garis Kiri -->
            <div class="absolute left-0 top-0 h-full w-2 bg-gundar-primary"></div>

            <header class="mb-10">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="inline-flex items-center rounded bg-gundar-primary/10 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-gundar-primary">
                        {{ $publication->type }}
                    </span>
                    <span class="inline-flex items-center rounded bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500">
                        {{ $publication->year }}
                    </span>
                    <!-- Badge Metode Riset di Header Detail -->
                    @if($publication->research_method)
                        <a href="{{ route('home', ['method' => $publication->research_method]) }}" class="inline-flex items-center rounded bg-purple-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-gundar-primary border border-purple-100 hover:bg-gundar-primary hover:text-white transition">
                            ⚙️ {{ $publication->research_method }}
                        </a>
                    @endif
                </div>

                <!-- [BARU - FASE 2B] Baris Statistik View & Download -->
                <div class="mb-4 flex flex-wrap items-center gap-3 text-xs font-bold text-slate-500">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1 text-slate-600 border border-slate-200/60">
                        👁️ {{ number_format($publication->views_count ?? 0) }} Kali Dilihat
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1 text-slate-600 border border-slate-200/60">
                        📥 {{ number_format($publication->files->sum('downloads_count') ?? 0) }} Kali Diunduh
                    </span>
                </div>
                
                <h1 class="text-3xl font-black leading-tight tracking-tight text-gundar-dark sm:text-4xl md:text-5xl">
                    {{ $publication->title }}
                </h1>
                
                <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-slate-100 pt-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Penulis Utama</p>
                        <p class="mt-1 text-base font-semibold text-slate-800">{{ $publication->author }}</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- [FASE 2A] Tombol & Modal Sitasi dalam Satu Scope x-data -->
                        <div x-data="{ openCitation: false, copied: '' }" class="inline-block">
                            <button @click="openCitation = true" 
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:border-gundar-primary hover:text-gundar-primary shadow-sm">
                                <span>📑 Kutip Karya Ini</span>
                            </button>

                            <!-- Modal Background -->
                            <div x-show="openCitation" 
                                 style="display: none;"
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                 @keydown.escape.window="openCitation = false">
                                
                                <!-- Modal Box -->
                                <div @click.away="openCitation = false" 
                                     class="w-full max-w-lg p-6 bg-white rounded-2xl shadow-xl border border-gray-100">
                                    
                                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                        <h3 class="text-lg font-bold text-gray-800">Sitasi Karya Ilmiah</h3>
                                        <button @click="openCitation = false" type="button" class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
                                    </div>

                                    <div class="mt-4 space-y-4 text-left">
                                        <!-- APA 7th -->
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-black text-gray-500 uppercase tracking-wider">APA (7th Edition)</span>
                                                <button @click="
                                                            navigator.clipboard.writeText($refs.apaText.innerText);
                                                            copied = 'APA';
                                                            setTimeout(() => copied = '', 2000);
                                                        " 
                                                        type="button"
                                                        class="text-xs font-bold text-gundar-primary hover:underline">
                                                    <span x-text="copied === 'APA' ? '✓ Tersalin!' : 'Salin'"></span>
                                                </button>
                                            </div>
                                            <div x-ref="apaText" class="p-3 mt-1 text-sm bg-slate-50 rounded-lg text-slate-700 border border-slate-200 font-mono leading-relaxed">
                                                {{ $publication->getCitation('APA') }}
                                            </div>
                                        </div>

                                        <!-- IEEE -->
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-black text-gray-500 uppercase tracking-wider">IEEE</span>
                                                <button @click="
                                                            navigator.clipboard.writeText($refs.ieeeText.innerText);
                                                            copied = 'IEEE';
                                                            setTimeout(() => copied = '', 2000);
                                                        " 
                                                        type="button"
                                                        class="text-xs font-bold text-gundar-primary hover:underline">
                                                    <span x-text="copied === 'IEEE' ? '✓ Tersalin!' : 'Salin'"></span>
                                                </button>
                                            </div>
                                            <div x-ref="ieeeText" class="p-3 mt-1 text-sm bg-slate-50 rounded-lg text-slate-700 border border-slate-200 font-mono leading-relaxed">
                                                {{ $publication->getCitation('IEEE') }}
                                            </div>
                                        </div>

                                        <!-- Harvard -->
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-black text-gray-500 uppercase tracking-wider">Harvard</span>
                                                <button @click="
                                                            navigator.clipboard.writeText($refs.harvardText.innerText);
                                                            copied = 'HARVARD';
                                                            setTimeout(() => copied = '', 2000);
                                                        " 
                                                        type="button"
                                                        class="text-xs font-bold text-gundar-primary hover:underline">
                                                    <span x-text="copied === 'HARVARD' ? '✓ Tersalin!' : 'Salin'"></span>
                                                </button>
                                            </div>
                                            <div x-ref="harvardText" class="p-3 mt-1 text-sm bg-slate-50 rounded-lg text-slate-700 border border-slate-200 font-mono leading-relaxed">
                                                {{ $publication->getCitation('HARVARD') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 text-right">
                                        <button @click="openCitation = false" 
                                                type="button"
                                                class="px-5 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Bookmark -->
                        @auth
                            <form action="{{ route('bookmarks.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:border-gundar-primary hover:text-gundar-primary">
                                    <span class="text-lg">{{ $publication->bookmarks()->where('user_id', auth()->id())->exists() ? '★' : '☆' }}</span>
                                    {{ $publication->bookmarks()->where('user_id', auth()->id())->exists() ? 'Tersimpan' : 'Simpan' }}
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- KOTAK META DATA -->
            <div class="mb-10 grid gap-4 rounded-xl bg-slate-50 p-6 md:grid-cols-3 border border-slate-100">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Diterbitkan di</p>
                    <p class="mt-1.5 text-sm font-semibold text-slate-800">{{ $publication->container ? $publication->container->name : 'Universitas Gunadarma' }}</p>
                </div>
                @if($publication->research_method)
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Metode Riset</p>
                        <a href="{{ route('home', ['method' => $publication->research_method]) }}" class="mt-1.5 inline-flex items-center gap-1 text-sm font-bold text-gundar-primary hover:underline">
                            ⚙️ {{ $publication->research_method }}
                        </a>
                    </div>
                @elseif($publication->container && $publication->container->identifier)
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Identifier (ISSN/ISBN)</p>
                        <p class="mt-1.5 text-sm font-mono font-semibold text-slate-800">{{ $publication->container->identifier }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status Akses Dokumen</p>
                    <p class="mt-1.5 text-sm font-semibold text-emerald-600">
                        {{ $publication->files->contains(fn ($file) => $file->isRestricted()) ? 'Akses Terbatas (Login Mahasiswa)' : 'Publikasi Terbuka' }}
                    </p>
                </div>
            </div>

            <!-- ABSTRAK -->
            <div class="mb-10">
                <h2 class="mb-4 text-sm font-black uppercase tracking-[0.2em] text-slate-900 border-b border-slate-100 pb-2">Abstrak Penulisan</h2>
                <p class="leading-relaxed text-slate-600 whitespace-pre-line text-lg text-justify">
                    {{ $publication->abstract }}
                </p>
            </div>

            <!-- KATA KUNCI & TOPIK -->
            <div class="mb-12 flex flex-col gap-6 sm:flex-row border-t border-slate-100 pt-8">
                @if($publication->keywords)
                <div class="flex-1">
                    <h3 class="mb-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Kata Kunci</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $publication->keywords) as $keyword)
                            <span class="rounded bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                {{ trim($keyword) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($publication->topics->isNotEmpty())
                <div class="flex-1">
                    <h3 class="mb-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Topik Kajian</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($publication->topics as $topic)
                            <div class="inline-flex items-center gap-1 rounded bg-gundar-primary/10 pl-2.5 pr-1 py-1">
                                <a href="{{ route('home', ['search' => $topic->name]) }}" class="text-xs font-bold text-gundar-primary hover:underline">
                                    {{ $topic->name }}
                                </a>
                                @auth
                                    <form action="{{ route('topics.preference', $topic) }}" method="POST" class="ml-1">
                                        @csrf
                                        <button type="submit" class="rounded bg-white px-1.5 py-0.5 text-[10px] font-bold text-slate-500 hover:text-gundar-primary transition shadow-sm" title="Jadikan Topik Favorit">
                                            {{ $topic->users()->where('user_id', auth()->id())->exists() ? '★' : '+' }}
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- DAFTAR DOKUMEN (FILE PDF) -->
            <div>
                <h2 class="mb-4 text-sm font-black uppercase tracking-[0.2em] text-slate-900 border-b border-slate-100 pb-2">Dokumen Tersedia</h2>
                
                @if($publication->files->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500 font-medium">
                        Belum ada lampiran file yang diunggah untuk karya ilmiah ini.
                    </div>
                @else
                    <div class="flex flex-col gap-3">
                        @foreach($publication->files as $file)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-gundar-primary hover:shadow-sm">
                                
                                <div class="flex items-center gap-4 min-w-0">
                                    @if($file->isPublic())
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                    @else
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 truncate">{{ $file->title }}</h4>
                                        <p class="mt-0.5 text-[10px] font-black uppercase tracking-wider {{ $file->isPublic() ? 'text-emerald-600' : 'text-amber-600' }}">
                                            {{ $file->visibility_label }} &bull; {{ $file->allow_download ? 'Bisa Diunduh' : 'Hanya Baca (Viewer)' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if(empty($file->file_path))
                                        <!-- Tampilan jika file_path kebetulan NULL di database -->
                                        <span class="rounded bg-rose-50 px-3 py-2 text-[11px] font-bold text-rose-600 border border-rose-200">
                                            ⚠️ File Belum Diunggah
                                        </span>
                                    @elseif($file->canBeViewedBy(auth()->user()))
                                        <a href="{{ route('publications.viewer', ['publication' => $publication, 'file' => $file]) }}" target="_blank" class="rounded bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-gundar-primary">
                                            Buka di Viewer
                                        </a>
                                        @if($file->canBeDownloadedBy(auth()->user()))
                                            <!-- [MODIFIKASI] Gunakan route publications.files.download untuk menambah download counter -->
                                            <a href="{{ route('publications.files.download', ['publication' => $publication, 'file' => $file]) }}" class="rounded border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                                Unduh PDF
                                            </a>
                                        @endif
                                    @elseif($file->visibility === 'authenticated')
                                        <a href="{{ route('student.login') }}" class="rounded bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-500 transition hover:bg-slate-200">
                                            Wajib Login
                                        </a>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <!-- KOTAK REKOMENDASI ILMU TERKAIT -->
        <div class="space-y-8">
            
            {{-- 1. DOKUMEN PALING MIRIP --}}
            @if(isset($similarRecommendations) && $similarRecommendations->isNotEmpty())
                <div>
                    <h2 class="mb-4 flex items-center gap-2 text-sm font-black uppercase tracking-[0.2em] text-slate-900">
                        <span class="text-xl">🔍</span> Mirip dengan dokumen ini
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($similarRecommendations as $item)
                            @if($pub = $item->recommendedPublication ?? null)
                                <a href="{{ route('publications.show', $pub) }}" class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:border-gundar-primary hover:shadow-sm">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gundar-primary">{{ $pub->type }}</p>
                                    <h3 class="mt-2 text-sm font-bold leading-snug text-slate-800 group-hover:text-gundar-primary line-clamp-2">{{ $pub->title }}</h3>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 2. BACAAN PELENGKAP --}}
            @if(isset($complementaryRecommendations) && $complementaryRecommendations->isNotEmpty())
                <div>
                    <h2 class="mb-4 flex items-center gap-2 text-sm font-black uppercase tracking-[0.2em] text-slate-900">
                        <span class="text-xl">📚</span> Referensi Pelengkap
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($complementaryRecommendations as $item)
                            @if($pub = $item->recommendedPublication ?? null)
                                <a href="{{ route('publications.show', $pub) }}" class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:border-gundar-primary hover:shadow-sm">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gundar-primary">{{ $pub->type }}</p>
                                    <h3 class="mt-2 text-sm font-bold leading-snug text-slate-800 group-hover:text-gundar-primary line-clamp-2">{{ $pub->title }}</h3>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. KONSEP DASAR --}}
            @if(isset($basicConcepts) && $basicConcepts->isNotEmpty())
                <div>
                    <h2 class="mb-4 flex items-center gap-2 text-sm font-black uppercase tracking-[0.2em] text-slate-900">
                        <span class="text-xl">🌱</span> Konsep Dasar Terkait
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($basicConcepts as $pub)
                            <a href="{{ route('publications.show', $pub) }}" class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:border-gundar-primary hover:shadow-sm">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gundar-primary">{{ $pub->type }}</p>
                                <h3 class="mt-2 text-sm font-bold leading-snug text-slate-800 group-hover:text-gundar-primary line-clamp-2">{{ $pub->title }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 4. METODE / TIPE SERUPA --}}
            @if(isset($similarMethods) && $similarMethods->isNotEmpty())
                <div>
                    <h2 class="mb-4 flex items-center gap-2 text-sm font-black uppercase tracking-[0.2em] text-slate-900">
                        <span class="text-xl">🔬</span> Metode & Tipe Dokumen Serupa
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($similarMethods as $pub)
                            <a href="{{ route('publications.show', $pub) }}" class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:border-gundar-primary hover:shadow-sm">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gundar-primary">{{ $pub->type }}</p>
                                <h3 class="mt-2 text-sm font-bold leading-snug text-slate-800 group-hover:text-gundar-primary line-clamp-2">{{ $pub->title }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 5. BACAAN LANJUTAN --}}
            @if(isset($advancedReadings) && $advancedReadings->isNotEmpty())
                <div>
                    <h2 class="mb-4 flex items-center gap-2 text-sm font-black uppercase tracking-[0.2em] text-slate-900">
                        <span class="text-xl">🚀</span> Bacaan Lanjutan Spesifik
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($advancedReadings as $pub)
                            <a href="{{ route('publications.show', $pub) }}" class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:border-gundar-primary hover:shadow-sm">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gundar-primary">{{ $pub->type }}</p>
                                <h3 class="mt-2 text-sm font-bold leading-snug text-slate-800 group-hover:text-gundar-primary line-clamp-2">{{ $pub->title }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </main>
@endsection