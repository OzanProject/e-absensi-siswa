<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- LOGIKA PENGAMBILAN SETTINGS UNTUK TITLE DAN FAVICON --}}
    @php
        use Illuminate\Support\Facades\Storage;
        
        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
        $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
        $schoolLogoPath = $settings['school_logo'] ?? null;
        
        // --- LOGIKA PATH LOGO ---
        $defaultLogo = asset('images/default_logo.png'); 
        $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
    @endphp
    
    <title>{{ $settings['school_name'] ?? 'E-Absensi Siswa' }}</title>
    
    <link rel="icon" type="image/png" href="{{ $finalLogo }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    {{-- Tailwind CSS Styles --}}
    @vite(['resources/css/app.css'])

    <style>
        /* --- TAILWIND UTILITY EXTENSIONS (Minimal Custom CSS) --- */
        
        /* Warna Brand */
        :root {
            --primary-color: #4f46e5; /* Indigo/Ungu */
            --secondary-color: #3b82f6; /* Biru */
        }
        
        /* Gradient Hero Section */
        .bg-gradient-hero {
            background: linear-gradient(145deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #f3f4f6; /* text-gray-100 */
        }
        
        /* Shadow yang Kuat */
        .shadow-custom-lg { box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4); }

        /* --- LAYOUTS --- */
        .hero-section {
            min-height: 100vh;
            padding-top: 5rem; /* pt-20 */
        }
        .hero-title {
            font-size: 3rem; /* text-5xl */
            line-height: 1.2;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- NAVBAR (Fixed Top, Transparent) --}}
    <nav class="fixed top-0 w-full z-10 bg-transparent py-4">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a class="flex items-center text-white text-xl font-bold" href="#">
                {{-- 💡 DINAMIS: Logo di Navbar --}}
                <img src="{{ $finalLogo }}" alt="Logo" class="max-h-10 rounded-lg mr-3">
                <span class="text-white">{{ $settings['school_name'] ?? 'E-Absensi Siswa' }}</span>
            </a>
            
            <div class="hidden lg:flex items-center space-x-6">
                <a class="text-white hover:text-gray-200 transition duration-150" href="#features">
                    <i class="fas fa-list-ul mr-1"></i> Fitur
                </a>
                
                @auth
                    <a class="btn bg-white text-indigo-600 px-4 py-2 text-sm font-semibold rounded-lg shadow-md hover:bg-gray-100" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                @else
                    <a class="btn bg-white text-indigo-600 px-4 py-2 text-sm font-semibold rounded-lg shadow-md hover:bg-gray-100" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- 1. HERO SECTION --}}
    <header class="hero-section bg-gradient-hero flex items-center justify-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 text-center lg:text-left mb-8 lg:mb-0">
                    <h1 class="hero-title text-white">
                        Sistem <span class="text-yellow-300">Absensi</span> Siswa Cepat & Terintegrasi
                    </h1>
                    {{-- 💡 DINAMIS: Deskripsi Situs --}}
                    <p class="text-xl text-gray-200 mt-4 mb-8 opacity-90">
                        {{ $settings['site_description'] ?? 'Sistem Absensi Siswa Digital berbasis Barcode yang cepat, akurat, dan terintegrasi dengan notifikasi orang tua.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center lg:justify-start">
                        @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-indigo-600 bg-yellow-300 shadow-custom-lg hover:bg-yellow-400 transition duration-300">
                            Lanjut ke Dashboard <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-indigo-600 bg-white shadow-custom-lg hover:bg-gray-100 transition duration-300">
                            Mulai Absen Sekarang <i class="fas fa-sign-in-alt ml-2"></i>
                        </a>
                        <a href="#features" class="inline-flex items-center px-6 py-3 border border-white text-base font-semibold rounded-lg text-white hover:bg-white/10 transition duration-300">
                            Pelajari Fitur
                        </a>
                        @endauth
                    </div>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="bg-white/10 border border-white/30 rounded-xl p-4 shadow-custom-lg w-full max-w-sm">
                        <h5 class="text-white text-lg font-semibold mb-3"><i class="fas fa-qrcode mr-2"></i> Scan Absensi Cepat</h5>
                        <p class="text-sm text-gray-200">Integrasikan kamera perangkat Anda untuk scanning QR code kartu pelajar secara instan.</p>
                         
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- 2. FEATURE SECTION --}}
    <section id="features" class="py-16 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-indigo-600 mb-10">Fitur Unggulan E-Absensi</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Card 1: Scan Barcode Instan --}}
                <div class="hover:shadow-lg transition duration-300 rounded-xl">
                    <div class="bg-white feature-card p-6 border border-gray-200 h-full">
                        <i class="fas fa-qrcode text-4xl text-indigo-600 mb-4"></i>
                        <h5 class="text-xl font-bold text-gray-800">Scan Barcode Instan</h5>
                        <p class="text-sm text-gray-500 mt-2">Mencatat waktu masuk dan pulang secara otomatis menggunakan scan QR/Barcode kartu pelajar dalam hitungan detik.</p>
                    </div>
                </div>
                
                {{-- Card 2: Notifikasi Orang Tua --}}
                <div class="hover:shadow-lg transition duration-300 rounded-xl">
                    <div class="bg-white feature-card p-6 border border-gray-200 h-full">
                        <i class="fab fa-whatsapp text-4xl text-green-600 mb-4"></i>
                        <h5 class="text-xl font-bold text-gray-800">Notifikasi Orang Tua</h5>
                        <p class="text-sm text-gray-500 mt-2">Kirim notifikasi WhatsApp otomatis kepada wali murid saat siswa masuk atau pulang sekolah, termasuk status terlambat.</p>
                    </div>
                </div>
                
                {{-- Card 3: Laporan Real-Time --}}
                <div class="hover:shadow-lg transition duration-300 rounded-xl">
                    <div class="bg-white feature-card p-6 border border-gray-200 h-full">
                        <i class="fas fa-chart-bar text-4xl text-red-600 mb-4"></i>
                        <h5 class="text-xl font-bold text-gray-800">Laporan Real-Time</h5>
                        <p class="text-sm text-gray-500 mt-2">Akses laporan kehadiran, rekap keterlambatan, dan status kehadiran (Sakit/Izin/Alpha) secara *real-time* dan akurat.</p>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    {{-- 3. FOOTER SEDERHANA --}}
    <footer class="text-center py-4 bg-gray-100 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <p class="text-sm text-gray-500">
                Copyright &copy; {{ date('Y') }} {{ $settings['school_name'] ?? 'E-Absensi Siswa' }}.
            </p>
        </div>
    </footer>

</body>
</html>