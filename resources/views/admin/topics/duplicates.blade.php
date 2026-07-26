<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicate Detection - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-slate-800">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Deteksi Topik Duplikat</h1>
                    <p class="text-sm text-slate-500 mt-1">Tinjau pasangan topik yang memiliki nama serupa sehingga bisa dijadikan kandidat penggabungan.</p>
                </div>
                <a href="{{ url()->previous() }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali</a>
            </div>

            @if(empty($pairs))
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-slate-600">Tidak ada pasangan topik serupa dengan kecocokan tinggi saat ini.</div>
            @else
                <div class="mb-4 text-sm text-slate-600">Menemukan {{ count($pairs) }} pasangan topik yang kemungkinan serupa.</div>
                <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-100 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Topik A</th>
                                <th class="px-4 py-3">Topik B</th>
                                <th class="px-4 py-3">Skor Kesamaan</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($pairs as $pair)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-slate-700">{{ $pair['topic']->name }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-700">{{ $pair['candidate']->name }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-700">{{ $pair['score'] }}%</td>
                                    <td class="px-4 py-4 text-sm text-slate-700 space-x-2">
                                        <a href="{{ route('filament.admin.resources.topics.edit', ['record' => $pair['topic']->id]) }}" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">Edit A</a>
                                        <a href="{{ route('filament.admin.resources.topics.edit', ['record' => $pair['candidate']->id]) }}" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">Edit B</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 mb-3">Cara Menggunakan</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm text-slate-600">
                <li>Gunakan daftar ini untuk menemukan nama topik yang sangat mirip.</li>
                <li>Buka halaman pengelolaan topik, lalu pilih target yang benar untuk digabung.</li>
                <li>Jika Anda yakin topik tersebut duplikat, Anda bisa menggabungkannya dari halaman admin Topik.</li>
            </ul>
        </div>
    </div>
</body>
</html>
