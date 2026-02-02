<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'E-Absensi Siswa')</title>

    {{-- Favicon Logic --}}
    @php
        // Ambil pengaturan dari DB
        $siteSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $logoPath = $siteSettings['school_logo'] ?? null;
        
        // Cek apakah file fisik logo ada, jika tidak pakai file favicon standar di folder public
        $finalFavicon = ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) 
                        ? asset('storage/' . $logoPath) 
                        : asset('favicon.ico'); 
    @endphp
    <link rel="icon" type="image/png" href="{{ $finalFavicon }}">

    {{-- Fonts & Icons --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js (Untuk Interaktivitas Navbar) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: #0f172a;
            /* Menghilangkan 'flash' putih saat loading */
            color: #cbd5e1;
            overflow-x: hidden;
            width: 100%;
        }

        h1,
        h2,
        h3,
        h4,
        .font-heading {
            font-family: 'Outfit', sans-serif !important;
        }

        /* Memastikan tidak ada elemen yang melampaui lebar layar */
        * {
            max-width: 100%;
            box-sizing: border-box;
        }
    </style>
    @stack('styles')
</head>

<body class="antialiased font-sans">

    @include('landing.partials.navbar')

    <main class="relative">
        @yield('content')
    </main>

    @include('landing.partials.footer')

    @stack('scripts')
</body>

</html>