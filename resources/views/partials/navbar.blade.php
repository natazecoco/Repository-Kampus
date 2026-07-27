<div class="fixed top-4 left-0 right-0 z-50 px-4">
    <nav class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 rounded-full border border-slate-200/50 bg-white/80 px-6 shadow-sm backdrop-blur-xl transition-all">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-black tracking-tight text-gundar-primary">Repo<span class="text-gundar-dark">Ilmiah.</span></span>
            </a>
        </div>

        <div class="flex items-center gap-4 sm:gap-6">
            <a href="{{ route('home') }}" class="hidden text-sm font-semibold text-slate-900 sm:block">Beranda</a>
            <a href="{{ route('bookmarks.index') }}" class="hidden text-sm font-medium text-slate-500 hover:text-gundar-primary sm:block">Koleksi Saya</a>
            
            <div class="h-4 w-px bg-slate-300 hidden sm:block"></div>
            
            @guest
                <a href="{{ route('student.login') }}" class="text-sm font-semibold text-slate-600 hover:text-gundar-primary">Masuk</a>
                <a href="{{ route('student.register') }}" class="rounded-full bg-gundar-dark px-5 py-2 text-sm font-semibold text-white transition hover:bg-gundar-primary">Daftar</a>
            @endguest
            @auth
                <a href="{{ route('student.dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-gundar-primary">{{ Auth::user()->name }}</a>
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-slate-400 hover:text-red-500">Logout</button>
                </form>
            @endauth
        </div>
    </nav>
</div>