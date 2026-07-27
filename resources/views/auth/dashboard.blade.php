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
                <button type="submit" class="inline-flex items-center gap-2 rounded-full border-2 border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition hover:border-rose-100 hover:bg-rose-50 hover:text-rose-600 shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar Akun
                </button>
            </form>
        </div>

        <!-- NOTIFIKASI SUKSES -->
        @if(session('message'))
            <div class="mb-8 rounded-xl border border-emerald-200 bg-emerald-50 p-4 flex items-start gap-3 shadow-sm">
                <span class="text-emerald-500 text-lg leading-none">✅</span>
                <p class="text-sm font-bold text-emerald-700">{{ session('message') }}</p>
            </div>
        @endif

        <!-- GRID LAYOUT -->
        <div class="grid gap-8 lg:grid-cols-12">
            
            <!-- KOLOM KIRI: FORM PROFIL (Lebar 7/12) -->
            <section class="lg:col-span-7">
                <div class="rounded-[24px] border border-slate-200 bg-white shadow-sm p-6 sm:p-10 relative overflow-hidden h-full">
                    <!-- Aksen Header Kotak -->
                    <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>
                    
                    <h2 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-6">Informasi Akun</h2>
                    
                    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Nama Lengkap</label>
                            <input name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
                        </div>
                        
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Email Utama</label>
                            <input name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
                        </div>
                        
                        <div class="grid gap-5 md:grid-cols-2 pt-2 border-t border-slate-100 mt-2">
                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Password Baru</label>
                                <input name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" placeholder="Kosongkan jika tetap">
                            </div>
                            <div>
                                <label class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Konfirmasi Password</label>
                                <input name="password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" placeholder="Ulangi password baru">
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
                <div class="rounded-[24px] border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                        <h2 class="text-base font-bold text-slate-800">Bookmark Terbaru</h2>
                        <a href="{{ route('bookmarks.index') }}" class="text-[10px] font-bold uppercase tracking-wider text-gundar-primary hover:text-gundar-accent transition">Lihat Semua &rarr;</a>
                    </div>
                    
                    @if($bookmarks->isEmpty())
                        <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <span class="text-2xl mb-2 block">📚</span>
                            <p class="text-xs font-medium text-slate-500">Belum ada publikasi yang disimpan.</p>
                        </div>
                    @else
                        <ul class="space-y-4">
                            @foreach($bookmarks->take(3) as $bookmark)
                                @if($bookmark->publication)
                                    <li class="group relative rounded-xl border border-slate-100 bg-slate-50 p-4 transition hover:bg-white hover:border-gundar-primary/30 hover:shadow-sm">
                                        <a href="{{ route('publications.show', $bookmark->publication) }}" class="block">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">{{ $bookmark->publication->type ?? 'JURNAL' }}</p>
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
                <div class="rounded-[24px] border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                        <span class="text-base">🎯</span>
                        <h2 class="text-base font-bold text-slate-800">Topik Favorit</h2>
                    </div>
                    
                    @if($preferredTopics->isEmpty())
                        <p class="text-sm text-slate-500 italic">Belum ada topik favorit yang dipilih.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($preferredTopics as $preference)
                                @if($preference->topic)
                                    <span class="inline-flex items-center rounded-full border border-gundar-primary/20 bg-slate-50 px-3 py-1.5 text-xs font-bold text-gundar-primary shadow-sm">
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