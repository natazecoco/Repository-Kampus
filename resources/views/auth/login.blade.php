<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa - RepoIlmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.16),_transparent_24%),linear-gradient(135deg,_#f8fbff_0%,_#eef4ff_100%)] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white/90 p-8 shadow-[0_20px_60px_-20px_rgba(15,23,42,0.35)] backdrop-blur">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-2xl font-black text-white">R</div>
            <h1 class="text-2xl font-extrabold text-slate-900">Login Mahasiswa</h1>
            <p class="mt-2 text-sm text-slate-500">Masuk dengan NPM dan password akun kamu.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('student.login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="npm" class="block text-sm font-semibold text-slate-700 mb-1">NPM</label>
                <input id="npm" name="npm" type="text" inputmode="numeric" maxlength="8" value="{{ old('npm') }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 20241001" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input id="password" name="password" type="password" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    Ingat saya
                </label>
                <a href="{{ route('home') }}" class="text-blue-600 font-medium hover:underline">Kembali</a>
            </div>

            <button type="submit" class="w-full bg-blue-700 text-white px-4 py-3 rounded-lg font-bold hover:bg-blue-800 transition">Masuk</button>
        </form>

        <p class="mt-5 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('student.register') }}" class="text-blue-600 font-semibold hover:underline">Daftar mahasiswa</a>
        </p>
    </div>
</body>
</html>
