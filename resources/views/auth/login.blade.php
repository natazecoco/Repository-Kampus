@extends('layouts.app')

@section('content')
<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">
    <div class="w-full max-w-md rounded-[32px] border border-white/60 bg-white/80 backdrop-blur-xl p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <!-- Aksen Header Kotak -->
        <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>

        <!-- Ikon dan Judul -->
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gundar-primary/10 text-gundar-primary shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
            </div>
            <h1 class="text-2xl font-black text-gundar-dark">Login Mahasiswa</h1>
            <p class="mt-2 text-sm text-slate-500 font-medium">Masuk dengan NPM dan password akun Anda.</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/80 backdrop-blur-sm p-4 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <p class="text-sm font-bold text-rose-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('student.login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="npm" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">NPM</label>
                <input id="npm" name="npm" type="text" inputmode="numeric" maxlength="8" value="{{ old('npm') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" placeholder="Contoh: 20241001" required>
            </div>

            <div>
                <label for="password" class="mb-2 block text-[11px] font-black uppercase tracking-wider text-slate-400">Password</label>
                <input id="password" name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3.5 text-sm font-semibold text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-gundar-primary/20" required>
            </div>

            <div class="flex items-center justify-between text-sm pt-1">
                <label class="flex items-center gap-2 text-slate-600 font-semibold cursor-pointer group">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-gundar-primary focus:ring-gundar-primary w-4 h-4">
                    <span class="group-hover:text-gundar-dark transition">Ingat saya</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-full bg-gundar-dark px-8 py-3.5 text-sm font-bold text-white transition hover:bg-gundar-primary shadow-md hover:shadow-lg hover:-translate-y-0.5">
                    Masuk
                </button>
            </div>
        </form>

        <p class="mt-8 border-t border-slate-100 pt-6 text-center text-sm font-medium text-slate-500">
            Belum punya akun?
            <a href="{{ route('student.register') }}" class="text-gundar-primary font-bold transition hover:text-gundar-dark">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection