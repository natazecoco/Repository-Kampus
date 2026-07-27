<footer class="mt-10 border-t border-slate-200/60 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.5fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="inline-flex items-center rounded-full bg-gundar-primary text-white px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.2em]">UG</span>
                    <h3 class="text-lg font-black text-gundar-primary">RepoIlmiah</h3>
                </div>
                <p class="max-w-sm text-sm leading-relaxed text-slate-500">
                    Pusat repositori digital untuk mengelola dan mendistribusikan publikasi akademik, skripsi, dan riset mahasiswa Universitas Gunadarma secara sistematis.
                </p>
            </div>
            <div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-4">Layanan Pengguna</h3>
                <ul class="space-y-3 text-sm text-slate-500">
                    <li><a href="{{ route('home') }}" class="hover:text-gundar-primary transition">Pencarian Dokumen</a></li>
                    <li><a href="{{ route('student.login') }}" class="hover:text-gundar-primary transition">Portal Mahasiswa</a></li>
                    <li><a href="{{ route('bookmarks.index') }}" class="hover:text-gundar-primary transition">Daftar Bacaan Anda</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-4">Manajemen Sistem</h3>
                <ul class="space-y-3 text-sm text-slate-500">
                    <li><a href="/admin" class="hover:text-gundar-accent transition flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Akses Administrator
                    </a></li>
                    <li><a href="#" class="hover:text-gundar-primary transition">Panduan Unggah Mandiri</a></li>
                    <li><a href="#" class="hover:text-gundar-primary transition">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-12 border-t border-slate-100 pt-8 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-slate-400">
                &copy; 2026 Universitas Gunadarma. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </div>
</footer>