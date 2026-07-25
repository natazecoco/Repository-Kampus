<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $publication->title }} - Repositori Ilmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">

    <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-700">Repo<span class="text-slate-800">Ilmiah</span></a>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <a href="/" class="font-medium text-slate-600 hover:text-blue-700 transition">← Kembali ke Beranda</a>
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="font-medium text-slate-600 hover:text-blue-700 transition">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
            <div class="mb-6 rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-700 px-5 py-5 text-white">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.24em] border border-white/20">
                        {{ $publication->type }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.24em] border border-white/20">
                        {{ $publication->year }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-4xl font-black leading-tight">
                    {{ $publication->title }}
                </h1>
                <p class="mt-3 text-blue-50 text-sm md:text-base">
                    Penulis: <span class="font-semibold text-white">{{ $publication->author }}</span>
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3 mb-8">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-[11px] uppercase tracking-[0.22em] font-bold text-slate-400">Diterbitkan di</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $publication->container ? $publication->container->name : 'Tidak diketahui' }}</p>
                </div>
                @if($publication->container && $publication->container->identifier)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] font-bold text-slate-400">Identifier</p>
                        <p class="mt-2 text-sm font-mono font-semibold text-slate-800">{{ $publication->container->identifier }}</p>
                    </div>
                @endif
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-[11px] uppercase tracking-[0.22em] font-bold text-slate-400">Status Akses</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $publication->files->contains(fn ($file) => $file->access_type === 'restricted') ? 'Terdapat file terbatas' : 'Semua dokumen publik' }}</p>
                </div>
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
                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50">
                        <ul class="divide-y divide-slate-200">
                            @foreach($publication->files as $file)
                                <li class="px-4 py-4 md:px-5 md:py-5 hover:bg-white transition-colors">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div class="flex items-center gap-3 min-w-0">
                                            @if($file->access_type === 'public')
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-amber-100">
                                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                </span>
                                            @endif
                                            
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-bold text-slate-800 truncate">{{ $file->title }}</h4>
                                                <span class="mt-1 inline-block text-[10px] uppercase tracking-wider font-semibold {{ $file->access_type === 'public' ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    {{ $file->access_type === 'public' ? 'Akses Terbuka' : 'Akses Terbatas' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="shrink-0 md:ml-4">
                                            @if($file->access_type === 'public')
                                                <a href="{{ route('file.akses', $file->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                                                    Lihat File
                                                </a>
                                            @else
                                                @auth
                                                    <a href="{{ route('document.viewer', $file->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-slate-700 transition-colors">
                                                        Baca (Restricted)
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-500 text-xs font-bold rounded-lg cursor-not-allowed">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                                                        Login Dulu
                                                    </span>
                                                @endauth
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </div>

        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Dokumen Terkait (Rekomendasi AI)</h2>
                    <p class="text-sm text-slate-500 mt-1">Sistem menemukan artikel terkait berdasarkan kedekatan teks Abstrak, Judul, dan Kata Kunci menggunakan Content-Based Filtering.</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.24em] text-blue-700 border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    AI Match
                </span>
            </div>
                
            @if($recommendations->isEmpty())
                <div class="p-6 bg-slate-50 rounded-xl text-center text-slate-400 italic text-sm border border-dashed border-slate-300">
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

                        <a href="{{ route('publications.show', $rec->recommendedPublication->id) }}" class="block p-5 border border-slate-200 rounded-xl hover:border-blue-500 hover:shadow-md transition bg-white">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $rec->recommendedPublication->title }}</h3>
                                    <p class="text-sm text-slate-600">{{ $rec->recommendedPublication->author }} ({{ $rec->recommendedPublication->year }})</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $percentage }}% relevan</span>
                            </div>
                                
                            <div class="mt-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
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

                            @if(count($matchingKeywords) > 0)
                                <div class="flex flex-wrap gap-1.5 items-center mt-4 text-xs">
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

                            <div class="flex justify-end items-center text-xs mt-4 pt-3 border-t border-slate-100">
                                <span class="text-blue-600 font-bold hover:text-blue-800 flex items-center gap-1">
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