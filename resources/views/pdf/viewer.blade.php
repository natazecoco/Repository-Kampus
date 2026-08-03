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
    
    <!-- Konfigurasi warna khusus agar Ungu Gunadarma terbaca di halaman ini -->
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

        <!-- Tengah: Indikator Info Dokumen (Continuous Scroll) -->
        <div class="flex items-center justify-center gap-2 shrink-0 w-1/3">
            <div class="flex items-center rounded-lg bg-black/20 px-4 py-1.5 text-xs font-medium tracking-wide text-slate-300 border border-white/10 shadow-inner">
                <span class="text-white font-bold mr-1.5">📄 Mode Scroll</span> 
                <span class="text-slate-400">&bull;</span>
                <span id="page-count-label" class="ml-1.5 text-slate-300">Memuat...</span>
            </div>
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
        
        <!-- SIDEBAR: DAFTAR BAGIAN (Struktur Dokumen) -->
        <nav class="hidden w-72 shrink-0 flex-col border-r border-slate-200 bg-white lg:flex z-10 shadow-[4px_0_24px_rgba(0,0,0,0.02)] relative">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-[11px] font-black uppercase tracking-widest text-slate-500">Struktur Dokumen</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-1 no-scrollbar">
                @foreach($publication->files as $documentFile)
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

        <!-- KANVAS RENDER PDF (Mode Continuous Scroll) -->
        <div id="pdf-scroll-area" class="relative flex-1 overflow-auto bg-slate-200/80 flex flex-col items-center p-4 sm:p-8">
            
            <!-- Loading Indicator Minimalis -->
            <div id="pdf-loader" class="flex flex-col items-center justify-center my-auto py-12 text-slate-500">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-gundar-primary border-t-transparent mb-3"></div>
                <p class="text-xs font-bold tracking-wider uppercase">Memuat Dokumen PDF...</p>
            </div>

            <!-- Kontainer Utama Tempat Halaman-Halaman PDF Disusun -->
            <div id="pdf-pages-container" class="flex flex-col items-center gap-6 pb-12 w-full"></div>
            
        </div>
    </main>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        const url = @json(route('document.stream', $file));
        const scrollArea = document.getElementById('pdf-scroll-area');
        const pagesContainer = document.getElementById('pdf-pages-container');
        const loader = document.getElementById('pdf-loader');
        
        let pdfDocument = null;
        let currentScale = 1.5; // Skala default 150%
        const isWatermarked = @json($isWatermarked);
        const userName = @json($currentUser->name ?? 'User');
        const userNpm = @json($currentUser->npm ?? 'NPM');
        const watermarkDate = @json(now()->format('d M Y'));

        function updateZoomLabel() {
            document.getElementById('zoom-label').textContent = Math.round(currentScale * 100) + '%';
        }

        // Fungsi utama render semua halaman secara berurutan (Scroll Mode)
        async function renderAllPages() {
            if (!pdfDocument) return;
            
            // Tampilkan loader saat memrender ulang
            loader.style.display = 'flex';
            pagesContainer.innerHTML = '';
            
            const totalPages = pdfDocument.numPages;
            document.getElementById('page-count-label').textContent = `${totalPages} Halaman`;

            // Loop untuk membuat kanvas tiap halaman
            for (let num = 1; num <= totalPages; num++) {
                const page = await pdfDocument.getPage(num);
                const viewport = page.getViewport({ scale: currentScale });

                // Wrapper per halaman supaya ada shadow dan support watermark per page
                const pageWrapper = document.createElement('div');
                pageWrapper.className = 'relative flex-shrink-0 bg-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] ring-1 ring-slate-900/5 overflow-hidden';
                pageWrapper.style.width = `${viewport.width}px`;
                pageWrapper.style.height = `${viewport.height}px`;

                // Element Canvas
                const canvas = document.createElement('canvas');
                canvas.className = 'block pointer-events-none';
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const context = canvas.getContext('2d');
                pageWrapper.appendChild(canvas);

                // Tambahkan Watermark jika dokumen dibatasi
                if (isWatermarked) {
                    const watermark = document.createElement('div');
                    // z-20 untuk di atas kanvas, pointer-events-none agar tidak mengganggu klik/scroll
                    watermark.className = 'pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden z-20';
                    watermark.innerHTML = `
                        <!-- Menggunakan opacity-20 agar sangat transparan dan mix-blend-multiply agar menyatu dengan tinta PDF -->
                        <div class="-rotate-[35deg] select-none text-center opacity-20 mix-blend-multiply w-full px-10">
                            <p class="text-4xl sm:text-5xl font-black tracking-widest text-slate-600 uppercase leading-tight">
                                ${userName} <br>
                                <span class="text-xl sm:text-2xl text-slate-600 font-bold">${userNpm} &bull; ${watermarkDate}</span>
                            </p>
                        </div>
                    `;
                    pageWrapper.appendChild(watermark);
                }

                pagesContainer.appendChild(pageWrapper);

                // Render halaman ke dalam canvas-nya masing-masing
                await page.render({ canvasContext: context, viewport }).promise;
            }

            // Sembunyikan loader setelah selesai
            loader.style.display = 'none';
        }

        // Kontrol Zoom
        document.getElementById('zoom-in').addEventListener('click', () => {
            if (currentScale < 3.0) { // Max zoom 300%
                currentScale += 0.25;
                updateZoomLabel();
                renderAllPages().then(() => {
                    scrollArea.scrollTop = 0; // Balikkan scroll ke atas dengan mulus saat ganti zoom
                });
            }
        });

        document.getElementById('zoom-out').addEventListener('click', () => {
            if (currentScale > 0.5) { // Min zoom 50%
                currentScale -= 0.25;
                updateZoomLabel();
                renderAllPages().then(() => {
                    scrollArea.scrollTop = 0;
                });
            }
        });

        // Inisialisasi Memuat PDF
        pdfjsLib.getDocument({ url, disableAutoFetch: true, disableStream: true }).promise
            .then((pdf) => {
                pdfDocument = pdf;
                renderAllPages();
            })
            .catch(() => {
                loader.style.display = 'none';
                alert('Gagal memuat dokumen. Periksa akses atau ketersediaan berkas.');
            });

        // Proteksi Keyboard (Mencegah Shortcut Save/Print)
        document.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && ['s', 'p'].includes(event.key.toLowerCase())) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>