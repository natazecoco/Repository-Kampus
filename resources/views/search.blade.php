@extends('layouts.app')

@section('content')
<div x-data="{ filterOpen: false }" x-init="$watch('filterOpen', value => document.documentElement.classList.toggle('overflow-hidden', value))" class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
    
    <!-- SEARCH HEADER BAR -->
    <section class="relative z-10 mb-8 rounded-[28px] border border-slate-200/80 bg-white/80 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.03)] backdrop-blur-xl sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-24 h-56 w-56 rounded-full bg-gundar-primary/10 blur-3xl"></div>
        
        <div class="relative">
            <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-gundar-primary mb-3">
                <span class="h-1.5 w-1.5 rounded-full bg-gundar-accent"></span>
                Pencarian & Eksplorasi Repositori
            </div>

            <!-- SEARCH INPUT -->
            <div class="mt-2">
                <form action="{{ route('search') }}" method="GET" class="">
                    @if(request('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('year_from')) <input type="hidden" name="year_from" value="{{ request('year_from') }}"> @endif
                    @if(request('year_to')) <input type="hidden" name="year_to" value="{{ request('year_to') }}"> @endif

                    <div class="flex items-center rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm transition focus-within:border-gundar-primary focus-within:ring-4 focus-within:ring-gundar-primary/10">
                        <svg class="ml-3 hidden h-5 w-5 shrink-0 text-slate-400 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="search" name="search" value="{{ $search }}" placeholder="Ketik kata kunci pencarian..." class="min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm sm:text-base font-semibold text-slate-800 outline-none placeholder:text-slate-400 placeholder:font-normal">
                        <button type="submit" class="rounded-xl bg-gundar-dark px-5 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-gundar-primary sm:px-7">
                            Cari
                        </button>

                        <!-- Mobile: Filter button -->
                        <button type="button" @click.prevent="filterOpen = true" class="ml-2 flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 sm:hidden">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-1.447.894L9 17l-4.553 2.894A1 1 0 013 19V4z"/></svg>
                            Filter
                        </button>
                    </div>
                </form>

                <!-- Mobile slide-over filter (moved to page root for correct positioning) -->
            </div>
        </div>
    </section>

    <!-- CHIP FILTER AKTIF -->
    @php
        $hasActiveFilter = $search || $topicSlug || $typeFilter || request('year_from') || request('year_to');
    @endphp

    @if($hasActiveFilter)
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Filter Aktif:</span>

            @if($search)
                <a href="{{ route('search', array_filter(['topic' => $topicSlug ?: null, 'type' => $typeFilter ?: null, 'year_from' => request('year_from') ?: null, 'year_to' => request('year_to') ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-gundar-primary/20 bg-gundar-primary/5 px-3 py-1 text-xs font-semibold text-gundar-primary transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Kata Kunci: "{{ $search }}"</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if($topicSlug)
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'type' => $typeFilter ?: null, 'year_from' => request('year_from') ?: null, 'year_to' => request('year_to') ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-gundar-primary/20 bg-gundar-primary/5 px-3 py-1 text-xs font-semibold text-gundar-primary transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Topik: {{ $topicSlug }}</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if($typeFilter)
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'topic' => $topicSlug ?: null, 'year_from' => request('year_from') ?: null, 'year_to' => request('year_to') ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Kategori: {{ $typeOptions[$typeFilter] ?? $typeFilter }}</span>
                    <span class="font-bold">&times;</span>
                </a>
            @endif

            @if(request('year_from') || request('year_to'))
                <a href="{{ route('search', array_filter(['search' => $search ?: null, 'topic' => $topicSlug ?: null, 'type' => $typeFilter ?: null])) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                    <span>Tahun: {{ request('year_from') ?: '...' }} - {{ request('year_to') ?: '...' }}</span>
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

                    <!-- Tahun Range -->
                    @if(isset($availableYears) && $availableYears->isNotEmpty())
                        <div x-data="{
                                minYear: {{ $availableYears->min() ?? 1900 }},
                                maxYear: {{ $availableYears->max() ?? now()->year }},
                                yearFrom: {{ request('year_from', $availableYears->min() ?? 1900) }},
                                yearTo: {{ request('year_to', $availableYears->max() ?? now()->year) }}
                            }"
                            x-init="
                                yearFrom = Number(yearFrom);
                                yearTo = Number(yearTo);
                                if (yearFrom < minYear) yearFrom = minYear;
                                if (yearTo > maxYear) yearTo = maxYear;
                                if (yearFrom > yearTo) yearFrom = yearTo;
                            "
                            class="space-y-4"
                        >
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Rentang Tahun</label>

                            <div class="grid gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                    <span>Dari</span>
                                    <span x-text="yearFrom"></span>
                                </div>

                                <input
                                    type="range"
                                    name="year_from"
                                    x-model.number="yearFrom"
                                    :min="minYear"
                                    :max="maxYear"
                                    @input="if (yearFrom > yearTo) yearTo = yearFrom"
                                    @change="$event.target.form.submit()"
                                    class="w-full accent-gundar-primary"
                                />

                                <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                    <span>Sampai</span>
                                    <span x-text="yearTo"></span>
                                </div>

                                <input
                                    type="range"
                                    name="year_to"
                                    x-model.number="yearTo"
                                    :min="minYear"
                                    :max="maxYear"
                                    @input="if (yearTo < yearFrom) yearFrom = yearTo"
                                    @change="$event.target.form.submit()"
                                    class="w-full accent-gundar-primary"
                                />

                                <div class="rounded-2xl bg-white/80 px-3 py-2 text-xs text-slate-600 shadow-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-slate-800">Rentang yang dipilih</span>
                                        <span class="text-slate-500">{{ $availableYears->min() }} - {{ $availableYears->max() }}</span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-500">Geser kedua slider untuk memilih rentang tahun publikasi.</p>
                                </div>
                            </div>
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

            <!-- NOTIFIKASI PERLUASAN ISTILAH BERBASIS KAMUS TOPIK -->
            @if($search && !empty($semanticTerms))
                <div class="mb-6 rounded-2xl bg-indigo-50/60 border border-indigo-100 p-4 text-xs sm:text-sm text-indigo-900 flex items-start gap-3">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-200 text-indigo-800 font-bold">i</span>
                    <p class="leading-relaxed">
                        <span class="font-bold">Perluasan Istilah Aktif:</span> Pencarian juga mencocokkan istilah terkait menurut kamus topik (alias kata): 
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

    <!-- Mobile slide-over filter (page root) -->
    <div>
        <div x-show="filterOpen" x-cloak class="fixed inset-0 z-40 flex">
            <div class="fixed inset-0 bg-black/40 z-40" @click="filterOpen = false"></div>

            <aside @click.stop x-show="filterOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 max-w-xs w-full sm:w-80 bg-white shadow-xl p-5 overflow-auto z-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-slate-800">Filter</h3>
                <button type="button" @click="filterOpen = false" class="text-slate-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('search') }}" method="GET" class="space-y-4">
                @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                @if($topicSlug) <input type="hidden" name="topic" value="{{ $topicSlug }}"> @endif

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2">Jenis Publikasi</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <option value="">Semua Jenis</option>
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}" {{ $typeFilter === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Slider pair inside modal -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2">Rentang Tahun</label>
                    <div x-data="{ min: {{ $availableYears->min() ?? 1900 }}, max: {{ $availableYears->max() ?? now()->year }}, from: {{ request('year_from', $availableYears->min() ?? 1900) }}, to: {{ request('year_to', $availableYears->max() ?? now()->year) }} }">
                        <div class="flex items-center justify-between text-xs text-slate-600 mb-2">
                            <span>Dari</span>
                            <span x-text="from"></span>
                        </div>
                        <input type="range" :min="min" :max="max" x-model.number="from" name="year_from" class="w-full mb-2 accent-gundar-primary">
                        <div class="flex items-center justify-between text-xs text-slate-600 mb-2">
                            <span>Sampai</span>
                            <span x-text="to"></span>
                        </div>
                        <input type="range" :min="min" :max="max" x-model.number="to" name="year_to" class="w-full accent-gundar-primary">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 rounded-full bg-gundar-dark text-white px-4 py-2 text-sm font-bold">Terapkan</button>
                    <a href="{{ route('search') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Reset</a>
                </div>
            </form>
        </aside>
    </div>
</div>
@endsection