<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Mahasiswa - RepoIlmiah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-extrabold text-slate-900">Daftar Mahasiswa</h1>
            <p class="text-sm text-slate-500 mt-2">Buat akun mahasiswa dengan NPM sebagai identitas utama.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('student.register.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="npm" class="block text-sm font-semibold text-slate-700 mb-1">NPM</label>
                    <input id="npm" name="npm" type="text" inputmode="numeric" maxlength="8" value="{{ old('npm') }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="8 digit" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-700 text-white px-4 py-3 rounded-lg font-bold hover:bg-blue-800 transition">Daftar</button>
        </form>

        <p class="mt-5 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('student.login') }}" class="text-blue-600 font-semibold hover:underline">Masuk sekarang</a>
        </p>
    </div>
</body>
</html>
