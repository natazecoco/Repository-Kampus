@extends('layouts.app')

@section('content')
<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">
    <div class="w-full max-w-md rounded-[24px] border border-slate-200 bg-white p-8 sm:p-10 shadow-sm relative overflow-hidden">
        <!-- Aksen Header Kotak -->
        <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>

        <!-- Ikon dan Judul -->
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gundar-primary/10 text-gundar-primary">
                <!-- Ikon User/Login -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
            </div>
            <h1 class="text-2xl font-black text-gundar-dark">Login Mahasiswa</h1>
            <p class="mt-2 text-sm text-slate-500">Masuk dengan NPM dan password akun Anda.</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 flex items-start gap-3 shadow-sm">
                <span class="text-rose-500 text-lg leading-none">⚠️</span>
                <p class="text-sm font-bold text-rose-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('student.login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="npm" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">NPM</label>
                <input id="npm" name="npm" type="text" inputmode="numeric" maxlength="8" value="{{ old('npm') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" placeholder="Contoh: 20241001" required>
            </div>

            <div>
                <label for="password" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Password</label>
                <input id="password" name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
            </div>

            <div class="flex items-center justify-between text-sm pt-1">
                <label class="flex items-center gap-2 text-slate-600 font-medium cursor-pointer group">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-gundar-primary focus:ring-gundar-primary">
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