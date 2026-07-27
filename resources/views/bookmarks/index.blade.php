@extends('layouts.app')

@section('content')

    <!-- KONTEN UTAMA: KOLEKSI BACAAN -->
    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- HEADER HALAMAN -->
        <div class="mb-10 text-center">
            <p class="text-[11px] font-black uppercase tracking-[0.28em] text-gundar-primary mb-2">Reading Shelf</p>
            <h1 class="text-3xl font-black text-gundar-dark sm:text-4xl md:text-5xl">Koleksi Bacaan Anda.</h1>
            <p class="mt-4 text-base text-slate-500 max-w-xl mx-auto">
                Kumpulan publikasi ilmiah yang Anda simpan untuk dibaca atau diunduh kembali nanti.
            </p>
        </div>

        <!-- AREA DAFTAR BACAAN & TOPIK FAVORIT -->
        <div class="rounded-[24px] border border-slate-200 bg-white shadow-sm p-6 sm:p-10 relative overflow-hidden">
            <!-- Aksen Header Kotak -->
            <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>

            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h2 class="text-lg font-bold text-slate-800">Daftar Tersimpan</h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">
                    {{ $bookmarks->count() }} item
                </span>
            </div>

            <!-- LIST JURNAL TERSIMPAN -->
            @if($bookmarks->isEmpty())
                <div class="py-16 text-center">
                    <span class="text-4xl">📚</span>
                    <h3 class="mt-4 text-lg font-bold text-gundar-dark">Koleksi Masih Kosong</h3>
                    <p class="text-slate-500 mt-1">Anda belum menyimpan publikasi ilmiah apa pun.</p>
                    <a href="{{ route('home') }}" class="mt-6 inline-block rounded-full bg-gundar-dark px-6 py-2.5 text-sm font-bold text-white transition hover:bg-gundar-primary">
                        Mulai Eksplorasi
                    </a>
                </div>
            @else
                <div class="flex flex-col">
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
                <div class="mt-12 rounded-xl bg-slate-50 border border-slate-100 p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">🎯</span>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900">Topik Kajian Favorit Anda</h2>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Sistem akan memprioritaskan rekomendasi bacaan berdasarkan topik ini.</p>
                    
                    <div class="flex flex-wrap gap-2">
                        @foreach($preferredTopics as $preference)
                            @if($preference->topic)
                                <div class="inline-flex items-center gap-2 rounded-full border border-gundar-primary/20 bg-white px-3 py-1.5 shadow-sm">
                                    <span class="text-xs font-bold text-gundar-primary">{{ $preference->topic->name }}</span>
                                    <form action="{{ route('topics.preference', $preference->topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-slate-400 hover:text-rose-500 transition font-bold text-xs" title="Hapus Topik">×</button>
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