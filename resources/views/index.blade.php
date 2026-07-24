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
                <div class="flex items-center gap-4">
                    @guest
                        <a href="{{ route('student.login') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Masuk Mahasiswa</a>
                        <a href="{{ route('student.register') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Daftar</a>
                    @endguest
                    @auth
                        <span class="text-sm font-medium text-slate-700">Halo, {{ Auth::user()->name }}</span>
                        <form action="{{ route('student.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Logout</button>
                        </form>
                    @endauth
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
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col hover:shadow-md transition">
                        
                        <div class="mb-3">
                            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-bold uppercase tracking-wider">
                                {{ $pub->type }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2" title="{{ $pub->title }}">
                            <a href="{{ route('publications.show', $pub->id) }}" class="hover:text-blue-700 transition">
                                {{ $pub->title }}
                            </a>
                        </h3>
                        <p class="text-sm text-slate-600 mb-4 font-medium">{{ $pub->author }} ({{ $pub->year }})</p>
                        
                        <div class="mt-auto pt-4 border-t border-slate-100 text-xs text-slate-500 flex justify-between items-center">
                            <span class="truncate max-w-[70%]">{{ $pub->container ? $pub->container->name : 'Wadah tidak diketahui' }}</span>
                            
                            @if($pub->file_path)
                                <a href="{{ asset('storage/' . $pub->file_path) }}" target="_blank" class="text-blue-600 font-semibold hover:underline flex items-center">
                                    PDF
                                </a>
                            @else
                                <span class="text-red-400 italic">No PDF</span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>