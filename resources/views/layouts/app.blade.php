<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositori Ilmiah - Universitas Gunadarma</title>
    
    <!-- [BARU] Google Fonts: Plus Jakarta Sans untuk kesan modern dan elegan -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        // Mengganti font bawaan Tailwind dengan Plus Jakarta Sans
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        gundar: {
                            primary: '#763a97', 
                            accent: '#f89723',
                            dark: '#111111', 
                            muted: '#938a8e', 
                            light: '#fdfcfb',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Mencegah kedipan UI Alpine.js saat halaman dimuat */
        [x-cloak] { display: none !important; }

        /* [BARU] Custom Scrollbar Premium */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen font-sans text-slate-800 selection:bg-gundar-primary selection:text-white relative bg-[#fdfcfb]">

    <!-- [BARU] Ambient Glow Background -->
    <!-- Elemen ini diam di tempat dan memberikan pancaran warna ungu sangat halus dari sudut kanan atas -->
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-gundar-primary/10 via-transparent to-transparent pointer-events-none"></div>

    <!-- PANGGIL NAVBAR (PARTIALS) -->
    @include('partials.navbar')

    <!-- MAIN WRAPPER -->
    <!-- Menambahkan z-10 agar konten selalu di atas background ambient -->
    <div class="pt-28 pb-16 relative z-10">
        @yield('content')
    </div>

    <!-- PANGGIL FOOTER (PARTIALS) -->
    @include('partials.footer')

</body>
</html>