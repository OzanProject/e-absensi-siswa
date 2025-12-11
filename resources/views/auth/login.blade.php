@extends('layouts.guest') 

@section('title', 'Login - ' . ($globalSettings['school_name'] ?? 'Sistem Absensi'))

@section('content')
<div class="min-h-screen flex text-gray-900">
    
    {{-- LEFT COLUMN: Branding & Hero (Hidden on Mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-7/12 bg-indigo-900 relative flex-col justify-between p-12 overflow-hidden" data-aos="fade-right">
        {{-- Background Pattern/Image --}}
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 to-blue-900 opacity-90"></div>
            {{-- Abstract Shapes --}}
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-500 opacity-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-500 opacity-20 blur-3xl"></div>
            {{-- Pattern Grid --}}
             <div class="absolute inset-0" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px; opacity: 0.05;"></div>
        </div>

        {{-- Top Branding --}}
        <div class="relative z-10 flex items-center space-x-3">
             @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-10 w-auto bg-white/10 p-1.5 rounded-lg backdrop-blur-sm">
            @else
                <div class="h-10 w-10 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-school text-white"></i>
                </div>
            @endif
            <span class="text-white font-bold text-lg tracking-wide uppercase opacity-90">{{ $globalSettings['school_name'] ?? 'E-Absensi' }}</span>
        </div>

        {{-- Hero Text --}}
        <div class="relative z-10 mt-10">
            <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-6">
                Selamat Datang di <br> 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-indigo-200">
                    Sistem Manajemen Absensi
                </span>
            </h1>
            <p class="text-indigo-200 text-lg max-w-md leading-relaxed">
                Kelola kehadiran siswa, pantau kedisiplinan, dan akses laporan akademik secara real-time dalam satu platform terintegrasi yang modern.
            </p>
        </div>

        {{-- Footer/Copyright --}}
        <div class="relative z-10 text-indigo-300 text-sm font-medium">
            &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'Sekolah' }}. All rights reserved.
        </div>
    </div>

    {{-- RIGHT COLUMN: Login Form --}}
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white relative" data-aos="fade-left">
        
        {{-- Mobile Header (Only visible on small screens) --}}
        <div class="lg:hidden text-center mb-10">
             @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-16 w-auto mx-auto mb-4">
            @endif
            <h2 class="text-2xl font-bold text-gray-900">{{ $globalSettings['school_name'] ?? 'E-Absensi Siswa' }}</h2>
        </div>

        <div class="mx-auto w-full max-w-sm lg:max-w-md">
            
            <div class="text-left mb-10">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Masuk ke Akun</h2>
                <p class="mt-2 text-sm text-gray-500">
                    Silakan masukkan kredensial Anda untuk melanjutkan.
                </p>
            </div>

            {{-- Alerts --}}
            @if (session('status') || session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('status') ?? session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                 <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6" id="loginForm">
                @csrf
                
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative rounded-md shadow-sm">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="appearance-none block w-full pl-10 px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200" 
                            placeholder="nama@email.com" value="{{ old('email') }}">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="appearance-none block w-full pl-10 px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200" 
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">Ingat Saya</label>
                    </div>

                    <div class="text-sm">
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Lupa password?
                        </a>
                        @endif
                    </div>
                </div>

                <div>
                    <button type="submit" id="loginBtn" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-sign-in-alt mt-0.5 mr-2"></i> Masuk Sekarang
                    </button>
                    
                    <p class="mt-4 text-center text-sm text-gray-600">
                        Belum punya akun? 
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition">Daftar disini</a>
                        @endif
                    </p>
                </div>
            </form>
            
            <div class="mt-10 border-t border-gray-100 pt-6">
                 <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    });
</script>
@endsection