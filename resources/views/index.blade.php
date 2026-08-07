@extends('layouts.app')

@section('content')
        
    <!-- TYPOGRAPHY HERO SECTION (DI TENGAH) -->
    <section class="mx-auto max-w-6xl px-4 pt-12 pb-16 sm:px-6 lg:px-8 text-center relative z-10">
        
        {{-- FIX: Ubah lebar cahaya dari 250% menjadi w-[300px] agar tidak bocor/melar di HP --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[300px] sm:w-[600px] h-[300px] bg-gundar-primary/10 rounded-full blur-[100px] -z-10"></div>

        <div class="max-w-4xl mx-auto">
            <div class="mb-4 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gundar-primary justify-center">
                <span class="h-1.5 w-1.5 rounded-full bg-gundar-accent"></span>
                Universitas Gunadarma
                <span class="h-1.5 w-1.5 rounded-full bg-gundar-accent"></span>
            </div>
            <h1 class="text-4xl font-black leading-[1.1] tracking-tight text-gundar-dark sm:text-6xl lg:text-7xl">
                Katalog <span class="text-transparent bg-clip-text bg-gradient-to-r from-gundar-primary to-[#a855f7]">Pengetahuan</span> <br> & Karya Ilmiah.
            </h1>
            <p class="mt-6 mx-auto max-w-2xl text-lg leading-relaxed text-slate-500">
                Akses perpustakaan digital terintegrasi. Temukan referensi riset, jurnal, dan skripsi dengan pencarian cerdas berbasis semantik.
            </p>

            <!-- MINIMALIST SEARCH DENGAN GLASSMORPHISM -->
            <form action="/" method="GET" class="mt-10 mx-auto max-w-3xl relative">
                @if(request('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                @if(request('method')) <input type="hidden" name="method" value="{{ request('method') }}"> @endif

                <div class="flex items-center rounded-full border border-white/60 bg-white/70 backdrop-blur-lg p-1 sm:p-1.5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-500 focus-within:bg-white focus-within:border-gundar-primary/40 focus-within:shadow-[0_10px_40px_rgba(118,58,151,0.12)] hover:bg-white/90">
                    <div class="pl-4 sm:pl-5 pr-1 sm:pr-2 text-slate-400 hidden sm:block">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, abstrak..." class="w-full bg-transparent px-4 sm:px-0 py-3 sm:py-3.5 text-sm sm:text-lg text-gundar-dark placeholder:text-slate-400 focus:outline-none">
                    <button type="submit" class="shrink-0 rounded-full bg-gradient-to-r from-gundar-primary to-[#8743ad] px-6 sm:px-8 py-2.5 sm:py-3.5 text-sm sm:text-base font-bold text-white shadow-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">Cari</button>
                </div>
            </form>
        </div>
    </section>

    <!-- LAYOUT: SIDEBAR & CONTENT DENGAN flex-col-reverse UNTUK MOBILE -->
    <main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col-reverse lg:grid lg:grid-cols-12 lg:gap-16">
            
            <!-- PANGGIL SIDEBAR -->
            <aside class="mt-12 lg:mt-0 lg:col-span-4">
                @include('partials.sidebar')
            </aside>

            <!-- KOLOM KANAN (Daftar Dokumen) -->
            <div class="lg:col-span-8">
                
                <!-- Rekomendasi Personal -->
                @auth
                    @if($personalizedRecommendations->isNotEmpty())
                        <div class="mb-8 rounded-[28px] border border-gundar-primary/10 bg-gradient-to-br from-gundar-primary/8 to-white p-6 shadow-[0_8px_30px_rgb(118,58,151,0.06)]">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gundar-primary">Rekomendasi untukmu</h3>
                                    <p class="mt-1 text-sm text-slate-500">Berdasarkan topik favorit, dokumen yang kamu simpan, dan popularitas repository.</p>
                                </div>
                                <span class="rounded-full bg-white/80 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Personal</span>
                            </div>

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                @foreach($personalizedRecommendations as $item)
                                    @php $reasonList = $item->recommendation_reasons ?? []; @endphp
                                    <a href="{{ route('publications.show', $item) }}" class="group block rounded-[24px] border border-slate-200 bg-white/90 p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-gundar-primary/30 hover:shadow-[0_10px_32px_rgba(118,58,151,0.08)]">
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gundar-primary">Direkomendasikan</p>
                                        <h4 class="mt-2 text-sm font-bold leading-tight text-gundar-dark transition group-hover:text-gundar-primary line-clamp-2">{{ $item->title }}</h4>
                                        <p class="mt-2 text-xs text-slate-500">{{ $item->author }}</p>
                                        @if(! empty($reasonList))
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($reasonList as $reason)
                                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600">{{ $reason }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth

                @if($topics->isNotEmpty())
                    <div class="mb-8 rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-[0_2px_10px_rgb(0,0,0,0.02)] backdrop-blur-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-800">Insight Repository</h3>
                                <p class="mt-1 text-sm text-slate-500">Topik yang paling aktif saat ini membantu Anda memulai pencarian dengan arah yang lebih jelas.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">{{ $topics->count() }} topik</span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            @foreach($topics->sortByDesc('publications_count')->take(3) as $topic)
                                <a href="{{ route('home', ['topic' => $topic->slug]) }}" class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition-all duration-300 hover:border-gundar-primary/30 hover:bg-white hover:shadow-sm">
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gundar-primary">Topik aktif</p>
                                    <h4 class="mt-2 text-sm font-bold text-slate-800">{{ $topic->name }}</h4>
                                    <p class="mt-2 text-xs text-slate-500">{{ $topic->publications_count }} publikasi terkait</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Bagian Judul dan Filter Dropdown Metode Riset -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-2xl font-black text-gundar-dark">Jurnal & Publikasi</h2>
                        @if($search)
                            <p class="mt-1 text-sm text-slate-500">Hasil pencarian untuk: <span class="font-bold text-gundar-primary">"{{ $search }}"</span></p>
                        @elseif($activeTopic)
                            <p class="mt-1 text-sm text-slate-500">Kategori: <span class="font-bold text-gundar-primary">{{ $activeTopic->name }}</span></p>
                        @elseif(isset($methodFilter) && $methodFilter !== '')
                            <p class="mt-1 text-sm text-slate-500">Metode Riset: <span class="font-bold text-gundar-primary">{{ $methodFilter }}</span></p>
                        @else
                            <p class="mt-1 text-sm text-slate-500">Relevansi, popularitas, dan pembaruan dipertimbangkan secara bersamaan.</p>
                        @endif
                        <p class="mt-2 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Telusuri dokumen, filter topik, dan simpan karya yang relevan.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-2">
                        <div class="flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50/70 p-1.5 shadow-sm">
                            <form action="{{ route('home') }}" method="GET" class="flex flex-wrap items-center gap-1.5">
                                @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                                @if(request('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif

                                @if(isset($typeOptions) && count($typeOptions) > 1)
                                    <select name="type" onchange="this.form.submit()" class="min-w-[120px] rounded-full border border-transparent bg-white px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:border-slate-200 focus:border-gundar-primary focus:outline-none">
                                        @foreach($typeOptions as $value => $label)
                                            <option value="{{ $value }}" {{ (isset($typeFilter) && $typeFilter == $value) ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @endif

                                @if(isset($availableMethods) && $availableMethods->isNotEmpty())
                                    <select name="method" onchange="this.form.submit()" class="min-w-[120px] rounded-full border border-transparent bg-white px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:border-slate-200 focus:border-gundar-primary focus:outline-none">
                                        <option value="">Semua Metode</option>
                                        @foreach($availableMethods as $m)
                                            <option value="{{ $m }}" {{ (isset($methodFilter) && $methodFilter == $m) ? 'selected' : '' }}>
                                                {{ $m }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif

                                @if(isset($availableYears) && $availableYears->isNotEmpty())
                                    <select name="year" onchange="this.form.submit()" class="min-w-[100px] rounded-full border border-transparent bg-white px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:border-slate-200 focus:border-gundar-primary focus:outline-none">
                                        <option value="">Semua Tahun</option>
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}" {{ (isset($yearFilter) && $yearFilter == $year) ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </form>
                        </div>

                        @if($search || $activeTopic || (isset($methodFilter) && $methodFilter !== '') || (isset($typeFilter) && $typeFilter !== '') || (isset($yearFilter) && $yearFilter !== ''))
                            <a href="{{ route('home') }}" class="text-[10px] font-semibold uppercase tracking-[0.16em] text-gundar-accent hover:text-orange-600 hover:underline transition-colors">Reset</a>
                        @endif
                    </div>
                </div>

                <div class="mb-6 rounded-[24px] border border-slate-200 bg-white/80 p-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Hasil saat ini</p>
                            <p class="text-sm font-semibold text-slate-700">{{ number_format($publications->total()) }} publikasi ditemukan</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if($search)
                                <span class="rounded-full bg-gundar-primary/10 px-3 py-1 text-[10px] font-semibold text-gundar-primary">Kata kunci: {{ $search }}</span>
                            @endif
                            @if($methodFilter)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-600">Metode: {{ $methodFilter }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($search || (isset($typeFilter) && $typeFilter !== '') || (isset($methodFilter) && $methodFilter !== '') || (isset($yearFilter) && $yearFilter !== ''))
                    <div class="mb-6 flex flex-wrap gap-2">
                        @if($search)
                            <a href="{{ route('home', array_filter(['type' => $typeFilter ?: null, 'method' => $methodFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="rounded-full border border-gundar-primary/20 bg-gundar-primary/5 px-3 py-1.5 text-xs font-semibold text-gundar-primary">Kata kunci: {{ $search }}</a>
                        @endif
                        @if($typeFilter)
                            <a href="{{ route('home', array_filter(['search' => $search ?: null, 'method' => $methodFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600">Kategori: {{ $typeOptions[$typeFilter] ?? $typeFilter }}</a>
                        @endif
                        @if($methodFilter)
                            <a href="{{ route('home', array_filter(['search' => $search ?: null, 'type' => $typeFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600">Metode: {{ $methodFilter }}</a>
                        @endif
                        @if($yearFilter)
                            <a href="{{ route('home', array_filter(['search' => $search ?: null, 'type' => $typeFilter ?: null, 'method' => $methodFilter ?: null])) }}" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600">Tahun: {{ $yearFilter }}</a>
                        @endif
                    </div>
                @endif

                <!-- Notifikasi Pencarian Semantik -->
                @if($search && !empty($semanticTerms))
                    <div class="mb-8 rounded-2xl bg-indigo-50/50 backdrop-blur-sm border border-indigo-100 p-5 flex items-start gap-4 shadow-sm transition-all hover:shadow-md">
                        <div class="shrink-0">
                            <x-icon-glow color="blue">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.82 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.496 1.509 1.333 1.509 2.316V18" />
                                </svg>
                            </x-icon-glow>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-indigo-900 mb-1">Pencarian Cerdas Aktif</h4>
                            <p class="text-sm text-indigo-700/90 leading-relaxed">
                                Sistem pencarian semantik memperluas istilah Anda dengan kata terkait: 
                                <span class="font-bold text-indigo-900">{{ implode(', ', $semanticTerms) }}</span>.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Tampilan Empty State -->
                @if($publications->isEmpty())
                    <div class="py-20 flex flex-col items-center justify-center text-center">
                        <div class="relative mb-6">
                            <div class="absolute inset-0 bg-gundar-primary/20 blur-2xl rounded-full scale-[1.8]"></div>
                            <div class="relative flex h-24 w-24 items-center justify-center rounded-[2rem] bg-gradient-to-br from-white to-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-white">
                                <svg class="w-10 h-10 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-4 text-xl font-black text-gundar-dark">Dokumen Tidak Ditemukan</h3>
                        <p class="mt-2 text-slate-500 max-w-sm mx-auto">Kami tidak dapat menemukan karya ilmiah yang cocok dengan kata kunci atau filter yang Anda gunakan.</p>
                    </div>
                @else
                    <!-- LOOPING ITEM JURNAL -->
                    <div class="flex flex-col gap-4">
                        @foreach($publications as $pub)
                            @include('partials.publication-item', ['pub' => $pub])
                        @endforeach
                    </div>

                    <!-- Paginasi -->
                    <div class="mt-12">
                        {{ $publications->links() }}
                    </div>
                @endif
            </div>

        </div>
    </main>
@endsection