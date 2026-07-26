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
                <div class="flex items-center gap-3">
                    <a href="/" class="text-2xl font-bold text-blue-700">Repo<span class="text-slate-800">Ilmiah</span></a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">← Kembali ke Beranda</a>
                    @guest
                        <a href="{{ route('student.login') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Masuk Mahasiswa</a>
                    @endguest
                    @auth
                        <span class="text-sm font-medium text-slate-700">Halo, {{ auth()->user()->name }}</span>
                        <form action="{{ route('student.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Logout</button>
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
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $publication->files->contains(fn ($file) => $file->isRestricted()) ? 'Terdapat file terbatas' : 'Semua dokumen publik' }}</p>
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

            @if($publication->topics->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Topik Repository</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($publication->topics as $topic)
                            <a href="{{ route('topic.show', $topic->slug) }}" class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1 rounded-md border border-indigo-200 font-medium hover:bg-indigo-100">
                                {{ $topic->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($recommendations->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Rekomendasi Bacaan</h2>
                            <p class="text-sm text-slate-500">Dokumen lain yang mirip dengan publikasi ini berdasarkan konten dan kata kunci.</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $recommendations->count() }} rekomendasi</span>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach($recommendations as $recommendation)
                            @if($recommendation->recommendedPublication)
                                <a href="{{ route('publications.show', $recommendation->recommendedPublication->id) }}" class="group block rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm">
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Rekomendasi</div>
                                        <div class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">{{ round($recommendation->similarity_score * 100) }}%</div>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 line-clamp-2 mb-2">{{ $recommendation->recommendedPublication->title }}</h3>
                                    <p class="text-sm text-slate-500">{{ $recommendation->recommendedPublication->author }} · {{ $recommendation->recommendedPublication->year }}</p>
                                    @if($recommendation->recommendedPublication->topics->isNotEmpty())
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach($recommendation->recommendedPublication->topics->take(2) as $topic)
                                                <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-600 bg-slate-100 px-2 py-1 rounded-md">{{ $topic->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-8 border-t border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Dokumen</h2>
                    <p class="text-xs text-slate-500">Akses dan izin unduh ditentukan pada setiap bagian.</p>
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
                                            @if($file->isPublic())
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
                                                <span class="mt-1 inline-block text-[10px] uppercase tracking-wider font-semibold {{ $file->isPublic() ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    {{ $file->visibility_label }}{{ $file->allow_download ? ' · Unduh diizinkan' : ' · Baca di viewer' }}
                                                </span>
                                            </div>
                                        </div>
 
                                        <div class="shrink-0 md:ml-4">
                                            @if($file->canBeViewedBy(auth()->user()))
                                                <a href="{{ route('publications.viewer', ['publication' => $publication, 'file' => $file]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-slate-700 transition-colors">
                                                    Baca di Viewer
                                                </a>
                                                @if($file->canBeDownloadedBy(auth()->user()))
                                                    <a href="{{ route('file.akses', ['file' => $file->id, 'download' => 1]) }}" class="ml-2 inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                                                        Unduh
                                                    </a>
                                                @endif
                                            @elseif($file->visibility === 'authenticated')
                                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-300">
                                                    Login untuk membaca
                                                </a>
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

    </main>

</body>
</html>
