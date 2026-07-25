@php
    $currentUser = auth()->user();
    $isRestricted = $file->access_type === 'restricted';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Document Viewer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        body {
            background-color: #f8fafc;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: sans-serif;
            user-select: none;
            -webkit-user-select: none;
        }
        .toolbar {
            background-color: #0f172a;
            width: 100%;
            padding: 15px 0;
            color: white;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        .toolbar button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
            font-weight: bold;
        }
        .toolbar button:hover { background-color: #1d4ed8; }
        .viewer-shell {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 20px 0 40px;
            position: relative;
        }
        #pdf-render {
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
            max-width: 100%;
            background: white;
            pointer-events: none;
        }
        .watermark {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 50;
            opacity: 0.3;
            transform: rotate(-18deg);
            color: #0f172a;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.05em;
            background: rgba(255,255,255,0.65);
            padding: 10px 14px;
            border: 1px solid rgba(15, 23, 42, 0.15);
            border-radius: 999px;
            pointer-events: none;
        }
    </style>
</head>
<body oncontextmenu="return false;">
    <div class="toolbar">
        <button id="prev-page">⬅ Sebelumnya</button>
        <span>Halaman: <span id="page-num"></span> / <span id="page-count"></span></span>
        <button id="next-page">Selanjutnya ➡</button>
    </div>

    <div class="viewer-shell">
        <canvas id="pdf-render"></canvas>
    </div>

    @if($isRestricted && $currentUser)
        <div class="watermark">
            {{ $currentUser->name }} · {{ $currentUser->npm ?? 'NPM belum terisi' }} · {{ now()->format('d M Y') }}
        </div>
    @endif

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const url = "{{ route('document.stream', ['id' => $file->id]) }}";

        let pdfDoc = null,
            pageNum = 1,
            pageIsRendering = false,
            pageNumIsPending = null;

        const scale = 1.5,
              canvas = document.getElementById('pdf-render'),
              ctx = canvas.getContext('2d');

        const renderPage = num => {
            pageIsRendering = true;

            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderCtx = { canvasContext: ctx, viewport };

                page.render(renderCtx).promise.then(() => {
                    pageIsRendering = false;
                    if (pageNumIsPending !== null) {
                        renderPage(pageNumIsPending);
                        pageNumIsPending = null;
                    }
                });

                document.getElementById('page-num').textContent = num;
            });
        };

        const queueRenderPage = num => {
            if (pageIsRendering) {
                pageNumIsPending = num;
            } else {
                renderPage(num);
            }
        };

        const showPrevPage = () => {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        };

        const showNextPage = () => {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        };

        pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        }).catch(err => {
            console.error('Error loading PDF:', err);
            alert('Gagal memuat dokumen. Pastikan Anda sudah login dan memiliki akses.');
        });

        document.getElementById('prev-page').addEventListener('click', showPrevPage);
        document.getElementById('next-page').addEventListener('click', showNextPage);

        document.addEventListener('keydown', (e) => {
            const blockedKeys = ['PrintScreen', 'F12', 'Meta', 'Control', 's', 'p'];
            if ((e.ctrlKey || e.metaKey) && blockedKeys.includes(e.key)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>