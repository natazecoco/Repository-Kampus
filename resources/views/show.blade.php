<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $publication->title }} - Repositori Ilmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.13),_transparent_25%),linear-gradient(135deg,_#f8fbff_0%,_#f4f7fb_100%)] font-sans text-slate-800">

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

    <main class="mx-auto max-w-6xl px-4 py-12">
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_24px_80px_-28px_rgba(15,23,42,0.35)]">
            <div class="bg-gradient-to-r from-blue-700 via-blue-700 to-indigo-700 px-6 py-8 text-white md:px-8">
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
                <p class="mt-3 text-sm text-blue-50 md:text-base">
                    Penulis: <span class="font-semibold text-white">{{ $publication->author }}</span>
                </p>
                @auth
                    <form action="{{ route('bookmarks.toggle') }}" method="POST" class="mt-5">
                        @csrf
                        <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                        <button type="submit" class="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1 text-sm font-semibold text-white hover:bg-white/25">
                            {{ $publication->bookmarks()->where('user_id', auth()->id())->exists() ? '★ Tersimpan di daftar bacaan' : '☆ Simpan ke daftar bacaan' }}
                        </button>
                    </form>
                @endauth
            </div>

            <div class="grid gap-4 border-b border-slate-200 bg-slate-50/80 p-6 md:grid-cols-3 md:p-8">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-[11px] uppercase tracking-[0.22em] font-bold text-slate-400">Diterbitkan di</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $publication->container ? $publication->container->name : 'Tidak diketahui' }}</p>
                </div>
                @if($publication->container && $publication->container->identifier)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-[11px] uppercase tracking-[0.22em] font-bold text-slate-400">Identifier</p>
                        <p class="mt-2 text-sm font-mono font-semibold text-slate-800">{{ $publication->container->identifier }}</p>
                    </div>
                @endif
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-[11px] uppercase tracking-[0.22em] font-bold text-slate-400">Status Akses</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $publication->files->contains(fn ($file) => $file->isRestricted()) ? 'Terdapat file terbatas' : 'Semua dokumen publik' }}</p>
                </div>
            </div>

            <div class="space-y-8 p-6 md:p-8">
                <div>
                    <h2 class="mb-3 text-lg font-bold text-slate-800">Abstrak</h2>
                    <p class="leading-relaxed text-slate-700 whitespace-pre-line">
                        {{ $publication->abstract }}
                    </p>
                </div>

                <div>
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wider text-slate-400">Kata Kunci</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $publication->keywords) as $keyword)
                            <span class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                {{ trim($keyword) }}
                            </span>
                        @endforeach
                    </div>
                </div>

            @if($publication->topics->isNotEmpty())
                <div>
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wider text-slate-400">Topik Repository</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($publication->topics as $topic)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('home', ['search' => $topic->name]) }}" class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                    {{ $topic->name }}
                                </a>
                                @auth
                                    <form action="{{ route('topics.preference', $topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-full border border-slate-300 px-2 py-1 text-[11px] font-semibold text-slate-600 hover:bg-slate-100">
                                            {{ $topic->users()->where('user_id', auth()->id())->exists() ? 'Favorit' : 'Tambah' }}
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 1. DOKUMEN PALING MIRIP (Content-Based) --}}
            @if(isset($similarRecommendations) && $similarRecommendations->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h2 class="text-lg font-bold text-slate-800">1. Dokumen Paling Mirip</h2>
                    <p class="mt-1 text-sm text-slate-500">Berdasarkan tingkat kemiripan teks, judul, dan abstrak (Content-Based).</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach($similarRecommendations as $item)
                            @php $pub = $item->recommendedPublication ?? null; @endphp
                            @if($pub)
                                <a href="{{ route('publications.show', $pub) }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-blue-700">{{ $pub->type }}</p>
                                    <h3 class="mt-2 font-semibold text-slate-900 text-sm line-clamp-2">{{ $pub->title }}</h3>
                                    <p class="mt-1 text-xs text-slate-600">{{ $pub->author }}</p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 2. BACAAN PELENGKAP (Knowledge-Based Overlap) --}}
            @if(isset($complementaryRecommendations) && $complementaryRecommendations->isNotEmpty())
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-5">
                    <h2 class="text-lg font-bold text-indigo-900">2. Bacaan Pelengkap</h2>
                    <p class="mt-1 text-sm text-indigo-700/80">Dokumen relevan yang terhubung melalui hirarki taksonomi topik ilmu.</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach($complementaryRecommendations as $item)
                            @php $pub = $item->recommendedPublication ?? null; @endphp
                            @if($pub)
                                <a href="{{ route('publications.show', $pub) }}" class="rounded-xl border border-indigo-200/80 bg-white p-4 shadow-sm transition hover:border-indigo-400 hover:shadow-md">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-indigo-600">{{ $pub->type }}</p>
                                    <h3 class="mt-2 font-semibold text-slate-900 text-sm line-clamp-2">{{ $pub->title }}</h3>
                                    <p class="mt-1 text-xs text-slate-600">{{ $pub->author }}</p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. KONSEP DASAR (Parent Topics) --}}
            @if(isset($basicConcepts) && $basicConcepts->isNotEmpty())
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5">
                    <h2 class="text-lg font-bold text-emerald-900">3. Konsep Dasar</h2>
                    <p class="mt-1 text-sm text-emerald-700/80">Dokumen referensi dengan cakupan topik induk (parent topic) yang mendasari.</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach($basicConcepts as $pub)
                            <a href="{{ route('publications.show', $pub) }}" class="rounded-xl border border-emerald-200/80 bg-white p-4 shadow-sm transition hover:border-emerald-400 hover:shadow-md">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-600">{{ $pub->type }}</p>
                                <h3 class="mt-2 font-semibold text-slate-900 text-sm line-clamp-2">{{ $pub->title }}</h3>
                                <p class="mt-1 text-xs text-slate-600">{{ $pub->author }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 4. METODE SERUPA (Same Publication Type) --}}
            @if(isset($similarMethods) && $similarMethods->isNotEmpty())
                <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-5">
                    <h2 class="text-lg font-bold text-amber-900">4. Metode Serupa</h2>
                    <p class="mt-1 text-sm text-amber-700/80">Dokumen lain yang menggunakan bentuk atau jenis publikasi sejenis.</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach($similarMethods as $pub)
                            <a href="{{ route('publications.show', $pub) }}" class="rounded-xl border border-amber-200/80 bg-white p-4 shadow-sm transition hover:border-amber-400 hover:shadow-md">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-amber-600">{{ $pub->type }}</p>
                                <h3 class="mt-2 font-semibold text-slate-900 text-sm line-clamp-2">{{ $pub->title }}</h3>
                                <p class="mt-1 text-xs text-slate-600">{{ $pub->author }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 5. BACAAN LANJUTAN (Child Topics) --}}
            @if(isset($advancedReadings) && $advancedReadings->isNotEmpty())
                <div class="rounded-2xl border border-purple-100 bg-purple-50/40 p-5">
                    <h2 class="text-lg font-bold text-purple-900">5. Bacaan Lanjutan</h2>
                    <p class="mt-1 text-sm text-purple-700/80">Dokumen pendalaman dengan cakupan sub-topik lanjutan (child topic).</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach($advancedReadings as $pub)
                            <a href="{{ route('publications.show', $pub) }}" class="rounded-xl border border-purple-200/80 bg-white p-4 shadow-sm transition hover:border-purple-400 hover:shadow-md">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-purple-600">{{ $pub->type }}</p>
                                <h3 class="mt-2 font-semibold text-slate-900 text-sm line-clamp-2">{{ $pub->title }}</h3>
                                <p class="mt-1 text-xs text-slate-600">{{ $pub->author }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Daftar Dokumen</h2>
                    <p class="text-xs text-slate-500">Akses dan izin unduh ditentukan pada setiap bagian.</p>
                </div>

                @if($publication->files->isEmpty())
                    <div class="p-6 bg-slate-50 rounded-lg text-center text-slate-500 text-sm italic border border-dashed border-slate-300">
                        Belum ada file dokumen yang diunggah untuk publikasi ini.
                    </div>
                @else
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
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

        </div>

    </main>

</body>
</html>