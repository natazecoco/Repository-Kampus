<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositori Ilmiah - Universitas Gunadarma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
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

    <!-- [BARU] CSS x-cloak agar modal tidak muncul sebelum diklik -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- [BARU] CDN Alpine.js untuk mengaktifkan fitur modal & interaktivitas -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gundar-light font-sans text-slate-800 selection:bg-gundar-primary selection:text-white">

    <!-- PANGGIL NAVBAR (PARTIALS) -->
    @include('partials.navbar')

    <!-- MAIN WRAPPER -->
    <div class="pt-28 pb-16">
        @yield('content')
    </div>

    <!-- PANGGIL FOOTER (PARTIALS) -->
    @include('partials.footer')

</body>
</html>