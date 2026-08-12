<footer class="mt-16 bg-gradient-to-b from-white to-slate-50 border-t border-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.5fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-gundar-primary to-[#8743ad] text-white shadow-sm">
                        <span class="text-sm font-black uppercase tracking-wider">UG</span>
                    </div>
                    <h3 class="text-xl font-black text-gundar-primary tracking-tight">RepoIlmiah.</h3>
                </div>
                <p class="max-w-sm text-sm leading-relaxed text-slate-500">
                    Pusat repositori digital terintegrasi untuk mengelola dan mendistribusikan publikasi akademik, skripsi, dan riset mahasiswa Universitas Gunadarma secara sistematis.
                </p>
            </div>
            <div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-5">Layanan Pengguna</h3>
                <ul class="space-y-3.5 text-sm font-medium text-slate-500">
                    <li><a href="{{ route('home') }}" class="hover:text-gundar-primary hover:translate-x-1 transition-all inline-block">Pencarian Dokumen</a></li>
                    <li><a href="{{ route('student.login') }}" class="hover:text-gundar-primary hover:translate-x-1 transition-all inline-block">Portal Mahasiswa</a></li>
                    <li><a href="{{ route('bookmarks.index') }}" class="hover:text-gundar-primary hover:translate-x-1 transition-all inline-block">Daftar Bacaan Anda</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-5">Sumber & Bantuan</h3>
                <ul class="space-y-3.5 text-sm font-medium text-slate-500">
                    <li><a href="#" class="hover:text-gundar-primary hover:translate-x-1 transition-all inline-block">Panduan Unggah Mandiri</a></li>
                    <li><a href="#" class="hover:text-gundar-primary hover:translate-x-1 transition-all inline-block">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-gundar-primary hover:translate-x-1 transition-all inline-block">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-16 border-t border-slate-200/60 pt-8 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs font-medium text-slate-400">
                &copy; {{ date('Y') }} Universitas Gunadarma. Hak cipta dilindungi undang-undang.
            </p>
            <div class="flex gap-4">
                <span class="text-xs font-bold text-slate-300">v2.0.0</span>
            </div>
        </div>
    </div>
</footer>