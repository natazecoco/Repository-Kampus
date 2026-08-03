@extends('layouts.app')

@section('content')

    <!-- KONTEN UTAMA: DASHBOARD -->
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- HEADER HALAMAN -->
        <div class="mb-10 text-center lg:text-left flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-gundar-primary mb-2">Area Mahasiswa</p>
                <h1 class="text-3xl font-black text-gundar-dark sm:text-4xl md:text-5xl">Dashboard Profil.</h1>
                <p class="mt-4 text-base text-slate-500 max-w-xl">
                    Kelola informasi akun, lihat pratinjau aktivitas, dan pantau topik kajian favorit Anda.
                </p>
            </div>
            
            <!-- Tombol Logout Cepat -->
            <form action="{{ route('student.logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-full border-2 border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition-all hover:border-rose-100 hover:bg-rose-50 hover:text-rose-600 shadow-sm hover:shadow">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar Akun
                </button>
            </form>
        </div>

        <!-- NOTIFIKASI SUKSES -->
        @if(session('message'))
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50/80 backdrop-blur-sm p-4 flex items-center gap-3 shadow-sm">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <p class="text-sm font-bold text-emerald-800">{{ session('message') }}</p>
            </div>
        @endif

        <!-- GRID LAYOUT -->
        <div class="grid gap-8 lg:grid-cols-12">
            
            <!-- KOLOM KIRI: FORM PROFIL (Lebar 7/12) -->
            <section class="lg:col-span-7">
                <div class="rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 sm:p-10 relative overflow-hidden h-full">
                    <!-- Aksen Header Kotak -->
                    <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>
                    
                    <h2 class="text-lg font-black text-slate-800 border-b border-slate-100 pb-4 mb-6">Informasi Akun</h2>
                    
                    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Nama Lengkap</label>
                            <input name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
                        </div>
                        
                        <div>
                            <label class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Email Utama</label>
                            <input name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
                        </div>
                        
                        <div class="grid gap-5 md:grid-cols-2 pt-2 border-t border-slate-100 mt-2">
                            <div>
                                <label class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Password Baru</label>
                                <input name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" placeholder="Kosongkan jika tetap">
                            </div>
                            <div>
                                <label class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Konfirmasi Password</label>
                                <input name="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" placeholder="Ulangi password baru">
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full sm:w-auto rounded-full bg-gundar-dark px-8 py-3.5 text-sm font-bold text-white transition hover:bg-gundar-primary shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- KOLOM KANAN: WIDGETS (Lebar 5/12) -->
            <section class="lg:col-span-5 flex flex-col gap-8">
                
                <!-- Widget: Bookmark Terbaru -->
                <div class="rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 sm:p-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                        <h2 class="text-base font-black text-slate-800">Bookmark Terbaru</h2>
                        <a href="{{ route('bookmarks.index') }}" class="text-[10px] font-black uppercase tracking-wider text-gundar-primary hover:text-gundar-accent transition">Lihat Semua &rarr;</a>
                    </div>
                    
                    @if($bookmarks->isEmpty())
                        <div class="text-center py-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
                            <p class="text-xs font-bold text-slate-400">Belum ada publikasi yang disimpan.</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach($bookmarks->take(3) as $bookmark)
                                @if($bookmark->publication)
                                    <li class="group relative rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:bg-white hover:border-gundar-primary/30 hover:shadow-sm">
                                        <a href="{{ route('publications.show', $bookmark->publication) }}" class="block">
                                            <p class="text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">{{ $bookmark->publication->type_label ?? 'JURNAL' }}</p>
                                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-gundar-primary line-clamp-2 leading-snug">
                                                {{ $bookmark->publication->title }}
                                            </h3>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Widget: Topik Favorit -->
                <div class="rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 sm:p-8">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                        <svg class="w-5 h-5 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /></svg>
                        <h2 class="text-base font-black text-slate-800">Topik Favorit</h2>
                    </div>
                    
                    @if($preferredTopics->isEmpty())
                        <p class="text-sm text-slate-400 font-medium italic">Belum ada topik favorit yang dipilih.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($preferredTopics as $preference)
                                @if($preference->topic)
                                    <span class="inline-flex items-center rounded-full border border-gundar-primary/20 bg-gundar-primary/5 px-3 py-1.5 text-xs font-bold text-gundar-primary shadow-sm">
                                        {{ $preference->topic->name }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                
            </section>
        </div>
    </main>

@endsection