@extends('layouts.guest') 

@section('title', 'Daftar - ' . ($globalSettings['school_name'] ?? 'Sistem Absensi'))

@section('content')
<div class="min-h-screen flex text-gray-900">

    {{-- LEFT COLUMN: Branding (Hidden on Mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-5/12 bg-indigo-900 relative flex-col justify-between p-12 overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-tr from-purple-900 to-indigo-900 opacity-90"></div>
            {{-- Abstract Shapes --}}
            <div class="absolute bottom-0 right-0 -mr-20 -mb-20 w-96 h-96 rounded-full bg-purple-500 opacity-20 blur-3xl"></div>
            <div class="absolute top-0 left-0 -ml-20 -mt-20 w-80 h-80 rounded-full bg-indigo-500 opacity-20 blur-3xl"></div>
             <div class="absolute inset-0" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px; opacity: 0.05;"></div>
        </div>

        {{-- Branding --}}
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
                Bergabunglah <br> Bersama Kami
            </h1>
            <p class="text-indigo-200 text-lg max-w-md leading-relaxed">
                Buat akun baru untuk mulai mengelola atau memantau aktivitas akademik secara digital. Cepat, mudah, dan aman.
            </p>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 text-indigo-300 text-sm font-medium">
             &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'Sekolah' }}. All rights reserved.
        </div>
    </div>

    {{-- RIGHT COLUMN: Register Form --}}
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white relative overflow-y-auto">
        
        {{-- Mobile Header --}}
        <div class="lg:hidden text-center mb-8">
             @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-14 w-auto mx-auto mb-4">
            @endif
            <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
        </div>

        <div class="mx-auto w-full max-w-sm lg:max-w-md">
            
            <div class="text-left mb-8 hidden lg:block">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Registrasi Akun</h2>
                <p class="mt-2 text-sm text-gray-500">
                    Lengkapi formulir di bawah ini dengan benar.
                </p>
            </div>

            @if ($errors->any())
                 <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">Mohon periksa inputan Anda.</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5" id="registerForm">
                @csrf
                
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" autocomplete="name" required autofocus
                        class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition @error('name') border-red-500 @enderror" 
                        placeholder="Contoh: Budi Santoso" value="{{ old('name') }}">
                     @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition @error('email') border-red-500 @enderror" 
                        placeholder="nama@email.com" value="{{ old('email') }}">
                     @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Mendaftar Sebagai</label>
                    <div class="relative">
                        <select id="role" name="role" required class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition bg-white cursor-pointer @error('role') border-red-500 @enderror">
                            <option value="" disabled selected>Pilih Peran...</option>
                            {{-- <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option> --}}
                            <option value="wali_kelas" {{ old('role') == 'wali_kelas' ? 'selected' : '' }}>Guru / Wali Kelas</option>
                            <option value="orang_tua" {{ old('role') == 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                             <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                     @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Password Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition @error('password') border-red-500 @enderror" 
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Ulangi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition" 
                            placeholder="••••••••">
                    </div>
                </div>
                 @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="pt-2">
                    <button type="submit" id="registerBtn" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-user-plus mt-0.5 mr-2"></i> Daftar Sekarang
                    </button>
                    
                    <p class="mt-4 text-center text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition">Login disini</a>
                    </p>
                </div>
            </form>

            <div class="mt-8 border-t border-gray-100 pt-6 text-center">
                 <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 transition">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('registerBtn');
        if(this.checkValidity()){
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    });
</script>
@endsection