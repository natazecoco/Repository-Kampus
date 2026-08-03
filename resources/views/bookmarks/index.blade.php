@extends('layouts.app')

@section('content')

    <!-- KONTEN UTAMA: KOLEKSI BACAAN -->
    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- HEADER HALAMAN -->
        <div class="mb-10 text-center">
            <p class="text-[11px] font-black uppercase tracking-[0.28em] text-gundar-primary mb-2">Reading Shelf</p>
            <h1 class="text-3xl font-black text-gundar-dark sm:text-4xl md:text-5xl">Koleksi Bacaan Anda.</h1>
            <p class="mt-4 text-base text-slate-500 max-w-xl mx-auto font-medium">
                Kumpulan publikasi ilmiah yang Anda simpan untuk dibaca atau diunduh kembali nanti.
            </p>
        </div>

        <!-- AREA DAFTAR BACAAN & TOPIK FAVORIT -->
        <div class="rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 sm:p-10 relative overflow-hidden">
            <!-- Aksen Header Kotak -->
            <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>

            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h2 class="text-lg font-black text-slate-800">Daftar Tersimpan</h2>
                <span class="rounded-full bg-gundar-primary/10 px-3.5 py-1 text-xs font-extrabold text-gundar-primary">
                    {{ $bookmarks->count() }} item
                </span>
            </div>

            <!-- LIST JURNAL TERSIMPAN -->
            @if($bookmarks->isEmpty())
                <div class="py-16 text-center flex flex-col items-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    </div>
                    <h3 class="text-lg font-black text-gundar-dark">Koleksi Masih Kosong</h3>
                    <p class="text-slate-500 mt-1 text-sm font-medium">Anda belum menyimpan publikasi ilmiah apa pun.</p>
                    <a href="{{ route('home') }}" class="mt-6 inline-block rounded-full bg-gundar-dark px-8 py-3 text-sm font-bold text-white transition hover:bg-gundar-primary shadow-md hover:shadow-lg">
                        Mulai Eksplorasi
                    </a>
                </div>
            @else
                <div class="flex flex-col gap-4">
                    <!-- LOOPING MEMAKAI PARTIALS -->
                    @foreach($bookmarks as $bookmark)
                        @if($publication = $bookmark->publication)
                            
                            <!-- Panggil Partial dengan sinyal isBookmarkPage -->
                            @include('partials.publication-item', ['pub' => $publication, 'isBookmarkPage' => true])
                            
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- TOPIK FAVORIT (PREFERENSI) -->
            @if(isset($preferredTopics) && $preferredTopics->isNotEmpty())
                <div class="mt-12 rounded-2xl bg-slate-50/80 border border-slate-100 p-6 shadow-sm">
                    <div class="flex items-center gap-2.5 mb-2">
                        <svg class="w-5 h-5 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /></svg>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900">Topik Kajian Favorit Anda</h2>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 font-medium">Sistem akan memprioritaskan rekomendasi bacaan berdasarkan topik ini.</p>
                    
                    <div class="flex flex-wrap gap-2">
                        @foreach($preferredTopics as $preference)
                            @if($preference->topic)
                                <div class="inline-flex items-center gap-2 rounded-full border border-gundar-primary/20 bg-white px-3.5 py-1.5 shadow-sm">
                                    <span class="text-xs font-bold text-gundar-primary">{{ $preference->topic->name }}</span>
                                    <form action="{{ route('topics.preference', $preference->topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-slate-400 hover:text-rose-500 transition font-black text-sm leading-none" title="Hapus Topik">&times;</button>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </main>

@endsection