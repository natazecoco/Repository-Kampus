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
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">← Kembali ke Beranda</a>
                    @guest
                        <a href="{{ route('student.login') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Masuk Mahasiswa</a>
                    @endguest
                    @auth
                        <span class="text-sm font-medium text-slate-700">Halo, {{ Auth::user()->name }}</span>
                        <form action="{{ route('student.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Logout</button>
                        </form>
                    @endauth
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

            <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                @if($publication->file_path)
                    <a href="{{ asset('storage/' . $publication->file_path) }}" target="_blank" class="inline-flex items-center bg-blue-700 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-800 transition shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Buka / Unduh PDF Full Text
                    </a>
                @else
                    <span class="text-red-500 italic text-sm">Berkas PDF fisik belum tersedia untuk dokumen ini.</span>
                @endif
            </div>

        </div>

        <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-2">Dokumen Terkait (Rekomendasi AI)</h2>
            <p class="text-sm text-slate-500 mb-6">Sistem menemukan artikel terkait berdasarkan kedekatan teks Abstrak, Judul, dan Kata Kunci menggunakan Content-Based Filtering.</p>
            
            @if($recommendations->isEmpty())
                <div class="p-6 bg-slate-50 rounded-lg text-center text-slate-400 italic text-sm border border-dashed">
                    Sistem belum menemukan dokumen lain yang relevan dengan topik ini.
                </div>
            @else
                <div class="space-y-4">
                    {{-- PERUBAHAN ADA DI BLOK FOREACH INI --}}
                    @foreach($recommendations as $rec)
                        <a href="{{ route('publications.show', $rec->recommendedPublication->id) }}" class="block p-5 border border-slate-200 rounded-lg hover:border-blue-500 hover:shadow-md transition bg-slate-50 hover:bg-white">
                            <h3 class="text-md font-bold text-slate-800 mb-1">{{ $rec->recommendedPublication->title }}</h3>
                            <p class="text-sm text-slate-600 mb-3">{{ $rec->recommendedPublication->author }} ({{ $rec->recommendedPublication->year }})</p>
                            <div class="flex justify-between items-center text-xs">
                                <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded font-bold tracking-wide">
                                    {{-- Kolom similarity_score tetap langsung dari $rec karena ada di tabel recommendations --}}
                                    Skor Relevansi: {{ round($rec->similarity_score * 100, 2) }}%
                                </span>
                                <span class="text-slate-500 font-medium">Baca Selengkapnya →</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

</body>
</html>