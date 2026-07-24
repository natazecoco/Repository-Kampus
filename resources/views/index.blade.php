<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositori Ilmiah - Universitas Gunadarma</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans">

    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-bold text-blue-700">Repo<span class="text-slate-800">Ilmiah</span></span>
                </div>
                <div>
                    <a href="/admin" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Masuk Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-blue-700 text-white py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-extrabold mb-4">Temukan Referensi Ilmiah Terbaik</h1>
            <p class="text-blue-200 mb-8 text-lg">Jelajahi kumpulan skripsi, artikel, dan jurnal untuk mendukung penelitianmu.</p>
            
            <form action="/" method="GET" class="flex shadow-lg rounded-md overflow-hidden">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik judul, penulis, atau kata kunci..." class="flex-1 px-5 py-4 text-slate-800 focus:outline-none text-lg">
                <button type="submit" class="bg-blue-800 px-8 py-4 font-bold hover:bg-blue-900 transition">Cari</button>
            </form>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Koleksi Terbaru</h2>
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
                                @if($pub->file_path)
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
        @endif
    </main>

</body>
</html>