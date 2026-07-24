<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Document Viewer</title>
    <!-- Memanggil library PDF.js dari CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        body {
            background-color: #2c3e50;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: sans-serif;
            
            /* Fitur Anti-Maling CSS */
            user-select: none; /* Mencegah text block/select */
            -webkit-user-select: none;
        }
        .toolbar {
            background-color: #1a252f;
            width: 100%;
            padding: 15px 0;
            color: white;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .toolbar button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
            font-weight: bold;
        }
        .toolbar button:hover { background-color: #2980b9; }
        #pdf-render {
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            max-width: 100%;
            /* Mematikan klik kanan khusus di area kertas PDF */
            pointer-events: none; 
        }
    </style>
</head>
<!-- oncontextmenu="return false;" mematikan klik kanan di seluruh layar -->
<body oncontextmenu="return false;">

    <div class="toolbar">
        <button id="prev-page">⬅ Sebelumnya</button>
        <span>Halaman: <span id="page-num"></span> / <span id="page-count"></span></span>
        <button id="next-page">Selanjutnya ➡</button>
    </div>

    <!-- Tempat PDF akan dirender menjadi gambar kanvas -->
    <canvas id="pdf-render"></canvas>

    <script>
        // Set lokasi worker PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Mengambil aliran (stream) data PDF dari rute Laravel
        const url = "{{ route('document.stream', ['id' => $file->id]) }}";

        let pdfDoc = null,
            pageNum = 1,
            pageIsRendering = false,
            pageNumIsPending = null;

        const scale = 1.5,
              canvas = document.getElementById('pdf-render'),
              ctx = canvas.getContext('2d');

        // Fungsi render halaman
        const renderPage = num => {
            pageIsRendering = true;

            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderCtx = { canvasContext: ctx, viewport: viewport };

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

        // Muat Dokumen PDF
        pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        }).catch(err => {
            console.error("Error loading PDF:", err);
            alert("Gagal memuat dokumen. Pastikan Anda sudah login dan memiliki akses.");
        });

        // Event listener tombol navigasi
        document.getElementById('prev-page').addEventListener('click', showPrevPage);
        document.getElementById('next-page').addEventListener('click', showNextPage);

        // Mencegah screenshot via keyboard Print Screen
        document.addEventListener('keyup', (e) => {
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                alert('Screenshot dinonaktifkan untuk keamanan dokumen!');
            }
        });
    </script>
</body>
</html>