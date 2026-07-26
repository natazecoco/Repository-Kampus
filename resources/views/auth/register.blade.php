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
    <title>Register - Repository Publikasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 my-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Buat Akun</h1>
            <p class="text-gray-500 mt-2">Daftar untuk mengakses publikasi</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
 
            <div>
                <label for="npm" class="block text-sm font-medium text-gray-700">NPM</label>
                <input type="text" name="npm" id="npm" value="{{ old('npm') }}" placeholder="Contoh: 12345678" maxlength="8" inputmode="numeric" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">NPM digunakan sebagai identitas login mahasiswa dan wajib 8 digit angka.</p>
                @error('npm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required minlength="8"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit" 
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Daftar Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Login di sini</a>
        </p>
    </div>
</body>
</html>
