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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        body { background: #f8fafc; margin: 0; color: #0f172a; font-family: system-ui, sans-serif; }
        .toolbar { position: sticky; top: 0; z-index: 20; display: flex; flex-wrap: wrap; align-items: center; gap: 12px; padding: 14px 20px; background: #0f172a; color: white; box-shadow: 0 4px 12px rgb(15 23 42 / 20%); }
        .toolbar strong { margin-right: auto; max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .toolbar button { border: 0; border-radius: 6px; padding: 8px 12px; background: #2563eb; color: white; cursor: pointer; font-weight: 700; }
        .toolbar button:hover { background: #1d4ed8; }
        .layout { display: grid; grid-template-columns: minmax(0, 1fr); min-height: calc(100vh - 68px); }
        .file-nav { border-bottom: 1px solid #e2e8f0; background: white; padding: 14px; }
        .file-nav h2 { margin: 0 0 8px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: #64748b; }
        .file-nav a { display: inline-block; margin: 4px 4px 4px 0; padding: 8px 10px; border-radius: 6px; color: #334155; font-size: 13px; text-decoration: none; }
        .file-nav a:hover, .file-nav a.active { background: #dbeafe; color: #1d4ed8; font-weight: 700; }
        .viewer-shell { display: flex; justify-content: center; padding: 24px; }
        #pdf-render { max-width: 100%; background: white; box-shadow: 0 10px 25px rgb(15 23 42 / 18%); pointer-events: none; }
        .watermark { position: fixed; right: 16px; bottom: 16px; z-index: 30; opacity: .35; transform: rotate(-18deg); background: rgb(255 255 255 / 70%); border: 1px solid rgb(15 23 42 / 15%); border-radius: 999px; padding: 10px 14px; color: #0f172a; font-size: 12px; font-weight: 700; letter-spacing: .05em; pointer-events: none; }
        @media (min-width: 960px) { .layout { grid-template-columns: 280px minmax(0, 1fr); } .file-nav { border-bottom: 0; border-right: 1px solid #e2e8f0; } .file-nav a { display: block; margin: 2px 0; } }
    </style>
</head>
<body oncontextmenu="return false;">
    <div class="toolbar">
        <strong title="{{ $publication->title }}">{{ $publication->title }}</strong>
        <button id="prev-page" type="button">← Sebelumnya</button>
        <span>Halaman <span id="page-num">-</span> / <span id="page-count">-</span></span>
        <button id="next-page" type="button">Selanjutnya →</button>
    </div>

    <main class="layout">
        <nav class="file-nav" aria-label="Bagian dokumen">
            <h2>Bagian yang dapat diakses</h2>
            @foreach($files as $documentFile)
                <a class="{{ $documentFile->is($file) ? 'active' : '' }}" href="{{ route('publications.viewer', ['publication' => $publication, 'file' => $documentFile]) }}">
                    {{ $documentFile->title }}
                </a>
            @endforeach
        </nav>

        <div class="viewer-shell">
            <canvas id="pdf-render"></canvas>
        </div>
    </main>

    @if($isWatermarked)
        <div class="watermark">{{ $currentUser->name }} · {{ $currentUser->npm ?? 'NPM belum terisi' }} · {{ now()->format('d M Y') }}</div>
    @endif

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const url = @json(route('document.stream', $file));
        const canvas = document.getElementById('pdf-render');
        const context = canvas.getContext('2d');
        let pdfDocument = null;
        let pageNumber = 1;
        let isRendering = false;
        let pendingPage = null;

        function renderPage(number) {
            isRendering = true;
            pdfDocument.getPage(number).then((page) => {
                const viewport = page.getViewport({ scale: 1.5 });
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

        document.getElementById('prev-page').addEventListener('click', () => {
            if (pageNumber > 1) {
                queueRenderPage(--pageNumber);
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            if (pdfDocument && pageNumber < pdfDocument.numPages) {
                queueRenderPage(++pageNumber);
            }
        });

        pdfjsLib.getDocument({ url, disableAutoFetch: true, disableStream: true }).promise
            .then((pdf) => {
                pdfDocument = pdf;
                window.document.getElementById('page-count').textContent = pdfDocument.numPages;
                renderPage(pageNumber);
            })
            .catch(() => alert('Gagal memuat dokumen. Periksa akses atau ketersediaan berkas.'));

        document.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && ['s', 'p'].includes(event.key.toLowerCase())) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
