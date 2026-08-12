@extends('layouts.app')

@section('content')

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    <!-- Kontainer Utama Detail Jurnal -->
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali Minimalis dengan Hover Effect -->
        <a href="{{ route('home') }}" class="group mb-8 inline-flex items-center gap-2 text-sm font-bold text-slate-400 transition-colors hover:text-gundar-primary">
            <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Katalog
        </a>

        <!-- HEADER ARTIKEL -->
        <article class="rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:p-12 mb-12 relative overflow-hidden transition-all hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)]">
            <!-- Aksen Glow Kiri -->
            <div class="absolute left-0 top-0 h-full w-1.5 bg-gradient-to-b from-gundar-primary to-[#8743ad]"></div>
            <!-- Latar Cahaya Bias di Pojok -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-gundar-primary/5 blur-3xl pointer-events-none"></div>

            <header class="mb-10 relative z-10">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <span class="inline-flex items-center rounded-md bg-gundar-primary/10 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-gundar-primary">
                        {{ $publication->type_label }}
                    </span>
                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500">
                        {{ $publication->year }}
                    </span>
                </div>

                <!-- Baris Statistik View & Download (Dengan SVG Heroicons) -->
                <div class="mb-5 flex flex-wrap items-center gap-4 text-xs font-bold text-slate-500">
                    <span class="inline-flex items-center gap-2 rounded-xl bg-white px-3.5 py-1.5 text-slate-600 border border-slate-100 shadow-sm">
                        <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ number_format($publication->views_count ?? 0) }} Kali Dilihat
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-white px-3.5 py-1.5 text-slate-600 border border-slate-100 shadow-sm">
                        <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        {{ number_format($publication->files->sum('downloads_count') ?? 0) }} Kali Diunduh
                    </span>
                </div>
                
                <h1 class="text-3xl font-black leading-tight tracking-tight text-gundar-dark sm:text-4xl md:text-5xl lg:leading-[1.1]">
                    {{ $publication->title }}
                </h1>
                
                <div class="mt-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-t border-slate-100 pt-8">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Penulis Utama</p>
                        <p class="mt-1 text-lg font-bold text-slate-800">{{ $publication->author }}</p>
                        <p class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span class="h-2 w-2 rounded-full bg-gundar-primary"></span>
                            {{ $publication->container ? $publication->container->name : 'Universitas Gunadarma' }}
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
                            <svg class="h-4 w-4 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                            <span>{{ $publication->files->count() }} dokumen tersedia</span>
                        </div>
                        
                        <!-- TOMBOL KUTIP KARYA (Hanya tombolnya saja di sini) -->
                        <div class="inline-block" x-data>
                            <button @click="$dispatch('open-citation-modal')" 
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:border-gundar-primary hover:text-gundar-primary shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                <span>Kutip Karya</span>
                            </button>
                        </div>

                        <!-- Tombol Bookmark -->
                        @auth
                            @php
                                $isBookmarked = $publication->bookmarks()->where('user_id', auth()->id())->exists();
                            @endphp
                            <form action="{{ route('bookmarks.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                                <button type="submit" class="group inline-flex items-center gap-2 rounded-full border {{ $isBookmarked ? 'border-amber-200 bg-amber-50 text-amber-600' : 'border-slate-200 bg-white text-slate-600' }} px-5 py-2.5 text-sm font-bold transition hover:border-amber-300 hover:bg-amber-100 hover:text-amber-700 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 {{ $isBookmarked ? 'fill-amber-500' : 'fill-none group-hover:fill-amber-500/20' }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                    </svg>
                                    {{ $isBookmarked ? 'Tersimpan' : 'Simpan' }}
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- KOTAK META DATA -->
            <div class="mb-10 grid gap-4 rounded-2xl bg-slate-50/50 p-6 md:grid-cols-3 border border-slate-100 relative z-10">
                <div>
                    <p class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>
                        Diterbitkan di
                    </p>
                    <p class="mt-2 text-sm font-bold text-slate-800">{{ $publication->container ? $publication->container->name : 'Universitas Gunadarma' }}</p>
                </div>
                
                @if($publication->research_method)
                    <div>
                        <p class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                            Metode Riset
                        </p>
                        <a href="{{ route('search', ['method' => $publication->research_method]) }}" class="mt-2 inline-flex items-center gap-1 text-sm font-bold text-gundar-primary hover:text-orange-500 transition-colors">
                            {{ $publication->research_method }}
                        </a>
                    </div>
                @elseif($publication->container && $publication->container->identifier)
                    <div>
                        <p class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-5.1-19.5l-3.9 19.5" /></svg>
                            Identifier (ISSN/ISBN)
                        </p>
                        <p class="mt-2 text-sm font-mono font-bold text-slate-800">{{ $publication->container->identifier }}</p>
                    </div>
                @endif

                <div>
                    <p class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        Status Akses Dokumen
                    </p>
                    <p class="mt-2 text-sm font-bold {{ $publication->files->contains(fn ($file) => $file->isRestricted()) ? 'text-amber-600' : 'text-emerald-600' }}">
                        {{ $publication->files->contains(fn ($file) => $file->isRestricted()) ? 'Akses Terbatas (Login Mahasiswa)' : 'Publikasi Terbuka' }}
                    </p>
                </div>
            </div>

            <!-- ABSTRAK -->
            <div class="mb-10 relative z-10">
                <h2 class="mb-4 text-xs font-black uppercase tracking-[0.2em] text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                    Abstrak Penulisan
                </h2>
                <p class="leading-[1.8] text-slate-600 whitespace-pre-line text-[15px] sm:text-base text-justify font-medium">
                    {{ $publication->abstract }}
                </p>
            </div>

            <!-- KATA KUNCI & TOPIK -->
            <div class="mb-10 flex flex-col gap-8 sm:flex-row border-t border-slate-100 pt-8 relative z-10">
                @if($publication->keywords)
                <div class="flex-1">
                    <h3 class="mb-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Kata Kunci</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $publication->keywords) as $keyword)
                            @php $keywordValue = trim($keyword); @endphp
                            @if($keywordValue !== '')
                                <a href="{{ route('search', ['search' => $keywordValue]) }}" class="rounded-lg border border-slate-200/60 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm transition hover:border-gundar-primary/30 hover:bg-gundar-primary/5 hover:text-gundar-primary">
                                    {{ $keywordValue }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($publication->topics->isNotEmpty())
                <div class="flex-1">
                    <h3 class="mb-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Topik Kajian</h3>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($publication->topics as $topic)
                            <div class="inline-flex items-center gap-1.5 rounded-lg bg-gundar-primary/5 pl-3 pr-1.5 py-1 border border-gundar-primary/10">
                                <a href="{{ route('search', ['topic' => $topic->slug]) }}" class="text-xs font-bold text-gundar-primary hover:text-orange-500 transition-colors">
                                    {{ $topic->name }}
                                </a>
                                @auth
                                    @php $isPref = $topic->users()->where('user_id', auth()->id())->exists(); @endphp
                                    <form action="{{ route('topics.preference', $topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center justify-center w-6 h-6 rounded-md bg-white text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-all shadow-sm" title="Jadikan Topik Favorit">
                                            <svg class="w-3.5 h-3.5 {{ $isPref ? 'text-amber-500 fill-amber-500' : 'fill-none' }}" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
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
            <div class="relative z-10 border-t border-slate-100 pt-8">
                <h2 class="mb-6 text-xs font-black uppercase tracking-[0.2em] text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    Dokumen Tersedia
                </h2>
                
                @if($publication->files->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 text-center flex flex-col items-center">
                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <p class="text-sm text-slate-500 font-bold">Belum ada lampiran file yang diunggah untuk karya ilmiah ini.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($publication->files as $file)
                            <div class="group flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 transition-all duration-300 hover:border-gundar-primary/40 hover:shadow-[0_8px_30px_rgb(118,58,151,0.08)]">
                                
                                <div class="flex items-center gap-4 min-w-0">
                                    @if($file->isPublic())
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-transform group-hover:scale-105">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                        </div>
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition-transform group-hover:scale-105">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 truncate">{{ $file->title }}</h4>
                                        <p class="mt-1 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider {{ $file->isPublic() ? 'text-emerald-600' : 'text-amber-600' }}">
                                            <span>{{ $file->visibility_label }}</span>
                                            <span class="w-1 h-1 rounded-full bg-current opacity-50"></span>
                                            <span>{{ $file->allow_download ? 'Bisa Diunduh' : 'Hanya Baca (Viewer)' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0 w-full sm:w-auto mt-2 sm:mt-0">
                                    @if(empty($file->file_path))
                                        <!-- Tampilan jika file_path NULL -->
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 border border-rose-200">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                            File Belum Diunggah
                                        </span>
                                    @elseif($file->canBeViewedBy(auth()->user()))
                                        <a href="{{ route('publications.viewer', ['publication' => $publication, 'file' => $file]) }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition-all hover:bg-gundar-primary shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            Buka di Viewer
                                        </a>
                                        @if($file->canBeDownloadedBy(auth()->user()))
                                            <a href="{{ route('publications.files.download', ['publication' => $publication, 'file' => $file]) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-300 shadow-sm hover:-translate-y-0.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                Unduh PDF
                                            </a>
                                        @endif
                                    @elseif($file->visibility === 'authenticated')
                                        <a href="{{ route('student.login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-5 py-2.5 text-xs font-bold text-slate-500 transition-colors hover:bg-slate-200">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
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
        <div class="space-y-12">
            
            {{-- 1. KARYA ILMIAH TERKAIT (TF-IDF) --}}
            @if(isset($similarRecommendations) && $similarRecommendations->isNotEmpty())
                <div>
                    <h2 class="mb-5 flex items-center gap-3 text-xs font-black uppercase tracking-[0.2em] text-slate-900">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        Rekomendasi Serupa
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($similarRecommendations->take(3) as $item)
                            @if($pub = $item->recommendedPublication ?? null)
                                @include('partials.publication-item', ['pub' => $pub, 'compact' => true])
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 2. BUKU & LITERATUR RUJUKAN (Khusus Buku) --}}
            @if(isset($bookReferences) && $bookReferences->isNotEmpty())
                <div>
                    <h2 class="mb-5 flex items-center gap-3 text-xs font-black uppercase tracking-[0.2em] text-slate-900">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.82 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.496 1.509 1.333 1.509 2.316V18" /></svg>
                        </div>
                        Buku & Literatur Rujukan
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($bookReferences->take(3) as $pub)
                            @include('partials.publication-item', ['pub' => $pub, 'compact' => true])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. EKSPLORASI TOPIK SPESIFIK --}}
            @if(isset($advancedReadings) && $advancedReadings->isNotEmpty())
                <div>
                    <h2 class="mb-5 flex items-center gap-3 text-xs font-black uppercase tracking-[0.2em] text-slate-900">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-rose-100 text-rose-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.433 4.433 0 002.771 2.771 4.902 4.902 0 003.123.06 4.5 4.5 0 002.592-2.592c.451-1.034.031-2.25-1.024-2.856M8.25 15.75l-3-3m0 0l3-3m-3 3h15" /></svg>
                        </div>
                        Eksplorasi Topik Spesifik
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($advancedReadings->take(3) as $pub)
                            @include('partials.publication-item', ['pub' => $pub, 'compact' => true])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 4. REFERENSI METODE SERUPA --}}
            @if(isset($similarMethods) && $similarMethods->isNotEmpty())
                <div>
                    <h2 class="mb-5 flex items-center gap-3 text-xs font-black uppercase tracking-[0.2em] text-slate-900">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" /></svg>
                        </div>
                        Referensi Metode Serupa
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($similarMethods->take(3) as $pub)
                            @include('partials.publication-item', ['pub' => $pub, 'compact' => true])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 5. REKOMENDASI TAMBAHAN --}}
            @if(isset($complementaryRecommendations) && $complementaryRecommendations->isNotEmpty())
                <div>
                    <h2 class="mb-5 flex items-center gap-3 text-xs font-black uppercase tracking-[0.2em] text-slate-900">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        </div>
                        Rekomendasi Tambahan
                    </h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($complementaryRecommendations->take(3)git as $item)
                            @if($pub = $item->recommendedPublication ?? null)
                                @include('partials.publication-item', ['pub' => $pub, 'compact' => true])
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- BLOK MODAL SITASI (Dipindah ke luar article, sebelum tutup main) -->
        <div x-data="{ openCitation: false, copied: '' }"
             @open-citation-modal.window="openCitation = true">
            
            <!-- Modal Background dengan efek Kaca -->
            <div x-cloak x-show="openCitation" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 backdrop-blur-none"
                 x-transition:enter-end="opacity-100 backdrop-blur-sm"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 backdrop-blur-sm"
                 x-transition:leave-end="opacity-0 backdrop-blur-none"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                 @keydown.escape.window="openCitation = false">
                
                <!-- Modal Box -->
                <div @click.away="openCitation = false" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="w-full max-w-lg p-6 sm:p-8 bg-white rounded-3xl shadow-2xl border border-slate-100 relative">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <h3 class="text-xl font-black text-slate-800">Sitasi Karya Ilmiah</h3>
                        <button @click="openCitation = false" type="button" class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-rose-100 hover:text-rose-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="mt-6 space-y-6 text-left">
                        <!-- APA 7th -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">APA (7th Edition)</span>
                                <button @click="
                                            navigator.clipboard.writeText($refs.apaText.innerText);
                                            copied = 'APA';
                                            setTimeout(() => copied = '', 2000);
                                        " 
                                        type="button"
                                        class="flex items-center gap-1.5 text-xs font-bold text-gundar-primary hover:text-[#8743ad] transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                    <span x-text="copied === 'APA' ? 'Tersalin!' : 'Salin'"></span>
                                </button>
                            </div>
                            <div x-ref="apaText" class="p-4 bg-slate-50 rounded-xl text-slate-700 border border-slate-200 font-mono text-sm leading-relaxed selection:bg-purple-200 break-words whitespace-normal break-all max-h-32 overflow-y-auto custom-scrollbar">
                                {{ $publication->getCitation('APA') }}
                            </div>
                        </div>

                        <!-- IEEE -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">IEEE</span>
                                <button @click="
                                            navigator.clipboard.writeText($refs.ieeeText.innerText);
                                            copied = 'IEEE';
                                            setTimeout(() => copied = '', 2000);
                                        " 
                                        type="button"
                                        class="flex items-center gap-1.5 text-xs font-bold text-gundar-primary hover:text-[#8743ad] transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>
                                    <span x-text="copied === 'IEEE' ? 'Tersalin!' : 'Salin'"></span>
                                </button>
                            </div>
                            <div x-ref="ieeeText" class="p-4 bg-slate-50 rounded-xl text-slate-700 border border-slate-200 font-mono text-sm leading-relaxed selection:bg-purple-200 break-words whitespace-normal break-all max-h-32 overflow-y-auto custom-scrollbar">
                                {{ $publication->getCitation('IEEE') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Akhir Blok Modal -->

    </main>
@endsection