<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Topik - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">
    <div class="mx-auto max-w-5xl p-6">
        <div class="mb-6 rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Impor Topik</h1>
                    <p class="text-sm text-slate-500 mt-1">Unggah data topik menggunakan CSV. Kolom yang didukung: <strong>name, slug, parent, is_active, sort_order</strong>.</p>
                </div>
                <a href="{{ url()->previous() }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali</a>
            </div>

            @if(session('status'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.topics.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-6 xl:grid-cols-[1.4fr,0.6fr]">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">CSV Text</label>
                        <textarea name="csv" rows="14" class="w-full rounded-3xl border border-slate-300 bg-slate-50 p-4 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('csv') }}</textarea>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Upload File CSV</label>
                            <input type="file" name="csv_file" accept=".csv,text/csv" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none file:mr-4 file:rounded-full file:border-0 file:bg-blue-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-800" />
                            <p class="mt-3 text-sm text-slate-500">Gunakan file CSV yang berisi kolom sama seperti contoh. File akan diproses jika teks CSV kosong.</p>
                        </div>
                        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                            <p class="font-semibold">Petunjuk:</p>
                            <p>Isi salah satu dari teks CSV atau upload file CSV. Jika keduanya diisi, file akan diutamakan.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-slate-500">
                        Contoh baris header: <code>name,slug,parent,is_active,sort_order</code>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-3xl bg-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">Proses Impor</button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-3">Panduan CSV</h2>
            <p class="text-sm text-slate-600 mb-4">Setiap baris boleh memuat kolom berikut:</p>
            <pre class="whitespace-pre-wrap rounded-2xl bg-slate-900/5 p-4 text-sm text-slate-700"><code>name,slug,parent,is_active,sort_order
Kecerdasan Buatan,ai,,1,10
Sistem Informasi,sistem-informasi,Teknologi Informasi,1,20</code></pre>
            <p class="text-sm text-slate-600">Kolom <strong>parent</strong> dapat berisi slug atau nama topik induk yang sudah ada. Jika belum ada, parent tidak akan disetel.</p>
        </div>
    </div>
</body>
</html>
