@extends('layouts.app')

@section('content')
        
    <!-- TYPOGRAPHY HERO SECTION (DI TENGAH) -->
    <section class="mx-auto max-w-6xl px-4 pt-12 pb-16 sm:px-6 lg:px-8 text-center relative z-10">
        {{-- Opsional: Tambahkan efek cahaya samar di belakang judul untuk menambah kesan premium --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-gundar-primary/10 rounded-full blur-[100px] -z-10"></div>

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

                {{-- Efek Glassmorphism: bg-white/70, backdrop-blur, dan transisi shadow --}}
                <div class="flex items-center rounded-full border border-white/60 bg-white/70 backdrop-blur-lg p-1.5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-500 focus-within:bg-white focus-within:border-gundar-primary/40 focus-within:shadow-[0_10px_40px_rgba(118,58,151,0.12)] hover:bg-white/90">
                    <div class="pl-5 pr-2 text-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, abstrak..." class="w-full bg-transparent py-3.5 text-lg text-gundar-dark placeholder:text-slate-400 focus:outline-none">
                    <button type="submit" class="shrink-0 rounded-full bg-gradient-to-r from-gundar-primary to-[#8743ad] px-8 py-3.5 font-bold text-white shadow-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">Cari</button>
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
                        <div class="mb-8 rounded-3xl bg-gundar-primary/5 p-6 border border-gundar-primary/10 transition duration-300 hover:shadow-sm">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gundar-primary">Disarankan Untuk Anda</h3>
                            <p class="mt-1 text-xs text-slate-500">Berdasarkan dokumen tersimpan dan riwayat topik.</p>
                            
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                @foreach($personalizedRecommendations as $item)
                                    <a href="{{ route('publications.show', $item) }}" class="group block bg-white p-4 rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgb(0,0,0,0.02)] hover:border-gundar-primary/30 hover:shadow-[0_8px_30px_rgb(118,58,151,0.08)] transition-all duration-300 hover:-translate-y-1">
                                        <h4 class="text-sm font-bold text-gundar-dark leading-tight group-hover:text-gundar-primary transition line-clamp-2">{{ $item->title }}</h4>
                                        <p class="mt-2 text-xs text-slate-500">{{ $item->author }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endauth

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
                            <p class="mt-1 text-sm text-slate-500">Terbaru diunggah ke dalam sistem.</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Dropdown Filter Metode Riset -->
                        @if(isset($availableMethods) && $availableMethods->isNotEmpty())
                            <form action="{{ route('home') }}" method="GET" class="inline-block">
                                @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                                @if(request('topic')) <input type="hidden" name="topic" value="{{ request('topic') }}"> @endif
                                
                                <select name="method" onchange="this.form.submit()" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 focus:border-gundar-primary focus:ring-1 focus:ring-gundar-primary focus:outline-none shadow-sm cursor-pointer transition-colors hover:bg-slate-50">
                                    <option value="">-- Semua Metode Riset --</option>
                                    @foreach($availableMethods as $m)
                                        <option value="{{ $m }}" {{ (isset($methodFilter) && $methodFilter == $m) ? 'selected' : '' }}>
                                            {{ $m }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        @if($search || $activeTopic || (isset($methodFilter) && $methodFilter !== ''))
                            <a href="{{ route('home') }}" class="text-sm font-semibold text-gundar-accent hover:text-orange-600 hover:underline transition-colors">Hapus Filter</a>
                        @endif
                    </div>
                </div>

                <!-- Notifikasi Pencarian Semantik Tanpa Emoji -->
                @if($search && !empty($semanticTerms))
                    <div class="mb-8 rounded-2xl bg-indigo-50/50 backdrop-blur-sm border border-indigo-100 p-5 flex items-start gap-4 shadow-sm transition-all hover:shadow-md">
                        <div class="shrink-0">
                            {{-- Menggunakan komponen icon-glow yang baru dibuat --}}
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

                <!-- Tampilan Empty State Kelas Atas -->
                @if($publications->isEmpty())
                    <div class="py-20 flex flex-col items-center justify-center text-center">
                        <div class="relative mb-6">
                            <!-- Background Cahaya Lembut -->
                            <div class="absolute inset-0 bg-gundar-primary/20 blur-2xl rounded-full scale-[1.8]"></div>
                            
                            <!-- Kotak Ikon -->
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
                    <!-- LOOPING ITEM JURNAL MENGGUNAKAN PARTIALS -->
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