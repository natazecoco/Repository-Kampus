@extends('layouts.app')

@section('content')
<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">
    <div class="w-full max-w-xl rounded-[24px] border border-slate-200 bg-white p-8 sm:p-10 shadow-sm relative overflow-hidden">
        <!-- Aksen Header Kotak -->
        <div class="absolute left-0 top-0 w-full h-1.5 bg-gradient-to-r from-gundar-primary to-gundar-accent"></div>

        <!-- Ikon dan Judul -->
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gundar-primary/10 text-gundar-primary">
                <!-- Ikon Add User -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h1 class="text-2xl font-black text-gundar-dark">Daftar Mahasiswa</h1>
            <p class="mt-2 text-sm text-slate-500">Buat akun untuk mulai menyimpan publikasi favorit Anda.</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 flex items-start gap-3 shadow-sm">
                <span class="text-rose-500 text-lg leading-none">⚠️</span>
                <p class="text-sm font-bold text-rose-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('student.register.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
                </div>
                <div>
                    <label for="npm" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">NPM</label>
                    <input id="npm" name="npm" type="text" inputmode="numeric" maxlength="8" value="{{ old('npm') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" placeholder="8 digit angka" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Password</label>
                    <input id="password" name="password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 transition focus:border-gundar-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-gundar-primary" required>
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
@endsection