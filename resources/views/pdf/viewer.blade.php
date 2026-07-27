@php
    $currentUser = auth()->user();
    $isWatermarked = $file->visibility !== 'public' && $currentUser;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->title }} - {{ $publication->title }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FIX: Konfigurasi warna khusus agar Ungu Gunadarma terbaca di halaman ini -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'gundar-primary': '#763a97', 
                        'gundar-dark': '#4b2163',    
                        'gundar-accent': '#911B62'   
                    }
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Custom scrollbar untuk area dokumen */
        #pdf-scroll-area::-webkit-scrollbar { width: 8px; height: 8px; }
        #pdf-scroll-area::-webkit-scrollbar-track { background: transparent; }
        #pdf-scroll-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        #pdf-scroll-area::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-[#f8fbff] font-sans text-slate-800 overflow-hidden h-screen flex flex-col selection:bg-gundar-primary/30" oncontextmenu="return false;">
    
    <!-- TOOLBAR ADVANCED (Ungu Gelap, Teks Putih) -->
    <header class="relative z-40 flex h-16 shrink-0 items-center justify-between bg-gundar-dark px-4 sm:px-6 shadow-md text-white border-b-[3px] border-gundar-accent">
        
        <!-- Kiri: Tombol Kembali & Judul -->
        <div class="flex items-center gap-4 overflow-hidden pr-4 w-1/3">
            <a href="{{ route('publications.show', $publication) }}" class="shrink-0 flex items-center justify-center h-9 w-9 rounded-full bg-white/10 hover:bg-gundar-primary transition text-slate-200 hover:text-white" title="Kembali ke Detail">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div class="hidden sm:block truncate">
                <h1 class="truncate text-sm font-bold tracking-wide text-white" title="{{ $publication->title }}">
                    {{ $publication->title }}
                </h1>
                <p class="text-[10px] text-slate-300 tracking-wider uppercase mt-0.5">{{ $file->title }}</p>
            </div>
        </div>

        <!-- Tengah: Kontrol Navigasi Halaman -->
        <div class="flex items-center justify-center gap-1 sm:gap-2 shrink-0 w-1/3">
            <button id="prev-page" type="button" class="flex h-9 w-9 items-center justify-center rounded-lg hover:bg-white/10 text-slate-200 transition disabled:opacity-30 disabled:cursor-not-allowed" title="Halaman Sebelumnya">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            
            <div class="flex items-center rounded-lg bg-black/20 px-3 py-1.5 text-xs font-medium tracking-widest text-slate-300 border border-white/10 shadow-inner">
                <span id="page-num" class="text-white font-bold w-4 text-center">-</span> 
                <span class="mx-1 text-slate-400">/</span> 
                <span id="page-count" class="text-slate-300 w-4 text-center">-</span>
            </div>
            
            <button id="next-page" type="button" class="flex h-9 w-9 items-center justify-center rounded-lg hover:bg-white/10 text-slate-200 transition disabled:opacity-30 disabled:cursor-not-allowed" title="Halaman Selanjutnya">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>

        <!-- Kanan: Kontrol Zoom -->
        <div class="flex items-center justify-end gap-1 w-1/3">
            <div class="flex items-center rounded-lg bg-black/20 p-1 border border-white/10 shadow-inner">
                <button id="zoom-out" type="button" class="flex h-7 w-7 items-center justify-center rounded hover:bg-white/10 text-slate-200 transition" title="Perkecil">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                </button>
                <span id="zoom-label" class="text-[11px] font-bold text-white w-12 text-center">150%</span>
                <button id="zoom-in" type="button" class="flex h-7 w-7 items-center justify-center rounded hover:bg-white/10 text-slate-200 transition" title="Perbesar">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="flex flex-1 overflow-hidden">
        
        <!-- SIDEBAR: DAFTAR BAGIAN (Tema Selaras) -->
        <nav class="hidden w-72 shrink-0 flex-col border-r border-slate-200 bg-white lg:flex z-10 shadow-[4px_0_24px_rgba(0,0,0,0.02)] relative">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-[11px] font-black uppercase tracking-widest text-slate-500">Struktur Dokumen</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-1 no-scrollbar">
                @foreach($files as $documentFile)
                    <a href="{{ route('publications.viewer', ['publication' => $publication, 'file' => $documentFile]) }}" 
                       class="block rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ $documentFile->is($file) ? 'bg-gundar-primary/10 text-gundar-primary shadow-sm border border-gundar-primary/20' : 'text-slate-600 hover:bg-slate-50 hover:text-gundar-dark' }}">
                        <div class="flex items-start gap-3">
                            <svg class="h-4 w-4 mt-0.5 shrink-0 {{ $documentFile->is($file) ? 'text-gundar-primary' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="leading-snug">{{ $documentFile->title }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </nav>

        <!-- KANVAS RENDER PDF (Dengan area scroll) -->
        <div id="pdf-scroll-area" class="relative flex-1 overflow-auto bg-slate-200/80 flex justify-center p-4 sm:p-8">
            
            <!-- Wrapper untuk shadow yang lebih realistis -->
            <div class="relative flex-shrink-0 h-max">
                <canvas id="pdf-render" class="bg-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] ring-1 ring-slate-900/5 pointer-events-none"></canvas>
            </div>
            
            <!-- WATERMARK (Modern Frosted Glass) -->
            @if($isWatermarked)
                <div class="pointer-events-none fixed bottom-8 right-8 z-50 -rotate-6 rounded-2xl border border-white/40 bg-white/60 px-6 py-3 backdrop-blur-md shadow-xl">
                    <p class="text-xs font-black tracking-[0.15em] text-slate-800/80 uppercase">
                        {{ $currentUser->name }} <br>
                        <span class="text-[10px] text-gundar-primary">{{ $currentUser->npm ?? 'NPM' }} &bull; {{ now()->format('d M Y') }}</span>
                    </p>
                </div>
            @endif
            
        </div>
    </main>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        const url = @json(route('document.stream', $file));
        const canvas = document.getElementById('pdf-render');
        const context = canvas.getContext('2d');
        
        let pdfDocument = null;
        let pageNumber = 1;
        let isRendering = false;
        let pendingPage = null;
        let currentScale = 1.5; // Skala default 150%

        function updateZoomLabel() {
            document.getElementById('zoom-label').textContent = Math.round(currentScale * 100) + '%';
        }

        function renderPage(number) {
            isRendering = true;
            pdfDocument.getPage(number).then((page) => {
                const viewport = page.getViewport({ scale: currentScale }); 
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                return page.render({ canvasContext: context, viewport }).promise;
            }).then(() => {
                isRendering = false;
                if (pendingPage !== null) {
                    const nextPage = pendingPage;
                    pendingPage = null;
                    renderPage(nextPage);
                }
            });

            document.getElementById('page-num').textContent = number;
        }

        function queueRenderPage(number) {
            if (isRendering) {
                pendingPage = number;
                return;
            }
            renderPage(number);
        }

        // Navigasi Halaman
        document.getElementById('prev-page').addEventListener('click', () => {
            if (pageNumber > 1) queueRenderPage(--pageNumber);
        });

        document.getElementById('next-page').addEventListener('click', () => {
            if (pdfDocument && pageNumber < pdfDocument.numPages) queueRenderPage(++pageNumber);
        });

        // Kontrol Zoom
        document.getElementById('zoom-in').addEventListener('click', () => {
            if (currentScale < 3.0) { // Max zoom 300%
                currentScale += 0.25;
                updateZoomLabel();
                queueRenderPage(pageNumber);
            }
        });

        document.getElementById('zoom-out').addEventListener('click', () => {
            if (currentScale > 0.5) { // Min zoom 50%
                currentScale -= 0.25;
                updateZoomLabel();
                queueRenderPage(pageNumber);
            }
        });

        // Inisialisasi Dokumen
        pdfjsLib.getDocument({ url, disableAutoFetch: true, disableStream: true }).promise
            .then((pdf) => {
                pdfDocument = pdf;
                window.document.getElementById('page-count').textContent = pdfDocument.numPages;
                renderPage(pageNumber);
            })
            .catch(() => alert('Gagal memuat dokumen. Periksa akses atau ketersediaan berkas.'));

        // Proteksi Keyboard (Mencegah Save/Print)
        document.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && ['s', 'p'].includes(event.key.toLowerCase())) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>