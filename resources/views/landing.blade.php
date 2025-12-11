<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- LOGIKA PENGAMBILAN SETTINGS --}}
    @php
        use Illuminate\Support\Facades\Storage;
        
        $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
        $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
        $schoolLogoPath = $settings['school_logo'] ?? null;
        
        $defaultLogo = asset('images/default_logo.png'); 
        $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
    @endphp
    
    <title>{{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ $finalLogo }}">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Styles & Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Navbar Solid on Scroll */
        .scrolled-nav {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        /* Subtle Background Gradients instead of Blobs for cleaner look */
        .bg-subtle-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-white overflow-x-hidden" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
    
    {{-- GLOBAL LOADER --}}
    @include('layouts.partials.loader')

    {{-- NAVBAR --}}
    <nav :class="{ 'scrolled-nav': scrolled, 'bg-transparent py-5': !scrolled }" class="fixed top-0 w-full z-50 transition-all duration-300">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <a href="#" class="flex items-center gap-3">
                    <img src="{{ $finalLogo }}" alt="Logo" class="w-10 h-10 object-contain bg-white rounded-lg shadow-sm p-1">
                    <div>
                        <span class="block text-lg font-bold text-slate-900 leading-tight">{{ $schoolName }}</span>
                        <span class="text-xs text-slate-500 font-medium tracking-wide">SYSTEM</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-2">
                    <a href="#features" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition rounded-lg hover:bg-slate-50">Fitur</a>
                    <a href="#how" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-indigo-600 transition rounded-lg hover:bg-slate-50">Cara Kerja</a>
                    
                    <div class="h-6 w-px bg-slate-200 mx-2"></div>

                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                            Masuk
                        </a>
                    @endauth
                </div>

                {{-- Mobile Toggle --}}
                <div class="md:hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute top-full right-4 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 p-2 flex flex-col space-y-1"
                         style="display: none;">
                        <a href="#features" @click="open = false" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">Fitur</a>
                        <a href="#how" @click="open = false" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">Cara Kerja</a>
                        <div class="border-t border-slate-100 my-1"></div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm font-bold text-indigo-600 hover:bg-indigo-50 rounded-lg">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-sm font-bold text-indigo-600 hover:bg-indigo-50 rounded-lg">Masuk</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <header class="relative pt-32 pb-16 lg:pt-48 lg:pb-32 bg-subtle-gradient overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <div class="text-center lg:text-left z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-6">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Absensi Digital v2.0
                    </div>
                    
                    <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 mb-6 leading-tight">
                        Manajemen Absensi <br>
                        <span class="text-indigo-600">Lebih Cepat & Akurat</span>
                    </h1>
                    
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg mx-auto lg:mx-0">
                        {{ $settings['site_description'] ?? 'Sistem absensi siswa terintegrasi dengan QR Code dan notifikasi WhatsApp real-time untuk sekolah modern.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 text-white bg-indigo-600 rounded-lg font-bold shadow-md hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 text-white bg-indigo-600 rounded-lg font-bold shadow-md hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-qrcode mr-2"></i> Mulai Absensi
                            </a>
                            <a href="#more" class="w-full sm:w-auto px-8 py-3.5 text-slate-700 bg-white border border-slate-300 rounded-lg font-bold hover:bg-slate-50 transition-all">
                                Pelajari Lebih Lanjut
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="relative flex justify-center z-10">
                    {{-- Simple Clean Illustration Card --}}
                    <div class="bg-white p-2 rounded-2xl shadow-xl border border-slate-100 transform rotate-2 hover:rotate-0 transition-transform duration-500 max-w-sm w-full">
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 flex flex-col items-center">
                            <i class="fas fa-qrcode text-6xl text-slate-800 mb-4"></i>
                            <div class="h-1.5 w-32 bg-slate-200 rounded-full mb-2"></div>
                            <div class="h-1.5 w-20 bg-slate-200 rounded-full mb-6"></div>
                            
                            <div class="w-full bg-white p-4 rounded-lg border border-slate-100 shadow-sm flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-full text-green-600"><i class="fas fa-check"></i></div>
                                <div>
                                    <div class="text-xs text-slate-400 font-bold uppercase">Status</div>
                                    <div class="text-sm font-bold text-slate-800">Berhasil Masuk</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- FEATURES SECTION --}}
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-indigo-600 font-bold uppercase text-sm mb-2">Fitur Unggulan</h2>
                <h3 class="text-3xl font-bold text-slate-900">Solusi Modern Sekolah</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="group p-8 rounded-3xl bg-white border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-50 rounded-full blur-3xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Scan Cepat</h4>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Absensi siswa hanya dalam hitungan detik menggunakan QR Code. Akurat, real-time, dan anti-titip absen.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="group p-8 rounded-3xl bg-white border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-green-50 rounded-full blur-3xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="w-14 h-14 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Notif WhatsApp</h4>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Kirim pesan otomatis ke orang tua saat siswa absen masuk atau pulang sekolah. Transparan dan terpantau.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="group p-8 rounded-3xl bg-white border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-purple-50 rounded-full blur-3xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Laporan Lengkap</h4>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Unduh rekap kehadiran harian, bulanan, hingga semester dalam format PDF siap cetak untuk administrasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how" class="py-20 bg-slate-50 border-y border-slate-200">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-2xl font-bold text-slate-900 mb-12">Alur Penggunaan</h3>
            
            <div class="flex flex-col md:flex-row justify-center items-center gap-8 md:gap-12">
                <div class="flex flex-col items-center max-w-xs">
                    <span class="w-10 h-10 rounded-full bg-white border border-slate-300 flex items-center justify-center font-bold text-slate-600 mb-4 shadow-sm">1</span>
                    <h5 class="font-bold text-slate-900">Buka Menu Scan</h5>
                    <p class="text-sm text-slate-500 mt-2">Admin/Guru membuka halaman scan QR.</p>
                </div>
                
                <i class="fas fa-arrow-right text-slate-300 hidden md:block"></i>
                
                <div class="flex flex-col items-center max-w-xs">
                    <span class="w-10 h-10 rounded-full bg-white border border-slate-300 flex items-center justify-center font-bold text-slate-600 mb-4 shadow-sm">2</span>
                    <h5 class="font-bold text-slate-900">Siswa Scan Kartu</h5>
                    <p class="text-sm text-slate-500 mt-2">Arahkan kartu ke kamera laptop/PC.</p>
                </div>
                
                <i class="fas fa-arrow-right text-slate-300 hidden md:block"></i>
                
                <div class="flex flex-col items-center max-w-xs">
                    <span class="w-10 h-10 rounded-full bg-white border border-slate-300 flex items-center justify-center font-bold text-slate-600 mb-4 shadow-sm">3</span>
                    <h5 class="font-bold text-slate-900">Data Tersimpan</h5>
                    <p class="text-sm text-slate-500 mt-2">Sistem mencatat & mengirim notifikasi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-white py-12">
        <div class="container mx-auto px-4 border-t border-slate-100 pt-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ $finalLogo }}" class="h-8 w-8 object-contain bg-slate-50 rounded p-0.5">
                        <span class="font-bold text-slate-900">{{ $schoolName }}</span>
                    </div>
                </div>
                
                <div>
                     <h5 class="font-bold text-slate-900 mb-4 text-sm uppercase">Menu</h5>
                     <ul class="space-y-2 text-sm text-slate-500">
                         <li><a href="#" class="hover:text-indigo-600">Beranda</a></li>
                         <li><a href="{{ route('login') }}" class="hover:text-indigo-600">Login Admin</a></li>
                     </ul>
                </div>

                <div>
                    <h5 class="font-bold text-slate-900 mb-4 text-sm uppercase">Hubungi Kami</h5>
                    <ul class="space-y-2 text-sm text-slate-500">
                        @if(!empty($settings['school_email']))
                            <li class="flex items-center gap-2"><i class="fas fa-envelope w-4"></i> {{ $settings['school_email'] }}</li>
                        @endif
                        @if(!empty($settings['school_phone']))
                            <li class="flex items-center gap-2"><i class="fas fa-phone w-4"></i> {{ $settings['school_phone'] }}</li>
                        @endif
                         @if(!empty($settings['school_address']))
                            <li class="flex items-start gap-2"><i class="fas fa-map-marker-alt w-4 mt-1"></i> {{ $settings['school_address'] }}</li>
                        @endif
                        
                        <li class="flex gap-4 mt-4">
                            @if(!empty($settings['social_facebook']))
                                <a href="{{ $settings['social_facebook'] }}" class="text-slate-400 hover:text-blue-600 text-xl"><i class="fab fa-facebook"></i></a>
                            @endif
                            @if(!empty($settings['social_instagram']))
                                <a href="{{ $settings['social_instagram'] }}" class="text-slate-400 hover:text-pink-600 text-xl"><i class="fab fa-instagram"></i></a>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center text-xs text-slate-400 border-t border-slate-50 pt-8">
                &copy; {{ date('Y') }} {{ $schoolName }}. Powered by E-Absensi.
            </div>
        </div>
    </footer>
</body>
</html>