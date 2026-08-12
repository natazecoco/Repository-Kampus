@extends('layouts.app')

@section('content')
<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">
    <div class="w-full max-w-xl rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <!-- Aksen Header Kotak -->
        <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>

        <!-- Ikon dan Judul -->
        <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-2 text-xs font-bold text-slate-400 transition hover:text-gundar-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18" /></svg>
            Kembali ke repository
        </a>

        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gundar-primary/10 text-gundar-primary shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z"></path></svg>
            </div>
            <h1 class="text-2xl font-black text-gundar-dark">Daftar Mahasiswa</h1>
            <p class="mt-2 text-sm text-slate-500 font-medium">Buat akun untuk mulai menyimpan publikasi favorit Anda.</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/80 backdrop-blur-sm p-4 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <p class="text-sm font-bold text-rose-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('student.register.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
                @error('name') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
                    @error('email') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="npm" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">NPM</label>
                    <input id="npm" name="npm" type="text" inputmode="numeric" maxlength="8" value="{{ old('npm') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" placeholder="8 digit angka" required>
                    @error('npm') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 pr-12 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-gundar-primary" title="Tampilkan password">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                    </div>
                    @error('password') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 pr-12 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-gundar-primary" title="Tampilkan password">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                    </div>
                    @error('password_confirmation') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full rounded-full bg-gundar-dark px-8 py-3.5 text-sm font-bold text-white transition hover:bg-gundar-primary shadow-md hover:shadow-lg hover:-translate-y-0.5">
                    Daftar Akun
                </button>
            </div>
        </form>

        <p class="mt-8 border-t border-slate-100 pt-6 text-center text-sm font-medium text-slate-500">
            Sudah punya akun?
            <a href="{{ route('student.login') }}" class="text-gundar-primary font-bold transition hover:text-gundar-dark">Masuk di sini</a>
        </p>
    </div>
</div>
<script>
    function togglePassword(id, button) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.title = input.type === 'password' ? 'Tampilkan password' : 'Sembunyikan password';
    }
</script>
@endsection