<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $publication->title }} - Repositori Ilmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans">

    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-700">Repo<span class="text-slate-800">Ilmiah</span></a>
                </div>
                <div>
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">← Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            
            <div class="mb-4">
                <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-bold uppercase tracking-wider mb-2">
                    {{ $publication->type }}
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ $publication->title }}
                </h1>
                <p class="text-slate-600 mt-2 font-medium">
                    Penulis: <span class="text-slate-900">{{ $publication->author }}</span> | Tahun Terbit: <span class="text-slate-900">{{ $publication->year }}</span>
                </p>
            </div>

            <div class="border-t border-b border-slate-100 py-3 my-6 text-sm text-slate-500 flex flex-wrap gap-4">
                <div>Diterbitkan oleh/di: <span class="font-semibold text-slate-700">{{ $publication->container ? $publication->container->name : 'Tidak diketahui' }}</span></div>
                @if($publication->container && $publication->container->identifier)
                    <div>Identifier: <span class="font-mono text-slate-700">{{ $publication->container->identifier }}</span></div>
                @endif
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-bold text-slate-800 mb-3">Abstrak</h2>
                <p class="text-slate-700 leading-relaxed text-justify whitespace-pre-line">
                    {{ $publication->abstract }}
                </p>
            </div>

            <div class="mb-8">
                <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Kata Kunci</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $publication->keywords) as $keyword)
                        <span class="bg-slate-100 text-slate-700 text-xs px-3 py-1 rounded-md border border-slate-200 font-medium">
                            {{ trim($keyword) }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- ========================================== -->
            <!-- DAFTAR DOKUMEN (SPLIT PDF) PENGGANTI BARU -->
            <!-- ========================================== -->
            <div class="pt-8 border-t border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Dokumen</h2>
                    <p class="text-xs text-slate-500">Beberapa bagian mungkin memerlukan akses masuk (Login).</p>
                </div>

                @if($publication->files->isEmpty())
                    <div class="p-6 bg-slate-50 rounded-lg text-center text-slate-500 text-sm italic border border-dashed border-slate-300">
                        Belum ada file dokumen yang diunggah untuk publikasi ini.
                    </div>
                @else
                    <div class="border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                        <ul class="divide-y divide-slate-200">
                            @foreach($publication->files as $file)
                                <li class="px-5 py-4 flex items-center justify-between hover:bg-white transition-colors">
                                    <div class="flex items-center gap-3">
                                        <!-- Ikon Status -->
                                        @if($file->access_type === 'public')
                                            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        @endif
                                        
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-800">{{ $file->title }}</h4>
                                            <span class="text-[10px] uppercase tracking-wider font-semibold {{ $file->access_type === 'public' ? 'text-emerald-600' : 'text-amber-600' }}">
                                                {{ $file->access_type === 'public' ? 'Akses Terbuka' : 'Akses Terbatas' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="shrink-0 ml-4">
                                        <!-- LOGIKA TOMBOL AKSES -->
                                        @if($file->access_type === 'public')
                                            <a href="{{ route('file.akses', $file->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded hover:bg-blue-100 transition-colors border border-blue-200">
                                                Lihat File
                                            </a>
                                        @else
                                            @auth
                                                <a href="{{ route('file.akses', $file->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded hover:bg-slate-700 transition-colors">
                                                    Baca (Restricted)
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-500 text-xs font-bold rounded cursor-not-allowed">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                                                    Login Dulu
                                                </span>
                                            @endauth
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </div>

        <!-- ========================================== -->
        <!-- REKOMENDASI AI TETAP SAMA SEPERTI ASLINYA -->
        <!-- ========================================== -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-2">Dokumen Terkait (Rekomendasi AI)</h2>
            <p class="text-sm text-slate-500 mb-6">Sistem menemukan artikel terkait berdasarkan kedekatan teks Abstrak, Judul, dan Kata Kunci menggunakan Content-Based Filtering.</p>
            
            @if($recommendations->isEmpty())
                <div class="p-6 bg-slate-50 rounded-lg text-center text-slate-400 italic text-sm border border-dashed">
                    Sistem belum menemukan dokumen lain yang relevan dengan topik ini.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($recommendations as $rec)
                        @php
                            $mainKeywords = array_map('trim', explode(',', strtolower($publication->keywords)));
                            $recKeywords = array_map('trim', explode(',', strtolower($rec->recommendedPublication->keywords)));
                            $matchingKeywords = array_intersect($mainKeywords, $recKeywords);
                            $percentage = round($rec->similarity_score * 100, 2);
                            $barColor = $percentage >= 50 ? 'bg-emerald-500' : ($percentage >= 20 ? 'bg-blue-500' : 'bg-slate-400');
                        @endphp

                        <a href="{{ route('publications.show', $rec->recommendedPublication->id) }}" class="block p-5 border border-slate-200 rounded-lg hover:border-blue-500 hover:shadow-md transition bg-white mb-4">
                            <h3 class="text-md font-bold text-slate-800 mb-1">{{ $rec->recommendedPublication->title }}</h3>
                            <p class="text-sm text-slate-600 mb-4">{{ $rec->recommendedPublication->author }} ({{ $rec->recommendedPublication->year }})</p>
                            
                            {{-- Visualisasi Progress Bar Skor Relevansi --}}
                            <div class="mb-3 bg-slate-50 p-3 rounded border border-slate-100">
                                <div class="flex justify-between items-center text-xs mb-1.5">
                                    <span class="font-semibold text-slate-600 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Relevansi Topik (AI)
                                    </span>
                                    <span class="font-bold text-slate-800">{{ $percentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5">
                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $percentage >= 100 ? 100 : $percentage }}%"></div>
                                </div>
                            </div>

                            {{-- Highlight Kata Kunci yang Cocok --}}
                            @if(count($matchingKeywords) > 0)
                                <div class="flex flex-wrap gap-1.5 items-center mb-3 text-xs">
                                    <span class="text-slate-500 italic mr-1">Cocok pada kata:</span>
                                    @foreach(array_slice($matchingKeywords, 0, 3) as $match)
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-semibold border border-blue-200 shadow-sm">
                                            {{ ucwords($match) }}
                                        </span>
                                    @endforeach
                                    
                                    @if(count($matchingKeywords) > 3)
                                        <span class="text-slate-400 font-medium">+{{ count($matchingKeywords) - 3 }} lainnya</span>
                                    @endif
                                </div>
                            @endif

                            <div class="flex justify-end items-center text-xs mt-2 pt-3 border-t border-slate-100">
                                <span class="text-blue-600 font-bold group-hover:text-blue-800 flex items-center gap-1">
                                    Baca Selengkapnya
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

</body>
</html>