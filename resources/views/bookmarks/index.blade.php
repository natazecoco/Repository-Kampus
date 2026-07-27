<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Bacaan Saya - RepoIlmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f7f2eb] font-sans text-slate-800">
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(118,58,151,0.16),_transparent_24%),radial-gradient(circle_at_bottom_right,_rgba(248,151,35,0.16),_transparent_30%)]"></div>
        <nav class="sticky top-0 z-40 border-b border-[#eadfe8] bg-[#f7f2eb]/90 backdrop-blur-xl">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-2xl font-black text-[#911B62]">Repo<span class="text-slate-800">Ilmiah</span></a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-600 transition hover:text-[#763a97]">← Kembali ke Beranda</a>
                    <form action="{{ route('student.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-slate-600 transition hover:text-[#763a97]">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
            <section class="rounded-[32px] border border-[#eadfe8] bg-white p-6 shadow-[0_20px_60px_rgba(17,17,17,0.06)] sm:p-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.28em] text-[#763a97]">Reading Shelf</p>
                        <h1 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Daftar bacaan yang kamu simpan.</h1>
                        <p class="mt-3 max-w-2xl text-slate-600">Kumpulan publikasi yang kamu pilih untuk dibaca nanti, dikumpulkan dengan nuansa yang lebih santai dan visual.</p>
                    </div>
                    <div class="rounded-full border border-[#eadfe8] bg-[#fcf7fb] px-4 py-2 text-sm font-semibold text-[#911B62]">
                        {{ $bookmarks->count() }} item tersimpan
                    </div>
                </div>

                @if($bookmarks->isEmpty())
                    <div class="mt-8 rounded-[24px] border border-dashed border-[#d9cddb] bg-[#fcf9fb] p-10 text-center">
                        <p class="text-slate-600">Belum ada publikasi yang kamu simpan.</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-flex text-sm font-semibold text-[#763a97] hover:text-[#5e3078]">Jelajahi koleksi repositori</a>
                    </div>
                @else
                    <div class="mt-8 grid gap-6 md:grid-cols-2">
                        @foreach($bookmarks as $bookmark)
                            @php $publication = $bookmark->publication; @endphp
                            @if($publication)
                                <article class="rounded-[24px] border border-[#eadfe8] bg-[#fffdfb] p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-[0_12px_32px_rgba(118,58,151,0.10)]">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="rounded-full bg-[#fdf6fb] px-3 py-1 text-[11px] font-black uppercase tracking-[0.24em] text-[#763a97]">{{ $publication->type }}</span>
                                        <form action="{{ route('bookmarks.toggle') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                                            <button type="submit" class="text-sm font-semibold text-rose-600 transition hover:text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                    <h2 class="mt-4 text-xl font-black text-slate-900">
                                        <a href="{{ route('publications.show', $publication) }}" class="hover:text-[#763a97]">{{ $publication->title }}</a>
                                    </h2>
                                    <p class="mt-2 text-sm text-slate-600">{{ $publication->author }}</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($publication->topics->take(3) as $topic)
                                            <span class="rounded-full bg-[#f7f2eb] px-2.5 py-1 text-[11px] font-semibold text-slate-700">{{ $topic->name }}</span>
                                        @endforeach
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if($preferredTopics->isNotEmpty())
                    <div class="mt-10 rounded-[24px] border border-[#eadfe8] bg-[#fcf7fb] p-6">
                        <h2 class="text-xl font-black text-slate-900">Topik Favorit Kamu</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($preferredTopics as $preference)
                                @if($preference->topic)
                                    <span class="rounded-full border border-[#eadfe8] bg-white px-3 py-1 text-sm font-semibold text-[#763a97]">{{ $preference->topic->name }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        </main>
    </div>
</body>
</html>
