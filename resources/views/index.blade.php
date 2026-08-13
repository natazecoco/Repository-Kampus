@extends('layouts.app')

@section('content')

    <!-- HERO SECTION ELEGAN & SOLID -->
    <section class="relative z-10 mx-auto max-w-5xl px-4 pt-10 pb-12 text-center sm:px-6 lg:px-8">
        
        <!-- Ambient Glow Lembut -->
        <div class="pointer-events-none absolute left-1/2 top-0 -translate-x-1/2 w-[350px] sm:w-[550px] h-[280px] bg-gundar-primary/10 rounded-full blur-[100px] -z-10"></div>

        <div class="inline-flex items-center gap-2 rounded-full border border-gundar-primary/20 bg-white px-3.5 py-1 text-[11px] font-bold uppercase tracking-widest text-gundar-primary shadow-sm mb-5">
            <span class="h-1.5 w-1.5 rounded-full bg-gundar-accent"></span>
            Universitas Gunadarma
            <span class="h-1.5 w-1.5 rounded-full bg-gundar-accent"></span>
        </div>
        
        <!-- JUDUL SOLID TEGAS (TANPA GRADASI) -->
        <h1 class="text-4xl font-black tracking-tight text-gundar-dark sm:text-6xl lg:text-7xl leading-[1.15]">
            Katalog Pengetahuan <br>
            <span class="text-gundar-dark">& Karya Ilmiah.</span>
        </h1>
        
        <p class="mx-auto mt-5 max-w-2xl text-base sm:text-lg text-slate-500 font-medium leading-relaxed">
            Akses perpustakaan digital terintegrasi. Temukan referensi riset, jurnal, dan skripsi dengan perluasan istilah berbasis kamus topik.
        </p>

        <!-- SEARCH BAR LANGSUNG MENGARAH KE /SEARCH -->
        <form action="{{ route('search') }}" method="GET" class="mx-auto mt-8 max-w-2xl">
            <div class="group flex items-center rounded-2xl border border-slate-200/90 bg-white p-2 shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all duration-300 focus-within:border-gundar-primary focus-within:ring-4 focus-within:ring-gundar-primary/10 hover:shadow-lg">
                <div class="pl-3 pr-2 text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input type="text" name="search" placeholder="Cari judul skripsi, topik AI, metode, penulis..." class="w-full bg-transparent px-2 py-2 text-sm sm:text-base font-semibold text-slate-800 outline-none placeholder:text-slate-400 placeholder:font-normal">
                <button type="submit" class="shrink-0 rounded-xl bg-gundar-dark px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow transition-all hover:bg-gundar-primary hover:scale-[1.02]">
                    Cari Riset
                </button>
            </div>
        </form>

        <!-- TOPIK POPULER -->
        @if(isset($popularTopics) && $popularTopics->isNotEmpty())
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <span class="text-[11px] font-black uppercase tracking-widest text-slate-400 mr-1">Tren Topik:</span>
                @foreach($popularTopics as $topic)
                    <a href="{{ route('search', ['topic' => $topic->slug]) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 shadow-sm transition hover:border-gundar-primary/40 hover:bg-gundar-primary/5 hover:text-gundar-primary hover:-translate-y-0.5">
                        <span>{{ $topic->name }}</span>
                        <span class="rounded-full bg-slate-100 px-1.5 py-0.2 text-[9px] font-bold text-slate-400">{{ $topic->publications_count }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <!-- SECTION CONTENT BERANDA (CLEAN & LEGA) -->
    <main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-16 pb-16">

        <!-- 1. REKOMENDASI UNTUK MAHASISWA (JIKA LOGIN) -->
        @auth
            @if(isset($personalizedRecommendations) && $personalizedRecommendations->isNotEmpty())
                <section>
                    <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-xl font-black text-gundar-dark flex items-center gap-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" /></svg>
                                </span>
                                Rekomendasi Untuk Anda
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Disesuaikan dengan topik minat dan riwayat bookmark Anda.</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($personalizedRecommendations as $item)
                            <a href="{{ route('publications.show', $item) }}" class="group flex flex-col justify-between rounded-2xl border border-slate-200/70 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-gundar-primary/40 hover:shadow-md">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-gundar-primary">{{ $item->type_label }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">{{ $item->year }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold leading-snug text-slate-800 group-hover:text-gundar-primary line-clamp-2 transition-colors">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="mt-2 text-xs text-slate-500 truncate">{{ $item->author }}</p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] font-bold text-slate-400">
                                    <span>{{ $item->research_method ?? 'Kajian Teori' }}</span>
                                    <span class="text-gundar-primary group-hover:translate-x-0.5 transition-transform">Baca &rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        @endauth

        <!-- 2. JELAJAHI RUMPUN & BIDANG ILMU -->
        @if(isset($taxonomyTopics) && $taxonomyTopics->isNotEmpty())
            <section>
                <div class="mb-6">
                    <h2 class="text-xl font-black text-gundar-dark">Direktori Bidang Ilmu</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Pilih cabang keilmuan untuk memfilter publikasi secara terstruktur.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($taxonomyTopics as $parentTopic)
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                                <a href="{{ route('search', ['topic' => $parentTopic->slug]) }}" class="text-sm font-bold text-gundar-dark hover:text-gundar-primary transition-colors flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-gundar-primary"></span>
                                    {{ $parentTopic->name }}
                                </a>
                                <span class="text-[10px] font-bold text-slate-400">{{ $parentTopic->publications_count ?? 0 }} karya</span>
                            </div>
                            
                            @if($parentTopic->children->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($parentTopic->children->take(6) as $childTopic)
                                        <a href="{{ route('search', ['topic' => $childTopic->slug]) }}" class="rounded-lg bg-slate-50 border border-slate-200/60 px-2.5 py-1 text-[11px] font-semibold text-slate-600 hover:bg-gundar-primary/5 hover:border-gundar-primary/30 hover:text-gundar-primary transition-colors">
                                            {{ $childTopic->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 3. PUBLIKASI TERBARU (Murni Terkini) -->
        <section>
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-xl font-black text-gundar-dark">Publikasi Terbaru</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Dokumen ilmiah yang baru saja ditambahkan ke repositori.</p>
                </div>
                <a href="{{ route('search') }}" class="text-xs font-bold uppercase tracking-wider text-gundar-primary hover:text-gundar-accent transition">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid gap-4">
                @foreach($latestPublications as $pub)
                    @include('partials.publication-item', ['pub' => $pub])
                @endforeach
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('search') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-8 py-3 text-xs font-bold text-slate-700 shadow-sm transition hover:border-gundar-primary hover:text-gundar-primary hover:shadow-md">
                    Eksplorasi Semua {{ number_format($totalPublicationsCount ?? 0) }} Dokumen di Repositori
                </a>
            </div>
        </section>

    </main>

@endsection