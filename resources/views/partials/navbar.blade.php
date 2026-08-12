<div class="fixed left-0 right-0 top-4 z-50 px-4" x-data="{ menuOpen: false }">
    <!-- Navbar Glassmorphism -->
    <nav class="mx-auto flex h-14 sm:h-16 max-w-6xl items-center justify-between gap-2 sm:gap-4 rounded-full border border-white/60 bg-white/70 px-4 sm:px-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] backdrop-blur-xl transition-all">
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-gradient-to-br from-gundar-primary to-[#8743ad] text-white shadow-sm group-hover:scale-105 transition-transform">
                    <span class="text-[10px] sm:text-sm font-black uppercase tracking-wider">UG</span>
                </div>
                <!-- Sembunyikan tulisan RepoIlmiah di layar HP yang sangat kecil -->
                <span class="text-lg sm:text-xl font-black tracking-tight text-gundar-primary hidden md:block">Repo<span class="text-gundar-dark transition-colors group-hover:text-gundar-primary">Ilmiah.</span></span>
            </a>
        </div>

        <div class="hidden items-center gap-3 sm:flex sm:gap-6">
            <a href="{{ route('home') }}" class="text-sm font-bold transition-colors {{ request()->routeIs('home') ? 'text-gundar-primary' : 'text-slate-500 hover:text-gundar-primary' }}">Beranda</a>

            <a href="{{ route('search') }}" class="text-sm font-bold transition-colors {{ request()->routeIs('search', 'topic.show') ? 'text-gundar-primary' : 'text-slate-500 hover:text-gundar-primary' }}">Eksplorasi</a>
            
            <a href="{{ route('bookmarks.index') }}" class="group flex items-center gap-1.5 text-sm font-bold transition-colors {{ request()->routeIs('bookmarks.index') ? 'text-amber-600' : 'text-slate-500 hover:text-amber-500' }}">
                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
                Koleksi Saya
            </a>
            
            <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
            
            @guest
                <a href="{{ route('student.login') }}" class="text-xs sm:text-sm font-bold text-slate-600 hover:text-gundar-primary transition-colors">Masuk</a>
                <a href="{{ route('student.register') }}" class="rounded-full bg-slate-900 px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-bold text-white shadow-md transition-all hover:bg-gundar-primary hover:shadow-lg hover:-translate-y-0.5">Daftar</a>
            @endguest
            @auth
                <a href="{{ route('student.dashboard') }}" class="text-xs sm:text-sm font-bold text-slate-700 hover:text-gundar-primary flex items-center gap-2">
                    <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-gundar-primary/10 text-gundar-primary flex items-center justify-center text-[10px] sm:text-xs uppercase">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    <!-- Sembunyikan nama di HP biar tidak menabrak tombol keluar -->
                    <span class="hidden sm:inline">{{ explode(' ', Auth::user()->name)[0] }}</span>
                </a>
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-slate-400 hover:text-rose-500 transition-colors p-1.5 sm:p-2" title="Keluar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    </button>
                </form>
            @endauth
        </div>

        <button type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white/80 text-slate-600 shadow-sm sm:hidden" title="Buka menu">
            <svg x-show="!menuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            <svg x-cloak x-show="menuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" /></svg>
        </button>
    </nav>

    <div x-cloak x-show="menuOpen" x-transition @click.outside="menuOpen = false" class="mx-auto mt-3 max-w-6xl rounded-2xl border border-white/70 bg-white/95 p-3 shadow-lg backdrop-blur-xl sm:hidden">
        <a href="{{ route('home') }}" class="block rounded-xl px-3 py-2.5 text-sm font-bold {{ request()->routeIs('home') ? 'bg-gundar-primary/10 text-gundar-primary' : 'text-slate-700' }}">Beranda</a>
        <a href="{{ route('search') }}" class="block rounded-xl px-3 py-2.5 text-sm font-bold {{ request()->routeIs('search', 'topic.show') ? 'bg-gundar-primary/10 text-gundar-primary' : 'text-slate-700' }}">Eksplorasi</a>
        <a href="{{ route('bookmarks.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-bold {{ request()->routeIs('bookmarks.index') ? 'bg-amber-50 text-amber-700' : 'text-slate-700' }}">Koleksi Saya</a>
        @guest
            <div class="mt-2 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                <a href="{{ route('student.login') }}" class="rounded-xl px-3 py-2.5 text-center text-sm font-bold text-slate-600">Masuk</a>
                <a href="{{ route('student.register') }}" class="rounded-xl bg-slate-900 px-3 py-2.5 text-center text-sm font-bold text-white">Daftar</a>
            </div>
        @endguest
    </div>
</div>