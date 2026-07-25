<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositori Ilmiah - Universitas Gunadarma</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">

    <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 uppercase tracking-[0.24em]">Gunadarma</span>
                    <span class="text-2xl font-bold text-blue-700">Repo<span class="text-slate-800">Ilmiah</span></span>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    @guest
                        <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-blue-700 transition">Masuk Mahasiswa</a>
                        <a href="{{ route('register') }}" class="font-medium text-slate-600 hover:text-blue-700 transition">Daftar</a>
                    @endguest
                    @auth
                        <span class="hidden sm:inline text-slate-600">Halo, {{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="font-medium text-slate-600 hover:text-blue-700 transition">Logout</button>
                        </form>
                    @endauth
                    <a href="/admin" class="rounded-full border border-slate-200 px-3 py-2 font-medium text-slate-700 hover:border-blue-600 hover:text-blue-700 transition">Masuk Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-blue-800 via-blue-700 to-indigo-700 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.24),transparent_18%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.16),transparent_24%)]"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="max-w-3xl mx-auto text-center">
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.24em] border border-white/20 backdrop-blur">Repository Digital</span>
                <h1 class="mt-5 text-4xl md:text-5xl lg:text-6xl font-black leading-tight">Temukan Referensi Ilmiah Terbaik</h1>
                <p class="mt-4 text-base md:text-lg text-blue-100 max-w-2xl mx-auto">Jelajahi kumpulan skripsi, artikel, buku, dan karya ilmiah yang siap mendukung riset, tugas akhir, dan pengembangan akademikmu.</p>

                <form action="/" method="GET" class="mt-8 flex flex-col sm:flex-row gap-3 shadow-2xl rounded-2xl overflow-hidden bg-white/10 p-2 backdrop-blur-sm border border-white/20">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, kata kunci, tahun, atau container..." class="flex-1 rounded-xl px-5 py-4 text-slate-800 placeholder:text-slate-400 focus:outline-none text-base">
                    <button type="submit" class="rounded-xl bg-slate-950 px-6 py-4 font-bold hover:bg-slate-900 transition">Cari Dokumen</button>
                </form>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-sm text-blue-50">
                    <span class="rounded-full bg-white/10 px-3 py-1 border border-white/20">Skripsi</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 border border-white/20">Artikel</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 border border-white/20">Buku</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 border border-white/20">Akses Aman</span>
                </div>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Koleksi Terbaru</h2>
                @if($search)
                    <p class="text-sm text-slate-500 mt-1">Menampilkan hasil pencarian untuk: <span class="font-semibold text-slate-700">{{ $search }}</span></p>
                @endif
            </div>
        </div>

        @if($publications->isEmpty())
            <div class="bg-white p-10 text-center rounded-lg border border-slate-200 shadow-sm">
                <p class="text-slate-500 text-lg">Belum ada dokumen yang diunggah ke repositori.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($publications as $pub)
                    
                    <!-- UPDATE: Implementasi Card UI Modern Editorial dengan navigasi klik menyeluruh -->
                    <article onclick="window.location.href='{{ route('publications.show', $pub->id) }}'" class="group bg-white border border-slate-200/60 rounded-xl p-6 transition-all duration-300 hover:shadow-md hover:border-slate-300 hover:bg-slate-50/30 cursor-pointer flex flex-col h-full">
                        
                        <!-- Header Card: Menampilkan metadata rilis dokumen -->
                        <div class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-3 flex items-center gap-2">
                            <span>{{ $pub->year ?? 'Tahun N/A' }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span class="text-slate-500">{{ $pub->type ?? 'JURNAL' }}</span> 
                        </div>

                        <!-- Tipografi Judul: Menggunakan rumpun Serif -->
                        <h3 class="text-xl font-bold text-slate-900 mb-2 line-clamp-2 font-serif group-hover:text-blue-700 transition" title="{{ $pub->title }}">
                            <a href="{{ route('publications.show', $pub->id) }}">
                                {{ $pub->title }}
                            </a>
                        </h3>
                        
                        <!-- Metadata Penulis dan Kontainer -->
                        <div class="text-sm text-slate-500 mb-4 font-medium">
                            Oleh: {{ $pub->author }} <br>
                            <span class="text-xs font-normal text-slate-400">
                                {{ $pub->container ? $pub->container->name : 'Wadah tidak diketahui' }}
                            </span>
                        </div>
                        
                        <!-- TAMBAHAN: Menampilkan abstrak dokumen yang sudah difilter highlight -->
                        {{-- Sintaks {!! !!} (raw echo) digunakan untuk merender tag span dari proses preg_replace --}}
                        <p class="font-sans text-sm text-slate-600 leading-relaxed line-clamp-3 mb-6 flex-grow">
                            {!! $pub->highlighted_abstract ?? 'Abstrak tidak tersedia.' !!}
                        </p>
                        
                        <!-- Footer Card: Indikator file akses -->
                        <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if($pub->files->count() > 0)
                                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        PDF
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-slate-400 italic">No PDF</span>
                                @endif
                            </div>
                            
                            <span class="text-sm font-medium text-blue-600 opacity-0 transition-opacity group-hover:opacity-100">
                                Baca detail &rarr;
                            </span>
                        </div>

                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $publications->links() }}
            </div>
        @endif
    </main>

</body>
</html>