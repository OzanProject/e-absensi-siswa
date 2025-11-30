<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🚨 LOGIKA PENGAMBILAN SETTINGS UNTUK TITLE DAN FAVICON --}}
    @php
        use Illuminate\Support\Facades\Storage;
        
        // Menggunakan fallback jika settings belum dimuat
        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
        $schoolName = $settings['school_name'] ?? config('app.name', 'E-Absensi');
        $schoolLogoPath = $settings['school_logo'] ?? 'default/favicon.ico'; 
        
        // --- LOGIKA PATH FAVICON/LOGO ---
        $faviconUrl = asset('images/default/favicon.ico'); 
        
        // 🔥 PERBAIKAN: Gunakan path default yang lebih aman jika path DB kosong
        if (!empty($schoolLogoPath) && $schoolLogoPath != 'default/favicon.ico' && Storage::disk('public')->exists($schoolLogoPath)) {
            $faviconUrl = asset('storage/' . $schoolLogoPath);
        }
    @endphp
    
    <title>@yield('title') - {{ $schoolName }}</title>

    {{-- FAVICON DINAMIS --}}
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/x-icon">

    {{-- Font Tailwind: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    
    {{-- Font Awesome (Tetap dipakai untuk ikon) --}}
    <link rel="stylesheet" href="{{ asset('template/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    
    {{-- 🔥 TAILWIND CSS (MENGGANTIKAN SEMUA CSS ADMINLTE) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    {{-- Custom CSS --}}
    @stack('css')

    {{-- Custom Styling untuk Form Login yang Di-Tailwind-kan --}}
    <style>
        /* Menggantikan 'hold-transition login-page' dengan class Tailwind */
        .tailwind-login-page {
            /* Flexbox penuh layar, latar belakang abu-abu */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f3f4f6; /* bg-gray-100 */
            font-family: 'Inter', sans-serif;
        }
        /* Style untuk container login (Opsional, tergantung konten @yield('content')) */
        .login-box {
            width: 90%;
            max-width: 400px;
            margin: 1rem;
        }
    </style>

</head>
<body class="tailwind-login-page">

    {{-- Wrapper Konten (Misalnya untuk form Login/Register) --}}
    <div class="login-box">
        @yield('content')
    </div>
    
    {{-- REQUIRED SCRIPTS (Hanya JQuery dan Bootstrap jika diperlukan) --}}
    <script src="{{ asset('template/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    {{-- adminlte.min.js TIDAK diperlukan lagi karena styling diganti Tailwind --}}

    {{-- Custom JavaScript --}}
    @stack('js')

</body>
</html>