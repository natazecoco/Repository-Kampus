@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
    
    <!-- SEARCH HEADER BAR -->
    <section class="relative z-10 mb-8 rounded-[28px] border border-slate-200/80 bg-white/80 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.03)] backdrop-blur-xl sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-24 h-56 w-56 rounded-full bg-gundar-primary/10 blur-3xl"></div>
        
        <div class="relative">
            <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-gundar-primary mb-3">
                <span class="h-1.5 w-1.5 rounded-full bg-gundar-accent"></span>
                Pencarian & Eksplorasi Repositori
            </div>

            <!-- SEARCH INPUT -->
            <form action="{{ route('search') }}" method="GET" class="mt-2">
                @if(request('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                @if(request('method')) <input type="hidden" name="method" value="{{ request('method') }}"> @endif
                @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                @if(request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif

                <div class="flex items-center rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm transition focus-within:border-gundar-primary focus-within:ring-4 focus-within:ring-gundar-primary/10">
                    <svg class="ml-3 hidden h-5 w-5 shrink-0 text-slate-400 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." class="min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm sm:text-base font-semibold text-slate-800 outline-none placeholder:text-slate-400 placeholder:font-normal">
                    <button type="submit" class="rounded-xl bg-gundar-dark px-5 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-gundar-primary sm:px-7">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- CHIP FILTER AKTIF -->
    @php
        $hasActiveFilter = $search || $topicSlug || $typeFilter || $methodFilter || $yearFilter;
    @endphp

    @if($hasActiveFilter)
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Filter Aktif:</span>

            @if($search)
                <a href="{{ route('search', array_filter(['topic' => $topicSlug ?: null, 'type' => $typeFilter ?: null, 'method' => $methodFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-gundar-primary/20 bg-gundar-primary/5 px-3 py-1 text-xs font-semibold text-gundar-primary transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Kata Kunci: "{{ $search }}"</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if($topicSlug)
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'type' => $typeFilter ?: null, 'method' => $methodFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-gundar-primary/20 bg-gundar-primary/5 px-3 py-1 text-xs font-semibold text-gundar-primary transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Topik: {{ $topicSlug }}</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if($typeFilter)
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'topic' => $topicSlug ?: null, 'method' => $methodFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Kategori: {{ $typeOptions[$typeFilter] ?? $typeFilter }}</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if($methodFilter)
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'topic' => $topicSlug ?: null, 'type' => $typeFilter ?: null, 'year' => $yearFilter ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Metode: {{ $methodFilter }}</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if($yearFilter)
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'topic' => $topicSlug ?: null, 'type' => $typeFilter ?: null, 'method' => $methodFilter ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Tahun: {{ $yearFilter }}</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            <a href="{{ route('search') }}" class="text-[11px] font-bold text-gundar-accent hover:underline ml-2">
                Reset Semua
            </a>
        </div>
    @endif

    <!-- MAIN SEARCH LAYOUT (SIDEBAR FILTER TERKUNCI RAPI DI KIRI) -->
    <div class="grid gap-8 lg:grid-cols-[240px_minmax(0,1fr)] pb-16">
        
        <!-- ASIDE: FILTER PANEL (DESKTOP) -->
        <aside class="hidden lg:block">
            <div class="sticky top-28 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-800">Filter Hasil</h3>
                </div>

                <form action="{{ route('search') }}" method="GET" class="space-y-4">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    @if($topicSlug) <input type="hidden" name="topic" value="{{ $topicSlug }}"> @endif

                    <!-- Jenis Publikasi -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jenis Publikasi</label>
                        <select name="type" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-gundar-primary focus:bg-white">
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ $typeFilter === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Metode Riset -->
                    @if(isset($availableMethods) && $availableMethods->isNotEmpty())
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Metode Riset</label>
                            <select name="method" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-gundar-primary focus:bg-white">
                                <option value="">Semua Metode</option>
                                @foreach($availableMethods as $method)
                                    <option value="{{ $method }}" {{ $methodFilter === $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Tahun -->
                    @if(isset($availableYears) && $availableYears->isNotEmpty())
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tahun Terbit</label>
                            <select name="year" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-gundar-primary focus:bg-white">
                                <option value="">Semua Tahun</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $yearFilter === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($hasActiveFilter)
                        <div class="pt-2 border-t border-slate-100">
                            <a href="{{ route('search') }}" class="block text-center text-xs font-bold text-slate-400 hover:text-rose-500 transition">
                                Bersihkan Filter
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </aside>

        <!-- MAIN COLUMN: HASIL DOKUMEN -->
        <main class="min-w-0">
            
            <!-- HEADER HASIL PENCARIAN -->
            <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-xl font-black text-gundar-dark">
                        @if($search)
                            Hasil: "{{ $search }}"
                        @elseif($topicSlug)
                            Topik: {{ $topicSlug }}
                        @else
                            Semua Publikasi
                        @endif
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                        Ditemukan <span class="font-bold text-slate-800">{{ number_format($publications->total()) }}</span> karya ilmiah terkait.
                    </p>
                </div>
            </div>

            <!-- NOTIFIKASI PENCARIAN CERDAS (SEMANTIC) -->
            @if($search && !empty($semanticTerms))
                <div class="mb-6 rounded-2xl bg-indigo-50/60 border border-indigo-100 p-4 text-xs sm:text-sm text-indigo-900 flex items-start gap-3">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-200 text-indigo-800 font-bold">i</span>
                    <p class="leading-relaxed">
                        <span class="font-bold">Pencarian Semantik Aktif:</span> Pencarian juga mencocokkan istilah terkait: 
                        <span class="font-semibold text-indigo-700">{{ implode(', ', $semanticTerms) }}</span>.
                    </p>
                </div>
            @endif

            <!-- EMPTY STATE -->
            @if($publications->isEmpty())
                <div class="rounded-3xl border border-slate-200/80 bg-white px-6 py-16 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-gundar-dark">Dokumen Tidak Ditemukan</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        Coba gunakan kata kunci yang lebih umum atau kurangi filter pencarian yang Anda pilih.
                    </p>
                    <a href="{{ route('search') }}" class="mt-5 inline-block rounded-full bg-gundar-dark px-6 py-2.5 text-xs font-bold text-white transition hover:bg-gundar-primary">
                        Tampilkan Semua Dokumen
                    </a>
                </div>
            @else
                <!-- DAFTAR KARTU PUBLIKASI -->
                <div class="space-y-4">
                    @foreach($publications as $pub)
                        @include('partials.publication-item', ['pub' => $pub])
                    @endforeach
                </div>

                <!-- PAGINATION -->
                <div class="mt-10">
                    {{ $publications->links() }}
                </div>
            @endif

        </main>

    </div>
</div>
@endsection