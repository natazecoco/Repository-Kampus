<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - RepoIlmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">
    <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-700">Repo<span class="text-slate-800">Ilmiah</span></a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('bookmarks.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">Daftar Bacaan Saya</a>
                    <form action="{{ route('student.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-12">
        <div class="mb-8 rounded-3xl bg-gradient-to-r from-blue-700 to-indigo-700 p-8 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-blue-100">Area Mahasiswa</p>
            <h1 class="mt-2 text-3xl font-black">Profil Saya</h1>
            <p class="mt-2 max-w-2xl text-blue-100">Kelola akun, lihat bookmark, dan pantau topik yang paling kamu suka.</p>
        </div>

        @if(session('message'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Informasi Akun</h2>
                <form action="{{ route('student.profile.update') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                        <input name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Password Baru</label>
                            <input name="password" type="password" class="w-full rounded-lg border border-slate-300 px-4 py-3" placeholder="Kosongkan jika tidak ingin ubah">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                            <input name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-300 px-4 py-3">
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-3 font-semibold text-white hover:bg-blue-800">Simpan Perubahan</button>
                </form>
            </section>

            <section class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Bookmark Saya</h2>
                        <a href="{{ route('bookmarks.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat semua</a>
                    </div>
                    @if($bookmarks->isEmpty())
                        <p class="mt-4 text-sm text-slate-500">Belum ada publikasi yang disimpan.</p>
                    @else
                        <ul class="mt-4 space-y-3">
                            @foreach($bookmarks->take(3) as $bookmark)
                                @if($bookmark->publication)
                                    <li class="rounded-lg border border-slate-200 p-3">
                                        <a href="{{ route('publications.show', $bookmark->publication) }}" class="font-semibold text-slate-800 hover:text-blue-700">{{ $bookmark->publication->title }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">Topik Favorit</h2>
                    @if($preferredTopics->isEmpty())
                        <p class="mt-4 text-sm text-slate-500">Belum ada topik favorit yang dipilih.</p>
                    @else
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($preferredTopics as $preference)
                                @if($preference->topic)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">{{ $preference->topic->name }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </main>
</body>
</html>
