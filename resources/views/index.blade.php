@extends('layouts.app')

@section('content')
        
    <!-- TYPOGRAPHY HERO SECTION (DI TENGAH) -->
    <section class="mx-auto max-w-6xl px-4 pt-12 pb-16 sm:px-6 lg:px-8 text-center">
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

            <!-- MINIMALIST SEARCH -->
            <form action="/" method="GET" class="mt-10 mx-auto max-w-3xl">
                <div class="flex items-center rounded-full border-2 border-slate-200 bg-white p-1.5 transition-all focus-within:border-gundar-primary/50 focus-within:shadow-[0_0_20px_rgba(118,58,151,0.1)]">
                    <div class="pl-4 pr-2 text-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, abstrak..." class="w-full bg-transparent py-3 text-lg text-gundar-dark placeholder:text-slate-400 focus:outline-none">
                    <button type="submit" class="shrink-0 rounded-full bg-gundar-primary px-8 py-3.5 font-bold text-white transition hover:bg-[#5e2e79]">Cari</button>
                </div>
            </form>
        </div>
    </section>

    <!-- LAYOUT: SIDEBAR (KIRI) & CONTENT (KANAN) -->
    <main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:grid lg:grid-cols-12 lg:gap-16">
            
            <!-- PANGGIL SIDEBAR DARI FOLDER PARTIALS -->
            @include('partials.sidebar')

            <!-- KOLOM KANAN (Daftar Dokumen) -->
            <div class="lg:col-span-8">
                
                <!-- Rekomendasi Personal -->
                @auth
                    @if($personalizedRecommendations->isNotEmpty())
                        <div class="mb-8 rounded-3xl bg-gundar-primary/5 p-6 border border-gundar-primary/10">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gundar-primary">Disarankan Untuk Anda</h3>
                            <p class="mt-1 text-xs text-slate-500">Berdasarkan dokumen tersimpan dan riwayat topik.</p>
                            
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                @foreach($personalizedRecommendations as $item)
                                    <a href="{{ route('publications.show', $item) }}" class="group block bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:border-gundar-primary transition">
                                        <h4 class="text-sm font-bold text-gundar-dark leading-tight group-hover:text-gundar-primary transition line-clamp-2">{{ $item->title }}</h4>
                                        <p class="mt-2 text-xs text-slate-500">{{ $item->author }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth

                <div class="mb-6 flex items-end justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-xl font-black text-gundar-dark">Jurnal & Publikasi</h2>
                        @if($search)
                            <p class="mt-1 text-sm text-slate-500">Hasil pencarian untuk: <span class="font-bold text-gundar-primary">"{{ $search }}"</span></p>
                        @elseif($activeTopic)
                            <p class="mt-1 text-sm text-slate-500">Kategori: <span class="font-bold text-gundar-primary">{{ $activeTopic->name }}</span></p>
                        @else
                            <p class="mt-1 text-sm text-slate-500">Terbaru diunggah ke dalam sistem.</p>
                        @endif
                    </div>
                    @if($search || $activeTopic)
                        <a href="{{ route('home') }}" class="text-sm font-semibold text-gundar-accent hover:underline">Hapus Filter</a>
                    @endif
                </div>

                <!-- Notifikasi Pencarian Semantik -->
                @if($search && !empty($semanticTerms))
                    <div class="mb-8 rounded-xl bg-indigo-50 border border-indigo-100 p-4 flex gap-3 shadow-sm">
                        <span class="text-xl">💡</span>
                        <p class="text-sm text-indigo-800">
                            Sistem pencarian semantik memperluas istilah Anda dengan kata terkait: 
                            <span class="font-bold">{{ implode(', ', $semanticTerms) }}</span>.
                        </p>
                    </div>
                @endif

                @if($publications->isEmpty())
                    <div class="py-12 text-center">
                        <span class="text-4xl">📭</span>
                        <h3 class="mt-4 text-lg font-bold text-gundar-dark">Tidak ditemukan</h3>
                        <p class="text-slate-500">Coba gunakan kata kunci yang lebih umum.</p>
                    </div>
                @else
                    <!-- LOOPING ITEM JURNAL MENGGUNAKAN PARTIALS -->
                    <div class="flex flex-col">
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